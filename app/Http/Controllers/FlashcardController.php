<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlashcardReviewRequest;
use App\Models\FavoriteItem;
use App\Models\LearningEvent;
use App\Services\FlashcardService;
use App\Services\GamificationService;
use App\Services\LearningEventService;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlashcardController extends Controller
{
    public function __construct(
        private FlashcardService $flashcardService,
        private SpacedRepetitionService $spacedRepetitionService,
        private LearningEventService $learningEventService
    ) {}

    public function index()
    {
        $lessonsWithCount = $this->flashcardService->getLessonsWithVocabCount();
        $user = Auth::user();
        $favoriteCount = $user
            ? FavoriteItem::query()->where('user_id', $user->id)->count()
            : 0;
        $srsDashboard = $user
            ? $this->flashcardService->getSrsDashboard($user)
            : [
                'reviewed_count' => 0,
                'due_count' => 0,
                'upcoming_count' => 0,
                'weak_count' => 0,
                'next_due_at' => null,
                'weak_lessons' => collect(),
                'weak_cards' => collect(),
            ];

        return view('flashcard.index', compact('lessonsWithCount', 'srsDashboard', 'favoriteCount'));
    }

    public function study(Request $request, ?int $number = null)
    {
        $numbers = $request->input('bai');
        if (is_string($numbers)) {
            $numbers = array_filter(array_map('intval', explode(',', $numbers)));
        }
        if (empty($numbers) || ! is_array($numbers)) {
            $numbers = $number ? [$number] : [1];
        }

        $srsMode = $request->query('mode') === 'srs';
        if ($srsMode && ! Auth::check()) {
            return redirect()->route('login')
                ->with('warning', 'Vui lòng đăng nhập để dùng chế độ ôn tập SRS.');
        }

        if ($srsMode) {
            $result = $this->flashcardService->getFlashcardsForSrs(Auth::user(), $numbers);
        } else {
            $result = $this->flashcardService->getFlashcardsByLessons(
                $numbers,
                (bool) $request->query('shuffle', false)
            );
            $result['stats'] = null;
        }

        if (empty($result['cards'])) {
            if ($srsMode) {
                return redirect()->route('flashcard.index')
                    ->with('warning', 'Không có thẻ đến hạn hoặc thẻ mới trong phạm vi đã chọn. Thử chế độ ôn thường (tất cả thẻ) hoặc chọn thêm bài.');
            }
            abort(404, 'Không tìm thấy từ vựng cho bài đã chọn.');
        }

        $this->learningEventService->record($request->user(), LearningEvent::FLASHCARD_DECK_OPENED, [
            'subject_type' => 'flashcard_deck',
            'metadata' => [
                'lesson_numbers' => array_values($numbers),
                'card_count' => count($result['cards']),
                'mode' => $srsMode ? 'srs' : 'normal',
            ],
        ], $request);

        return view('flashcard.study', [
            'lesson' => $result['lessons'][0] ?? null,
            'lessons' => $result['lessons'],
            'cards' => $result['cards'],
            'reverse' => (bool) $request->query('reverse', false),
            'srsMode' => $srsMode,
            'srsStats' => $result['stats'] ?? null,
        ]);
    }

    public function favorites(Request $request)
    {
        $result = $this->flashcardService->getFavoriteFlashcards($request->user());

        if (empty($result['cards'])) {
            return redirect()
                ->route('flashcard.index')
                ->with('warning', 'Bạn chưa lưu từ/câu yêu thích nào để ôn.');
        }

        $this->learningEventService->record($request->user(), LearningEvent::FLASHCARD_DECK_OPENED, [
            'subject_type' => 'favorite_flashcard_deck',
            'metadata' => [
                'card_count' => count($result['cards']),
                'mode' => 'favorites',
            ],
        ], $request);

        return view('flashcard.study', [
            'lesson' => null,
            'lessons' => [],
            'cards' => $result['cards'],
            'reverse' => (bool) $request->query('reverse', false),
            'srsMode' => false,
            'srsStats' => null,
            'deckTitle' => 'Từ yêu thích',
        ]);
    }

    public function review(StoreFlashcardReviewRequest $request)
    {
        $data = $request->validated();
        $state = $this->spacedRepetitionService->recordReview(
            $request->user(),
            (int) $data['minna_section_id'],
            (int) $data['card_index'],
            (int) $data['quality']
        );

        $gamification = app(GamificationService::class)->onFlashcardReviewed(
            $request->user(),
            (int) $data['quality']
        );

        $this->learningEventService->record($request->user(), LearningEvent::FLASHCARD_REVIEWED, [
            'subject_type' => 'flashcard_card_state',
            'subject_id' => $state->id,
            'minna_section_id' => (int) $data['minna_section_id'],
            'metadata' => [
                'card_index' => (int) $data['card_index'],
                'quality' => (int) $data['quality'],
                'next_review_at' => $state->next_review_at?->toIso8601String(),
                'interval_days' => $state->interval_days,
                'repetitions' => $state->repetitions,
                'lapses' => $state->lapses,
            ],
        ], $request);

        return response()->json([
            'ok' => true,
            'next_review_at' => $state->next_review_at?->toIso8601String(),
            'interval_days' => $state->interval_days,
            'repetitions' => $state->repetitions,
            'lapses' => $state->lapses,
            'gamification' => $gamification,
        ]);
    }
}
