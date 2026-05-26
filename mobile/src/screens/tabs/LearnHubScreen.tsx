import { StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';

import { AppButton } from '../../components/AppButton';
import type { LearnStackParamList } from '../../navigation/AppNavigator';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function LearnHubScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<LearnStackParamList>>();

  const links: Array<{ key: keyof LearnStackParamList; label: string; emoji: string }> = [
    { key: 'LessonList', label: 'Bài học Minna', emoji: '📚' },
    { key: 'Kanji', label: 'Hán tự', emoji: '漢' },
    { key: 'Vocabulary', label: 'Từ vựng', emoji: '📝' },
    { key: 'Courses', label: 'Lộ trình', emoji: '🎯' },
    { key: 'Progress', label: 'Tiến độ học tập', emoji: '📈' },
    { key: 'Search', label: 'Tìm kiếm nhanh', emoji: '🔎' },
    { key: 'Flashcards', label: 'Thẻ ghi nhớ (SRS)', emoji: '🧠' },
  ];

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Trung tâm học tập</Text>
      <Text style={styles.subtitle}>Chọn chế độ học theo mục tiêu hôm nay</Text>

      <View style={styles.grid}>
        {links.map((link) => (
          <AppButton
            key={link.key}
            onPress={() => navigation.navigate(link.key as never)}
            label={`${link.emoji} ${link.label}`}
            variant="outline"
            style={styles.card}
            textStyle={styles.cardText}
          />
        ))}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: playfulColors.page,
    padding: ui.spacing.lg,
    gap: ui.spacing.sm,
  },
  title: { ...ui.text.h1 },
  subtitle: { ...ui.text.body },
  grid: {
    gap: 10,
  },
  card: { borderRadius: ui.radius.md, minHeight: ui.control.button.lg, justifyContent: 'center' },
  cardText: {
    fontWeight: '700',
    color: playfulColors.textPrimary,
    fontSize: 15,
  },
});
