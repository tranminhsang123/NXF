import { ScrollView, StyleSheet, Text, View } from 'react-native';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function ProfileTabScreen() {
  const { user, signOut, isSubmitting, statusMessage, setStatusMessage } = useAuth();
  const initials = user?.name
    ? user.name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase())
        .join('')
    : 'U';

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Tài khoản</Text>

      <AppCard style={styles.profileHeaderCard}>
        <View style={styles.avatar}>
          <Text style={styles.avatarText}>{initials}</Text>
        </View>
        <View style={styles.profileHeaderText}>
          <Text style={styles.name}>{user?.name ?? 'Người dùng'}</Text>
          <Text style={styles.email}>{user?.email ?? '-'}</Text>
          <View style={styles.roleChip}>
            <Text style={styles.roleChipText}>{(user?.role ?? 'user').toUpperCase()}</Text>
          </View>
        </View>
      </AppCard>

      <AppCard style={styles.card}>
        <Text style={styles.sectionTitle}>Thông tin tài khoản</Text>
        <View style={styles.infoRow}>
          <Text style={styles.label}>Tên hiển thị</Text>
          <Text style={styles.value}>{user?.name ?? '-'}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.infoRow}>
          <Text style={styles.label}>Email</Text>
          <Text style={styles.value}>{user?.email ?? '-'}</Text>
        </View>
        <View style={styles.divider} />
        <View style={styles.infoRow}>
          <Text style={styles.label}>Vai trò</Text>
          <Text style={styles.value}>{user?.role ?? '-'}</Text>
        </View>
      </AppCard>

      <AppCard style={styles.card}>
        <Text style={styles.sectionTitle}>Tùy chọn</Text>
        <AppButton label="🔐 Đổi mật khẩu" variant="outline" onPress={() => setStatusMessage('Tính năng đổi mật khẩu sẽ được cập nhật sớm.')} style={styles.menuButton} textStyle={styles.menuButtonText} />
        <AppButton label="🔔 Cài đặt thông báo" variant="outline" onPress={() => setStatusMessage('Tính năng thông báo sẽ được cập nhật sớm.')} style={styles.menuButton} textStyle={styles.menuButtonText} />
        <AppButton label="☁️ Đồng bộ dữ liệu" variant="outline" onPress={() => setStatusMessage('Tính năng đồng bộ tài khoản sẽ được cập nhật sớm.')} style={styles.menuButton} textStyle={styles.menuButtonText} />
      </AppCard>

      <AppButton label="Đăng xuất" variant="danger" loading={isSubmitting} onPress={signOut} style={styles.logoutButton} />

      <Text style={styles.status}>{statusMessage}</Text>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: playfulColors.page,
    padding: ui.spacing.lg,
    gap: ui.spacing.md,
    paddingBottom: 30,
  },
  title: { ...ui.text.h1, fontSize: 26 },
  profileHeaderCard: { borderRadius: ui.radius.lg, padding: ui.spacing.md, flexDirection: 'row', gap: 12, alignItems: 'center' },
  avatar: {
    width: 64,
    height: 64,
    borderRadius: 32,
    backgroundColor: playfulColors.brand,
    alignItems: 'center',
    justifyContent: 'center',
  },
  avatarText: {
    color: '#ffffff',
    fontSize: 24,
    fontWeight: '800',
  },
  profileHeaderText: {
    flex: 1,
    gap: 2,
  },
  name: { ...ui.text.h2, fontSize: 20 },
  email: { ...ui.text.body },
  roleChip: {
    marginTop: 4,
    alignSelf: 'flex-start',
    backgroundColor: playfulColors.softBlue,
    borderRadius: ui.radius.pill,
    paddingHorizontal: ui.spacing.sm,
    paddingVertical: 4,
  },
  roleChipText: {
    color: playfulColors.brandDark,
    fontWeight: '800',
    fontSize: 11,
  },
  card: { borderRadius: ui.radius.md, padding: ui.spacing.lg, gap: ui.spacing.sm },
  sectionTitle: { ...ui.text.h2, fontSize: 15 },
  infoRow: { gap: 4 },
  divider: {
    height: 1,
    backgroundColor: playfulColors.border,
  },
  label: { ...ui.text.caption, textTransform: 'uppercase' },
  value: { ...ui.text.bodyStrong, fontSize: 16 },
  menuButton: { minHeight: ui.control.button.md, justifyContent: 'center', paddingHorizontal: 12 },
  menuButtonText: {
    color: playfulColors.textPrimary,
    fontWeight: '700',
  },
  logoutButton: { minHeight: ui.control.button.lg, borderRadius: ui.radius.md },
  status: { ...ui.statusText, color: playfulColors.textSecondary },
});
