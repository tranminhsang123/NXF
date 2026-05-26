<?php

namespace Tests\Feature;

use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatResilienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_group_message_idempotency_returns_same_message_for_retry(): void
    {
        config(['chat.write_mode' => 'normal']);

        $user = User::factory()->create(['role' => 'user']);
        $group = ChatGroup::create([
            'name' => 'N5 Group',
            'created_by' => $user->id,
        ]);
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $payload = [
            'content' => 'Xin chao',
            'client_message_id' => 'msg-12345',
        ];

        $first = $this->postJson("/api/social/groups/{$group->id}/messages", $payload)
            ->assertCreated()
            ->json();

        $second = $this->postJson("/api/social/groups/{$group->id}/messages", $payload)
            ->assertCreated()
            ->assertJsonPath('meta.idempotent', true)
            ->json();

        $this->assertSame($first['message']['id'], $second['message']['id']);
        $this->assertSame($first['message']['message_uuid'], $second['message']['message_uuid']);
        $this->assertSame($first['message']['created_at'], $second['message']['created_at']);
        $this->assertSame(1, ChatMessage::query()->count());
    }

    public function test_chat_write_disabled_returns_structured_error(): void
    {
        config(['chat.write_mode' => 'disable_write']);

        $user = User::factory()->create(['role' => 'user']);
        $group = ChatGroup::create([
            'name' => 'N5 Group',
            'created_by' => $user->id,
        ]);
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $this->postJson("/api/social/groups/{$group->id}/messages", [
            'content' => 'Thu nghiem',
            'client_message_id' => 'msg-disabled-1',
        ])->assertStatus(503)
            ->assertJsonPath('error.code', 'CHAT_DISABLED');
    }

    public function test_kill_switch_flip_changes_response_without_crash(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $group = ChatGroup::create([
            'name' => 'N5 Group',
            'created_by' => $user->id,
        ]);
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        config(['chat.write_mode' => 'normal']);
        $this->postJson("/api/social/groups/{$group->id}/messages", [
            'content' => 'first message',
            'client_message_id' => 'flip-1',
        ])->assertCreated();

        config(['chat.write_mode' => 'disable_write']);
        $this->postJson("/api/social/groups/{$group->id}/messages", [
            'content' => 'second message',
            'client_message_id' => 'flip-2',
        ])->assertStatus(503)->assertJsonPath('error.code', 'CHAT_DISABLED');
    }

    public function test_cursor_pagination_returns_expected_windows(): void
    {
        config(['chat.write_mode' => 'degrade_no_broadcast']);

        $user = User::factory()->create(['role' => 'user']);
        $group = ChatGroup::create([
            'name' => 'N5 Group',
            'created_by' => $user->id,
        ]);
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);
        Sanctum::actingAs($user);

        for ($i = 1; $i <= 5; $i++) {
            $this->postJson("/api/social/groups/{$group->id}/messages", [
                'content' => "msg {$i}",
                'client_message_id' => "cursor-{$i}",
            ])->assertCreated();
        }

        $page = $this->getJson("/api/social/groups/{$group->id}/messages?before_id=5&limit=2")
            ->assertOk()
            ->json();

        $this->assertCount(2, $page['messages']);
        $this->assertSame(3, $page['messages'][0]['id']);
        $this->assertSame(4, $page['messages'][1]['id']);
        $this->assertSame(3, $page['meta']['cursor']['before_id']);
        $this->assertSame(4, $page['meta']['cursor']['after_id']);
    }

    public function test_cleanup_idempotency_command_clears_old_keys(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $group = ChatGroup::create([
            'name' => 'N5 Group',
            'created_by' => $user->id,
        ]);
        ChatGroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
        ]);

        $message = ChatMessage::create([
            'group_id' => $group->id,
            'sender_id' => $user->id,
            'message_uuid' => '11111111-1111-1111-1111-111111111111',
            'content' => 'old message',
            'client_message_id' => 'old-key-1',
            'event_id' => '22222222-2222-2222-2222-222222222222',
            'event_status' => 'sent',
        ]);

        $message->created_at = now()->subHours(100);
        $message->save();

        $this->artisan('chat:cleanup-idempotency', ['--hours' => 72])->assertSuccessful();

        $this->assertNull($message->fresh()->client_message_id);
    }
}
