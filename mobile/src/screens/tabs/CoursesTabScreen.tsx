import { useQuery } from '@tanstack/react-query';
import { FlatList, StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';

import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchCourseLevels } from '../../services/learning/learningService';
import type { LearnStackParamList } from '../../navigation/AppNavigator';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

export function CoursesTabScreen() {
  const { token } = useAuth();
  const navigation = useNavigation<NativeStackNavigationProp<LearnStackParamList>>();

  const query = useQuery({
    queryKey: ['courses', token],
    queryFn: async () => fetchCourseLevels(),
    enabled: Boolean(token),
  });

  return (
    <View style={styles.container}>
      <Text style={styles.title}>Lộ trình học 🎯</Text>
      <FlatList
        data={query.data?.data.courses ?? []}
        keyExtractor={(item) => item.level}
        renderItem={({ item }) => (
          <AppCard onPress={() => navigation.navigate('CourseDetail', { level: item.level, title: item.title })} style={styles.card}>
            <Text style={styles.level}>{item.level}</Text>
            <Text style={styles.courseTitle}>{item.title}</Text>
            <Text style={styles.desc}>{item.description}</Text>
            <Text style={styles.tapHint}>Nhấn để xem chi tiết</Text>
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
  card: { borderRadius: ui.radius.md, padding: 12, marginBottom: 8, gap: 4 },
  level: { ...ui.text.captionStrong, fontWeight: '800' },
  courseTitle: { ...ui.text.bodyStrong, fontWeight: '800' },
  desc: { ...ui.text.body },
  tapHint: { ...ui.text.captionStrong, marginTop: 2 },
  status: { ...ui.statusText, color: playfulColors.textSecondary, marginTop: 24 },
});
