<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatJoinRequest;
use App\Models\ChatMessage;
use App\Models\ChatMessageReport;
use App\Models\DevtoolsViolation;
use App\Models\DirectConversation;
use App\Models\DirectMessage;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;

class SupportModerationController extends Controller
{
    public function index(Request $request)
    {
        $admin = $request->user();

        $pendingJoinRequests = ChatJoinRequest::query()
            ->with(['group:id,name', 'user:id,name,email'])
            ->where('status', 'pending')
            ->latest()
            ->take(20)
            ->get();

        $unreadDirectMessages = DirectMessage::query()
            ->with(['sender:id,name,email', 'conversation.user:id,name,email'])
            ->where('recipient_id', $admin->id)
            ->whereNull('read_at')
            ->latest()
            ->take(20)
            ->get();

        $recentConversations = DirectConversation::query()
            ->where('admin_id', $admin->id)
            ->with(['user:id,name,email', 'messages' => fn ($q) => $q->latest()->limit(1)])
            ->withCount(['messages as unread_count' => fn ($q) => $q->where('recipient_id', $admin->id)->whereNull('read_at')])
            ->orderByDesc('last_message_at')
            ->take(12)
            ->get();

        $recentChatMessages = ChatMessage::query()
            ->with(['group:id,name', 'sender:id,name,email'])
            ->latest()
            ->take(20)
            ->get();

        $pendingReports = ChatMessageReport::query()
            ->with(['message.sender:id,name,email', 'group:id,name', 'reporter:id,name,email'])
            ->where('status', ChatMessageReport::STATUS_PENDING)
            ->latest()
            ->take(20)
            ->get();

        $devtoolsViolations = DevtoolsViolation::query()
            ->with('user:id,name,email')
            ->latest()
            ->take(10)
            ->get();

        $cannedReplies = [
            'Cảm ơn bạn đã báo lỗi. Admin sẽ kiểm tra và phản hồi sớm.',
            'Bạn thử tải lại trang hoặc đăng nhập lại giúp mình nhé.',
            'Mình đã ghi nhận yêu cầu và chuyển cho người phụ trách nội dung.',
        ];

        return view('admin.support-moderation.index', compact(
            'pendingJoinRequests',
            'unreadDirectMessages',
            'recentConversations',
            'recentChatMessages',
            'pendingReports',
            'devtoolsViolations',
            'cannedReplies'
        ));
    }

    public function dismissReport(Request $request, ChatMessageReport $report)
    {
        $data = $request->validate([
            'resolution_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update([
            'status' => ChatMessageReport::STATUS_DISMISSED,
            'resolved_by' => $request->user()->id,
            'resolution_note' => $data['resolution_note'] ?? null,
            'resolved_at' => now(),
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $report,
            'chat_report_dismissed',
            'Đã bỏ qua báo cáo tin nhắn chat #'.$report->chat_message_id
        );

        return back()->with('success', 'Đã bỏ qua báo cáo.');
    }

    public function removeReportedMessage(Request $request, ChatMessageReport $report)
    {
        $data = $request->validate([
            'resolution_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $message = $report->message;

        if ($message && ! $message->trashed()) {
            $message->delete();
        }

        $report->update([
            'status' => ChatMessageReport::STATUS_REMOVED,
            'resolved_by' => $request->user()->id,
            'resolution_note' => $data['resolution_note'] ?? null,
            'resolved_at' => now(),
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $report,
            'chat_report_message_removed',
            'Đã xoá tin nhắn bị báo cáo #'.$report->chat_message_id
        );

        return back()->with('success', 'Đã xoá tin nhắn bị báo cáo.');
    }
}
