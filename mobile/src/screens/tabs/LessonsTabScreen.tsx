import { useCallback, useEffect, useState } from 'react';
import { ActivityIndicator, FlatList, StyleSheet, Text, View } from 'react-native';
import { useNavigation } from '@react-navigation/native';
import type { NativeStackNavigationProp } from '@react-navigation/native-stack';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import { useApiStatus } from '../../hooks/useApiStatus';
import { fetchMinnaLessons } from '../../services/minna/minnaService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';
import type { MinnaLesson } from '../../types/minna';
import type { LessonsStackParamList } from '../../navigation/AppNavigator';

export function LessonsTabScreen() {
  const navigation = useNavigation<NativeStackNavigationProp<LessonsStackParamList>>();
  const { token } = useAuth();
  const [lessons, setLessons] = useState<MinnaLesson[]>([]);
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage } = useApiStatus('Đang tải danh sách bài học...');
  const [isLoading, setIsLoading] = useState(true);

  const loadLessons = useCallback(async () => {
    if (!token) {
      setStatusMessage('Bạn chưa đăng nhập.');
      setIsLoading(false);
      return;
    }

    try {
      setIsLoading(true);
      setLoadingMessage('Đang tải danh sách bài học...');
      const response = await fetchMinnaLessons();
      setLessons(response.data.lessons);
      setSuccessMessage(`Đã tải ${response.data.lessons.length} bài học.`);
    } catch (error) {
      setErrorFromApi(error, 'Không tải được danh sách bài học.');
    } finally {
      setIsLoading(false);
    }
  }, [setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage, token]);

  useEffect(() => {
    loadLessons();
  }, [loadLessons]);

  return (
    <View style={styles.container}>
      <View style={styles.header}>
        <Text style={styles.title}>Bài học Minna</Text>
        <AppButton label="Tải lại" onPress={loadLessons} style={styles.reloadButton} />
      </View>

      {isLoading ? (
        <View style={styles.loadingWrap}>
          <ActivityIndicator size="large" color={playfulColors.brand} />
        </View>
      ) : (
        <FlatList
          contentContainerStyle={styles.listContent}
          data={lessons}
          keyExtractor={(item) => String(item.id)}
          renderItem={({ item }) => (
            <AppCard
              onPress={() => navigation.navigate('LessonDetail', { lessonNumber: item.number })}
              style={styles.lessonCard}
            >
              <Text style={styles.lessonTitle}>Bài {item.number}: {item.title}</Text>
              <Text style={styles.lessonMeta}>Số section: {item.sections_count}</Text>
              {item.description ? <Text style={styles.lessonDescription}>{item.description}</Text> : null}
              <Text style={styles.tapHint}>Nhấn để xem chi tiết</Text>
            </AppCard>
          )}
        />
      )}

      <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: playfulColors.page,
    padding: ui.spacing.lg,
    gap: 12,
  },
  header: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  title: { ...ui.text.h1, fontSize: 24 },
  reloadButton: { minHeight: ui.control.button.sm, paddingHorizontal: ui.spacing.md },
  loadingWrap: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  listContent: { gap: ui.spacing.sm, paddingBottom: ui.spacing.lg },
  lessonCard: { borderRadius: ui.radius.md, padding: ui.spacing.md, gap: 4 },
  lessonTitle: { ...ui.text.bodyStrong, fontSize: 16, fontWeight: '800' },
  lessonMeta: { ...ui.text.body },
  lessonDescription: { ...ui.text.body },
  tapHint: {
    marginTop: 4,
    color: playfulColors.brandDark,
    fontWeight: '800',
  },
  status: { ...ui.statusText, color: playfulColors.textSecondary },
});
