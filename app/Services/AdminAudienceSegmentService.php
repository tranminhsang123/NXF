<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class AdminAudienceSegmentService
{
    public function definitions(): array
    {
        return [
            'all_users' => [
                'label' => 'Tất cả người học',
                'description' => 'Toàn bộ tài khoản user, dùng cho thông báo chung.',
                'priority' => 'dai_han',
            ],
            'streak_due_today' => [
                'label' => 'Sắp mất streak hôm nay',
                'description' => 'Có streak nhưng lần học cuối là hôm qua, cần nhắc ngay.',
                'priority' => 'cao',
            ],
            'inactive_3d' => [
                'label' => 'Không học 3 ngày',
                'description' => 'Dừng học ngắn hạn, phù hợp campaign kéo quay lại.',
                'priority' => 'cao',
            ],
            'at_risk_5_10' => [
                'label' => 'Nguy cơ bỏ học 5-10 ngày',
                'description' => 'Từng học nhưng đã dừng 5-10 ngày, cần can thiệp.',
                'priority' => 'cao',
            ],
            'inactive_7d' => [
                'label' => 'Không hoạt động 7 ngày',
                'description' => 'Nhóm lạnh hơn, nên dùng email hoặc ưu đãi nội dung.',
                'priority' => 'vua',
            ],
            'new_7d' => [
                'label' => 'User mới 7 ngày',
                'description' => 'Người mới đăng ký, dùng để đo onboarding và bài học đầu tiên.',
                'priority' => 'vua',
            ],
            'onboarded_no_first_lesson' => [
                'label' => 'Xong onboarding nhưng chưa học',
                'description' => 'Đã chọn mục tiêu/trình độ nhưng chưa bắt đầu bài nào.',
                'priority' => 'cao',
            ],
            'completed_first_lesson' => [
                'label' => 'Đã hoàn thành bài đầu tiên',
                'description' => 'Nhóm vừa có tín hiệu tốt, nên đẩy bài kế tiếp.',
                'priority' => 'vua',
            ],
            'active_3_sessions_week' => [
                'label' => 'Học từ 3 lượt/tuần',
                'description' => 'Nhóm tương tác cao, dùng để thử tính năng mới.',
                'priority' => 'dai_han',
            ],
            'jlpt_n5' => [
                'label' => 'Mục tiêu JLPT N5',
                'description' => 'Người học đặt mục tiêu N5 trong onboarding.',
                'priority' => 'dai_han',
            ],
            'high_streak_30' => [
                'label' => 'Streak trên 30 ngày',
                'description' => 'Nhóm trung thành, phù hợp chia sẻ thành tích hoặc khảo sát.',
                'priority' => 'dai_han',
            ],
        ];
    }

    public function labels(): array
    {
        return collect($this->definitions())
            ->mapWithKeys(fn (array $definition, string $key) => [$key => $definition['label']])
            ->all();
    }

    public function counts(): array
    {
        $counts = [];
        foreach (array_keys($this->definitions()) as $key) {
            $counts[$key] = (clone $this->query($key))->count();
        }

        return $counts;
    }

    public function query(string $segment): Builder
    {
        $query = User::query()->where('role', 'user');

        return match ($segment) {
            'streak_due_today' => $query
                ->where('current_streak', '>', 0)
                ->whereDate('last_study_date', Carbon::yesterday()),
            'inactive_3d' => $query->where(function (Builder $inner) {
                $inner->whereNull('last_study_date')
                    ->orWhereDate('last_study_date', '<=', Carbon::today()->subDays(3));
            }),
            'at_risk_5_10' => $query
                ->whereNotNull('last_study_date')
                ->whereDate('last_study_date', '<=', Carbon::today()->subDays(5))
                ->whereDate('last_study_date', '>=', Carbon::today()->subDays(10)),
            'inactive_7d' => $query->where(function (Builder $inner) {
                $inner->whereNull('last_study_date')
                    ->orWhereDate('last_study_date', '<=', Carbon::today()->subDays(7));
            }),
            'new_7d' => $query->where('created_at', '>=', Carbon::today()->subDays(7)),
            'onboarded_no_first_lesson' => $query
                ->whereNotNull('onboarding_completed_at')
                ->whereDoesntHave('progresses'),
            'completed_first_lesson' => $query->whereHas('progresses', function (Builder $inner) {
                $inner->where('status', UserProgress::STATUS_COMPLETED);
            }),
            'active_3_sessions_week' => $query->whereIn('id', UserProgress::query()
                ->select('user_id')
                ->where('last_accessed_at', '>=', Carbon::today()->subDays(6))
                ->groupBy('user_id')
                ->havingRaw('COUNT(*) >= 3')),
            'jlpt_n5' => $query->where('jlpt_goal', 'N5'),
            'high_streak_30' => $query->where('current_streak', '>=', 30),
            default => $query,
        };
    }
}
