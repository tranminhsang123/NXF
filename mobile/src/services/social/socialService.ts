import { apiGet, apiPost } from '../api/apiClient';
import type { PaginatedResponse } from '../../types/api';

export type ChatGroupItem = {
  id: number;
  name: string;
  members_count: number;
};

export type GroupMessage = {
  id: number;
  message_uuid?: string;
  sender_id: number;
  sender_name: string;
  content: string;
  status?: string;
  created_at: string;
};

export async function fetchGroups() {
  return apiGet<{
    groups: PaginatedResponse<ChatGroupItem>;
    available_groups: ChatGroupItem[];
    pending_group_ids: number[];
  }>('/social/groups');
}

export async function joinGroup(groupId: number) {
  return apiPost<{ message: string }>(`/social/groups/${groupId}/join`);
}

export async function fetchGroupMessages(groupId: number) {
  return apiGet<{ messages: GroupMessage[] }>(`/social/groups/${groupId}/messages`);
}

export async function sendGroupMessage(groupId: number, content: string, clientMessageId: string) {
  return apiPost<{ message: GroupMessage; meta?: { idempotent?: boolean; mode?: string } }, { content: string; client_message_id: string }>(
    `/social/groups/${groupId}/messages`,
    { content, client_message_id: clientMessageId },
  );
}

export type ConversationItem = {
  id: number;
  admin?: { id: number; name: string };
  unread_count: number;
  last_message_at?: string | null;
};

export async function fetchConversations() {
  return apiGet<PaginatedResponse<ConversationItem>>('/social/inbox/conversations');
}

export async function fetchConversationMessages(conversationId: number) {
  return apiGet<{ messages: Array<{ id: number; sender_name: string; content: string; created_at: string }> }>(
    `/social/inbox/conversations/${conversationId}/messages`,
  );
}

export async function sendConversationMessage(conversationId: number, content: string, clientMessageId: string) {
  return apiPost<
    { message: { id: number; message_uuid?: string; sender_name: string; content: string; status?: string; created_at: string }; meta?: { idempotent?: boolean; mode?: string } },
    { content: string; client_message_id: string }
  >(
    `/social/inbox/conversations/${conversationId}/messages`,
    { content, client_message_id: clientMessageId },
  );
}
