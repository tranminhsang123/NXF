import { createContext, useContext, useEffect, useMemo, useState, type ReactNode } from 'react';

import { login, loginWithGoogle, logout, me, register } from '../services/auth/authService';
import { onUnauthorized } from '../services/auth/sessionEvents';
import { clearAccessToken, getAccessToken, saveAccessToken } from '../services/auth/tokenStorage';
import { normalizeApiError } from '../services/api/normalizeApiError';
import type { AuthUser } from '../types/auth';

type LoginInput = {
  email: string;
  password: string;
};

type RegisterInput = {
  name: string;
  email: string;
  password: string;
  passwordConfirmation: string;
  onboardingLevel: string;
  jlptGoal: string;
  dailyStudyMinutes: number;
  emailRemindersEnabled: boolean;
};

type AuthContextValue = {
  user: AuthUser | null;
  token: string | null;
  isBootstrapping: boolean;
  isSubmitting: boolean;
  statusMessage: string;
  signIn: (payload: LoginInput) => Promise<void>;
  signInWithGoogle: (idToken: string) => Promise<void>;
  signUp: (payload: RegisterInput) => Promise<void>;
  signOut: () => Promise<void>;
  setStatusMessage: (message: string) => void;
};

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [token, setToken] = useState<string | null>(null);
  const [isBootstrapping, setIsBootstrapping] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [statusMessage, setStatusMessage] = useState('San sang dang nhap.');

  useEffect(() => {
    const restoreSession = async () => {
      try {
        const storedToken = await getAccessToken();

        if (!storedToken) {
          return;
        }

        const response = await me(storedToken);
        setUser(response.data.user);
        setToken(storedToken);
        setStatusMessage(`Da khoi phuc session: ${response.baseUrl}`);
      } catch {
        await clearAccessToken();
      } finally {
        setIsBootstrapping(false);
      }
    };

    restoreSession();
  }, []);

  useEffect(() => {
    return onUnauthorized(() => {
      setToken(null);
      setUser(null);
      setStatusMessage('Phien dang nhap da het han. Vui long dang nhap lai.');
    });
  }, []);

  const signIn = async (payload: LoginInput) => {
    try {
      setIsSubmitting(true);
      const response = await login(payload);

      await saveAccessToken(response.data.token);
      setToken(response.data.token);
      setUser(response.data.user);
      setStatusMessage(`Dang nhap thanh cong: ${response.baseUrl}`);
    } catch (error) {
      const normalized = normalizeApiError(error);
      setStatusMessage(normalized.message || 'Dang nhap that bai.');

      throw error;
    } finally {
      setIsSubmitting(false);
    }
  };

  const signUp = async (payload: RegisterInput) => {
    try {
      setIsSubmitting(true);
      const response = await register(payload);

      await saveAccessToken(response.data.token);
      setToken(response.data.token);
      setUser(response.data.user);
      setStatusMessage(`Dang ky thanh cong: ${response.baseUrl}`);
    } catch (error) {
      const normalized = normalizeApiError(error);
      setStatusMessage(normalized.message || 'Dang ky that bai.');

      throw error;
    } finally {
      setIsSubmitting(false);
    }
  };

  const signInWithGoogle = async (idToken: string) => {
    try {
      setIsSubmitting(true);
      const response = await loginWithGoogle({ idToken });

      await saveAccessToken(response.data.token);
      setToken(response.data.token);
      setUser(response.data.user);
      setStatusMessage(`Dang nhap Google thanh cong: ${response.baseUrl}`);
    } catch (error) {
      const normalized = normalizeApiError(error);
      setStatusMessage(normalized.message || 'Dang nhap Google that bai.');

      throw error;
    } finally {
      setIsSubmitting(false);
    }
  };

  const signOut = async () => {
    try {
      setIsSubmitting(true);

      if (token) {
        await logout();
      }
    } catch {
      // Continue logout locally even if network fails.
    } finally {
      await clearAccessToken();
      setToken(null);
      setUser(null);
      setStatusMessage('Da dang xuat.');
      setIsSubmitting(false);
    }
  };

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      token,
      isBootstrapping,
      isSubmitting,
      statusMessage,
      signIn,
      signInWithGoogle,
      signUp,
      signOut,
      setStatusMessage,
    }),
    [isBootstrapping, isSubmitting, statusMessage, token, user],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }

  return context;
}
