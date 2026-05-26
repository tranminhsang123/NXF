<?php

namespace App\Console\Commands;

use App\Models\ContentPublishRequest;
use App\Services\AdminAuditService;
use App\Services\ContentPublishQualityService;
use App\Support\AdminContentRegistry;
use App\Support\PublishStatus;
use Illuminate\Console\Command;

class PublishScheduledContent extends Command
{
    protected $signature = 'content:publish-scheduled {--limit=100}';

    protected $description = 'Xuất bản các nội dung đã được duyệt và đến lịch.';

    public function handle(): int
    {
        $limit = max(1, min((int) $this->option('limit'), 500));
        $processed = 0;

        ContentPublishRequest::query()
            ->where('status', ContentPublishRequest::STATUS_SCHEDULED)
            ->whereNotNull('scheduled_publish_at')
            ->where('scheduled_publish_at', '<=', now())
            ->oldest('scheduled_publish_at')
            ->limit($limit)
            ->get()
            ->each(function (ContentPublishRequest $request) use (&$processed) {
                $item = AdminContentRegistry::find($request->content_type, (int) $request->content_id);
                $qualityBlockers = app(ContentPublishQualityService::class)->blockingMessages($item);

                if ($qualityBlockers !== []) {
                    $request->update([
                        'status' => ContentPublishRequest::STATUS_PENDING,
                        'review_notes' => 'Checklist QA chưa đạt khi đến lịch: '.implode(' | ', $qualityBlockers),
                    ]);

                    app(AdminAuditService::class)->audit(
                        null,
                        $item,
                        'content_scheduled_publish_blocked',
                        'Đã chặn xuất bản hẹn giờ vì checklist QA chưa đạt: '.AdminContentRegistry::titleFor($item),
                        null,
                        null,
                        ['publish_request_id' => $request->id, 'quality_blockers' => $qualityBlockers]
                    );

                    return;
                }

                $item->forceFill([
                    'publish_status' => PublishStatus::PUBLISHED,
                    'published_at' => now(),
                    'archived_at' => null,
                ])->save();

                $request->update([
                    'status' => ContentPublishRequest::STATUS_PUBLISHED,
                    'published_at' => now(),
                ]);

                app(AdminAuditService::class)->audit(
                    null,
                    $item,
                    'content_scheduled_published',
                    'Đã tự động xuất bản nội dung theo lịch: '.AdminContentRegistry::titleFor($item),
                    null,
                    null,
                    ['publish_request_id' => $request->id]
                );

                $processed++;
            });

        $this->info('Đã xuất bản '.$processed.' nội dung đến lịch.');

        return self::SUCCESS;
    }
}
