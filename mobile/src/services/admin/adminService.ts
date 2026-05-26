import { apiGet, apiPost } from '../api/apiClient';
import type { PaginatedResponse } from '../../types/api';

export type AdminDashboardStats = {
  total_users: number;
  total_kanjis: number;
  total_groups: number;
  pending_join_requests: number;
};

export type AdminUserItem = {
  id: number;
  name: string;
  email: string;
  role: string;
  locked_at?: string | null;
};

export async function fetchAdminDashboard() {
  return apiGet<AdminDashboardStats>('/admin/dashboard');
}

export async function fetchAdminUsers(page = 1, search = '') {
  const q = search ? `&search=${encodeURIComponent(search)}` : '';
  return apiGet<PaginatedResponse<AdminUserItem>>(`/admin/users?page=${page}${q}`);
}

export async function lockAdminUser(userId: number, reason: string) {
  return apiPost<{ message: string }, { reason: string }>(`/admin/users/${userId}/lock`, { reason });
}

export async function unlockAdminUser(userId: number) {
  return apiPost<{ message: string }>(`/admin/users/${userId}/unlock`);
}

export async function fetchAdminNotifications(page = 1) {
  return apiGet<PaginatedResponse<{ id: number; title: string; message?: string; created_at: string }>>(`/admin/notifications?page=${page}`);
}

export async function markNotificationRead(notificationId: number) {
  return apiPost<{ ok: boolean }>(`/admin/notifications/${notificationId}/read`);
}

export async function fetchModeration() {
  return apiGet<{
    kanji: PaginatedResponse<{ id: number; character: string; meaning: string; level: string }>;
    minna_sections: PaginatedResponse<{ id: number; key: string; title: string; lesson?: { number: number; title: string } }>;
    join_requests: PaginatedResponse<{ id: number; user?: { name: string }; group?: { name: string } }>;
  }>('/admin/moderation');
}

export async function approveJoinRequest(joinRequestId: number) {
  return apiPost<{ message: string }>(`/admin/join-requests/${joinRequestId}/approve`);
}

export async function declineJoinRequest(joinRequestId: number) {
  return apiPost<{ message: string }>(`/admin/join-requests/${joinRequestId}/decline`);
}
