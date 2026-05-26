export type AuthUser = {
  id: number;
  name: string;
  email: string;
  role: string;
  onboarding?: {
    level: string;
    level_label: string;
    jlpt_goal: string;
    jlpt_goal_label: string;
    daily_study_minutes: number;
    email_reminders_enabled: boolean;
    completed: boolean;
  };
};

export type AuthResponse = {
  message: string;
  token: string;
  user: AuthUser;
};

export type MeResponse = {
  user: AuthUser;
};
