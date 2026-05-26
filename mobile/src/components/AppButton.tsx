import { ActivityIndicator, Pressable, StyleSheet, Text, type StyleProp, type TextStyle, type ViewStyle } from 'react-native';

import { playfulColors } from '../theme/duolingo';
import { ui } from '../theme/ui';

type AppButtonVariant = 'primary' | 'danger' | 'outline';

type AppButtonProps = {
  label: string;
  onPress: () => void;
  disabled?: boolean;
  loading?: boolean;
  variant?: AppButtonVariant;
  style?: StyleProp<ViewStyle>;
  textStyle?: StyleProp<TextStyle>;
};

export function AppButton({
  label,
  onPress,
  disabled = false,
  loading = false,
  variant = 'primary',
  style,
  textStyle,
}: AppButtonProps) {
  const isDisabled = disabled || loading;

  return (
    <Pressable
      accessibilityRole="button"
      disabled={isDisabled}
      onPress={onPress}
      style={({ pressed }) => [styles.base, VARIANT_STYLES[variant], pressed && styles.pressed, isDisabled && styles.disabled, style]}
    >
      {loading ? <ActivityIndicator color={variant === 'outline' ? playfulColors.brandDark : '#ffffff'} /> : <Text style={[styles.text, TEXT_VARIANT_STYLES[variant], textStyle]}>{label}</Text>}
    </Pressable>
  );
}

const VARIANT_STYLES: Record<AppButtonVariant, ViewStyle> = {
  primary: {
    backgroundColor: playfulColors.brand,
  },
  danger: {
    backgroundColor: playfulColors.accentPink,
  },
  outline: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: playfulColors.border,
  },
};

const TEXT_VARIANT_STYLES: Record<AppButtonVariant, TextStyle> = {
  primary: {
    color: '#ffffff',
  },
  danger: {
    color: '#ffffff',
  },
  outline: {
    color: playfulColors.brandDark,
  },
};

const styles = StyleSheet.create({
  base: {
    minHeight: ui.control.button.md,
    borderRadius: ui.radius.sm,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: ui.spacing.md,
  },
  text: {
    fontWeight: '800',
  },
  pressed: {
    opacity: 0.86,
  },
  disabled: {
    opacity: 0.65,
  },
});

