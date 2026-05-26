import { useQuery } from '@tanstack/react-query';
import { FlatList, StyleSheet, Text, View } from 'react-native';

import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchVocabularyLessons } from '../../services/learning/learningService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function VocabularyTabScreen() {
  const { token } = useAuth();

  const query = useQuery({
    queryKey: ['vocabulary-lessons', token],
    queryFn: async () => fetchVocabularyLessons(),
    enabled: Boolean(token),
  });

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Từ vựng theo bài 📝</Text>
      <FlatList
        data={query.data?.data.lessons ?? []}
        keyExtractor={(item) => String(item.lesson.id)}
        renderItem={({ item }) => (
          <AppCard style={styles.card}>
            <Text style={styles.lessonTitle}>
              Bài {item.lesson.number}: {item.lesson.title}
            </Text>
            <Text style={styles.meta}>Số thẻ: {item.count}</Text>
          </AppCard>
        )}
        ListEmptyComponent={<Text style={styles.status}>{query.isLoading ? 'Đang tải...' : 'Không có dữ liệu.'}</Text>}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: playfulColors.page, padding: ui.spacing.lg, gap: ui.spacing.sm },
  title: { ...ui.text.h1, fontSize: 24 },
  card: { borderRadius: ui.radius.md, padding: 12, marginBottom: 8 },
  lessonTitle: { ...ui.text.bodyStrong, fontWeight: '800' },
  meta: { ...ui.text.body },
  status: { ...ui.statusText, color: playfulColors.textSecondary, marginTop: 24 },
});
