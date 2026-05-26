import { apiGet } from '../api/apiClient';
import type { MinnaLessonDetailResponse, MinnaLessonListResponse } from '../../types/minna';

export async function fetchMinnaLessons() {
  return apiGet<MinnaLessonListResponse>('/minna/lessons');
}

export async function fetchMinnaLessonDetail(lessonNumber: number) {
  return apiGet<MinnaLessonDetailResponse>(`/minna/lessons/${lessonNumber}`);
}
