import { useEffect, useState } from 'react';
import { ScrollView, StyleSheet, Text, View } from 'react-native';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import {
  approveJoinRequest,
  declineJoinRequest,
  fetchAdminDashboard,
  fetchAdminNotifications,
  fetchAdminUsers,
  fetchModeration,
  lockAdminUser,
  markNotificationRead,
  unlockAdminUser,
} from '../../services/admin/adminService';
import { useApiStatus } from '../../hooks/useApiStatus';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function AdminTabScreen() {
  const { token } = useAuth();
  const [stats, setStats] = useState<{ total_users: number; total_kanjis: number; total_groups: number; pending_join_requests: number } | null>(null);
  const [users, setUsers] = useState<Array<{ id: number; name: string; email: string; locked_at?: string | null }>>([]);
  const [notifications, setNotifications] = useState<Array<{ id: number; title: string; message?: string }>>([]);
  const [joinRequests, setJoinRequests] = useState<Array<{ id: number; user?: { name: string }; group?: { name: string } }>>([]);
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setSuccessMessage } = useApiStatus('Đang tải dữ liệu quản trị...');

  const load = async () => {
    if (!token) return;
    setLoadingMessage('Đang tải dữ liệu quản trị...');
    try {
      const [dashboardRes, usersRes, notificationsRes, moderationRes] = await Promise.all([
        fetchAdminDashboard(),
        fetchAdminUsers(),
        fetchAdminNotifications(),
        fetchModeration(),
      ]);

      setStats(dashboardRes.data);
      setUsers(usersRes.data.data);
      setNotifications(notificationsRes.data.data);
      setJoinRequests(moderationRes.data.join_requests.data);
      setSuccessMessage('Đã tải dữ liệu quản trị.');
    } catch (error) {
      setErrorFromApi(error, 'Không tải được dữ liệu quản trị.');
    }
  };

  useEffect(() => {
    load();
  }, [token]);

  if (!token) {
    return (
      <View style={styles.center}>
        <Text style={styles.item}>Cần đăng nhập admin.</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Quản trị hệ thống 🛡️</Text>
      {stats ? (
        <AppCard style={styles.card}>
          <Text style={styles.cardTitle}>Tổng quan hệ thống</Text>
          <Text style={styles.item}>Người dùng: {stats.total_users}</Text>
          <Text style={styles.item}>Hán tự: {stats.total_kanjis}</Text>
          <Text style={styles.item}>Nhóm: {stats.total_groups}</Text>
          <Text style={styles.item}>Yêu cầu chờ duyệt: {stats.pending_join_requests}</Text>
        </AppCard>
      ) : null}

      <AppCard style={styles.card}>
        <Text style={styles.cardTitle}>Người dùng (khóa/mở khóa)</Text>
        {users.map((user) => (
          <View key={user.id} style={styles.row}>
            <View style={{ flex: 1 }}>
              <Text style={styles.itemStrong}>{user.name}</Text>
              <Text style={styles.item}>{user.email}</Text>
            </View>
            {user.locked_at ? (
              <AppButton
                onPress={async () => {
                  try {
                    await unlockAdminUser(user.id);
                    await load();
                  } catch (error) {
                    setErrorFromApi(error, 'Mở khóa thất bại.');
                  }
                }}
                style={styles.actionButton}
                label="Mở khóa"
              />
            ) : (
              <AppButton
                onPress={async () => {
                  try {
                    await lockAdminUser(user.id, 'Khóa tài khoản từ mobile admin');
                    await load();
                  } catch (error) {
                    setErrorFromApi(error, 'Khóa tài khoản thất bại.');
                  }
                }}
                style={styles.actionButton}
                label="Khóa"
              />
            )}
          </View>
        ))}
      </AppCard>

      <AppCard style={styles.card}>
        <Text style={styles.cardTitle}>Thông báo</Text>
        {notifications.map((notification) => (
          <AppButton
            key={notification.id}
            onPress={async () => {
              try {
                await markNotificationRead(notification.id);
                setSuccessMessage('Đã đánh dấu thông báo là đã đọc.');
              } catch (error) {
                setErrorFromApi(error, 'Đánh dấu đã đọc thất bại.');
              }
            }}
            style={styles.notification}
            variant="outline"
            label={notification.message ? `${notification.title} - ${notification.message}` : notification.title}
            textStyle={styles.notificationText}
          />
        ))}
      </AppCard>

      <AppCard style={styles.card}>
        <Text style={styles.cardTitle}>Duyệt yêu cầu tham gia</Text>
        {joinRequests.map((request) => (
          <View key={request.id} style={styles.row}>
            <View style={{ flex: 1 }}>
              <Text style={styles.itemStrong}>{request.user?.name ?? 'Người dùng'}</Text>
              <Text style={styles.item}>Nhóm: {request.group?.name ?? '-'}</Text>
            </View>
            <AppButton
              onPress={async () => {
                try {
                  await approveJoinRequest(request.id);
                  await load();
                } catch (error) {
                  setErrorFromApi(error, 'Phê duyệt request thất bại.');
                }
              }}
              style={[styles.actionButton, { backgroundColor: playfulColors.accentGreen }]}
              label="Duyệt"
            />
            <AppButton
              onPress={async () => {
                try {
                  await declineJoinRequest(request.id);
                  await load();
                } catch (error) {
                  setErrorFromApi(error, 'Từ chối request thất bại.');
                }
              }}
              style={[styles.actionButton, { backgroundColor: playfulColors.accentPink }]}
              label="Từ chối"
            />
          </View>
        ))}
      </AppCard>

      <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  center: { flex: 1, alignItems: 'center', justifyContent: 'center' },
  container: { padding: ui.spacing.lg, backgroundColor: playfulColors.page, gap: ui.spacing.sm, paddingBottom: 32 },
  title: { ...ui.text.h1, fontSize: 24 },
  card: { borderRadius: ui.radius.md, padding: 12, gap: 8 },
  cardTitle: { ...ui.text.h2, fontSize: 16 },
  item: { ...ui.text.body },
  itemStrong: { ...ui.text.bodyStrong },
  row: { flexDirection: 'row', gap: ui.spacing.xs, alignItems: 'center' },
  actionButton: { minHeight: ui.control.button.sm, borderRadius: 8, paddingHorizontal: ui.spacing.sm, paddingVertical: 6 },
  notification: { borderRadius: ui.radius.sm, paddingHorizontal: 8, justifyContent: 'flex-start', alignItems: 'flex-start', minHeight: ui.control.button.lg },
  notificationText: { color: playfulColors.textPrimary, fontWeight: '700', textAlign: 'left' },
  status: { ...ui.statusText, color: playfulColors.textSecondary },
});
