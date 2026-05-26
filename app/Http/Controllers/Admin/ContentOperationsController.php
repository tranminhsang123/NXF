<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentPublishRequest;
use App\Models\ContentVersion;
use App\Services\AdminAuditService;
use App\Services\ContentPublishQualityService;
use App\Services\ContentValidationService;
use App\Support\AdminContentRegistry;
use App\Support\PublishStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ContentOperationsController extends Controller
{
    public function __construct(
        private ContentValidationService $validationService,
        private ContentPublishQualityService $publishQualityService
    ) {}

    public function index(Request $request)
    {
        $selectedType = (string) $request->query('type', 'minna_lesson');
        if (! array_key_exists($selectedType, AdminContentRegistry::types())) {
            $selectedType = 'minna_lesson';
        }

        $class = AdminContentRegistry::classFor($selectedType);
        $query = $class::query()->latest();

        if ($request->filled('status')) {
            $query->where('publish_status', (string) $request->query('status'));
        }

        $items = $query->paginate(20)->withQueryString();
        $stats = [];

        foreach (AdminContentRegistry::types() as $type => $entry) {
            $modelClass = $entry['class'];
            $stats[$type] = [
                'label' => $entry['label'],
                'draft' => $modelClass::query()->where('publish_status', PublishStatus::DRAFT)->count(),
                'published' => $modelClass::query()->where('publish_status', PublishStatus::PUBLISHED)->count(),
                'archived' => $modelClass::query()->where('publish_status', PublishStatus::ARCHIVED)->count(),
            ];
        }

        return view('admin.content-ops.index', [
            'types' => AdminContentRegistry::types(),
            'statuses' => PublishStatus::labels(),
            'selectedType' => $selectedType,
            'items' => $items,
            'stats' => $stats,
        ]);
    }

    public function updateStatus(Request $request, string $type, int $id)
    {
        $data = $request->validate([
            'publish_status' => ['required', 'in:draft,published,archived'],
        ]);

        $item = AdminContentRegistry::find($type, $id);
        $status = $data['publish_status'];

        $qualityBlockers = $status === PublishStatus::PUBLISHED
            ? $this->publishQualityService->blockingMessages($item)
            : [];

        if ($qualityBlockers !== []) {
            return back()->withErrors([
                'quality_gate' => 'Chưa thể xuất bản vì checklist QA chưa đạt: '.implode(' | ', $qualityBlockers),
            ]);
        }

        $item->forceFill([
            'publish_status' => $status,
            'published_at' => $status === PublishStatus::PUBLISHED ? now() : $item->published_at,
            'archived_at' => $status === PublishStatus::ARCHIVED ? now() : null,
        ])->save();

        return back()->with('success', 'Đã cập nhật trạng thái nội dung.');
    }

    public function preview(string $type, int $id)
    {
        $item = AdminContentRegistry::find($type, $id);
        $issues = $this->validationService->validate($item);
        $qualityChecklist = $this->publishQualityService->checklist($item);
        $publishRequests = ContentPublishRequest::query()
            ->where('content_type', $type)
            ->where('content_id', $id)
            ->with(['requester:id,name,email', 'reviewer:id,name,email'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.content-ops.preview', [
            'type' => $type,
            'label' => AdminContentRegistry::labelFor($type),
            'title' => AdminContentRegistry::titleFor($item),
            'item' => $item,
            'snapshot' => AdminContentRegistry::snapshot($item),
            'issues' => $issues,
            'qualityChecklist' => $qualityChecklist,
            'publishRequests' => $publishRequests,
        ]);
    }

    public function requestPublish(Request $request, string $type, int $id)
    {
        $data = $request->validate([
            'requested_status' => ['required', 'in:published,archived'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'scheduled_publish_at' => ['nullable', 'date'],
        ]);

        $item = AdminContentRegistry::find($type, $id);
        $scheduledAt = ! empty($data['scheduled_publish_at'])
            ? Carbon::parse($data['scheduled_publish_at'])
            : null;

        $publishRequest = ContentPublishRequest::query()->create([
            'content_type' => $type,
            'content_id' => $id,
            'requested_by' => $request->user()->id,
            'requested_status' => $data['requested_status'],
            'status' => ContentPublishRequest::STATUS_PENDING,
            'notes' => $data['notes'] ?? null,
            'scheduled_publish_at' => $scheduledAt,
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $item,
            'content_publish_requested',
            'Đã gửi yêu cầu xuất bản nội dung: '.AdminContentRegistry::titleFor($item),
            null,
            null,
            ['publish_request_id' => $publishRequest->id, 'requested_status' => $data['requested_status']]
        );

        return back()->with('success', 'Đã gửi yêu cầu duyệt nội dung.');
    }

    public function approveRequest(Request $request, ContentPublishRequest $publishRequest)
    {
        abort_unless($publishRequest->status === ContentPublishRequest::STATUS_PENDING, 422);

        $data = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
            'scheduled_publish_at' => ['nullable', 'date'],
        ]);

        $item = AdminContentRegistry::find($publishRequest->content_type, (int) $publishRequest->content_id);
        $scheduledAt = ! empty($data['scheduled_publish_at'])
            ? Carbon::parse($data['scheduled_publish_at'])
            : $publishRequest->scheduled_publish_at;

        $qualityBlockers = $publishRequest->requested_status === PublishStatus::PUBLISHED
            ? $this->publishQualityService->blockingMessages($item)
            : [];

        if ($qualityBlockers !== []) {
            return back()->withErrors([
                'quality_gate' => 'Chưa thể duyệt xuất bản vì checklist QA chưa đạt: '.implode(' | ', $qualityBlockers),
            ]);
        }

        if ($publishRequest->requested_status === PublishStatus::PUBLISHED && $scheduledAt && $scheduledAt->isFuture()) {
            $publishRequest->update([
                'status' => ContentPublishRequest::STATUS_SCHEDULED,
                'reviewed_by' => $request->user()->id,
                'review_notes' => $data['review_notes'] ?? null,
                'scheduled_publish_at' => $scheduledAt,
                'reviewed_at' => now(),
            ]);

            $message = 'Đã duyệt và hẹn lịch xuất bản nội dung.';
        } else {
            $this->applyRequestedStatus($item, $publishRequest->requested_status);
            $publishRequest->update([
                'status' => $publishRequest->requested_status === PublishStatus::PUBLISHED
                    ? ContentPublishRequest::STATUS_PUBLISHED
                    : ContentPublishRequest::STATUS_APPROVED,
                'reviewed_by' => $request->user()->id,
                'review_notes' => $data['review_notes'] ?? null,
                'scheduled_publish_at' => $scheduledAt,
                'reviewed_at' => now(),
                'published_at' => $publishRequest->requested_status === PublishStatus::PUBLISHED ? now() : null,
            ]);

            $message = 'Đã duyệt và áp dụng trạng thái nội dung.';
        }

        app(AdminAuditService::class)->audit(
            $request->user(),
            $item,
            'content_publish_approved',
            'Đã duyệt yêu cầu nội dung: '.AdminContentRegistry::titleFor($item),
            null,
            null,
            ['publish_request_id' => $publishRequest->id, 'scheduled_publish_at' => $scheduledAt?->toDateTimeString()]
        );

        return back()->with('success', $message);
    }

    public function rejectRequest(Request $request, ContentPublishRequest $publishRequest)
    {
        abort_unless($publishRequest->status === ContentPublishRequest::STATUS_PENDING, 422);

        $data = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $item = AdminContentRegistry::find($publishRequest->content_type, (int) $publishRequest->content_id);

        $publishRequest->update([
            'status' => ContentPublishRequest::STATUS_REJECTED,
            'reviewed_by' => $request->user()->id,
            'review_notes' => $data['review_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $item,
            'content_publish_rejected',
            'Đã từ chối yêu cầu nội dung: '.AdminContentRegistry::titleFor($item),
            null,
            null,
            ['publish_request_id' => $publishRequest->id]
        );

        return back()->with('success', 'Đã từ chối yêu cầu duyệt.');
    }

    public function versions(string $type, int $id)
    {
        $item = AdminContentRegistry::find($type, $id);
        $versions = ContentVersion::query()
            ->where('versionable_type', $item::class)
            ->where('versionable_id', $item->getKey())
            ->with('actor:id,name,email')
            ->latest()
            ->paginate(20);

        return view('admin.content-ops.versions', [
            'type' => $type,
            'item' => $item,
            'title' => AdminContentRegistry::titleFor($item),
            'versions' => $versions,
        ]);
    }

    public function restore(ContentVersion $version)
    {
        /** @var Model|null $item */
        $item = $version->versionable;
        abort_unless($item, 404);

        $item->fill($version->snapshot ?? []);
        $item->save();

        $type = AdminContentRegistry::typeFor($item) ?? 'minna_lesson';

        return redirect()
            ->route('admin.content-ops.preview', ['type' => $type, 'id' => $item->getKey()])
            ->with('success', 'Đã khôi phục phiên bản.');
    }

    private function applyRequestedStatus(Model $item, string $status): void
    {
        $item->forceFill([
            'publish_status' => $status,
            'published_at' => $status === PublishStatus::PUBLISHED ? now() : $item->published_at,
            'archived_at' => $status === PublishStatus::ARCHIVED ? now() : null,
        ])->save();
    }
}
