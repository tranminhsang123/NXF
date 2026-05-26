export type MinnaLesson = {
  id: number;
  number: number;
  title: string;
  description: string | null;
  sections_count: number;
};

export type MinnaLessonListResponse = {
  lessons: MinnaLesson[];
};

export type MinnaSection = {
  id: number;
  lesson_id: number;
  order_index: number;
  key: string;
  title: string;
  content: unknown;
  media_url: string | null;
};

export type MinnaLessonDetail = {
  id: number;
  number: number;
  title: string;
  description: string | null;
  sections: MinnaSection[];
};

export type MinnaLessonDetailResponse = {
  lesson: MinnaLessonDetail;
};
