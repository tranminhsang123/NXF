import { Pressable, View, type StyleProp, type ViewStyle } from 'react-native';
import type { ReactNode } from 'react';

import { ui } from '../theme/ui';

type AppCardProps = {
  children: ReactNode;
  style?: StyleProp<ViewStyle>;
  onPress?: () => void;
};

export function AppCard({ children, style, onPress }: AppCardProps) {
  if (onPress) {
    return (
      <Pressable onPress={onPress} style={({ pressed }) => [ui.surfaceCard, style, pressed && { opacity: 0.9 }]}>
        {children}
      </Pressable>
    );
  }

  return <View style={[ui.surfaceCard, style]}>{children}</View>;
}

