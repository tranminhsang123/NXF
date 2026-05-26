import { useCallback, useEffect, useMemo, useState } from 'react';
import { ActivityIndicator, FlatList, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import {
  fetchConversationMessages,
  fetchConversations,
  fetchGroupMessages,
  fetchGroups,
  joinGroup,
  sendConversationMessage,
  sendGroupMessage,
} from '../../services/social/socialService';
import { useApiStatus } from '../../hooks/useApiStatus';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

function createClientMessageId(): string {
  return `${Date.now()}-${Math.random().toString(36).slice(2, 10)}`;
}

export function SocialTabScreen() {
  const { token, user } = useAuth();
  const [mode, setMode] = useState<'groups' | 'inbox'>('groups');
  const [groups, setGroups] = useState<Array<{ id: number; name: string; members_count: number }>>([]);
  const [conversations, setConversations] = useState<Array<{ id: number; admin?: { name: string }; unread_count: number }>>([]);
  const [selectedGroupId, setSelectedGroupId] = useState<number | null>(null);
  const [selectedConversationId, setSelectedConversationId] = useState<number | null>(null);
  const [messages, setMessages] = useState<Array<{ id: number; sender_name: string; content: string }>>([]);
  const [replyToMessage, setReplyToMessage] = useState<{ id: number; sender_name: string; content: string } | null>(null);
  const [text, setText] = useState('');
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setSuccessMessage, setStatusMessage } = useApiStatus('Đang tải dữ liệu cộng đồng...');
  const [isLoadingList, setIsLoadingList] = useState(false);
  const [isLoadingMessages, setIsLoadingMessages] = useState(false);
  const [isSending, setIsSending] = useState(false);

  const activeTargetLabel = useMemo(() => {
    if (mode === 'groups') {
      const target = groups.find((group) => group.id === selectedGroupId);
      return target?.name ?? 'Chưa chọn nhóm';
    }

    const target = conversations.find((conversation) => conversation.id === selectedConversationId);
    return target?.admin?.name ?? 'Chưa chọn cuộc trò chuyện';
  }, [conversations, groups, mode, selectedConversationId, selectedGroupId]);
  const hasSelectedTarget = mode === 'groups' ? Boolean(selectedGroupId) : Boolean(selectedConversationId);

  const loadSocialData = useCallback(async () => {
    if (!token) return;
    try {
      setIsLoadingList(true);
      setLoadingMessage('Đang tải dữ liệu cộng đồng...');
      const [groupRes, convoRes] = await Promise.all([fetchGroups(), fetchConversations()]);
      setGroups(groupRes.data.groups.data);
      setConversations(convoRes.data.data);
      setSuccessMessage('Đã tải dữ liệu cộng đồng.');
    } catch (error) {
      setErrorFromApi(error, 'Không tải được dữ liệu cộng đồng.');
    } finally {
      setIsLoadingList(false);
    }
  }, [setErrorFromApi, setLoadingMessage, setSuccessMessage, token]);

  const loadGroupMessages = useCallback(async (groupId: number) => {
    if (!token) return;
    try {
      setIsLoadingMessages(true);
      setLoadingMessage('Đang tải tin nhắn nhóm...');
      const res = await fetchGroupMessages(groupId);
      setMessages(res.data.messages.map((m) => ({ id: m.id, sender_name: m.sender_name, content: m.content })));
      setSuccessMessage('Đã tải tin nhắn nhóm.');
    } catch (error) {
      setErrorFromApi(error, 'Không tải được tin nhắn nhóm.');
    } finally {
      setIsLoadingMessages(false);
    }
  }, [setErrorFromApi, setLoadingMessage, setSuccessMessage, token]);

  const loadConversationMessages = useCallback(async (conversationId: number) => {
    if (!token) return;
    try {
      setIsLoadingMessages(true);
      setLoadingMessage('Đang tải tin nhắn inbox...');
      const res = await fetchConversationMessages(conversationId);
      setMessages(res.data.messages.map((m) => ({ id: m.id, sender_name: m.sender_name, content: m.content })));
      setSuccessMessage('Đã tải tin nhắn inbox.');
    } catch (error) {
      setErrorFromApi(error, 'Không tải được tin nhắn inbox.');
    } finally {
      setIsLoadingMessages(false);
    }
  }, [setErrorFromApi, setLoadingMessage, setSuccessMessage, token]);

  useEffect(() => {
    loadSocialData();
  }, [loadSocialData]);

  useEffect(() => {
    setMessages([]);
    setText('');
    setReplyToMessage(null);
    setSelectedGroupId(null);
    setSelectedConversationId(null);
  }, [mode]);

  const send = async () => {
    if (!token || !text.trim()) return;
    if (!hasSelectedTarget) {
      setStatusMessage(mode === 'groups' ? 'Hãy chọn nhóm trước khi gửi tin nhắn.' : 'Hãy chọn inbox trước khi gửi tin nhắn.');
      return;
    }

    const payload = replyToMessage
      ? `↪ ${replyToMessage.sender_name}: ${replyToMessage.content}\n${text.trim()}`
      : text.trim();

    try {
      setIsSending(true);
      setLoadingMessage('Đang gửi tin nhắn...');
      const clientMessageId = createClientMessageId();
      if (mode === 'groups' && selectedGroupId) {
        const response = await sendGroupMessage(selectedGroupId, payload, clientMessageId);
        if (response.data.meta?.idempotent) {
          setStatusMessage('Tin nhắn đã được ghi nhận trước đó, bỏ qua gửi trùng.');
        }
        await loadGroupMessages(selectedGroupId);
      }
      if (mode === 'inbox' && selectedConversationId) {
        const response = await sendConversationMessage(selectedConversationId, payload, clientMessageId);
        if (response.data.meta?.idempotent) {
          setStatusMessage('Tin nhắn đã được ghi nhận trước đó, bỏ qua gửi trùng.');
        }
        await loadConversationMessages(selectedConversationId);
      }
      setText('');
      setReplyToMessage(null);
      setSuccessMessage('Gửi tin nhắn thành công.');
    } catch (error) {
      setErrorFromApi(error, 'Gửi tin nhắn thất bại.');
    } finally {
      setIsSending(false);
    }
  };

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Cộng đồng 💬</Text>
      <View style={styles.modeRow}>
        <Pressable onPress={() => setMode('groups')} style={[styles.modeButton, mode === 'groups' && styles.modeButtonActive]}>
          <Text style={[styles.modeText, mode === 'groups' && styles.modeTextActive]}>Nhóm chat</Text>
        </Pressable>
        <Pressable onPress={() => setMode('inbox')} style={[styles.modeButton, mode === 'inbox' && styles.modeButtonActive]}>
          <Text style={[styles.modeText, mode === 'inbox' && styles.modeTextActive]}>Tin nhắn riêng</Text>
        </Pressable>
      </View>

      <View style={styles.listHeader}>
        <Text style={styles.listHeaderTitle}>{mode === 'groups' ? 'Danh sách nhóm' : 'Danh sách inbox'}</Text>
        <AppButton label="Tải lại" variant="outline" onPress={loadSocialData} style={styles.refreshButton} textStyle={styles.refreshButtonText} />
      </View>

      {isLoadingList ? (
        <View style={styles.listLoading}>
          <ActivityIndicator color={playfulColors.brand} />
        </View>
      ) : (
        <>
          {mode === 'groups' ? (
            <FlatList
              style={styles.list}
              data={groups}
              keyExtractor={(item) => String(item.id)}
              renderItem={({ item }) => (
                <AppCard
                  onPress={async () => {
                    setSelectedGroupId(item.id);
                    setReplyToMessage(null);
                    await loadGroupMessages(item.id);
                  }}
                  style={[styles.card, selectedGroupId === item.id && styles.cardActive]}
                >
                  <Text style={styles.cardTitle}>{item.name}</Text>
                  <Text style={styles.cardMeta}>{item.members_count} thành viên</Text>
                  <AppButton
                    label="Tham gia"
                    onPress={async () => {
                      if (!token) return;
                      try {
                        await joinGroup(item.id);
                        setSuccessMessage('Đã gửi yêu cầu tham gia (nếu chưa là thành viên).');
                      } catch (error) {
                        setErrorFromApi(error, 'Gửi yêu cầu tham gia thất bại.');
                      }
                    }}
                    style={styles.joinButton}
                  />
                </AppCard>
              )}
              ListEmptyComponent={<Text style={styles.status}>Chưa có dữ liệu.</Text>}
            />
          ) : (
            <FlatList
              style={styles.list}
              data={conversations}
              keyExtractor={(item) => String(item.id)}
              renderItem={({ item }) => (
                <AppCard
                  onPress={async () => {
                    setSelectedConversationId(item.id);
                    setReplyToMessage(null);
                    await loadConversationMessages(item.id);
                  }}
                  style={[styles.card, selectedConversationId === item.id && styles.cardActive]}
                >
                  <Text style={styles.cardTitle}>{item.admin?.name || 'Cuộc trò chuyện 1-1'}</Text>
                  <Text style={styles.cardMeta}>Chưa đọc: {item.unread_count}</Text>
                </AppCard>
              )}
              ListEmptyComponent={<Text style={styles.status}>Chưa có dữ liệu.</Text>}
            />
          )}
        </>
      )}

      <View style={styles.messageHeader}>
        <View style={styles.messageHeaderRow}>
          <Text style={styles.messageHeaderTitle}>Đang chat: {activeTargetLabel}</Text>
          {hasSelectedTarget ? (
            <Pressable
              accessibilityRole="button"
              accessibilityLabel="Quay lại danh sách"
              onPress={() => {
                setMessages([]);
                setReplyToMessage(null);
                if (mode === 'groups') {
                  setSelectedGroupId(null);
                  return;
                }
                setSelectedConversationId(null);
              }}
              style={({ pressed }) => [styles.backIconButton, pressed && styles.buttonPressed]}
            >
              <Text style={styles.backIconText}>←</Text>
            </Pressable>
          ) : null}
        </View>
      </View>
      <View style={styles.messageBox}>
        {isLoadingMessages ? (
          <View style={styles.messageLoading}>
            <ActivityIndicator color={playfulColors.brand} />
          </View>
        ) : (
          <FlatList
            data={messages}
            keyExtractor={(item) => String(item.id)}
            renderItem={({ item }) => {
              const isMine = item.sender_name === user?.name;
              return (
                <Pressable onPress={() => setReplyToMessage(item)} style={({ pressed }) => [styles.bubble, isMine ? styles.myBubble : styles.otherBubble, pressed && styles.buttonPressed]}>
                  <Text style={[styles.bubbleName, isMine && styles.myBubbleText]}>{item.sender_name}</Text>
                  <Text style={[styles.bubbleText, isMine && styles.myBubbleText]}>{item.content}</Text>
                </Pressable>
              );
            }}
            ListEmptyComponent={<Text style={styles.status}>Chọn nhóm/inbox để xem tin nhắn.</Text>}
          />
        )}
      </View>

      {replyToMessage ? (
        <View style={styles.replyBox}>
          <View style={styles.replyTextWrap}>
            <Text style={styles.replyLabel}>Đang trả lời {replyToMessage.sender_name}</Text>
            <Text numberOfLines={1} style={styles.replyPreview}>
              {replyToMessage.content}
            </Text>
          </View>
          <Pressable
            accessibilityRole="button"
            accessibilityLabel="Bỏ trả lời"
            onPress={() => setReplyToMessage(null)}
            style={({ pressed }) => [styles.replyCancel, pressed && styles.buttonPressed]}
          >
            <Text style={styles.replyCancelText}>✕</Text>
          </Pressable>
        </View>
      ) : null}

      <View style={styles.sendRow}>
        <TextInput
          value={text}
          onChangeText={setText}
          placeholder={hasSelectedTarget ? 'Nhập tin nhắn...' : mode === 'groups' ? 'Chọn nhóm để chat...' : 'Chọn inbox để chat...'}
          editable={hasSelectedTarget}
          style={[styles.input, !hasSelectedTarget && styles.inputDisabled]}
        />
        <AppButton label="Gửi" loading={isSending} disabled={!text.trim()} onPress={send} style={styles.sendButton} textStyle={styles.sendButtonText} />
      </View>

      <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: playfulColors.page, padding: 12, gap: ui.spacing.xs, paddingBottom: 96 },
  title: { ...ui.text.h1 },
  modeRow: { flexDirection: 'row', gap: ui.spacing.xs },
  modeButton: {
    flex: 1,
    minHeight: ui.control.chipHeight,
    borderWidth: 1,
    borderColor: playfulColors.border,
    paddingVertical: 6,
    borderRadius: ui.radius.sm,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#fff',
  },
  modeButtonActive: { backgroundColor: playfulColors.brand, borderColor: playfulColors.brand },
  modeText: { color: playfulColors.textSecondary, fontWeight: '700' },
  modeTextActive: { color: '#fff' },
  listHeader: { flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' },
  listHeaderTitle: { ...ui.text.captionStrong },
  refreshButton: { borderRadius: 8, minHeight: ui.control.button.sm, paddingHorizontal: 10, paddingVertical: 4 },
  refreshButtonText: { ...ui.text.captionStrong, fontWeight: '700' },
  listLoading: { minHeight: 80, alignItems: 'center', justifyContent: 'center' },
  list: { maxHeight: 150 },
  card: { borderRadius: ui.radius.md, padding: ui.spacing.sm, marginBottom: 8, gap: 2 },
  cardActive: { borderColor: playfulColors.brand },
  cardTitle: { ...ui.text.bodyStrong },
  cardMeta: { ...ui.text.caption },
  joinButton: { marginTop: 4, alignSelf: 'flex-start', borderRadius: 8, minHeight: ui.control.button.sm, paddingHorizontal: 8, paddingVertical: 4 },
  messageHeader: { ...ui.surfaceCard, borderRadius: ui.radius.sm, padding: ui.spacing.xs },
  messageHeaderRow: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: ui.spacing.xs },
  messageHeaderTitle: { ...ui.text.captionStrong },
  backIconButton: {
    minWidth: ui.control.button.sm,
    minHeight: ui.control.button.sm,
    borderRadius: ui.radius.pill,
    borderWidth: 1,
    borderColor: playfulColors.border,
    backgroundColor: '#fff',
    alignItems: 'center',
    justifyContent: 'center',
  },
  backIconText: { ...ui.text.bodyStrong, fontSize: 16 },
  buttonPressed: { opacity: 0.72 },
  messageBox: { flex: 1, ...ui.surfaceCard, borderRadius: ui.radius.sm, padding: ui.spacing.xs },
  messageLoading: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  bubble: { maxWidth: '85%', borderRadius: ui.radius.sm, paddingHorizontal: ui.spacing.sm, paddingVertical: ui.spacing.xs, marginBottom: 8 },
  myBubble: { alignSelf: 'flex-end', backgroundColor: playfulColors.brand },
  otherBubble: { alignSelf: 'flex-start', backgroundColor: playfulColors.softBlue },
  bubbleName: { ...ui.text.caption, marginBottom: 2, fontWeight: '700' },
  bubbleText: { ...ui.text.body },
  myBubbleText: { color: '#ffffff' },
  replyBox: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    backgroundColor: '#ffffff',
    borderRadius: ui.radius.sm,
    padding: ui.spacing.xs,
    flexDirection: 'row',
    alignItems: 'center',
    gap: ui.spacing.xs,
  },
  replyTextWrap: {
    flex: 1,
  },
  replyLabel: { ...ui.text.captionStrong },
  replyPreview: { ...ui.text.caption },
  replyCancel: {
    minHeight: ui.control.button.sm,
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 8,
    backgroundColor: playfulColors.softBlue,
    justifyContent: 'center',
  },
  replyCancelText: {
    color: playfulColors.brandDark,
    fontWeight: '700',
    fontSize: 16,
    lineHeight: 16,
  },
  sendRow: { flexDirection: 'row', gap: 8, marginBottom: 4 },
  input: {
    flex: 1,
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    minHeight: ui.control.inputHeight,
    backgroundColor: '#fff',
    paddingHorizontal: 10,
    paddingVertical: 8,
  },
  inputDisabled: { backgroundColor: '#f1f5ff' },
  sendButton: { borderRadius: ui.radius.sm, minHeight: ui.control.button.md, paddingHorizontal: 12, justifyContent: 'center' },
  sendButtonText: { color: '#fff', fontWeight: '700' },
  status: { ...ui.statusText, color: playfulColors.textSecondary },
});
