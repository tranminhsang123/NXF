import { useState } from 'react';
import { useQuery } from '@tanstack/react-query';
import { ScrollView, StyleSheet, Text, View } from 'react-native';
import { useNavigation, useRoute, type RouteProp } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';

import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchCourseSections } from '../../services/learning/learningService';
import type { LearnStackParamList } from '../../navigation/AppNavigator';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

type CourseDetailRoute = RouteProp<LearnStackParamList, 'CourseDetail'>;

export function CourseDetailScreen() {
  const { token } = useAuth();
  const route = useRoute<CourseDetailRoute>();
  const navigation = useNavigation<NativeStackNavigationProp<LearnStackParamList>>();
  const [notice, setNotice] = useState('');

  const sectionsQuery = useQuery({
    queryKey: ['course-sections', token, route.params.level],
    queryFn: async () => fetchCourseSections(route.params.level),
    enabled: Boolean(token),
  });

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Khóa {route.params.level}</Text>
      <Text style={styles.subtitle}>{route.params.title}</Text>

      {sectionsQuery.data?.data.course?.description ? <Text style={styles.description}>{sectionsQuery.data.data.course.description}</Text> : null}

      <AppCard style={styles.card}>
        <Text style={styles.sectionTitle}>Danh sách phần học</Text>
        {(sectionsQuery.data?.data.sections ?? []).map((section, index) => {
          const disabled = Boolean(section.disabled || !section.type);
          return (
            <AppCard
              key={`${section.title}-${index}`}
              onPress={() => {
                if (disabled) {
                  setNotice('Mục này trên web đang ở trạng thái "Sắp có".');
                  return;
                }
                setNotice('');
                navigation.navigate('CourseSectionList', {
                  level: route.params.level,
                  sectionType: section.type ?? '',
                  sectionTitle: section.title,
                });
              }}
              style={[styles.sectionButton, disabled && styles.sectionButtonDisabled]}
            >
              <Text style={styles.sectionButtonTitle}>
                {section.icon ? `${section.icon} ` : ''}
                {section.title}
              </Text>
              {section.description ? <Text style={styles.sectionButtonDesc}>{section.description}</Text> : null}
              {disabled ? <Text style={styles.disabledText}>Sắp có (giống web)</Text> : null}
              {!disabled ? <Text style={styles.itemHint}>Nhấn để mở trang mới</Text> : null}
            </AppCard>
          );
        })}
        {sectionsQuery.isLoading ? <Text style={styles.hint}>Đang tải danh sách phần học...</Text> : null}
        {notice ? <Text style={styles.notice}>{notice}</Text> : null}
      </AppCard>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: ui.spacing.lg, backgroundColor: playfulColors.page, gap: ui.spacing.sm, paddingBottom: 30 },
  title: { ...ui.text.h1 },
  subtitle: { ...ui.text.captionStrong, fontWeight: '700' },
  description: { ...ui.screenSubtitle },
  card: { borderRadius: ui.radius.md, padding: 12, gap: 8 },
  sectionTitle: { ...ui.text.h2, fontSize: 15 },
  sectionButton: { borderRadius: ui.radius.sm, padding: ui.spacing.sm, gap: 4 },
  sectionButtonDisabled: { opacity: 0.65 },
  sectionButtonTitle: { ...ui.text.bodyStrong, fontWeight: '800' },
  sectionButtonDesc: { ...ui.text.caption },
  disabledText: { color: playfulColors.accentOrange, fontSize: 12, fontWeight: '700' },
  hint: { ...ui.text.body },
  notice: { ...ui.text.captionStrong, fontWeight: '700' },
  itemHint: { ...ui.text.captionStrong, fontWeight: '700' },
});
