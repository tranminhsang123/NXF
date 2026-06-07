import { useEffect, useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';

import { AppCard } from '../../components/AppCard';
import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import { useApiStatus } from '../../hooks/useApiStatus';
import { fetchSearch } from '../../services/learning/learningService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

const CATEGORIES = [
  { key: 'vocabulary', label: 'Từ vựng' },
  { key: 'kanji', label: 'Kanji' },
  { key: 'lessons', label: 'Bài Minna' },
  { key: 'sentence_patterns', label: 'Mẫu câu' },
  { key: 'grammar', label: 'Ngữ pháp' },
  { key: 'favorites', label: 'Từ yêu thích' },
  { key: 'related', label: 'Gợi ý liên quan' },
] as const;

export function SearchTabScreen() {
  const { token } = useAuth();
  const [q, setQ] = useState('');
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage } = useApiStatus(
    'Nhập ít nhất 2 ký tự để tìm kiếm toàn hệ thống.',
  );

  const query = useQuery({
    queryKey: ['global-search', q, token],
    queryFn: async () => fetchSearch(q),
    enabled: Boolean(token) && q.trim().length >= 2,
  });

  const data = query.data?.data;

  useEffect(() => {
    if (q.trim().length < 2) {
      setStatusMessage('Nhập ít nhất 2 ký tự để tìm kiếm toàn hệ thống.');
      return;
    }

    if (query.isLoading) {
      setLoadingMessage('Đang tìm...');
      return;
    }

    if (query.error) {
      setErrorFromApi(query.error, 'Tìm kiếm thất bại.');
      return;
    }

    const total = data?.counts ? Object.values(data.counts).reduce((a, b) => a + b, 0) : 0;
    setSuccessMessage(total > 0 ? `Tìm thấy ${total} kết quả.` : 'Không tìm thấy kết quả.');
  }, [q, data, query.error, query.isLoading, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage]);

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Tìm kiếm toàn hệ thống 🔎</Text>
      <Text style={styles.subtitle}>Từ vựng · Kanji · Minna · Mẫu câu · Ngữ pháp · Yêu thích</Text>
      <TextInput value={q} onChangeText={setQ} placeholder="Nhập từ khóa..." style={styles.input} />

      <StatusText message={statusMessage} statusType={statusType} style={styles.hint} />

      {q.trim().length < 2 ? (
        <View style={styles.catGrid}>
          {CATEGORIES.map((c) => (
            <View key={c.key} style={styles.catChip}>
              <Text style={styles.catLabel}>{c.label}</Text>
            </View>
          ))}
        </View>
      ) : null}

      {data && (data.vocabulary?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Từ vựng ({data.counts?.vocabulary ?? 0})</Text>
          {data.vocabulary.map((item, index) => (
            <Text key={`v-${index}`} style={styles.item}>
              {item.term}
              {item.reading ? ` (${item.reading})` : ''} — {item.meaning}
              {item.lesson_number ? ` · Bài ${item.lesson_number}` : ''}
            </Text>
          ))}
        </AppCard>
      ) : null}

      {data && (data.kanji?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Kanji ({data.counts?.kanji ?? 0})</Text>
          {data.kanji.map((item) => (
            <Text key={`k-${item.id}`} style={styles.item}>
              {item.character} — {item.meaning} ({item.level})
            </Text>
          ))}
        </AppCard>
      ) : null}

      {data && (data.lessons?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Bài Minna ({data.counts?.lessons ?? 0})</Text>
          {data.lessons.map((lesson) => (
            <Text key={`l-${lesson.id}`} style={styles.item}>
              Bài {lesson.number}: {lesson.title}
            </Text>
          ))}
        </AppCard>
      ) : null}

      {data && (data.sentence_patterns?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Mẫu câu ({data.counts?.sentence_patterns ?? 0})</Text>
          {data.sentence_patterns.map((item, index) => (
            <Text key={`p-${index}`} style={styles.item}>
              {item.pattern} — {item.meaning}
              {item.lesson_number ? ` · Bài ${item.lesson_number}` : ''}
            </Text>
          ))}
        </AppCard>
      ) : null}

      {data && (data.grammar?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Ngữ pháp ({data.counts?.grammar ?? 0})</Text>
          {data.grammar.map((item, index) => (
            <Text key={`g-${index}`} style={styles.item}>
              {item.title}
              {item.pattern ? `: ${item.pattern}` : ''}
              {item.lesson_number ? ` · Bài ${item.lesson_number}` : ''}
            </Text>
          ))}
        </AppCard>
      ) : null}

      {data && (data.favorites?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Từ yêu thích ({data.counts?.favorites ?? 0})</Text>
          {data.favorites.map((item) => (
            <Text key={`f-${item.id}`} style={styles.item}>
              {item.front} — {item.back}
            </Text>
          ))}
        </AppCard>
      ) : null}

      {data && (data.related?.length ?? 0) > 0 ? (
        <AppCard style={styles.section}>
          <Text style={styles.sectionTitle}>Gợi ý liên quan ({data.counts?.related ?? 0})</Text>
          {data.related.map((row, index) => {
            const it = row.item ?? {};
            const label =
              (it.term as string) ||
              (it.character as string) ||
              (it.pattern as string) ||
              row.reason ||
              '';
            return (
              <Text key={`r-${index}`} style={styles.item}>
                {row.reason ? `${row.reason}: ` : ''}
                {label}
              </Text>
            );
          })}
        </AppCard>
      ) : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: ui.spacing.lg, backgroundColor: playfulColors.page, gap: 12 },
  title: { ...ui.text.h1, fontSize: 24 },
  subtitle: { ...ui.text.body, color: playfulColors.textMuted, marginTop: -4 },
  input: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    minHeight: ui.control.inputHeight,
    backgroundColor: '#fff',
    paddingHorizontal: 12,
    paddingVertical: 8,
  },
  hint: { ...ui.text.body },
  catGrid: { flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  catChip: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    backgroundColor: '#fff',
    paddingHorizontal: 10,
    paddingVertical: 6,
  },
  catLabel: { ...ui.text.body, fontSize: 13 },
  section: { borderRadius: ui.radius.md, padding: 12, gap: ui.spacing.xs },
  sectionTitle: { ...ui.text.h2, fontSize: 15 },
  item: { ...ui.text.body },
});
