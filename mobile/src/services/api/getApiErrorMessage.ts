import { normalizeApiError } from './normalizeApiError';

export function getApiErrorMessage(error: unknown, fallbackMessage: string) {
  const normalized = normalizeApiError(error);
  return normalized.message || fallbackMessage;
}

