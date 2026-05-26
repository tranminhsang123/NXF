import { Text, type StyleProp, type TextStyle } from 'react-native';

import type { ApiStatusType } from '../hooks/useApiStatus';
import { formatStatusMessage, getStatusTextStyle } from '../theme/status';

type StatusTextProps = {
  message: string;
  statusType?: ApiStatusType;
  style?: StyleProp<TextStyle>;
};

export function StatusText({ message, statusType, style }: StatusTextProps) {
  if (!statusType) {
    return <Text style={style}>{message}</Text>;
  }

  return <Text style={[style, getStatusTextStyle(statusType)]}>{formatStatusMessage(statusType, message)}</Text>;
}

