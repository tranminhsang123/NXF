import type { TextStyle } from 'react-native';

import type { ApiStatusType } from '../hooks/useApiStatus';
import { playfulColors } from './duolingo';

const STATUS_TEXT_STYLES: Record<ApiStatusType, TextStyle> = {
  idle: { color: playfulColors.textSecondary },
  loading: { color: playfulColors.brandDark },
  success: { color: playfulColors.accentGreen, fontWeight: '700' },
  error: { color: playfulColors.accentPink, fontWeight: '700' },
};

export function getStatusTextStyle(statusType: ApiStatusType): TextStyle {
  return STATUS_TEXT_STYLES[statusType];
}

const STATUS_PREFIX: Record<ApiStatusType, string> = {
  idle: '[i]',
  loading: '[~]',
  success: '[OK]',
  error: '[!]',
};

export function formatStatusMessage(statusType: ApiStatusType, message: string): string {
  if (!message.trim()) {
    return '';
  }

  return `${STATUS_PREFIX[statusType]} ${message}`;
}

