import { useQuery } from '@tanstack/react-query';
import { ScrollView, StyleSheet, Text, View } from 'react-native';
import { useNavigation, useRoute, type RouteProp } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';

import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchCourseSectionItems } from '../../services/learning/learningService';
import type { LearnStackParamList } from '../../navigation/AppNavigator';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

type CourseSectionListRoute = RouteProp<LearnStackParamList, 'CourseSectionList'>;

export function CourseSectionListScreen() {
  const { token } = useAuth();
  const route = useRoute<CourseSectionListRoute>();
  const navigation = useNavigation<NativeStackNavigationProp<LearnStackParamList>>();

  const itemsQuery = useQuery({
    queryKey: ['course-section-items', token, route.params.level, route.params.sectionType],
    queryFn: async () => fetchCourseSectionItems(route.params.level, route.params.sectionType),
    enabled: Boolean(token),
  });

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>{route.params.sectionTitle}</Text>
      <Text style={styles.subtitle}>
        {route.params.level} • Danh sách bài học
      </Text>

      {(itemsQuery.data?.data.items ?? []).map((item, index) => {
        if (typeof item === 'string') {
          return (
            <AppCard key={`item-${index}`} style={styles.itemCard}>
              <Text style={styles.itemTitle}>{item}</Text>
            </AppCard>
          );
        }

        const itemKey = item.id ? String(item.id) : item.bai ?? '';
        const canOpen = itemKey.length > 0;
        return (
          <AppCard
            key={`item-${index}`}
            onPress={
              canOpen
                ? () =>
                    navigation.navigate('CourseSectionDetail', {
                      level: route.params.level,
                      sectionType: route.params.sectionType,
                      itemKey,
                      itemTitle: item.title ?? `Mục ${index + 1}`,
                      sectionTitle: route.params.sectionTitle,
                    })
                : undefined
            }
            style={[styles.itemCard, !canOpen && styles.itemDisabled]}
          >
            <Text style={styles.itemTitle}>{item.title ?? `Mục ${index + 1}`}</Text>
            {item.bai ? <Text style={styles.itemMeta}>Bài: {item.bai}</Text> : null}
            {canOpen ? <Text style={styles.itemHint}>Nhấn để mở chi tiết</Text> : null}
          </AppCard>
        );
      })}

      {itemsQuery.isLoading ? <Text style={styles.hint}>Đang tải danh sách...</Text> : null}
      {!itemsQuery.isLoading && (itemsQuery.data?.data.items?.length ?? 0) === 0 ? <Text style={styles.hint}>Chưa có dữ liệu.</Text> : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: ui.spacing.lg, backgroundColor: playfulColors.page, gap: ui.spacing.sm, paddingBottom: 28 },
  title: { ...ui.text.h1, fontSize: 24 },
  subtitle: { ...ui.text.captionStrong, fontWeight: '700' },
  itemCard: { borderRadius: ui.radius.sm, padding: 12, gap: 4 },
  itemTitle: { ...ui.text.bodyStrong, fontWeight: '800' },
  itemMeta: { ...ui.text.caption },
  itemHint: { ...ui.text.captionStrong, fontWeight: '700' },
  itemDisabled: { opacity: 0.6 },
  hint: { ...ui.text.body },
});
