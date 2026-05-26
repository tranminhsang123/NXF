import axios from 'axios';
import * as Google from 'expo-auth-session/providers/google';
import * as WebBrowser from 'expo-web-browser';
import { useEffect, useState } from 'react';
import { Pressable, ScrollView, StyleSheet, Text, TextInput, View } from 'react-native';

import { AppButton } from '../components/AppButton';
import { AppCard } from '../components/AppCard';
import { StatusText } from '../components/StatusText';
import { env } from '../config/env';
import { useAuth } from '../context/AuthContext';
import { useApiStatus } from '../hooks/useApiStatus';
import { checkBackendHealth } from '../services/api/healthService';
import { playfulColors } from '../theme/duolingo';
import { ui } from '../theme/ui';

WebBrowser.maybeCompleteAuthSession();

const LEVEL_OPTIONS = [
  { value: 'new', label: 'Mới bắt đầu' },
  { value: 'kana', label: 'Biết kana' },
  { value: 'n5_started', label: 'Đã học N5' },
  { value: 'n5_review', label: 'Đang ôn N5' },
  { value: 'n4_plus', label: 'N4+' },
];

const JLPT_OPTIONS = ['N5', 'N4', 'N3', 'N2', 'N1'];
const DAILY_MINUTE_OPTIONS = [10, 20, 30, 45, 60];

