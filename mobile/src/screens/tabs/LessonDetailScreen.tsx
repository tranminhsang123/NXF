import { useEffect, useState, type ReactNode } from 'react';
import { ActivityIndicator, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { useRoute, type RouteProp } from '@react-navigation/native';

import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import { useApiStatus } from '../../hooks/useApiStatus';
import { fetchMinnaLessonDetail } from '../../services/minna/minnaService';
import { playfulColors } from '../../theme/duolingo';
import { ui } from '../../theme/ui';
import type { LessonsStackParamList } from '../../navigation/AppNavigator';
import type { MinnaLessonDetail } from '../../types/minna';

type LessonDetailRoute = RouteProp<LessonsStackParamList, 'LessonDetail'>;

const CATEGORY_LABELS: Record<string, string> = {
  vocab: 'Từ vựng',
  mau_cau: 'Mẫu câu',
  countries: 'Tên nước',
  proper_nouns: 'Danh từ riêng',
  cau: 'Câu',
  places: 'Địa danh',
  rail: 'Từ vựng tàu',
};

const FIELD_LABELS: Record<string, string> = {
  tu_vung: 'Từ vựng',
  han_tu: 'Hán tự',
  am_han: 'Âm Hán',
  phat_am: 'Phát âm',
  nghia: 'Nghĩa',
  ghi_chu: 'Ghi chú',
  loai_tu: 'Loại từ',
  jp: 'Tiếng Nhật',
  kunyomi: 'Kunyomi',
  onyomi: 'Onyomi',
  kanji: 'Kanji',
  han_viet: 'Hán Việt',
  nghia_vi: 'Nghĩa',
  media_url: 'Media',
};

function isRecord(value: unknown): value is Record<string, unknown> {
  return typeof value === 'object' && value !== null && !Array.isArray(value);
}

function toRecordArray(value: unknown): Record<string, unknown>[] {
  if (!Array.isArray(value)) {
    return [];
  }

  return value.filter((item): item is Record<string, unknown> => isRecord(item));
}

function formatValue(value: unknown): string {
  if (value === null || value === undefined || value === '') {
    return '-';
  }

  if (Array.isArray(value)) {
    return value.map((item) => formatValue(item)).join(', ');
  }

  if (isRecord(value)) {
    return Object.entries(value)
      .map(([key, item]) => `${FIELD_LABELS[key] ?? key}: ${formatValue(item)}`)
      .join(' | ');
  }

  return String(value);
}

function renderObjectFields(record: Record<string, unknown>, options?: { hideMeaning?: boolean }) {
  const entries = options?.hideMeaning
    ? Object.entries(record).filter(([key]) => key !== 'nghia' && key !== 'nghia_vi')
    : Object.entries(record);

  return entries.map(([key, value]) => (
    <Text key={key} style={styles.fieldText}>
      <Text style={styles.fieldLabel}>{FIELD_LABELS[key] ?? key}: </Text>
      {formatValue(value)}
    </Text>
  ));
}

function renderRecordCard(record: Record<string, unknown>, index: number, keyPrefix: string) {
  const primary = (record.tu_vung ?? record.jp ?? record.kanji ?? record.han_viet ?? `Muc ${index + 1}`) as string;
  const secondary = (record.nghia ?? record.nghia_vi) as string | undefined;

  return (
    <View key={`${keyPrefix}-${index}`} style={styles.itemCard}>
      <Text style={styles.itemPrimary}>{primary}</Text>
      {secondary ? <Text style={styles.itemSecondary}>{secondary}</Text> : null}
      <View style={styles.fieldsWrap}>{renderObjectFields(record)}</View>
    </View>
  );
}

function renderTuVungSection(content: unknown, hideMeaning: boolean): ReactNode {
  if (!isRecord(content)) {
    return <Text style={styles.sectionContent}>Khong co du lieu tu vung.</Text>;
  }

  const categories = Object.entries(content).filter(([, value]) => Array.isArray(value));

  if (categories.length === 0) {
    return <Text style={styles.sectionContent}>Khong co du lieu tu vung.</Text>;
  }

  return categories.map(([key, value]) => {
    const items = toRecordArray(value);

    return (
      <View key={`tv-${key}`} style={styles.groupBlock}>
        <Text style={styles.groupTitle}>{CATEGORY_LABELS[key] ?? key}</Text>
        <View style={styles.groupList}>
          {items.map((item, index) => {
            const word = formatValue(item.tu_vung ?? item.jp ?? item.kanji);
            const meaning = formatValue(item.nghia ?? item.nghia_vi);
            const hanTu = formatValue(item.han_tu);
            const loaiTu = formatValue(item.loai_tu);

            return (
              <View key={`tv-${key}-${index}`} style={styles.itemCard}>
                <Text style={styles.itemPrimary}>{word}</Text>
                {!hideMeaning && meaning !== '-' ? <Text style={styles.itemSecondary}>{meaning}</Text> : null}
                {(hanTu !== '-' || loaiTu !== '-') && (
                  <Text style={styles.fieldText}>
                    {hanTu !== '-' ? `Hán tự: ${hanTu}` : ''}
                    {hanTu !== '-' && loaiTu !== '-' ? ' | ' : ''}
                    {loaiTu !== '-' ? `Loại từ: ${loaiTu}` : ''}
                  </Text>
                )}
                <View style={styles.fieldsWrap}>{renderObjectFields(item, { hideMeaning })}</View>
              </View>
            );
          })}
        </View>
      </View>
    );
  });
}

function renderNguPhapSection(content: unknown, hideGrammarExplain: boolean): ReactNode {
  const grammarItems = toRecordArray(content);

  if (grammarItems.length === 0) {
    return <Text style={styles.sectionContent}>Khong co du lieu ngu phap.</Text>;
  }

  return grammarItems.map((item, index) => (
    <View key={`np-${index}`} style={styles.grammarCard}>
      <Text style={styles.grammarTitle}>{formatValue(item.title || `Diem ngu phap ${index + 1}`)}</Text>

      {item.pattern !== undefined && (
        <View style={styles.grammarBlock}>
          <Text style={styles.grammarLabel}>Cau truc</Text>
          {isRecord(item.pattern) ? (
            Object.entries(item.pattern).map(([k, v]) => (
              <Text key={`pattern-${k}`} style={styles.fieldText}>
                <Text style={styles.fieldLabel}>{k}: </Text>
                {formatValue(v)}
              </Text>
            ))
          ) : (
            <Text style={styles.sectionContent}>{formatValue(item.pattern)}</Text>
          )}
        </View>
      )}

      {!hideGrammarExplain && item.explain !== undefined && (
        <View style={styles.grammarBlock}>
          <Text style={styles.grammarLabel}>Giai thich</Text>
          {Array.isArray(item.explain) ? (
            item.explain.map((line, idx) => (
              <Text key={`explain-${idx}`} style={styles.sectionContent}>
                - {formatValue(line)}
              </Text>
            ))
          ) : isRecord(item.explain) ? (
            Object.entries(item.explain).map(([k, v]) => (
              <Text key={`explain-${k}`} style={styles.fieldText}>
                <Text style={styles.fieldLabel}>{k}: </Text>
                {formatValue(v)}
              </Text>
            ))
          ) : (
            <Text style={styles.sectionContent}>{formatValue(item.explain)}</Text>
          )}
        </View>
      )}

      {!hideGrammarExplain && Array.isArray(item.notes) && item.notes.length > 0 && (
        <View style={styles.grammarBlock}>
          <Text style={styles.grammarLabel}>Luu y</Text>
          {item.notes.map((note, idx) => (
            <Text key={`note-${idx}`} style={styles.sectionContent}>
              - {formatValue(note)}
            </Text>
          ))}
        </View>
      )}

      {Array.isArray(item.examples) && item.examples.length > 0 && (
        <View style={styles.grammarBlock}>
          <Text style={styles.grammarLabel}>Vi du</Text>
          {toRecordArray(item.examples).map((example, idx) => (
            <View key={`example-${idx}`} style={styles.exampleCard}>
              <Text style={styles.itemPrimary}>{formatValue(example.jp)}</Text>
              <Text style={styles.itemSecondary}>{formatValue(example.nghia)}</Text>
            </View>
          ))}
        </View>
      )}
    </View>
  ));
}

function renderHanTuSection(content: unknown): ReactNode {
  let items: Record<string, unknown>[] = [];

  if (Array.isArray(content)) {
    items = toRecordArray(content);
  } else if (isRecord(content)) {
    if (Array.isArray(content.items)) {
      items = toRecordArray(content.items);
    } else if (Array.isArray(content.kanji)) {
      items = toRecordArray(content.kanji);
    }
  }

  if (items.length === 0) {
    return <Text style={styles.sectionContent}>Khong co du lieu han tu.</Text>;
  }

  return (
    <View style={styles.groupList}>
      {items.map((item, index) => (
        <View key={`ht-${index}`} style={styles.kanjiCard}>
          <Text style={styles.kanjiChar}>{formatValue(item.kanji)}</Text>
          <Text style={styles.kanjiMeta}>{formatValue(item.han_viet)}</Text>
          <Text style={styles.sectionContent}>{formatValue(item.nghia_vi ?? item.nghia)}</Text>
          <View style={styles.fieldsWrap}>{renderObjectFields(item)}</View>
        </View>
      ))}
    </View>
  );
}

function renderArrayContent(values: unknown[], sectionKey: string): ReactNode {
  if (values.length === 0) {
    return <Text style={styles.sectionContent}>Khong co du lieu.</Text>;
  }

  if (values.every((item) => isRecord(item))) {
    return values.map((item, index) => renderRecordCard(item, index, sectionKey));
  }

  return values.map((item, index) => (
    <Text key={`${sectionKey}-${index}`} style={styles.sectionContent}>
      - {formatValue(item)}
    </Text>
  ));
}

function renderObjectContent(content: Record<string, unknown>, sectionKey: string): ReactNode {
  return Object.entries(content).map(([key, value]) => (
    <View key={`${sectionKey}-${key}`} style={styles.groupBlock}>
      <Text style={styles.groupTitle}>{CATEGORY_LABELS[key] ?? key}</Text>
      {Array.isArray(value) ? (
        <View style={styles.groupList}>{renderArrayContent(value, `${sectionKey}-${key}`)}</View>
      ) : isRecord(value) ? (
        <View style={styles.itemCard}>{renderObjectFields(value)}</View>
      ) : (
        <Text style={styles.sectionContent}>{formatValue(value)}</Text>
      )}
    </View>
  ));
}

function renderContent(
  content: unknown,
  sectionKey: string,
  options: { hideMeaning: boolean; hideGrammarExplain: boolean },
): ReactNode {
  if (sectionKey === 'tu-vung') {
    return renderTuVungSection(content, options.hideMeaning);
  }

  if (sectionKey === 'ngu-phap') {
    return renderNguPhapSection(content, options.hideGrammarExplain);
  }

  if (sectionKey === 'han-tu') {
    return renderHanTuSection(content);
  }

  if (content === null || content === undefined) {
    return <Text style={styles.sectionContent}>Khong co noi dung.</Text>;
  }

  if (typeof content === 'string' || typeof content === 'number' || typeof content === 'boolean') {
    return <Text style={styles.sectionContent}>{formatValue(content)}</Text>;
  }

  if (Array.isArray(content)) {
    return <View style={styles.groupList}>{renderArrayContent(content, sectionKey)}</View>;
  }

  if (isRecord(content)) {
    return renderObjectContent(content, sectionKey);
  }

  return <Text style={styles.sectionContent}>{String(content)}</Text>;
}

export function LessonDetailScreen() {
  const route = useRoute<LessonDetailRoute>();
  const { token } = useAuth();
  const [lesson, setLesson] = useState<MinnaLessonDetail | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage } = useApiStatus('Đang tải chi tiết bài học...');
  const [hideMeaning, setHideMeaning] = useState(false);
  const [hideGrammarExplain, setHideGrammarExplain] = useState(false);
  const [activeSectionKey, setActiveSectionKey] = useState<string | null>(null);

  useEffect(() => {
    const loadDetail = async () => {
      if (!token) {
        setStatusMessage('Bạn chưa đăng nhập.');
        setIsLoading(false);
        return;
      }

      try {
        setLoadingMessage('Đang tải chi tiết bài học...');
        const response = await fetchMinnaLessonDetail(route.params.lessonNumber);
        setLesson(response.data.lesson);
        setActiveSectionKey(response.data.lesson.sections[0]?.key ?? null);
        setSuccessMessage('Tải chi tiết bài học thành công.');
      } catch (error) {
        setErrorFromApi(error, 'Không tải được chi tiết bài học.');
      } finally {
        setIsLoading(false);
      }
    };

    loadDetail();
  }, [route.params.lessonNumber, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage, token]);

  if (isLoading) {
    return (
      <View style={styles.center}>
        <ActivityIndicator size="large" color={playfulColors.brand} />
      </View>
    );
  }

  if (!lesson) {
    return (
      <View style={styles.center}>
        <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
      </View>
    );
  }

  const activeSection = lesson.sections.find((section) => section.key === activeSectionKey) ?? lesson.sections[0] ?? null;

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <View style={styles.headerCard}>
        <Text style={styles.title}>Bài {lesson.number}</Text>
        <Text style={styles.lessonName}>{lesson.title}</Text>
        {lesson.description ? <Text style={styles.description}>{lesson.description}</Text> : null}
      </View>

      <View style={styles.sectionNavWrap}>
        {lesson.sections.map((section) => {
          const isActive = activeSection?.id === section.id;

          return (
            <Pressable
              key={section.id}
              accessibilityRole="button"
              onPress={() => setActiveSectionKey(section.key)}
              style={({ pressed }) => [
                styles.sectionNavButton,
                isActive && styles.sectionNavButtonActive,
                pressed && styles.sectionNavButtonPressed,
              ]}
            >
              <Text style={[styles.sectionNavButtonText, isActive && styles.sectionNavButtonTextActive]}>
                {CATEGORY_LABELS[section.key] ?? section.key}
              </Text>
            </Pressable>
          );
        })}
      </View>

      {activeSection ? (
        <View style={styles.sectionCard}>
          <View style={styles.sectionHeaderRow}>
            <View style={styles.sectionHeaderTextWrap}>
              <Text style={styles.sectionKey}>{activeSection.key}</Text>
              <Text style={styles.sectionTitle}>{activeSection.title}</Text>
            </View>
            {activeSection.key === 'tu-vung' ? (
              <Pressable
                accessibilityRole="button"
                onPress={() => setHideMeaning((prev) => !prev)}
                style={({ pressed }) => [styles.toggleButton, pressed && styles.toggleButtonPressed]}
              >
                <Text style={styles.toggleButtonText}>{hideMeaning ? 'Hiện nghĩa' : 'Ẩn nghĩa'}</Text>
              </Pressable>
            ) : null}
            {activeSection.key === 'ngu-phap' ? (
              <Pressable
                accessibilityRole="button"
                onPress={() => setHideGrammarExplain((prev) => !prev)}
                style={({ pressed }) => [styles.toggleButton, pressed && styles.toggleButtonPressed]}
              >
                <Text style={styles.toggleButtonText}>{hideGrammarExplain ? 'Hiện giải thích' : 'Ẩn giải thích'}</Text>
              </Pressable>
            ) : null}
          </View>
          {renderContent(activeSection.content, activeSection.key, { hideMeaning, hideGrammarExplain })}
        </View>
      ) : (
        <View style={styles.sectionCard}>
          <Text style={styles.sectionContent}>Bai hoc nay chua co section.</Text>
        </View>
      )}

      <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  center: {
    flex: 1,
    backgroundColor: playfulColors.page,
    alignItems: 'center',
    justifyContent: 'center',
    padding: ui.spacing.xl,
  },
  container: {
    padding: ui.spacing.lg,
    backgroundColor: playfulColors.page,
    gap: 12,
    paddingBottom: 28,
  },
  headerCard: {
    backgroundColor: '#ffffff',
    borderRadius: ui.radius.lg,
    borderWidth: 1,
    borderColor: playfulColors.border,
    padding: ui.spacing.lg,
    gap: ui.spacing.xs,
  },
  title: {
    ...ui.text.caption,
    textTransform: 'uppercase',
    fontWeight: '700',
  },
  lessonName: { ...ui.text.h1, fontSize: 22 },
  description: { ...ui.text.body },
  sectionCard: {
    backgroundColor: '#ffffff',
    borderRadius: ui.radius.lg,
    borderWidth: 1,
    borderColor: playfulColors.border,
    padding: ui.spacing.md,
    gap: ui.spacing.xs,
  },
  sectionNavWrap: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: ui.spacing.xs,
  },
  sectionNavButton: {
    minHeight: ui.control.chipHeight,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: ui.radius.pill,
    borderWidth: 1,
    borderColor: playfulColors.border,
    backgroundColor: '#ffffff',
    justifyContent: 'center',
  },
  sectionNavButtonActive: {
    backgroundColor: playfulColors.brand,
    borderColor: playfulColors.brand,
  },
  sectionNavButtonPressed: {
    opacity: 0.85,
  },
  sectionNavButtonText: {
    ...ui.text.caption,
    fontWeight: '700',
    textTransform: 'capitalize',
  },
  sectionNavButtonTextActive: {
    color: '#ffffff',
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'flex-start',
    gap: ui.spacing.xs,
  },
  sectionHeaderTextWrap: {
    flex: 1,
  },
  sectionKey: {
    ...ui.text.caption,
    textTransform: 'uppercase',
  },
  sectionTitle: { ...ui.text.h2, fontSize: 18 },
  toggleButton: {
    backgroundColor: playfulColors.softBlue,
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.pill,
    minHeight: ui.control.chipHeight,
    paddingHorizontal: 10,
    paddingVertical: 6,
    justifyContent: 'center',
  },
  toggleButtonPressed: {
    opacity: 0.8,
  },
  toggleButtonText: { ...ui.text.captionStrong, fontWeight: '800' },
  sectionContent: { ...ui.text.body },
  groupBlock: {
    gap: ui.spacing.xs,
    marginTop: 4,
  },
  groupTitle: { ...ui.text.bodyStrong, textTransform: 'uppercase' },
  groupList: {
    gap: 8,
  },
  itemCard: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    padding: ui.spacing.sm,
    gap: 4,
    backgroundColor: playfulColors.softBlue,
  },
  itemPrimary: { ...ui.text.bodyStrong, fontSize: 16, fontWeight: '800' },
  itemSecondary: { ...ui.text.body, fontWeight: '700' },
  fieldsWrap: {
    gap: 2,
  },
  fieldText: { ...ui.text.caption },
  fieldLabel: {
    fontWeight: '800',
    color: playfulColors.textPrimary,
  },
  grammarCard: {
    borderLeftWidth: 4,
    borderLeftColor: playfulColors.brand,
    backgroundColor: playfulColors.softBlue,
    borderRadius: ui.radius.sm,
    padding: 12,
    gap: ui.spacing.sm,
  },
  grammarTitle: { ...ui.text.h2, fontSize: 18 },
  grammarBlock: {
    gap: 4,
  },
  grammarLabel: { ...ui.text.caption, fontWeight: '800', textTransform: 'uppercase' },
  exampleCard: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: 8,
    padding: ui.spacing.sm,
    gap: 4,
  },
  kanjiCard: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    padding: 12,
    gap: 4,
  },
  kanjiChar: {
    fontSize: 40,
    fontWeight: '800',
    color: playfulColors.textPrimary,
  },
  kanjiMeta: {
    color: playfulColors.brandDark,
    fontWeight: '800',
  },
  status: { ...ui.statusText, color: playfulColors.textSecondary },
});
