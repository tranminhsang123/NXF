<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessageReport;
use App\Models\ContentErrorReport;
use App\Models\ContentPublishRequest;
use App\Models\GrowthCampaign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SystemHealthController extends Controller
{
    public function index()
    {
        $checks = [
            $this->databaseCheck(),
            $this->queueCheck(),
            $this->mailCheck(),
            $this->storageCheck(),
            $this->ttsCheck(),
            $this->schedulerCheck(),
        ];

        $stats = [
            'pending_publish_requests' => Schema::hasTable('content_publish_requests')
                ? ContentPublishRequest::query()->whereIn('status', [
                    ContentPublishRequest::STATUS_PENDING,
                    ContentPublishRequest::STATUS_SCHEDULED,
                ])->count()
                : 0,
            'pending_chat_reports' => Schema::hasTable('chat_message_reports')
                ? ChatMessageReport::query()->where('status', ChatMessageReport::STATUS_PENDING)->count()
                : 0,
            'pending_content_reports' => Schema::hasTable('content_error_reports')
                ? ContentErrorReport::query()->where('status', ContentErrorReport::STATUS_PENDING)->count()
                : 0,
            'draft_campaigns' => Schema::hasTable('growth_campaigns')
                ? GrowthCampaign::query()->where('status', GrowthCampaign::STATUS_DRAFT)->count()
                : 0,
            'queued_jobs' => Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0,
            'failed_jobs' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0,
        ];

        $commands = [
            'php artisan migrate',
            'php artisan queue:work',
            'php artisan schedule:work',
            'php artisan content:publish-scheduled',
        ];

        return view('admin.system-health.index', compact('checks', 'stats', 'commands'));
    }

    private function databaseCheck(): array
    {
        $tables = [
            'content_publish_requests',
            'chat_message_reports',
            'content_error_reports',
            'pronunciation_audios',
            'growth_campaigns',
            'jobs',
            'failed_jobs',
        ];

        $missing = array_values(array_filter($tables, fn (string $table) => ! Schema::hasTable($table)));

        return [
            'name' => 'Cơ sở dữ liệu',
            'status' => $missing === [] ? 'ok' : 'warning',
            'summary' => $missing === [] ? 'Đủ bảng vận hành admin.' : 'Thiếu bảng: '.implode(', ', $missing),
            'meta' => [
                'Kết nối' => config('database.default'),
            ],
        ];
    }

    private function queueCheck(): array
    {
        $connection = (string) config('queue.default');
        $hasJobsTable = Schema::hasTable('jobs');
        $isReady = $connection !== 'database' || $hasJobsTable;

        return [
            'name' => 'Hàng đợi xử lý',
            'status' => $isReady ? 'ok' : 'error',
            'summary' => $isReady
                ? 'Sẵn sàng xử lý bulk audio và tác vụ nền.'
                : 'Queue database cần bảng jobs.',
            'meta' => [
                'Driver' => $connection,
                'Bảng jobs' => $hasJobsTable ? 'Có' : 'Chưa có',
            ],
        ];
    }

    private function mailCheck(): array
    {
        $mailer = (string) config('mail.default');
        $from = (string) config('mail.from.address');
        $needsSmtp = in_array($mailer, ['smtp', 'ses', 'mailgun', 'postmark'], true);
        $isReady = ! $needsSmtp || $from !== '';

        return [
            'name' => 'Email campaign',
            'status' => $isReady ? 'ok' : 'warning',
            'summary' => $isReady ? 'Có cấu hình mailer để gửi campaign.' : 'Cần cấu hình địa chỉ gửi email.',
            'meta' => [
                'Mailer' => $mailer,
                'Email gửi' => $from ?: 'Chưa cấu hình',
            ],
        ];
    }

    private function storageCheck(): array
    {
        $path = 'health-check.txt';

        try {
            Storage::disk('public')->put($path, 'ok');
            Storage::disk('public')->delete($path);

            return [
                'name' => 'Lưu audio',
                'status' => 'ok',
                'summary' => 'Storage public ghi/xóa được.',
                'meta' => [
                    'Disk' => 'public',
                ],
            ];
        } catch (Throwable $e) {
            return [
                'name' => 'Lưu audio',
                'status' => 'error',
                'summary' => 'Storage public chưa ghi được.',
                'meta' => [
                    'Lỗi' => $e->getMessage(),
                ],
            ];
        }
    }

    private function ttsCheck(): array
    {
        $provider = (string) config('pronunciation.provider', 'browser');
        $ready = match ($provider) {
            'google' => (bool) config('pronunciation.google.api_key'),
            'azure' => (bool) (config('pronunciation.azure.key') && config('pronunciation.azure.region')),
            'forvo' => (bool) config('pronunciation.forvo.api_key'),
            default => true,
        };

        return [
            'name' => 'Audio/TTS',
            'status' => $ready ? 'ok' : 'warning',
            'summary' => $ready ? 'Provider phát âm có thể hoạt động.' : 'Provider đang chọn nhưng thiếu API key.',
            'meta' => [
                'Provider' => $provider,
                'Google key' => config('pronunciation.google.api_key') ? 'Có' : 'Chưa có',
                'Azure key' => config('pronunciation.azure.key') ? 'Có' : 'Chưa có',
                'Forvo key' => config('pronunciation.forvo.api_key') ? 'Có' : 'Chưa có',
            ],
        ];
    }

    private function schedulerCheck(): array
    {
        return [
            'name' => 'Lịch tự động',
            'status' => 'ok',
            'summary' => 'Đã khai báo lịch nhắc học và xuất bản hẹn giờ.',
            'meta' => [
                'Nhắc streak' => '20:00 Asia/Tokyo',
                'Xuất bản hẹn giờ' => 'Mỗi 5 phút',
            ],
        ];
    }
}
