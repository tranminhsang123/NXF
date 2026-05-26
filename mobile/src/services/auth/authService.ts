import { apiGet, apiPost } from '../api/apiClient';
import type { AuthResponse, MeResponse } from '../../types/auth';

type LoginPayload = {
  email: string;
  password: string;
};

type GoogleLoginPayload = {
  idToken: string;
};

type RegisterPayload = {
  name: string;
  email: string;
  password: string;
  passwordConfirmation: string;
  onboardingLevel: string;
  jlptGoal: string;
  dailyStudyMinutes: number;
  emailRemindersEnabled: boolean;
};

const DEVICE_NAME = 'expo-mobile';

export async function login(payload: LoginPayload) {
  return apiPost<AuthResponse, Record<string, string>>(
    '/auth/login',
    {
      email: payload.email,
      password: payload.password,
      device_name: DEVICE_NAME,
    },
    { requireAuth: false },
  );
}

export async function register(payload: RegisterPayload) {
  return apiPost<AuthResponse>(
    '/auth/register',
    {
      name: payload.name,
      email: payload.email,
      password: payload.password,
      password_confirmation: payload.passwordConfirmation,
      onboarding_level: payload.onboardingLevel,
      jlpt_goal: payload.jlptGoal,
      daily_study_minutes: payload.dailyStudyMinutes,
      email_reminders_enabled: payload.emailRemindersEnabled,
      device_name: DEVICE_NAME,
    },
    { requireAuth: false },
  );
}

export async function loginWithGoogle(payload: GoogleLoginPayload) {
  return apiPost<AuthResponse, Record<string, string>>(
    '/auth/google',
    {
      id_token: payload.idToken,
      device_name: `${DEVICE_NAME}-google`,
    },
    { requireAuth: false },
  );
}

export async function me(token: string) {
  return apiGet<MeResponse>('/auth/me', {
    token,
  });
}

export async function logout() {
  return apiPost<{ message: string }>('/auth/logout');
}
