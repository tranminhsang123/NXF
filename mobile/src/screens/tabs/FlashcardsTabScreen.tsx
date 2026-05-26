import { useEffect, useState } from 'react';
import { Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';

import { AppButton } from '../../components/AppButton';
import { AppCard } from '../../components/AppCard';
import { StatusText } from '../../components/StatusText';
import { useAuth } from '../../context/AuthContext';
import { useApiStatus } from '../../hooks/useApiStatus';
import { fetchFavoriteFlashcards, fetchFlashcards, submitFlashcardReview } from '../../services/flashcards/flashcardService';
import type { FlashcardItem, FlashcardMode } from '../../services/flashcards/flashcardService';
import { fetchMinnaLessons } from '../../services/minna/minnaService';
import { playfulColors } from '../../theme/duolingo';
import type { MinnaLesson } from '../../types/minna';
import { ui } from '../../theme/ui';

export function FlashcardsTabScreen() {
  const { token } = useAuth();
  const [lessons, setLessons] = useState<MinnaLesson[]>([]);
  const [selectedLessons, setSelectedLessons] = useState<number[]>([]);
  const [mode, setMode] = useState<FlashcardMode>('srs');
  const [reverse, setReverse] = useState(false);
  const [isLoading, setIsLoading] = useState(false);
  const [isReviewing, setIsReviewing] = useState(false);
  const [cards, setCards] = useState<FlashcardItem[]>([]);
  const [stats, setStats] = useState<{ due_count: number; new_count: number; total_in_scope: number } | null>(null);
  const [index, setIndex] = useState(0);
  const [showBack, setShowBack] = useState(false);
  const { statusMessage, statusType, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage } = useApiStatus('Chọn bài học và tải bộ thẻ để bắt đầu.');

  useEffect(() => {
    const loadLessons = async () => {
      if (!token) return;
      try {
        const response = await fetchMinnaLessons();
        setLessons(response.data.lessons);
        setSelectedLessons(response.data.lessons.slice(0, 3).map((lesson) => lesson.number));
      } catch (error) {
        setErrorFromApi(error, 'Không tải được danh sách bài học.');
      }
    };

    loadLessons();
  }, [token]);

  const loadDeck = async () => {
    if (!token) return;
    if (mode !== 'favorites' && selectedLessons.length === 0) {
      setStatusMessage('Hãy chọn ít nhất một bài học.');
      return;
    }

    setIsLoading(true);
    setLoadingMessage('Đang tải bộ thẻ...');
    try {
      const response = mode === 'favorites'
        ? await fetchFavoriteFlashcards()
        : await fetchFlashcards(selectedLessons, mode);
      setCards(response.data.cards);
      setStats('stats' in response.data ? response.data.stats ?? null : null);
      setIndex(0);
      setShowBack(false);
      setSuccessMessage(`Đã tải ${response.data.cards.length} thẻ (${mode === 'favorites' ? 'YEU THICH' : mode.toUpperCase()}).`);
    } catch (error) {
      setErrorFromApi(error, 'Không thể tải flashcards.');
    } finally {
      setIsLoading(false);
    }
  };

  const currentCard = cards[index];
  const progressPercent = cards.length > 0 ? ((index + 1) / cards.length) * 100 : 0;
  const frontText = reverse ? currentCard?.back : currentCard?.front;
  const backText = reverse ? currentCard?.front : currentCard?.back;
  const canGoPrev = index > 0;
  const canGoNext = index < cards.length - 1;

  const review = async (quality: number) => {
    if (!token || !currentCard) return;
    if (currentCard.section_id === undefined || currentCard.card_index === undefined) {
      setStatusMessage('Thẻ này không thể chấm SRS.');
      return;
    }

    try {
      setIsReviewing(true);
      await submitFlashcardReview({
        minna_section_id: currentCard.section_id,
        card_index: currentCard.card_index,
        quality,
      });

      if (mode === 'srs') {
        if (quality < 3) {
          setCards((prev) => {
            const clone = [...prev];
            const card = clone.splice(index, 1)[0];
            clone.push(card);
            return clone;
          });
        } else {
          const next = index + 1;
          if (next < cards.length) {
            setIndex(next);
          } else {
            setSuccessMessage('Đã hoàn thành bộ thẻ hiện tại.');
          }
        }
      }
      setShowBack(false);
    } catch (error) {
      setErrorFromApi(error, 'Không lưu được đánh giá, thử lại.');
    } finally {
      setIsReviewing(false);
    }
  };

  const toggleLesson = (lessonNumber: number) => {
    setSelectedLessons((prev) => (prev.includes(lessonNumber) ? prev.filter((n) => n !== lessonNumber) : [...prev, lessonNumber].sort((a, b) => a - b)));
  };

  return (
    <ScrollView contentContainerStyle={styles.container}>
      <Text style={styles.title}>Thẻ ghi nhớ (SRS) 🧠</Text>

      <View style={styles.modeRow}>
        <Pressable onPress={() => setMode('normal')} style={[styles.modeButton, mode === 'normal' && styles.modeButtonActive]}>
          <Text style={[styles.modeText, mode === 'normal' && styles.modeTextActive]}>Thường</Text>
        </Pressable>
        <Pressable onPress={() => setMode('srs')} style={[styles.modeButton, mode === 'srs' && styles.modeButtonActive]}>
          <Text style={[styles.modeText, mode === 'srs' && styles.modeTextActive]}>SRS</Text>
        </Pressable>
        <Pressable onPress={() => setMode('favorites')} style={[styles.modeButton, mode === 'favorites' && styles.modeButtonActive]}>
          <Text style={[styles.modeText, mode === 'favorites' && styles.modeTextActive]}>Yêu thích</Text>
        </Pressable>
        <Pressable onPress={() => setReverse((prev) => !prev)} style={styles.reverseButton}>
          <Text style={styles.reverseText}>{reverse ? '↩ Đang đảo thẻ' : '↩ Đảo thẻ'}</Text>
        </Pressable>
      </View>

      {mode !== 'favorites' ? (
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.lessonWrap}>
          {lessons.map((lesson) => {
            const selected = selectedLessons.includes(lesson.number);
            return (
              <Pressable key={lesson.id} onPress={() => toggleLesson(lesson.number)} style={[styles.lessonChip, selected && styles.lessonChipActive]}>
                <Text style={[styles.lessonChipText, selected && styles.lessonChipTextActive]}>Bài {lesson.number}</Text>
              </Pressable>
            );
          })}
        </ScrollView>
      ) : (
        <Text style={styles.hintText}>Bộ thẻ riêng từ các từ/câu bạn đã lưu.</Text>
      )}

      <AppButton label={mode === 'favorites' ? 'Tải từ yêu thích' : 'Tải bộ thẻ'} onPress={loadDeck} loading={isLoading} style={styles.button} />

      {mode === 'srs' && stats ? (
        <Text style={styles.hintText}>Đến hạn: {stats.due_count} • Mới: {stats.new_count} • Tổng: {stats.total_in_scope}</Text>
      ) : null}

      {cards.length > 0 ? (
        <>
          <Text style={styles.hintText}>
            Thẻ {index + 1}/{cards.length}
          </Text>
          <View style={styles.progressTrack}>
            <View style={[styles.progressFill, { width: `${progressPercent}%` }]} />
          </View>
        </>
      ) : null}

      {currentCard ? (
        <AppCard onPress={() => setShowBack((prev) => !prev)} style={styles.card}>
          <Text style={styles.cardLabel}>{showBack ? 'Mặt sau' : 'Mặt trước'} • nhấn để lật</Text>
          <Text style={styles.cardText}>{showBack ? backText : frontText}</Text>
        </AppCard>
      ) : (
        <StatusText message={statusMessage} statusType={statusType} style={styles.status} />
      )}

      {showBack && currentCard && mode === 'srs' ? (
        <View style={styles.reviewRow}>
          <AppButton disabled={isReviewing} onPress={() => review(0)} label="Quen" variant="danger" style={styles.reviewAgain} />
          <AppButton disabled={isReviewing} onPress={() => review(2)} label="Kho" style={styles.reviewBad} />
          <AppButton disabled={isReviewing} onPress={() => review(4)} label="Tot" style={styles.reviewGood} />
          <AppButton disabled={isReviewing} onPress={() => review(5)} label="De" style={styles.reviewEasy} />
        </View>
      ) : null}

      {mode !== 'srs' && cards.length > 0 ? (
        <View style={styles.navRow}>
          <AppButton
            disabled={!canGoPrev}
            onPress={() => setIndex((prev) => Math.max(0, prev - 1))}
            label="← Trước"
            variant="outline"
            style={[styles.navButton, !canGoPrev && styles.navButtonDisabled]}
          />
          <AppButton
            onPress={() => {
              if (canGoNext) {
                setIndex((prev) => prev + 1);
                setShowBack(false);
              } else {
                setSuccessMessage('Đã hoàn thành bộ thẻ hiện tại.');
              }
            }}
            label={canGoNext ? 'Sau →' : 'Xong'}
            style={styles.navButtonPrimary}
          />
        </View>
      ) : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: { backgroundColor: playfulColors.page, padding: ui.spacing.lg, gap: 12, paddingBottom: 30 },
  title: { ...ui.text.h1, fontSize: 24 },
  modeRow: { flexDirection: 'row', gap: ui.spacing.xs, flexWrap: 'wrap' },
  modeButton: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.pill,
    minHeight: ui.control.chipHeight,
    paddingHorizontal: 12,
    paddingVertical: 6,
    justifyContent: 'center',
  },
  modeButtonActive: {
    backgroundColor: playfulColors.brand,
    borderColor: playfulColors.brand,
  },
  modeText: { color: playfulColors.textPrimary, fontWeight: '700' },
  modeTextActive: { color: '#fff' },
  reverseButton: {
    backgroundColor: '#fff',
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.pill,
    minHeight: ui.control.chipHeight,
    paddingHorizontal: 12,
    paddingVertical: 6,
    justifyContent: 'center',
  },
  reverseText: { color: playfulColors.brandDark, fontWeight: '700' },
  lessonWrap: { gap: ui.spacing.xs, paddingRight: 4 },
  lessonChip: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.pill,
    backgroundColor: '#fff',
    minHeight: ui.control.chipHeight,
    paddingHorizontal: 12,
    paddingVertical: 6,
    justifyContent: 'center',
  },
  lessonChipActive: {
    borderColor: playfulColors.brand,
    backgroundColor: playfulColors.softBlue,
  },
  lessonChipText: { color: playfulColors.textSecondary, fontWeight: '700', fontSize: 12 },
  lessonChipTextActive: { color: playfulColors.brandDark },
  button: { minHeight: ui.control.button.md },
  hintText: { ...ui.text.caption },
  progressTrack: {
    height: 8,
    borderRadius: 999,
    backgroundColor: '#dbe8ff',
    overflow: 'hidden',
  },
  progressFill: {
    height: '100%',
    backgroundColor: playfulColors.brand,
    borderRadius: 999,
  },
  card: { minHeight: 180, borderRadius: ui.radius.lg, alignItems: 'center', justifyContent: 'center', padding: ui.spacing.md, gap: 8 },
  cardLabel: { color: playfulColors.textSecondary, textTransform: 'uppercase', fontSize: 12, fontWeight: '800' },
  cardText: { color: playfulColors.textPrimary, fontSize: 24, fontWeight: '800', textAlign: 'center' },
  status: { color: playfulColors.textSecondary, textAlign: 'center' },
  reviewRow: { flexDirection: 'row', gap: ui.spacing.xs, flexWrap: 'wrap' },
  reviewAgain: { flexGrow: 1, minWidth: '22%', minHeight: ui.control.button.sm, backgroundColor: '#ef4444' },
  reviewBad: { flex: 1, minHeight: ui.control.button.sm, backgroundColor: playfulColors.accentOrange },
  reviewGood: { flex: 1, minHeight: ui.control.button.sm, backgroundColor: playfulColors.accentGreen },
  reviewEasy: { flex: 1, minHeight: ui.control.button.sm, backgroundColor: playfulColors.accentPurple },
  navRow: { flexDirection: 'row', gap: ui.spacing.sm },
  navButton: { flex: 1, minHeight: ui.control.button.md },
  navButtonDisabled: { opacity: 0.5 },
  navButtonPrimary: { flex: 1, minHeight: ui.control.button.md },
});
