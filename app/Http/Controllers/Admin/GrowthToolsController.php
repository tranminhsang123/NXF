<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrowthCampaign;
use App\Models\GrowthCampaignRecipient;
use App\Models\Notification;
use App\Models\UserProgress;
use App\Services\AdminAudienceSegmentService;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class GrowthToolsController extends Controller
{
    public function index(AdminAudienceSegmentService $segmentService)
    {
        $campaigns = GrowthCampaign::query()
            ->with('creator:id,name,email')
            ->withCount('recipients')
            ->latest()
            ->paginate(20);

        $segments = $segmentService->labels();
        $segmentDefinitions = $segmentService->definitions();
        $segmentCounts = $segmentService->counts();
        $campaignMetrics = $campaigns->getCollection()
            ->mapWithKeys(fn (GrowthCampaign $campaign) => [$campaign->id => $this->campaignMetrics($campaign)])
            ->all();

        return view('admin.growth.index', [
            'campaigns' => $campaigns,
            'segments' => $segments,
            'segmentDefinitions' => $segmentDefinitions,
            'segmentCounts' => $segmentCounts,
            'campaignMetrics' => $campaignMetrics,
            'triggerTemplates' => $this->triggerTemplates(),
        ]);
    }

    public function create(Request $request, AdminAudienceSegmentService $segmentService)
    {
        return view('admin.growth.create', [
            'segments' => $segmentService->labels(),
            'segmentDefinitions' => $segmentService->definitions(),
            'triggerTemplates' => $this->triggerTemplates(),
            'prefill' => [
                'segment' => (string) $request->query('segment', 'all_users'),
                'trigger_key' => (string) $request->query('trigger_key', ''),
                'title' => (string) $request->query('title', ''),
                'message' => (string) $request->query('message', ''),
                'channel' => (string) $request->query('channel', GrowthCampaign::CHANNEL_NOTIFICATION),
            ],
        ]);
    }

    public function store(Request $request, AdminAudienceSegmentService $segmentService)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'segment' => ['required', 'string', 'max:64'],
            'channel' => ['required', 'in:notification,email,notification_email'],
            'trigger_key' => ['nullable', 'string', 'max:64'],
            'ab_test_enabled' => ['nullable', 'boolean'],
            'variant_b_title' => ['nullable', 'string', 'max:255'],
            'variant_b_message' => ['nullable', 'string', 'max:2000'],
        ]);

        abort_unless(array_key_exists($data['segment'], $segmentService->definitions()), 422);
        if (! empty($data['trigger_key'])) {
            abort_unless(array_key_exists($data['trigger_key'], $this->triggerTemplates()), 422);
        }

        $campaign = GrowthCampaign::query()->create([
            'title' => $data['title'],
            'message' => $data['message'],
            'segment' => $data['segment'],
            'channel' => $data['channel'],
            'created_by' => $request->user()->id,
            'status' => GrowthCampaign::STATUS_DRAFT,
            'audience_count' => $segmentService->query($data['segment'])->count(),
            'metadata' => [
                'trigger_key' => $data['trigger_key'] ?? null,
                'ab_test' => [
                    'enabled' => $request->boolean('ab_test_enabled'),
                    'variant_b_title' => $data['variant_b_title'] ?? null,
                    'variant_b_message' => $data['variant_b_message'] ?? null,
                ],
            ],
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $campaign,
            'growth_campaign_created',
            'Đã tạo chiến dịch tăng trưởng: '.$campaign->title
        );

        return redirect()->route('admin.growth.index')->with('success', 'Đã tạo bản nháp chiến dịch.');
    }

    public function send(Request $request, GrowthCampaign $campaign, AdminAudienceSegmentService $segmentService)
    {
        abort_unless($campaign->status === GrowthCampaign::STATUS_DRAFT, 422);

        $users = $segmentService->query($campaign->segment)->get(['id', 'email', 'name']);
        $notificationCount = 0;
        $emailCount = 0;
        $variantCounts = ['a' => 0, 'b' => 0];
        $sendNotification = in_array($campaign->channel, [
            GrowthCampaign::CHANNEL_NOTIFICATION,
            GrowthCampaign::CHANNEL_NOTIFICATION_EMAIL,
        ], true);
        $sendEmail = in_array($campaign->channel, [
            GrowthCampaign::CHANNEL_EMAIL,
            GrowthCampaign::CHANNEL_NOTIFICATION_EMAIL,
        ], true);
        $abTest = (bool) data_get($campaign->metadata, 'ab_test.enabled', false);
        $variantBTitle = data_get($campaign->metadata, 'ab_test.variant_b_title') ?: $campaign->title;
        $variantBMessage = data_get($campaign->metadata, 'ab_test.variant_b_message') ?: $campaign->message;
        $sentAt = now();

        foreach ($users as $index => $user) {
            $variant = $abTest && $index % 2 === 1 ? 'b' : 'a';
            $variantCounts[$variant]++;
            $title = $variant === 'b' ? $variantBTitle : $campaign->title;
            $messageBody = $variant === 'b' ? $variantBMessage : $campaign->message;
            $recipient = GrowthCampaignRecipient::query()->updateOrCreate(
                [
                    'growth_campaign_id' => $campaign->id,
                    'user_id' => $user->id,
                ],
                [
                    'variant' => $variant,
                    'channel' => $campaign->channel,
                    'sent_at' => $sentAt,
                ]
            );

            if ($sendNotification) {
                $notification = Notification::query()->create([
                    'user_id' => $user->id,
                    'type' => 'growth_campaign',
                    'title' => $title,
                    'message' => $messageBody,
                    'data' => [
                        'campaign_id' => $campaign->id,
                        'recipient_id' => $recipient->id,
                        'segment' => $campaign->segment,
                        'channel' => $campaign->channel,
                        'variant' => $variant,
                    ],
                ]);
                $recipient->forceFill([
                    'notification_id' => $notification->id,
                    'notification_sent_at' => $sentAt,
                ])->save();
                $notificationCount++;
            }

            if ($sendEmail && $user->email) {
                Mail::raw($messageBody, function ($message) use ($title, $user) {
                    $message->to($user->email, $user->name)->subject($title);
                });
                $recipient->forceFill(['email_sent_at' => $sentAt])->save();
                $emailCount++;
            }
        }

        $metadata = $campaign->metadata ?? [];
        $metadata['notification_count'] = $notificationCount;
        $metadata['email_count'] = $emailCount;
        $metadata['variant_counts'] = $variantCounts;

        $campaign->update([
            'status' => GrowthCampaign::STATUS_SENT,
            'audience_count' => $users->count(),
            'metadata' => $metadata,
            'sent_at' => $sentAt,
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $campaign,
            'growth_campaign_sent',
            'Đã gửi chiến dịch tới '.$users->count().' người dùng: '.$campaign->title
        );

        return back()->with('success', 'Đã gửi chiến dịch tới '.$users->count().' người dùng ('.$notificationCount.' thông báo, '.$emailCount.' email).');
    }

    private function campaignMetrics(GrowthCampaign $campaign): array
    {
        $recipients = $campaign->recipients()->get(['id', 'user_id', 'variant', 'sent_at', 'returned_at']);
        $returnedByVariant = ['a' => 0, 'b' => 0];
        $sentByVariant = ['a' => 0, 'b' => 0];
        $returned48h = 0;

        foreach ($recipients as $recipient) {
            $variant = $recipient->variant ?: 'a';
            $sentByVariant[$variant] = ($sentByVariant[$variant] ?? 0) + 1;

            $returned = $recipient->returned_at !== null;
            if (! $returned && $recipient->sent_at) {
                $returned = UserProgress::query()
                    ->where('user_id', $recipient->user_id)
                    ->whereBetween('last_accessed_at', [$recipient->sent_at, $recipient->sent_at->copy()->addHours(48)])
                    ->exists();
            }

            if ($returned) {
                $returned48h++;
                $returnedByVariant[$variant] = ($returnedByVariant[$variant] ?? 0) + 1;
            }
        }

        $audience = $recipients->count() ?: (int) $campaign->audience_count;

        return [
            'audience' => $audience,
            'returned_48h' => $returned48h,
            'return_rate' => $audience > 0 ? round($returned48h / $audience * 100, 1) : 0,
            'variant_a' => [
                'sent' => $sentByVariant['a'] ?? 0,
                'returned' => $returnedByVariant['a'] ?? 0,
            ],
            'variant_b' => [
                'sent' => $sentByVariant['b'] ?? 0,
                'returned' => $returnedByVariant['b'] ?? 0,
            ],
        ];
    }

    private function triggerTemplates(): array
    {
        return [
            'streak_due_today' => [
                'label' => 'Nhắc giữ streak trước khi đứt',
                'segment' => 'streak_due_today',
                'channel' => GrowthCampaign::CHANNEL_NOTIFICATION_EMAIL,
                'title' => 'Giữ streak hôm nay nhé',
                'message' => 'Bạn chỉ cần vài phút ôn lại bài gần nhất để giữ nhịp học tiếng Nhật hôm nay.',
            ],
            'inactive_3d' => [
                'label' => 'Kéo user quay lại sau 3 ngày',
                'segment' => 'inactive_3d',
                'channel' => GrowthCampaign::CHANNEL_NOTIFICATION_EMAIL,
                'title' => 'Học tiếp bài đang dang dở',
                'message' => 'Lộ trình của bạn vẫn đang chờ. Quay lại học một mục nhỏ hôm nay nhé.',
            ],
            'onboarded_no_first_lesson' => [
                'label' => 'Đẩy bài đầu tiên sau onboarding',
                'segment' => 'onboarded_no_first_lesson',
                'channel' => GrowthCampaign::CHANNEL_NOTIFICATION,
                'title' => 'Bài đầu tiên đã sẵn sàng',
                'message' => 'Bắt đầu bài học đầu tiên theo mục tiêu bạn đã chọn.',
            ],
            'completed_first_lesson' => [
                'label' => 'Gợi ý bài kế tiếp',
                'segment' => 'completed_first_lesson',
                'channel' => GrowthCampaign::CHANNEL_NOTIFICATION,
                'title' => 'Sang bài kế tiếp thôi',
                'message' => 'Bạn đã có khởi đầu tốt. Học tiếp bài tiếp theo để giữ đà tiến bộ.',
            ],
        ];
    }
}
