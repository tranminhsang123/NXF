import { useEffect } from 'react';
import { useQuery } from '@tanstack/react-query';
import { FlatList, StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import { useApiStatus } from '../../hooks/useApiStatus';
import { fetchProgress, fetchStatistics } from '../../services/learning/learningService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function ProgressTabScreen() {
  const { token } = useAuth();
  const navigation = useNavigation();
  const { statusMessage, statusType, setErrorFromApi, setStatusMessage } = useApiStatus('Theo dõi tiến độ học tập của bạn.');

  const query = useQuery({
    queryKey: ['progress', token],
    queryFn: async () => fetchProgress(),
    enabled: Boolean(token),
  });
  const statisticsQuery = useQuery({
    queryKey: ['learning-statistics', token],
    queryFn: async () => fetchStatistics(),
    enabled: Boolean(token),
  });

  const completedLessons = statisticsQuery.data?.data.summary.completed_lessons ?? 0;
  const vocabEstimate = statisticsQuery.data?.data.summary.total_vocab_estimate ?? 0;
  const recentByDay = statisticsQuery.data?.data.by_day.data ?? [];
  const recentDone = recentByDay.reduce((sum, value) => sum + value, 0);
  const progressItems = query.data?.data.data ?? [];
  const hasAnyError = Boolean(query.error || statisticsQuery.error);
  const isFetchingAny = query.isLoading || statisticsQuery.isLoading;

  useEffect(() => {
    if (hasAnyError) {
      setErrorFromApi(query.error ?? statisticsQuery.error, 'Không tải được thống kê tiến độ.');
      return;
    }

    if (!isFetchingAny && progressItems.length === 0) {
      setStatusMessage('Chưa có bản ghi tiến độ nào.');
      return;
    }

    if (!isFetchingAny) {
      setStatusMessage('Thống kê được cập nhật từ tiến độ bài Minna.');
    }
  }, [hasAnyError, isFetchingAny, progressItems.length, query.error, setErrorFromApi, setStatusMessage, statisticsQuery.error]);

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Tiến độ học tập 📈</Text>
      <AppCard style={styles.summaryCard}>
        <Text style={styles.summaryTitle}>Thống kê nhanh</Text>
        <View style={styles.summaryRow}>
          <View style={styles.summaryItem}>
            <Text style={styles.summaryLabel}>Đã xong</Text>
            <Text style={styles.summaryValue}>{completedLessons}</Text>
          </View>
          <View style={styles.summaryItem}>
            <Text style={styles.summaryLabel}>Từ vựng ước tính</Text>
            <Text style={styles.summaryValue}>{vocabEstimate}</Text>
          </View>
          <View style={styles.summaryItem}>
            <Text style={styles.summaryLabel}>7 ngày</Text>
            <Text style={styles.summaryValue}>{recentDone}</Text>
          </View>
        </View>
        <Text style={styles.summaryHint}>
          {statisticsQuery.isLoading ? 'Đang tải thống kê...' : 'Thống kê được cập nhật từ tiến độ bài Minna.'}
        </Text>
      </AppCard>

      <FlatList
        data={progressItems}
        keyExtractor={(item) => String(item.id)}
        renderItem={({ item }) => (
          <AppCard style={styles.card}>
            <Text style={styles.lessonTitle}>
              Bài {item.lesson?.number}: {item.lesson?.title}
            </Text>
            <Text style={styles.meta}>Trạng thái: {item.status}</Text>
            {item.last_accessed_at ? <Text style={styles.meta}>Lần học gần nhất: {item.last_accessed_at}</Text> : null}
          </AppCard>
        )}
        ListEmptyComponent={
          <AppCard style={styles.emptyCard}>
            <Text style={styles.status}>{query.isLoading ? 'Đang tải...' : 'Chưa có bản ghi tiến độ nào.'}</Text>
            <Text style={styles.emptyHint}>Mở một bài Minna để hệ thống ghi nhận tiến độ.</Text>
            <AppButton label="Mở bài học Minna" onPress={() => navigation.navigate('LessonList' as never)} style={styles.emptyButton} />
          </AppCard>
        }
      />
      <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: playfulColors.page, padding: ui.spacing.lg, gap: ui.spacing.sm },
  title: { ...ui.text.h1, fontSize: 24 },
  summaryCard: { borderRadius: ui.radius.md, padding: 12, gap: 8 },
  summaryTitle: { ...ui.text.h2, fontSize: 16 },
  summaryRow: { flexDirection: 'row', gap: ui.spacing.xs },
  summaryItem: { flex: 1, backgroundColor: playfulColors.softBlue, borderRadius: ui.radius.sm, paddingVertical: 8, alignItems: 'center' },
  summaryLabel: { ...ui.text.caption, fontSize: 11 },
  summaryValue: { color: playfulColors.brandDark, fontSize: 20, fontWeight: '800' },
  summaryHint: { ...ui.text.caption },
  card: { borderRadius: ui.radius.md, padding: 12, marginBottom: 8 },
  lessonTitle: { ...ui.text.bodyStrong, fontWeight: '800' },
  meta: { ...ui.text.body },
  emptyCard: { borderRadius: ui.radius.sm, padding: ui.spacing.md, gap: 8, alignItems: 'center' },
  status: { ...ui.statusText, color: playfulColors.textSecondary, marginTop: 24 },
  emptyHint: { ...ui.text.body, textAlign: 'center' },
  emptyButton: { minHeight: ui.control.button.sm, paddingHorizontal: ui.spacing.md },
});
