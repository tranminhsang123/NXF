import { apiGet, apiPost } from '../api/apiClient';

export type FlashcardItem = {
  front: string;
  back: string;
  lesson_number?: number | null;
  section_id?: number;
  card_index?: number;
  favorite_id?: number;
};

export type FlashcardMode = 'normal' | 'srs' | 'favorites';

export async function fetchFlashcards(lessonNumbers: number[], mode: 'normal' | 'srs' = 'normal') {
  return apiPost<{ cards: FlashcardItem[]; stats?: { due_count: number; new_count: number; total_in_scope: number } | null }, { lesson_numbers: number[] }>(
    `/flashcards/study?mode=${mode === 'srs' ? 'srs' : 'normal'}`,
    { lesson_numbers: lessonNumbers },
  );
}

export async function fetchFavoriteFlashcards() {
  return apiGet<{ cards: FlashcardItem[]; lessons: unknown[] }>('/flashcards/favorites');
}

export async function submitFlashcardReview(
  payload: { minna_section_id: number; card_index: number; quality: number },
) {
  return apiPost<{ ok: boolean; next_review_at?: string; interval_days?: number; repetitions?: number }, typeof payload>('/flashcards/review', payload);
}
