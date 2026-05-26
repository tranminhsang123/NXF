<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\MinnaQuizAttempt;
use App\Models\UserMinnaSectionProgress;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function __construct(
        private FlashcardService $flashcardService
    ) {}

    /**
     * Số bài Minna hoàn thành theo từng ngày (N ngày gần nhất)
     *
     * @return array{labels: string[], data: int[]}
     */
    public function getLessonsCompletedByDay(User $user, int $days = 7): array
    {
        $startOfRange = Carbon::today()->subDays($days - 1)->copy()->startOfDay();

        $dateExpr = $this->sqlDateOnly('completed_at');

        $rows = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $startOfRange)
            ->selectRaw("{$dateExpr} as d, COUNT(*) as aggregate")
            ->groupBy('d')
            ->pluck('aggregate', 'd')
            ->all();

        $labels = [];
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = Carbon::today()->subDays($i);
            $key = $d->toDateString();
            $labels[] = $d->format('d/m');
            $data[] = (int) ($rows[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Số bài Minna hoàn thành theo từng tuần (N tuần gần nhất), ISO tuần T2–CN — gom trong SQL.
     *
     * @return array{labels: string[], data: int[]}
     */
    public function getLessonsCompletedByWeek(User $user, int $weeks = 8): array
    {
        $start = Carbon::today()->subWeeks($weeks)->startOfWeek(Carbon::MONDAY);

        $weekKeyExpr = $this->sqlIsoWeekKey('completed_at');

        $rows = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->whereNotNull('completed_at')
            ->where('completed_at', '>=', $start)
            ->selectRaw("{$weekKeyExpr} as week_key, COUNT(*) as aggregate")
            ->groupBy('week_key')
            ->pluck('aggregate', 'week_key')
            ->mapWithKeys(fn ($count, $wk) => [(int) $wk => $count])
            ->all();

        $labels = [];
        $data = [];
        for ($i = $weeks - 1; $i >= 0; $i--) {
            $weekStart = Carbon::today()->subWeeks($i)->startOfWeek(Carbon::MONDAY);
            $weekEnd = $weekStart->copy()->addDays(6);
            $key = $weekStart->isoWeekYear() * 100 + $weekStart->isoWeek();
            $labels[] = $weekStart->format('d/m').'-'.$weekEnd->format('d/m');
            $data[] = (int) ($rows[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Tổng số bài đã hoàn thành và ước tính tổng từ vựng (từ các bài đã hoàn thành)
     */
    public function getSummary(User $user): array
    {
        $lessonIds = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->distinct()
            ->orderBy('lesson_id')
            ->pluck('lesson_id');

        $completedCount = $lessonIds->count();
        $totalVocab = $this->flashcardService->getTotalVocabCountByLessonIds($lessonIds->all());

        return [
            'completed_lessons' => $completedCount,
            'total_vocab_estimate' => $totalVocab,
        ];
    }

    public function getActivityTimeline(User $user, int $limit = 80): array
    {
        $items = collect();

        UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->with('lesson:id,number,title')
            ->whereNotNull('last_accessed_at')
            ->latest('last_accessed_at')
            ->take($limit)
            ->get()
            ->each(function (UserProgress $progress) use ($items) {
                $items->push([
                    'type' => $progress->status === UserProgress::STATUS_COMPLETED ? 'lesson_completed' : 'lesson_opened',
                    'title' => $progress->status === UserProgress::STATUS_COMPLETED ? 'Hoàn thành bài' : 'Học bài',
                    'subtitle' => $progress->lesson
                        ? 'Bài '.$progress->lesson->number.' - '.$progress->lesson->title
                        : 'Bài #'.$progress->lesson_id,
                    'at' => $progress->completed_at ?: $progress->last_accessed_at,
                    'url' => $progress->lesson ? route('minna.show', ['number' => $progress->lesson->number]) : null,
                ]);
            });

        UserMinnaSectionProgress::query()
            ->where('user_id', $user->id)
            ->with(['lesson:id,number,title', 'section:id,title'])
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take($limit)
            ->get()
            ->each(function (UserMinnaSectionProgress $progress) use ($items) {
                $items->push([
                    'type' => 'section_completed',
                    'title' => 'Hoàn thành phần',
                    'subtitle' => ($progress->lesson ? 'Bài '.$progress->lesson->number.' - ' : '').($progress->section?->title ?? $progress->section_key),
                    'at' => $progress->completed_at,
                    'url' => $progress->lesson ? route('minna.show', ['number' => $progress->lesson->number]) : null,
                ]);
            });

        MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->with('lesson:id,number,title')
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->take($limit)
            ->get()
            ->each(function (MinnaQuizAttempt $attempt) use ($items) {
                $items->push([
                    'type' => $attempt->passed ? 'quiz_passed' : 'quiz_failed',
                    'title' => $attempt->passed ? 'Quiz đạt' : 'Quiz chưa đạt',
                    'subtitle' => ($attempt->lesson ? 'Bài '.$attempt->lesson->number.' - ' : '').$attempt->score.'/'.$attempt->total.' câu đúng ('.$attempt->percent.'%)',
                    'at' => $attempt->completed_at,
                    'url' => $attempt->lesson ? route('minna.show', ['number' => $attempt->lesson->number]) : null,
                ]);
            });

        FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->with('minnaSection.lesson:id,number,title')
            ->whereNotNull('last_reviewed_at')
            ->latest('last_reviewed_at')
            ->take($limit)
            ->get()
            ->each(function (FlashcardCardState $state) use ($items) {
                $lesson = $state->minnaSection?->lesson;
                $items->push([
                    'type' => 'flashcard_reviewed',
                    'title' => 'Ôn flashcard',
                    'subtitle' => ($lesson ? 'Bài '.$lesson->number : 'Flashcard').' - mức nhớ '.($state->last_quality ?? '-'),
                    'at' => $state->last_reviewed_at,
                    'url' => $lesson ? route('flashcard.study', ['number' => $lesson->number, 'mode' => 'srs']) : route('flashcard.index'),
                ]);
            });

        return $items
            ->filter(fn ($item) => $item['at'] !== null)
            ->sortByDesc(fn ($item) => $item['at']->timestamp)
            ->take($limit)
            ->groupBy(fn ($item) => $item['at']->toDateString())
            ->all();
    }

    private function sqlDateOnly(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "DATE({$column})",
            'sqlite' => "date({$column})",
            'pgsql' => "({$column})::date",
            default => "DATE({$column})",
        };
    }

    /**
     * Khóa số tuần ISO khớp Carbon: isoWeekYear * 100 + isoWeek.
     */
    private function sqlIsoWeekKey(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "YEARWEEK({$column}, 3)",
            'sqlite' => '(cast(strftime(\'%G\', '.$column.') as int) * 100 + cast(strftime(\'%V\', '.$column.') as int))',
            'pgsql' => "(to_char({$column}, 'IYYY')::int * 100 + to_char({$column}, 'IW')::int)",
            default => "YEARWEEK({$column}, 3)",
        };
    }
}
