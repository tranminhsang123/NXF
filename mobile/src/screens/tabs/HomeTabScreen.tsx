import { useQuery } from '@tanstack/react-query';
import { Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchDashboard, type LearningPlanTask } from '../../services/learning/learningService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function HomeTabScreen() {
  const { user, token } = useAuth();
  const navigation = useNavigation();
  const nav = navigation as { navigate: (name: string, params?: unknown) => void };
  const dashboardQuery = useQuery({
    queryKey: ['dashboard', token],
    queryFn: async () => fetchDashboard(),
    enabled: Boolean(token),
  });

  const dashboard = dashboardQuery.data?.data;
  const progressPercent = Math.max(0, Math.min(100, dashboard?.minnaProgressPercent ?? 0));
  const completedLessons = dashboard?.completedMinnaLessons ?? 0;
  const streak = dashboard?.currentStreak ?? 0;
  const totalKanji = dashboard?.totalKanjis ?? 0;
  const leftToNextMilestone = Math.max(0, 10 - (completedLessons % 10));
  const learningPlan = dashboard?.learningPlan;
  const todayTasks = learningPlan?.tasks ?? [];
  const nextLesson = learningPlan?.resume_lesson ?? learningPlan?.next_lesson ?? null;
  const roadmap = dashboard?.roadmap;
  const recommendedSection = roadmap?.next_section ?? null;
  const srsStats = learningPlan?.srs;
  const recentActivities = [
    `Bạn đã hoàn thành ${completedLessons} bài Minna.`,
    `Tiến độ học hiện tại đạt ${progressPercent}%.`,
    streak > 0 ? `Chuỗi học liên tục: ${streak} ngày.` : 'Bắt đầu chuỗi học mới ngay hôm nay.',
  ];

  const openTask = (task: LearningPlanTask) => {
    if (task.target?.screen === 'LessonDetail' && task.target.lesson_number) {
      nav.navigate('Learn', { screen: 'LessonDetail', params: { lessonNumber: task.target.lesson_number } });
      return;
    }

    if (task.target?.screen === 'Flashcards') {
      nav.navigate('Learn', { screen: 'Flashcards' });
      return;
    }

    if (task.target?.screen === 'Progress') {
      nav.navigate('Learn', { screen: 'Progress' });
      return;
    }

    nav.navigate('Learn');
  };

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      <AppCard style={styles.hero}>
        <View style={styles.badgeRow}>
          <Text style={styles.heroBadge}>Tổng quan học tập</Text>
          <Text style={styles.heroBadge}>Streak {streak}d</Text>
        </View>
        <Text style={styles.title}>Xin chào {user?.name ?? 'bạn'} 👋</Text>
        <Text style={styles.subtitle}>Hôm nay mình đặt mục tiêu học và ôn tập thật gọn nhé.</Text>
      </AppCard>

      <AppCard style={styles.highlightCard}>
        <Text style={styles.highlightTitle}>
          {roadmap?.headline ?? (nextLesson ? `Bài ${nextLesson.number}: ${nextLesson.title}` : 'Tiếp tục bài học')}
        </Text>
        <Text style={styles.highlightMeta}>
          {roadmap?.reason ?? (srsStats ? `SRS đến hạn ${srsStats.due_count} thẻ, mới ${srsStats.new_count} thẻ` : `Bạn đã hoàn thành ${completedLessons} bài Minna`)}
        </Text>
        <View style={styles.highlightActions}>
          <AppButton
            label={nextLesson || recommendedSection ? 'Mở bài tiếp theo' : 'Mở trung tâm học'}
            onPress={() =>
              nextLesson
                ? nav.navigate('Learn', { screen: 'LessonDetail', params: { lessonNumber: nextLesson.number } })
                : recommendedSection
                  ? nav.navigate('Learn', { screen: 'LessonDetail', params: { lessonNumber: recommendedSection.lesson_number } })
                  : nav.navigate('Learn')
            }
            style={styles.primaryButton}
          />
          <AppButton label="Ôn thẻ SRS" variant="outline" onPress={() => nav.navigate('Learn', { screen: 'Flashcards' })} style={styles.secondaryButton} />
        </View>
      </AppCard>

      <View style={styles.sectionRow}>
        <Text style={styles.sectionTitle}>Tổng quan nhanh</Text>
      </View>
      <View style={styles.kpiRow}>
        <AppCard style={styles.kpiCard}>
          <Text style={styles.kpiLabel}>Minna</Text>
          <Text style={styles.kpiValue}>{completedLessons}</Text>
        </AppCard>
        <AppCard style={styles.kpiCard}>
          <Text style={styles.kpiLabel}>Tiến độ</Text>
          <Text style={styles.kpiValue}>{progressPercent}%</Text>
        </AppCard>
        <AppCard style={styles.kpiCard}>
          <Text style={styles.kpiLabel}>Kanji</Text>
          <Text style={styles.kpiValue}>{totalKanji}</Text>
        </AppCard>
      </View>

      <AppCard style={styles.progressCard}>
        <View style={styles.progressHeader}>
          <Text style={styles.sectionTitle}>Tiến độ Minna</Text>
          <Text style={styles.progressValue}>{progressPercent}%</Text>
        </View>
        <View style={styles.progressTrack}>
          <View style={[styles.progressFill, { width: `${progressPercent}%` }]} />
        </View>
        <Text style={styles.progressHint}>
          {leftToNextMilestone === 0
            ? 'Đạt mốc 10 bài. Rất tốt!'
            : `Còn ${leftToNextMilestone} bài để đạt mốc tiếp theo.`}
        </Text>
      </AppCard>

      <View style={styles.sectionRow}>
        <Text style={styles.sectionTitle}>Tác vụ nhanh</Text>
      </View>
      <View style={styles.quickActionsRow}>
        <AppButton label="📚 Học" variant="outline" onPress={() => navigation.navigate('Learn' as never)} style={styles.quickChip} textStyle={styles.quickChipText} />
        <AppButton label="💬 Cộng đồng" variant="outline" onPress={() => navigation.navigate('Social' as never)} style={styles.quickChip} textStyle={styles.quickChipText} />
        <AppButton label="👤 Tài khoản" variant="outline" onPress={() => navigation.navigate('Profile' as never)} style={styles.quickChip} textStyle={styles.quickChipText} />
      </View>

      <View style={styles.sectionRow}>
        <Text style={styles.sectionTitle}>Kế hoạch hôm nay</Text>
      </View>
      <AppCard style={styles.goalCard}>
        {todayTasks.map((task) => (
          <Pressable key={task.id} onPress={() => openTask(task)} style={styles.goalRow}>
            <Text style={[styles.goalCheck, task.done && styles.goalCheckDone]}>{task.done ? '✓' : '○'}</Text>
            <View style={styles.goalTextWrap}>
              <Text style={[styles.goalText, task.done && styles.goalTextDone]}>{task.title}</Text>
              {task.subtitle ? <Text style={styles.goalSubtitle}>{task.subtitle}</Text> : null}
            </View>
          </Pressable>
        ))}
      </AppCard>

      <View style={styles.sectionRow}>
        <Text style={styles.sectionTitle}>Hoạt động gần đây</Text>
      </View>
      <AppCard style={styles.activityCard}>
        {recentActivities.map((activity, idx) => (
          <View key={idx} style={styles.activityRow}>
            <Text style={styles.activityDot}>•</Text>
            <Text style={styles.activityText}>{activity}</Text>
          </View>
        ))}
      </AppCard>

      {dashboardQuery.isLoading ? <Text style={styles.status}>Đang tải tổng quan...</Text> : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    paddingHorizontal: ui.spacing.lg,
    paddingVertical: ui.spacing.xl,
    gap: ui.spacing.md,
    backgroundColor: playfulColors.page,
    paddingBottom: 120,
  },
  hero: { borderRadius: ui.radius.lg, padding: ui.spacing.lg, gap: ui.spacing.sm },
  badgeRow: {
    flexDirection: 'row',
    gap: ui.spacing.xs,
    flexWrap: 'wrap',
    alignItems: 'center',
  },
  heroBadge: {
    backgroundColor: playfulColors.softBlue,
    color: playfulColors.brandDark,
    fontSize: 11,
    fontWeight: '800',
    paddingHorizontal: ui.spacing.sm,
    paddingVertical: 4,
    borderRadius: ui.radius.pill,
    overflow: 'hidden',
  },
  title: { ...ui.text.h1, fontSize: 28 },
  subtitle: { ...ui.text.body },
  highlightCard: { borderRadius: ui.radius.md, padding: 12, gap: 8 },
  highlightTitle: { ...ui.text.h2 },
  highlightMeta: { ...ui.text.body },
  highlightActions: { flexDirection: 'row', gap: ui.spacing.xs },
  primaryButton: { minHeight: ui.control.button.md, borderRadius: ui.radius.sm },
  secondaryButton: { flex: 1, minHeight: ui.control.button.md, borderRadius: ui.radius.sm },
  sectionRow: {
    marginTop: 2,
  },
  sectionTitle: { ...ui.text.h2, fontSize: 15 },
  kpiRow: {
    flexDirection: 'row',
    gap: ui.spacing.sm,
  },
  kpiCard: { flex: 1, borderRadius: ui.radius.md, paddingVertical: ui.spacing.sm, alignItems: 'center' },
  kpiLabel: { ...ui.text.caption, fontSize: 11 },
  kpiValue: {
    color: playfulColors.textPrimary,
    fontWeight: '800',
    fontSize: 22,
  },
  progressCard: { borderRadius: ui.radius.md, padding: 12, gap: 8 },
  progressHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  progressValue: {
    color: playfulColors.brandDark,
    fontWeight: '800',
    fontSize: 18,
  },
  progressTrack: {
    height: 10,
    borderRadius: ui.radius.pill,
    backgroundColor: '#e7edff',
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    borderRadius: ui.radius.pill,
    backgroundColor: playfulColors.accentGreen,
  },
  progressHint: { ...ui.text.caption },
  quickActionsRow: {
    flexDirection: 'row',
    flexWrap: 'nowrap',
    gap: ui.spacing.sm,
  },
  quickChip: { flex: 1, borderRadius: ui.radius.pill, minHeight: ui.control.button.md, paddingHorizontal: 8 },
  quickChipText: {
    color: playfulColors.textPrimary,
    fontWeight: '700',
    fontSize: 12,
  },
  goalCard: { borderRadius: ui.radius.md, padding: 12, gap: ui.spacing.xs },
  goalRow: { flexDirection: 'row', alignItems: 'center', gap: ui.spacing.xs },
  goalCheck: { ...ui.text.bodyStrong, color: playfulColors.textSecondary, width: 16 },
  goalCheckDone: { color: playfulColors.accentGreen },
  goalTextWrap: { flex: 1, gap: 2 },
  goalText: { ...ui.text.body, flex: 1 },
  goalSubtitle: { ...ui.text.caption },
  goalTextDone: { color: playfulColors.textPrimary, fontWeight: '700' },
  activityCard: { borderRadius: ui.radius.md, padding: 12, gap: ui.spacing.xs },
  activityRow: { flexDirection: 'row', alignItems: 'flex-start', gap: ui.spacing.xs },
  activityDot: { ...ui.text.bodyStrong, color: playfulColors.brandDark, lineHeight: 20 },
  activityText: { ...ui.text.body, flex: 1 },
  status: { ...ui.statusText, color: playfulColors.textSecondary },
});
