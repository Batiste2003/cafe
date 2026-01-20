/**
 * Coffee Shop Premium Theme
 * Inspired by artisanal coffee aesthetics - warm, sophisticated, inviting
 */

import { Platform } from 'react-native';

// Coffee Shop Color Palette
export const CoffeeColors = {
  // Primary - Deep espresso tones
  espresso: '#1C1210',
  darkRoast: '#2D1F1A',
  mediumRoast: '#4A3728',

  // Secondary - Warm creamy tones
  cream: '#F7F3EE',
  latte: '#E8DFD4',
  cappuccino: '#D4C4B0',

  // Accent - Rich golden & caramel
  caramel: '#C69C6D',
  honey: '#D4A853',
  cinnamon: '#8B5A2B',

  // Semantic
  success: '#5D7A5D',
  error: '#A65D5D',
};

const tintColorLight = CoffeeColors.caramel;
const tintColorDark = CoffeeColors.honey;

export const Colors = {
  light: {
    text: CoffeeColors.espresso,
    textSecondary: CoffeeColors.mediumRoast,
    textMuted: '#8B7355',
    background: CoffeeColors.cream,
    backgroundSecondary: CoffeeColors.latte,
    card: '#FFFFFF',
    cardBorder: 'rgba(139, 115, 85, 0.12)',
    tint: tintColorLight,
    icon: CoffeeColors.mediumRoast,
    tabIconDefault: CoffeeColors.cappuccino,
    tabIconSelected: tintColorLight,
    accent: CoffeeColors.caramel,
    accentLight: 'rgba(198, 156, 109, 0.15)',
  },
  dark: {
    text: CoffeeColors.cream,
    textSecondary: CoffeeColors.cappuccino,
    textMuted: '#9B8B7A',
    background: CoffeeColors.espresso,
    backgroundSecondary: CoffeeColors.darkRoast,
    card: '#2A1F1A',
    cardBorder: 'rgba(212, 196, 176, 0.08)',
    tint: tintColorDark,
    icon: CoffeeColors.cappuccino,
    tabIconDefault: '#6B5B4F',
    tabIconSelected: tintColorDark,
    accent: CoffeeColors.honey,
    accentLight: 'rgba(212, 168, 83, 0.12)',
  },
};

export const Fonts = Platform.select({
  ios: {
    /** iOS `UIFontDescriptorSystemDesignDefault` */
    sans: 'system-ui',
    /** iOS `UIFontDescriptorSystemDesignSerif` */
    serif: 'ui-serif',
    /** iOS `UIFontDescriptorSystemDesignRounded` */
    rounded: 'ui-rounded',
    /** iOS `UIFontDescriptorSystemDesignMonospaced` */
    mono: 'ui-monospace',
  },
  default: {
    sans: 'normal',
    serif: 'serif',
    rounded: 'normal',
    mono: 'monospace',
  },
  web: {
    sans: "system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif",
    serif: "Georgia, 'Times New Roman', serif",
    rounded: "'SF Pro Rounded', 'Hiragino Maru Gothic ProN', Meiryo, 'MS PGothic', sans-serif",
    mono: "SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace",
  },
});