export function AuthScreen() {
  const { isSubmitting, signIn, signInWithGoogle, signUp, statusMessage } = useAuth();
  const { statusMessage: connectionStatus, statusType: connectionStatusType, setErrorFromApi, setLoadingMessage, setStatusMessage, setSuccessMessage } = useApiStatus('');
  const [mode, setMode] = useState<'login' | 'register'>('login');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [onboardingLevel, setOnboardingLevel] = useState('new');
  const [jlptGoal, setJlptGoal] = useState('N5');
  const [dailyStudyMinutes, setDailyStudyMinutes] = useState(20);
  const [emailRemindersEnabled, setEmailRemindersEnabled] = useState(true);
  const [isChecking, setIsChecking] = useState(false);
  const [googleError, setGoogleError] = useState('');
  const [googleRequest, googleResponse, promptGoogleLogin] = Google.useIdTokenAuthRequest({
    androidClientId: env.googleAndroidClientId || undefined,
    iosClientId: env.googleIosClientId || undefined,
    webClientId: env.googleWebClientId || undefined,
  });

  useEffect(() => {
    const handleGoogleResponse = async () => {
      if (googleResponse?.type !== 'success') {
        return;
      }

      const idToken = googleResponse.params?.id_token;
      if (!idToken) {
        setGoogleError('Khong nhan duoc Google ID token. Vui long thu lai.');
        return;
      }

      try {
        setGoogleError('');
        await signInWithGoogle(idToken);
      } catch {
        // Error state is handled by AuthContext statusMessage.
      }
    };

    void handleGoogleResponse();
  }, [googleResponse, signInWithGoogle]);

  const handleCheckConnection = async () => {
    try {
      setIsChecking(true);
      setLoadingMessage('Đang kiểm tra kết nối...');
      await checkBackendHealth();
      setSuccessMessage('Kết nối backend thành công.');
    } catch (error) {
      if (axios.isAxiosError(error) && error.response) {
        setSuccessMessage('Server phản hồi, kết nối đã thông.');
      } else {
        setErrorFromApi(error, 'Không kết nối được backend. Vui lòng kiểm tra mạng và địa chỉ API.');
      }
    } finally {
      setIsChecking(false);
    }
  };

  const handleSubmit = async () => {
    setStatusMessage('');
    if (mode === 'register') {
      await signUp({
        name,
        email,
        password,
        passwordConfirmation,
        onboardingLevel,
        jlptGoal,
        dailyStudyMinutes,
        emailRemindersEnabled,
      });
      return;
    }

    await signIn({ email, password });
  };

  return (
    <ScrollView contentContainerStyle={styles.container} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
      <AppCard style={styles.hero}>
        <Text style={styles.heroBadge}>Học tiếng Nhật</Text>
        <Text style={styles.title}>Japanese Study</Text>
        <Text style={styles.subtitle}>Học vui mỗi ngày, tiến bộ từng bài.</Text>
      </AppCard>

      <AppCard style={styles.card}>
        <Text style={styles.label}>Địa chỉ API chính</Text>
        <Text style={styles.value}>{env.apiUrl}</Text>
      </AppCard>
      {env.detectedLanApiUrl ? (
        <AppCard style={styles.card}>
          <Text style={styles.label}>Địa chỉ API LAN</Text>
          <Text style={styles.value}>{env.detectedLanApiUrl}</Text>
        </AppCard>
      ) : null}

      <AppButton label="Test kết nối API" loading={isChecking} disabled={isSubmitting} onPress={handleCheckConnection} />

      <AppCard style={styles.card}>
        <View style={styles.modeRow}>
          <Pressable accessibilityRole="button" onPress={() => setMode('login')} style={[styles.modeButton, mode === 'login' && styles.modeButtonActive]}>
            <Text style={[styles.modeText, mode === 'login' && styles.modeTextActive]}>Đăng nhập</Text>
          </Pressable>
          <Pressable
            accessibilityRole="button"
            onPress={() => setMode('register')}
            style={[styles.modeButton, mode === 'register' && styles.modeButtonActive]}
          >
            <Text style={[styles.modeText, mode === 'register' && styles.modeTextActive]}>Đăng ký</Text>
          </Pressable>
        </View>

        {mode === 'register' ? (
          <TextInput autoCapitalize="words" onChangeText={setName} placeholder="Tên hiển thị" style={styles.input} value={name} />
        ) : null}
        <TextInput
          autoCapitalize="none"
          keyboardType="email-address"
          onChangeText={setEmail}
          placeholder="Email"
          style={styles.input}
          value={email}
        />
        <TextInput onChangeText={setPassword} placeholder="Mật khẩu" secureTextEntry style={styles.input} value={password} />
        {mode === 'register' ? (
          <TextInput
            onChangeText={setPasswordConfirmation}
            placeholder="Nhập lại mật khẩu"
            secureTextEntry
            style={styles.input}
            value={passwordConfirmation}
          />
        ) : null}

        {mode === 'register' ? (
          <View style={styles.onboardingBox}>
            <Text style={styles.onboardingTitle}>Lộ trình cá nhân</Text>
            <Text style={styles.onboardingHint}>Chọn điểm bắt đầu để dashboard gợi ý bài tiếp theo đúng hơn.</Text>

            <Text style={styles.fieldLabel}>Trình độ hiện tại</Text>
            <View style={styles.chipWrap}>
              {LEVEL_OPTIONS.map((option) => (
                <Pressable
                  key={option.value}
                  accessibilityRole="button"
                  onPress={() => setOnboardingLevel(option.value)}
                  style={[styles.chip, onboardingLevel === option.value && styles.chipActive]}
                >
                  <Text style={[styles.chipText, onboardingLevel === option.value && styles.chipTextActive]}>{option.label}</Text>
                </Pressable>
              ))}
            </View>

            <Text style={styles.fieldLabel}>Mục tiêu JLPT</Text>
            <View style={styles.chipWrap}>
              {JLPT_OPTIONS.map((goal) => (
                <Pressable
                  key={goal}
                  accessibilityRole="button"
                  onPress={() => setJlptGoal(goal)}
                  style={[styles.chip, jlptGoal === goal && styles.chipActive]}
                >
                  <Text style={[styles.chipText, jlptGoal === goal && styles.chipTextActive]}>{goal}</Text>
                </Pressable>
              ))}
            </View>

            <Text style={styles.fieldLabel}>Thời gian rảnh mỗi ngày</Text>
            <View style={styles.chipWrap}>
              {DAILY_MINUTE_OPTIONS.map((minutes) => (
                <Pressable
                  key={minutes}
                  accessibilityRole="button"
                  onPress={() => setDailyStudyMinutes(minutes)}
                  style={[styles.chip, dailyStudyMinutes === minutes && styles.chipActive]}
                >
                  <Text style={[styles.chipText, dailyStudyMinutes === minutes && styles.chipTextActive]}>{minutes}p</Text>
                </Pressable>
              ))}
            </View>

            <Pressable
              accessibilityRole="button"
              onPress={() => setEmailRemindersEnabled((value) => !value)}
              style={styles.toggleRow}
            >
              <Text style={[styles.toggleMark, emailRemindersEnabled && styles.toggleMarkActive]}>{emailRemindersEnabled ? 'ON' : 'OFF'}</Text>
              <Text style={styles.toggleText}>Email nhắc học khi streak sắp đứt</Text>
            </Pressable>
          </View>
        ) : null}

        <AppButton label={mode === 'register' ? 'Tạo tài khoản' : 'Đăng nhập'} loading={isSubmitting} onPress={handleSubmit} />
        <AppButton
          label="Dang nhap voi Google"
          disabled={isSubmitting || !googleRequest}
          onPress={() => {
            setGoogleError('');
            void promptGoogleLogin();
          }}
        />
      </AppCard>

      {connectionStatus ? (
        <StatusText message={connectionStatus} statusType={connectionStatusType} style={styles.status} />
      ) : (
        <Text style={styles.status}>{statusMessage}</Text>
      )}
      {googleError ? <Text style={styles.status}>{googleError}</Text> : null}
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    flexGrow: 1,
    paddingHorizontal: ui.spacing.xl,
    paddingVertical: ui.spacing.xl,
    justifyContent: 'center',
    gap: ui.spacing.lg,
    backgroundColor: playfulColors.page,
  },
  hero: { borderRadius: ui.radius.lg, padding: ui.spacing.lg, gap: ui.spacing.xs },
  heroBadge: {
    alignSelf: 'flex-start',
    backgroundColor: playfulColors.softBlue,
    color: playfulColors.brandDark,
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 999,
    fontSize: 12,
    fontWeight: '800',
  },
  title: { ...ui.text.h1, fontSize: 30 },
  subtitle: { ...ui.text.body },
  card: { padding: ui.spacing.lg, borderRadius: ui.radius.md, gap: ui.spacing.sm },
  label: { ...ui.text.caption, marginBottom: 4, textTransform: 'uppercase' },
  value: { ...ui.text.bodyStrong },
  modeRow: {
    flexDirection: 'row',
    borderRadius: ui.radius.sm,
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: playfulColors.border,
  },
  modeButton: {
    flex: 1,
    minHeight: ui.control.inputHeight,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: playfulColors.softBlue,
  },
  modeButtonActive: {
    backgroundColor: playfulColors.brand,
  },
  modeText: {
    color: playfulColors.brandDark,
    fontWeight: '800',
  },
  modeTextActive: {
    color: '#ffffff',
  },
  input: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.sm,
    minHeight: ui.control.inputHeight,
    paddingHorizontal: 12,
    paddingVertical: 8,
    backgroundColor: '#ffffff',
  },
  onboardingBox: {
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: ui.radius.md,
    padding: ui.spacing.md,
    gap: ui.spacing.xs,
    backgroundColor: playfulColors.softBlue,
  },
  onboardingTitle: {
    ...ui.text.bodyStrong,
    color: playfulColors.textPrimary,
  },
  onboardingHint: {
    ...ui.text.caption,
  },
  fieldLabel: {
    ...ui.text.caption,
    marginTop: 6,
    textTransform: 'uppercase',
  },
  chipWrap: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  chip: {
    minHeight: 34,
    paddingHorizontal: 10,
    borderRadius: ui.radius.pill,
    borderWidth: 1,
    borderColor: playfulColors.border,
    backgroundColor: '#ffffff',
    justifyContent: 'center',
  },
  chipActive: {
    backgroundColor: playfulColors.brand,
    borderColor: playfulColors.brand,
  },
  chipText: {
    color: playfulColors.textPrimary,
    fontSize: 12,
    fontWeight: '700',
  },
  chipTextActive: {
    color: '#ffffff',
  },
  toggleRow: {
    marginTop: 6,
    minHeight: 38,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  toggleMark: {
    minWidth: 42,
    textAlign: 'center',
    borderRadius: ui.radius.pill,
    borderWidth: 1,
    borderColor: playfulColors.border,
    paddingHorizontal: 8,
    paddingVertical: 4,
    color: playfulColors.textSecondary,
    fontWeight: '800',
    overflow: 'hidden',
  },
  toggleMarkActive: {
    backgroundColor: playfulColors.accentGreen,
    borderColor: playfulColors.accentGreen,
    color: '#ffffff',
  },
  toggleText: {
    ...ui.text.caption,
    flex: 1,
  },
  status: {
    color: playfulColors.textSecondary,
    lineHeight: 20,
    textAlign: 'center',
  },
});
