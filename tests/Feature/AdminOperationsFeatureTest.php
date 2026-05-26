<?php

namespace Tests\Feature;

use App\Models\GrowthCampaign;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatMessage;
use App\Models\ChatMessageReport;
use App\Models\ContentErrorReport;
use App\Models\ContentPublishRequest;
use App\Models\ContentVersion;
use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\FavoriteItem;
use App\Models\Notification;
use App\Models\PronunciationAudio;
use App\Models\GrowthCampaignRecipient;
use App\Models\User;
use App\Models\UserProgress;
use App\Services\PronunciationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminOperationsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_draft_content_and_keep_it_out_of_public_learning(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'user']);
        $lesson = MinnaLesson::query()->create([
            'number' => 88,
            'title' => 'Bài nháp ẩn',
            'description' => 'Nội dung nháp',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.content-ops.index', ['type' => 'minna_lesson']))
            ->assertOk()
            ->assertSee('Vận hành nội dung');

        $this->actingAs($admin)
            ->patch(route('admin.content-ops.status', ['type' => 'minna_lesson', 'id' => $lesson->id]), [
                'publish_status' => 'draft',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('minna_lessons', [
            'id' => $lesson->id,
            'publish_status' => 'draft',
        ]);
        $this->assertDatabaseHas('content_versions', [
            'versionable_type' => MinnaLesson::class,
            'versionable_id' => $lesson->id,
        ]);
        $this->assertDatabaseHas('admin_audit_logs', [
            'auditable_type' => MinnaLesson::class,
            'auditable_id' => $lesson->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.content-ops.preview', ['type' => 'minna_lesson', 'id' => $lesson->id]))
            ->assertOk()
            ->assertSee('Bài nháp ẩn');

        $this->actingAs($learner)
            ->get(route('minna.index'))
            ->assertOk()
            ->assertDontSee('Bài nháp ẩn');
    }

    public function test_admin_can_request_and_approve_content_publish_workflow(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = $this->createPublishReadyMinnaLesson();

        $this->actingAs($admin)
            ->post(route('admin.content-ops.publish-requests.store', ['type' => 'minna_lesson', 'id' => $lesson->id]), [
                'requested_status' => 'published',
                'notes' => 'Đã kiểm tra nội dung.',
            ])
            ->assertRedirect();

        $publishRequest = ContentPublishRequest::query()->firstOrFail();
        $this->assertSame(ContentPublishRequest::STATUS_PENDING, $publishRequest->status);

        $this->actingAs($admin)
            ->post(route('admin.content-ops.publish-requests.approve', $publishRequest), [
                'review_notes' => 'Cho xuất bản.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('minna_lessons', [
            'id' => $lesson->id,
            'publish_status' => 'published',
        ]);
        $this->assertSame(ContentPublishRequest::STATUS_PUBLISHED, $publishRequest->fresh()->status);
    }

    public function test_publish_approval_is_blocked_when_quality_checklist_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = MinnaLesson::query()->create([
            'number' => 90,
            'title' => 'Bài chưa đủ QA',
            'description' => 'Thiếu từ vựng, audio và quiz',
            'publish_status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.content-ops.publish-requests.store', ['type' => 'minna_lesson', 'id' => $lesson->id]), [
                'requested_status' => 'published',
            ])
            ->assertRedirect();

        $publishRequest = ContentPublishRequest::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.content-ops.publish-requests.approve', $publishRequest), [
                'review_notes' => 'Thử duyệt khi chưa QA.',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('quality_gate');

        $this->assertSame('draft', $lesson->fresh()->publish_status);
        $this->assertSame(ContentPublishRequest::STATUS_PENDING, $publishRequest->fresh()->status);
    }

    public function test_direct_publish_is_blocked_when_quality_checklist_fails(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = MinnaLesson::query()->create([
            'number' => 92,
            'title' => 'Bài chưa được QA',
            'description' => 'Thiếu checklist trước khi xuất bản',
            'publish_status' => 'draft',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.content-ops.status', ['type' => 'minna_lesson', 'id' => $lesson->id]), [
                'publish_status' => 'published',
            ])
            ->assertRedirect()
            ->assertSessionHasErrors('quality_gate');

        $this->assertSame('draft', $lesson->fresh()->publish_status);
    }

    public function test_admin_can_restore_content_version(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = MinnaLesson::query()->create([
            'number' => 93,
            'title' => 'Tiêu đề hiện tại',
            'description' => 'Mô tả hiện tại',
            'publish_status' => 'draft',
        ]);

        $version = ContentVersion::query()->create([
            'versionable_type' => MinnaLesson::class,
            'versionable_id' => $lesson->id,
            'actor_id' => $admin->id,
            'action' => 'updated',
            'snapshot' => [
                'number' => 93,
                'title' => 'Tiêu đề cũ',
                'description' => 'Mô tả cũ',
                'publish_status' => 'draft',
                'published_at' => null,
                'archived_at' => null,
            ],
            'changes' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.content-ops.restore', $version))
            ->assertRedirect(route('admin.content-ops.preview', ['type' => 'minna_lesson', 'id' => $lesson->id]));

        $fresh = $lesson->fresh();
        $this->assertSame('Tiêu đề cũ', $fresh->title);
        $this->assertSame('Mô tả cũ', $fresh->description);
    }

    public function test_admin_audio_manager_can_generate_pronunciation_audio(): void
    {
        Storage::fake('public');
        Http::fake([
            'texttospeech.googleapis.com/*' => Http::response([
                'audioContent' => base64_encode('fake-mp3-data'),
            ]),
        ]);

        config()->set('pronunciation.provider', 'google');
        config()->set('pronunciation.google.api_key', 'test-key');

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.audio.index'))
            ->assertOk()
            ->assertSee('Quản lý Audio/TTS');

        $this->actingAs($admin)
            ->post(route('admin.audio.generate'), [
                'text' => 'konnichiwa',
                'language' => 'ja-JP',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('pronunciation_audios', [
            'text' => 'konnichiwa',
            'source' => 'google',
        ]);
        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'audio_generated',
        ]);
        $this->assertSame(1, PronunciationAudio::query()->count());
    }

    public function test_growth_campaign_can_send_notifications_to_segment(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('admin.growth.store'), [
                'title' => 'Hoc tiep nao',
                'message' => 'Quay lai on bai hom nay.',
                'segment' => 'all_users',
                'channel' => 'notification',
            ])
            ->assertRedirect(route('admin.growth.index'));

        $campaign = GrowthCampaign::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.growth.send', $campaign))
            ->assertRedirect();

        $this->assertSame(GrowthCampaign::STATUS_SENT, $campaign->fresh()->status);
        $this->assertSame(2, Notification::query()->where('type', 'growth_campaign')->count());
    }

    public function test_growth_campaign_can_send_email_channel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('admin.growth.store'), [
                'title' => 'Nhắc học qua email',
                'message' => 'Hôm nay mình học tiếp nhé.',
                'segment' => 'all_users',
                'channel' => GrowthCampaign::CHANNEL_EMAIL,
            ])
            ->assertRedirect(route('admin.growth.index'));

        $campaign = GrowthCampaign::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.growth.send', $campaign))
            ->assertRedirect();

        $fresh = $campaign->fresh();
        $this->assertSame(GrowthCampaign::STATUS_SENT, $fresh->status);
        $this->assertSame(2, $fresh->metadata['email_count'] ?? null);
        $this->assertSame(0, Notification::query()->where('type', 'growth_campaign')->count());
    }

    public function test_growth_tools_support_dynamic_segments_ab_test_and_recipient_metrics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->count(4)->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('admin.growth.store'), [
                'title' => 'Hoc tiep ban A',
                'message' => 'Noi dung A',
                'segment' => 'all_users',
                'channel' => GrowthCampaign::CHANNEL_NOTIFICATION,
                'ab_test_enabled' => '1',
                'variant_b_title' => 'Hoc tiep ban B',
                'variant_b_message' => 'Noi dung B',
            ])
            ->assertRedirect(route('admin.growth.index'));

        $campaign = GrowthCampaign::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.growth.send', $campaign))
            ->assertRedirect();

        $this->assertSame(4, GrowthCampaignRecipient::query()->where('growth_campaign_id', $campaign->id)->count());
        $this->assertSame(2, GrowthCampaignRecipient::query()->where('variant', 'a')->count());
        $this->assertSame(2, GrowthCampaignRecipient::query()->where('variant', 'b')->count());
        $this->assertSame(4, Notification::query()->where('type', 'growth_campaign')->count());

        $this->actingAs($admin)
            ->get(route('admin.growth.index'))
            ->assertOk()
            ->assertSee('Segment user động')
            ->assertSee('A/B test')
            ->assertSee('Hiệu quả 48h');
    }

    public function test_admin_analytics_exposes_retention_dropoff_quality_and_user_profile(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create([
            'role' => 'user',
            'current_streak' => 3,
            'last_study_date' => now()->subDays(6)->toDateString(),
            'onboarding_completed_at' => now()->subDays(10),
        ]);
        $lesson = MinnaLesson::query()->create([
            'number' => 94,
            'title' => 'Bai phan tich',
            'description' => 'Du lieu test analytics',
        ]);

        UserProgress::query()->create([
            'user_id' => $learner->id,
            'lesson_type' => UserProgress::TYPE_MINNA,
            'lesson_id' => $lesson->id,
            'status' => UserProgress::STATUS_IN_PROGRESS,
            'last_accessed_at' => now()->subDays(6),
        ]);
        MinnaQuizAttempt::query()->create([
            'user_id' => $learner->id,
            'minna_lesson_id' => $lesson->id,
            'score' => 1,
            'total' => 10,
            'percent' => 10,
            'passed' => false,
            'completed_at' => now()->subDays(6),
        ]);
        FavoriteItem::query()->create([
            'user_id' => $learner->id,
            'item_key' => FavoriteItem::keyFor($learner->id, 'あさ', 'buoi sang'),
            'front' => 'あさ',
            'back' => 'buoi sang',
            'item_type' => 'vocabulary',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.analytics.index'))
            ->assertOk()
            ->assertSee('Retention dashboard')
            ->assertSee('Phễu onboarding')
            ->assertSee('Bản đồ điểm rớt')
            ->assertSee('Dashboard chất lượng nội dung')
            ->assertSee('Segment user động');

        $this->actingAs($admin)
            ->get(route('admin.users.edit', $learner))
            ->assertOk()
            ->assertSee('Hồ sơ học tập chi tiết');
    }

    public function test_user_can_report_chat_message_and_admin_can_remove_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $sender = User::factory()->create(['role' => 'user']);
        $reporter = User::factory()->create(['role' => 'user']);
        $group = ChatGroup::query()->create([
            'name' => 'Nhóm luyện tập',
            'created_by' => $admin->id,
        ]);
        ChatGroupMember::query()->create(['group_id' => $group->id, 'user_id' => $sender->id]);
        ChatGroupMember::query()->create(['group_id' => $group->id, 'user_id' => $reporter->id]);
        $message = ChatMessage::query()->create([
            'group_id' => $group->id,
            'sender_id' => $sender->id,
            'content' => 'Tin nhắn cần kiểm duyệt',
        ]);

        $this->actingAs($reporter)
            ->postJson(route('chat.messages.report', $message), [
                'reason' => 'Nội dung không phù hợp',
            ])
            ->assertOk()
            ->assertJson(['message' => 'Đã gửi báo cáo cho admin.']);

        $report = ChatMessageReport::query()->firstOrFail();
        $this->assertSame(ChatMessageReport::STATUS_PENDING, $report->status);

        $this->actingAs($admin)
            ->post(route('admin.support-moderation.reports.remove-message', $report), [
                'resolution_note' => 'Đã xoá sau khi kiểm tra.',
            ])
            ->assertRedirect();

        $this->assertSoftDeleted('chat_messages', ['id' => $message->id]);
        $this->assertSame(ChatMessageReport::STATUS_REMOVED, $report->fresh()->status);
    }

    public function test_user_can_report_content_error_and_admin_can_resolve_it(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $learner = User::factory()->create(['role' => 'user']);
        $lesson = MinnaLesson::query()->create([
            'number' => 91,
            'title' => 'Bài có lỗi cần báo',
            'description' => 'Nội dung đang học',
        ]);

        $this->actingAs($learner)
            ->postJson(route('content-reports.store'), [
                'category' => ContentErrorReport::CATEGORY_AUDIO,
                'description' => 'Audio của từ này đang đọc sai.',
                'selected_text' => 'こんにちは',
                'content_type' => 'minna_lesson',
                'content_id' => $lesson->id,
                'content_title' => $lesson->title,
                'page_url' => route('minna.show', ['number' => $lesson->number]),
            ])
            ->assertOk()
            ->assertJson(['message' => 'Đã gửi báo lỗi nội dung cho admin.']);

        $report = ContentErrorReport::query()->firstOrFail();
        $this->assertSame(ContentErrorReport::STATUS_PENDING, $report->status);

        $this->actingAs($admin)
            ->get(route('admin.content-reports.index'))
            ->assertOk()
            ->assertSee('Báo lỗi nội dung');

        $this->actingAs($admin)
            ->patch(route('admin.content-reports.update', $report), [
                'status' => ContentErrorReport::STATUS_RESOLVED,
                'assigned_to' => $admin->id,
                'resolution_note' => 'Đã thay audio.',
            ])
            ->assertRedirect(route('admin.content-reports.show', $report));

        $fresh = $report->fresh();
        $this->assertSame(ContentErrorReport::STATUS_RESOLVED, $fresh->status);
        $this->assertSame($admin->id, $fresh->resolved_by);
    }

    public function test_admin_operation_dashboards_are_accessible(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.analytics.index'))->assertOk()->assertSee('Phân tích học tập');
        $this->actingAs($admin)->get(route('admin.support-moderation.index'))->assertOk()->assertSee('Trung tâm hỗ trợ / kiểm duyệt');
        $this->actingAs($admin)->get(route('admin.system-health.index'))->assertOk()->assertSee('Sức khỏe hệ thống');
        $this->actingAs($admin)->get(route('admin.content-reports.index'))->assertOk()->assertSee('Báo lỗi nội dung');
        $this->actingAs($admin)->get(route('admin.audit-logs.index'))->assertOk()->assertSee('Nhật ký thao tác admin');
    }

    private function createPublishReadyMinnaLesson(): MinnaLesson
    {
        $lesson = MinnaLesson::query()->create([
            'number' => 89,
            'title' => 'Bài cần duyệt',
            'description' => 'Nội dung đang chuẩn bị',
            'publish_status' => 'draft',
        ]);

        $items = [
            ['tu_vung' => 'あさ', 'nghia' => 'buổi sáng'],
            ['tu_vung' => 'ひる', 'nghia' => 'buổi trưa'],
            ['tu_vung' => 'よる', 'nghia' => 'buổi tối'],
            ['tu_vung' => 'ほん', 'nghia' => 'sách'],
        ];

        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Từ vựng',
            'content' => ['vocab' => $items],
            'publish_status' => 'published',
        ]);

        $service = app(PronunciationService::class);
        foreach ($items as $item) {
            $text = $service->normalizeText($item['tu_vung']);
            PronunciationAudio::query()->create([
                'text_hash' => $service->hash($text, 'ja-JP'),
                'text' => $text,
                'language' => 'ja-JP',
                'source' => 'manual',
                'audio_url' => '/storage/pronunciation/'.$text.'.mp3',
                'usage_count' => 1,
                'last_used_at' => now(),
            ]);
        }

        return $lesson->fresh('sections');
    }
}
