import { playfulColors } from './duolingo';

export const ui = {
  spacing: {
    xs: 6,
    sm: 10,
    md: 14,
    lg: 16,
    xl: 20,
  },
  radius: {
    sm: 10,
    md: 14,
    lg: 16,
    pill: 999,
  },
  control: {
    inputHeight: 44,
    chipHeight: 36,
    button: {
      sm: 34,
      md: 44,
      lg: 48,
    },
  },
  screenTitle: {
    fontSize: 24,
    fontWeight: '800' as const,
    color: playfulColors.textPrimary,
  },
  screenSubtitle: {
    color: playfulColors.textSecondary,
    lineHeight: 20,
  },
  text: {
    h1: {
      fontSize: 24,
      fontWeight: '800' as const,
      color: playfulColors.textPrimary,
    },
    h2: {
      fontSize: 18,
      fontWeight: '800' as const,
      color: playfulColors.textPrimary,
    },
    body: {
      fontSize: 14,
      color: playfulColors.textSecondary,
      lineHeight: 20,
    },
    bodyStrong: {
      fontSize: 14,
      fontWeight: '700' as const,
      color: playfulColors.textPrimary,
      lineHeight: 20,
    },
    caption: {
      fontSize: 12,
      color: playfulColors.textSecondary,
      lineHeight: 18,
    },
    captionStrong: {
      fontSize: 12,
      fontWeight: '700' as const,
      color: playfulColors.brandDark,
      lineHeight: 18,
    },
  },
  surfaceCard: {
    backgroundColor: '#ffffff',
    borderWidth: 1,
    borderColor: playfulColors.border,
    borderRadius: 16,
    shadowColor: '#1f2a44',
    shadowOpacity: 0.06,
    shadowRadius: 8,
    shadowOffset: { width: 0, height: 2 },
    elevation: 2,
  },
  statusText: {
    textAlign: 'center' as const,
    lineHeight: 20,
  },
};

