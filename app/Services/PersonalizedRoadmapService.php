<?php

namespace App\Services;

use App\Models\MinnaLesson;
use App\Models\User;
use App\Models\UserMinnaSectionProgress;
use App\Models\UserProgress;
use App\Support\OnboardingOptions;

class PersonalizedRoadmapService
{
    public function __construct(
        private FlashcardService $flashcardService
    ) {}

    /**
     * @return array{
     *   next_section: ?array,
     *   weak_vocab: array,
     *   srs: array,
     *   kanji_tip: string,
     *   resume_lesson_number: ?int
     * }
     */
    public function build(User $user): array
    {
        $onboarding = OnboardingOptions::summaryFor($user);
        $startLessonNumber = OnboardingOptions::startLessonNumber($user->onboarding_level);
        $srs = $this->flashcardService->getSrsDashboard($user);
        $weakVocab = $srs['weak_cards']->take(6)->values()->all();

        $completedIds = UserMinnaSectionProgress::query()
            ->where('user_id', $user->id)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->pluck('minna_section_id')
            ->flip();

        $nextSection = null;
        $lessons = MinnaLesson::query()
            ->published()
            ->where('number', '>=', $startLessonNumber)
            ->orderBy('number')
            ->with(['sections' => fn ($q) => $q->published()])
            ->get();

        if ($lessons->isEmpty() && $startLessonNumber > 1) {
            $lessons = MinnaLesson::query()
                ->published()
                ->orderBy('number')
                ->with(['sections' => fn ($q) => $q->published()])
                ->get();
        }

        foreach ($lessons as $lesson) {
            foreach ($lesson->sections as $section) {
                if (! isset($completedIds[$section->id])) {
                    $nextSection = [
                        'lesson_number' => $lesson->number,
                        'lesson_title' => $lesson->title,
                        'section_key' => $section->key,
                        'section_title' => $section->title,
                    ];
                    break 2;
                }
            }
        }

        $resume = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->whereNotNull('last_accessed_at')
            ->orderByDesc('last_accessed_at')
            ->with('lesson:id,number')
            ->first();

        $resumeNumber = $resume?->lesson?->number;

        $completedLessons = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->count();

        $kanjiTip = $completedLessons < 3
            ? 'Ưu tiên Minna bài 1–3; song song xem Kanji N5 để làm quen chữ Hán.'
            : 'Ôn SRS các thẻ yếu; quay lại Kanji theo cấp độ JLPT khi từ vựng Minna đã vững.';

        $kanjiTip = $completedLessons < 3
            ? 'Ưu tiên Minna, song song xem Kanji '.$onboarding['jlpt_goal'].' để làm quen chữ Hán.'
            : 'Ôn SRS các thẻ yếu; quay lại Kanji theo mục tiêu '.$onboarding['jlpt_goal'].' khi từ vựng Minna đã vững.';

        $headline = $nextSection
            ? 'Bài '.$nextSection['lesson_number'].' - '.$nextSection['section_title']
            : 'Bạn đã hoàn thành các phần đang có trong lộ trình này';
        $reasonPrefix = $onboarding['reason_focus_text']
            ? $onboarding['reason_focus_text'].' '
            : '';
        $placementText = ! empty($onboarding['placement_test_score'])
            ? 'Kết quả placement test '.$onboarding['placement_test_score'].'/'.count(OnboardingOptions::placementQuestions()).' câu đã được dùng để chọn điểm bắt đầu. '
            : '';
        $reason = $nextSection
            ? $reasonPrefix.$placementText.'Gợi ý dựa trên trình độ '.$onboarding['level_label'].', mục tiêu '.$onboarding['jlpt_goal_label'].', tiến độ section đã hoàn thành và '.$onboarding['daily_study_minutes'].' phút mỗi ngày.'
            : 'Không còn section chưa hoàn thành trong phạm vi hiện tại. Hãy ôn SRS hoặc tăng mục tiêu JLPT.';

        return [
            'next_section' => $nextSection,
            'weak_vocab' => $weakVocab,
            'srs' => [
                'due_count' => $srs['due_count'],
                'weak_count' => $srs['weak_count'],
            ],
            'kanji_tip' => $kanjiTip,
            'resume_lesson_number' => $resumeNumber ? (int) $resumeNumber : null,
            'headline' => $headline,
            'reason' => $reason,
            'onboarding' => $onboarding,
        ];
    }
}
