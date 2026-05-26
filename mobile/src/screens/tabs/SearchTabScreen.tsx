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

export function SearchTabScreen() {
  const { token } = useAuth();
  const [q, setQ] = useState('');
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage } = useApiStatus('Nhập ít nhất 2 ký tự để tìm kiếm.');

  const query = useQuery({
    queryKey: ['global-search', q, token],
    queryFn: async () => fetchSearch(q),
    enabled: Boolean(token) && q.trim().length >= 2,
  });

  useEffect(() => {
    if (q.trim().length < 2) {
      setStatusMessage('Nhập ít nhất 2 ký tự để tìm kiếm.');
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

    const lessonCount = query.data?.data.lessons.length ?? 0;
    const kanjiCount = query.data?.data.kanji.length ?? 0;
    const vocabCount = query.data?.data.vocabulary.length ?? 0;
    const total = lessonCount + kanjiCount + vocabCount;
    setSuccessMessage(total > 0 ? `Tìm thấy ${total} kết quả.` : 'Không tìm thấy kết quả.');
  }, [q, query.data, query.error, query.isLoading, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage]);

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Tìm kiếm nhanh 🔎</Text>
      <TextInput value={q} onChangeText={setQ} placeholder="Tìm bài học, hán tự, từ vựng..." style={styles.input} />

      <StatusText message={statusMessage} statusType={statusType} style={styles.hint} />

      <AppCard style={styles.section}>
        <Text style={styles.sectionTitle}>Bài học</Text>
        {(query.data?.data.lessons ?? []).map((lesson) => (
          <Text key={`l-${lesson.id}`} style={styles.item}>
            Bài {lesson.number}: {lesson.title}
          </Text>
        ))}
      </AppCard>

      <AppCard style={styles.section}>
        <Text style={styles.sectionTitle}>Hán tự</Text>
        {(query.data?.data.kanji ?? []).map((item) => (
          <Text key={`k-${item.id}`} style={styles.item}>
            {item.character} - {item.meaning} ({item.level})
          </Text>
        ))}
      </AppCard>

      <AppCard style={styles.section}>
        <Text style={styles.sectionTitle}>Từ vựng</Text>
        {(query.data?.data.vocabulary ?? []).map((item, index) => (
          <Text key={`v-${index}`} style={styles.item}>
            [Bài {item.lesson_number}] {item.word} - {item.meaning}
          </Text>
        ))}
      </AppCard>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: ui.spacing.lg, backgroundColor: playfulColors.page, gap: 12 },
  title: { ...ui.text.h1, fontSize: 24 },
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
  section: { borderRadius: ui.radius.md, padding: 12, gap: ui.spacing.xs },
  sectionTitle: { ...ui.text.h2, fontSize: 15 },
  item: { ...ui.text.body },
});
