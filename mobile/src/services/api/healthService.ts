import { apiGet } from './apiClient';

export async function checkBackendHealth() {
  return apiGet<{ status: string; service: string }>('/health', { timeoutMs: 5000, requireAuth: false });
}
