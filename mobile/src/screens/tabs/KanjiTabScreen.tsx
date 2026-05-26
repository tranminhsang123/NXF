import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { FlatList, Pressable, StyleSheet, Text, TextInput, View } from 'react-native';

import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchKanjiByLevel, type KanjiItem } from '../../services/learning/learningService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

const levels = ['N5', 'N4', 'N3', 'N2', 'N1'] as const;

export function KanjiTabScreen() {
  const { token } = useAuth();
  const [level, setLevel] = useState<(typeof levels)[number]>('N5');
  const [search, setSearch] = useState('');

  const query = useQuery({
    queryKey: ['kanji', level, search, token],
    queryFn: async () => fetchKanjiByLevel(level, 1, search),
    enabled: Boolean(token),
  });

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Hán tự theo cấp độ 漢</Text>
      <View style={styles.levelRow}>
        {levels.map((item) => (
          <Pressable key={item} onPress={() => setLevel(item)} style={[styles.levelButton, level === item && styles.levelButtonActive]}>
            <Text style={[styles.levelButtonText, level === item && styles.levelButtonTextActive]}>{item}</Text>
          </Pressable>
        ))}
      </View>
      <TextInput placeholder="Tìm hán tự..." value={search} onChangeText={setSearch} style={styles.input} />

      <FlatList
        data={query.data?.data.data ?? []}
        keyExtractor={(item: KanjiItem) => String(item.id)}
        renderItem={({ item }) => (
          <AppCard style={styles.card}>
            <Text style={styles.character}>{item.character}</Text>
            <Text style={styles.meaning}>{item.meaning}</Text>
            <Text style={styles.reading}>On: {item.on_reading || '-'} | Kun: {item.kun_reading || '-'}</Text>
          </AppCard>
        )}
        ListEmptyComponent={<Text style={styles.status}>{query.isLoading ? 'Đang tải...' : 'Không có dữ liệu.'}</Text>}
      />
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: playfulColors.page, padding: 16, gap: 10 },
  title: { ...ui.text.h1, fontSize: 24 },
  levelRow: { flexDirection: 'row', gap: 6, flexWrap: 'wrap' },
  levelButton: {
    minHeight: ui.control.chipHeight,
    paddingHorizontal: 12,
    paddingVertical: 7,
    borderRadius: 999,
    borderWidth: 1,
    borderColor: playfulColors.border,
    backgroundColor: '#fff',
    justifyContent: 'center',
  },
  levelButtonActive: { backgroundColor: playfulColors.brand, borderColor: playfulColors.brand },
  levelButtonText: { ...ui.text.caption, fontWeight: '700' },
  levelButtonTextActive: { color: '#fff' },
  input: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    minHeight: ui.control.inputHeight,
    backgroundColor: '#fff',
    paddingHorizontal: 12,
    paddingVertical: 8,
  },
  card: { borderRadius: 14, padding: 12, marginBottom: 8 },
  character: { fontSize: 32, fontWeight: '800', color: playfulColors.textPrimary },
  meaning: { ...ui.text.bodyStrong },
  reading: { ...ui.text.body },
  status: { ...ui.statusText, color: playfulColors.textSecondary, marginTop: 24 },
});
