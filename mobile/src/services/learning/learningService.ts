import { apiGet } from '../api/apiClient';
import type { PaginatedResponse } from '../../types/api';
import type { MinnaLessonListResponse } from '../../types/minna';

export type DashboardResponse = {
  completedMinnaLessons: number;
  inProgressMinnaLessons: number;
  minnaProgressPercent: number;
  totalMinnaLessons: number;
  totalKanjis: number;
  currentStreak: number;
  onboarding?: {
    level: string;
    level_label: string;
    jlpt_goal: string;
    jlpt_goal_label: string;
    daily_study_minutes: number;
    email_reminders_enabled: boolean;
    completed: boolean;
  };
  roadmap?: {
    headline?: string;
    reason?: string;
    next_section?: {
      lesson_number: number;
      lesson_title: string;
      section_key: string;
      section_title: string;
    } | null;
  };
  advancedDashboard?: {
    charts: {
      lessons_by_day: ChartSeries;
      lessons_by_week: ChartSeries;
    };
    forecast: {
      remaining_lessons: number;
      avg_lessons_per_week: number;
      estimated_completion_date?: string | null;
      weeks_remaining?: number | null;
      confidence: 'low' | 'medium' | 'high' | string;
      message: string;
    };
    daily_goal_lessons: number;
  };
  learningPlan?: {
    daily_goal: {
      target_lessons: number;
      completed_lessons: number;
      remaining_lessons: number;
      percent: number;
      completed: boolean;
    };
    resume_lesson?: DashboardLesson | null;
    next_lesson?: DashboardLesson | null;
    review_lesson_numbers: number[];
    srs: {
      due_count: number;
      new_count: number;
      total_in_scope: number;
    };
    tasks: LearningPlanTask[];
  };
};

export type ChartSeries = {
  labels: string[];
  data: number[];
};

export type DashboardLesson = {
  id: number;
  number: number;
  title: string;
  description?: string | null;
};

export type LearningPlanTask = {
  id: string;
  type: 'lesson' | 'flashcard' | 'daily_goal' | string;
  title: string;
  subtitle?: string | null;
  done: boolean;
  target?: {
    screen?: string;
    lesson_number?: number;
    lesson_numbers?: number[];
    mode?: string;
  };
};

export type CourseLevelItem = {
  level: string;
  title: string;
  subtitle: string;
  description: string;
  icon?: string;
};

export type CourseSectionItem = {
  title: string;
  description?: string;
  icon?: string;
  type?: string | null;
  disabled?: boolean;
};

export type KanjiItem = {
  id: number;
  character: string;
  meaning: string;
  level: string;
  on_reading?: string | null;
  kun_reading?: string | null;
};

export type ProgressItem = {
  id: number;
  status: string;
  completed_at?: string | null;
  last_accessed_at?: string | null;
  lesson?: {
    id: number;
    number: number;
    title: string;
    description?: string | null;
  };
};

export type SearchResponse = {
  lessons: Array<{ id: number; number: number; title: string; description?: string | null }>;
  kanji: Array<{ id: number; character: string; meaning: string; level: string }>;
  vocabulary: Array<{ lesson_number: number; lesson_title: string; word: string; meaning: string; group: string }>;
};

export type LearningStatisticsResponse = {
  by_day: { labels: string[]; data: number[] };
  by_week: { labels: string[]; data: number[] };
  summary: {
    completed_lessons: number;
    total_vocab_estimate: number;
  };
};

export async function fetchDashboard() {
  return apiGet<DashboardResponse>('/learning/dashboard');
}

export async function fetchProgress(page = 1) {
  return apiGet<PaginatedResponse<ProgressItem>>(`/learning/progress?page=${page}`);
}

export async function fetchStatistics() {
  return apiGet<LearningStatisticsResponse>('/learning/statistics');
}

export async function fetchKanjiByLevel(level: string, page = 1, search = '') {
  const q = search ? `&search=${encodeURIComponent(search)}` : '';
  return apiGet<PaginatedResponse<KanjiItem>>(`/learning/kanji/${level}?page=${page}${q}`);
}

export async function fetchVocabularyLessons() {
  return apiGet<{ lessons: Array<{ lesson: { id: number; number: number; title: string }; count: number }> }>('/learning/vocabulary/lessons');
}

export async function fetchCourseLevels() {
  return apiGet<{ courses: CourseLevelItem[] }>('/learning/courses');
}

export async function fetchCourseSections(level: string) {
  return apiGet<{ level: string; course: CourseLevelItem; sections: CourseSectionItem[] }>(`/learning/courses/${level}`);
}

export async function fetchCourseSectionItems(level: string, sectionType: string) {
  return apiGet<{ level?: string; items: Array<{ id?: number; bai?: string; title?: string; order?: number } | string> }>(
    `/learning/courses/${level}/${sectionType}`,
  );
}

export async function fetchCourseSectionItemDetail(level: string, sectionType: string, itemKey: string) {
  return apiGet<{
    level?: string;
    bai?: string;
    title?: string;
    groupedData?: Record<string, unknown>;
    item?: { id?: number; bai?: string; title?: string; content?: unknown; section_key?: string | null };
  }>(`/learning/courses/${level}/${sectionType}/${encodeURIComponent(itemKey)}`);
}

export async function fetchSearch(q: string) {
  return apiGet<SearchResponse>(`/learning/search?q=${encodeURIComponent(q)}`);
}

export async function fetchMinnaLessons() {
  return apiGet<MinnaLessonListResponse>('/minna/lessons');
}
