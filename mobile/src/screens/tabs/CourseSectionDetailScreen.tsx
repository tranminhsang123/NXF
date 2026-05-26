import { type ReactNode } from 'react';
import { useQuery } from '@tanstack/react-query';
import { ScrollView, StyleSheet, Text, View } from 'react-native';
import { useRoute, type RouteProp } from '@react-navigation/native';

import { AppCard } from '../../components/AppCard';
import { useAuth } from '../../context/AuthContext';
import { fetchCourseSectionItemDetail } from '../../services/learning/learningService';
import type { LearnStackParamList } from '../../navigation/AppNavigator';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';

type CourseSectionDetailRoute = RouteProp<LearnStackParamList, 'CourseSectionDetail'>;

const FIELD_LABELS: Record<string, string> = {
  tu: 'Từ',
  nghia: 'Nghĩa',
  title: 'Tiêu đề',
  bai: 'Bài',
  section_key: 'Nhóm',
  section_type: 'Loại',
};

function toLabel(key: string): string {
  return FIELD_LABELS[key] ?? key.replace(/_/g, ' ');
}

function formatScalar(value: unknown): string {
  if (value === null || value === undefined || value === '') {
    return '-';
  }

  if (typeof value === 'string' || typeof value === 'number' || typeof value === 'boolean') {
    return String(value);
  }

  return '[Dữ liệu phức tạp]';
}

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null && !Array.isArray(value);
}

function renderRecord(record: Record<string, unknown>, keyPrefix: string): ReactNode {
  return Object.entries(record).map(([key, value]) => {
    if (Array.isArray(value)) {
      return (
        <View key={`${keyPrefix}-${key}`} style={styles.nestedBlock}>
          <Text style={styles.blockTitle}>{toLabel(key)}</Text>
          {value.map((item, idx) => (
            <AppCard key={`${keyPrefix}-${key}-${idx}`} style={styles.listItemCard}>
              {isRecord(item) ? (
                renderRecord(item, `${keyPrefix}-${key}-${idx}`)
              ) : (
                <Text style={styles.blockText}>- {formatScalar(item)}</Text>
              )}
            </AppCard>
          ))}
        </View>
      );
    }

    if (isRecord(value)) {
      return (
        <View key={`${keyPrefix}-${key}`} style={styles.nestedBlock}>
          <Text style={styles.blockTitle}>{toLabel(key)}</Text>
          <AppCard style={styles.listItemCard}>{renderRecord(value, `${keyPrefix}-${key}`)}</AppCard>
        </View>
      );
    }

    return (
      <Text key={`${keyPrefix}-${key}`} style={styles.blockText}>
        <Text style={styles.fieldLabel}>{toLabel(key)}: </Text>
        {formatScalar(value)}
      </Text>
    );
  });
}

function renderStructuredContent(value: unknown, keyPrefix: string): ReactNode {
  if (Array.isArray(value)) {
    if (value.length === 0) {
      return <Text style={styles.hint}>Chưa có nội dung.</Text>;
    }

    return value.map((item, idx) => (
      <AppCard key={`${keyPrefix}-${idx}`} style={styles.listItemCard}>
        {isRecord(item) ? renderRecord(item, `${keyPrefix}-${idx}`) : <Text style={styles.blockText}>- {formatScalar(item)}</Text>}
      </AppCard>
    ));
  }

  if (isRecord(value)) {
    return renderRecord(value, keyPrefix);
  }

  return <Text style={styles.blockText}>{formatScalar(value)}</Text>;
}

export function CourseSectionDetailScreen() {
  const { token } = useAuth();
  const route = useRoute<CourseSectionDetailRoute>();

  const detailQuery = useQuery({
    queryKey: ['course-section-detail', token, route.params.level, route.params.sectionType, route.params.itemKey],
    queryFn: async () => fetchCourseSectionItemDetail(route.params.level, route.params.sectionType, route.params.itemKey),
    enabled: Boolean(token),
  });

  const data = detailQuery.data?.data;
  const title = data?.item?.title ?? data?.title ?? route.params.itemTitle;
  const bai = data?.item?.bai ?? data?.bai ?? 'Chi tiết';

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>{title}</Text>
      <Text style={styles.subtitle}>
        {route.params.sectionTitle} • {bai}
      </Text>

      {detailQuery.isLoading ? <Text style={styles.hint}>Đang tải chi tiết...</Text> : null}

      {data?.groupedData ? (
        <AppCard style={styles.card}>
          <Text style={styles.cardTitle}>Nội dung nhóm</Text>
          {Object.entries(data.groupedData).map(([key, value]) => (
            <View key={key} style={styles.block}>
              <Text style={styles.blockTitle}>{key}</Text>
              {renderStructuredContent(value, key)}
            </View>
          ))}
        </AppCard>
      ) : null}

      {data?.item?.content !== undefined ? (
        <AppCard style={styles.card}>
          <Text style={styles.cardTitle}>Nội dung</Text>
          <View style={styles.block}>{renderStructuredContent(data.item.content, 'content')}</View>
        </AppCard>
      ) : null}

      {!detailQuery.isLoading && !data ? <Text style={styles.hint}>Không tải được chi tiết mục học này.</Text> : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { padding: ui.spacing.lg, backgroundColor: playfulColors.page, gap: ui.spacing.sm, paddingBottom: 32 },
  title: { ...ui.text.h1, fontSize: 24 },
  subtitle: { ...ui.text.captionStrong, fontWeight: '700' },
  card: { borderRadius: ui.radius.sm, padding: 12, gap: 8 },
  cardTitle: { ...ui.text.h2, fontSize: 16 },
  block: { borderWidth: 1, borderColor: playfulColors.border, borderRadius: ui.radius.sm, padding: ui.spacing.sm, gap: 4, backgroundColor: '#fff' },
  nestedBlock: { gap: 4, marginTop: 4 },
  listItemCard: { borderRadius: 8, padding: ui.spacing.xs, gap: 3, backgroundColor: '#f9fbff' },
  blockTitle: { ...ui.text.captionStrong, fontWeight: '700' },
  blockText: { ...ui.text.body },
  fieldLabel: { ...ui.text.bodyStrong, fontWeight: '700' },
  hint: { ...ui.text.body },
});
