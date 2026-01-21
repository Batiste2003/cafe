import React from 'react';
import { StyleSheet, useColorScheme, Text } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Carrousel from '@/src/widgets/carrousel/Carrousel';
import { CustomHeader } from '@/components/custom-header';
import { Colors } from '@/constants/theme';

export default function HomeScreen() {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
      <CustomHeader title={'Smart Coffee'} />
      <Text style={[styles.heroText, { color: colors.textMuted }]}>
        Le plaisir, en toute simplicité.
      </Text>
      <Carrousel />
    </SafeAreaView >
  );
};

const styles = StyleSheet.create({
  safeArea: {
    flex: 1,
  },
  heroText: {
    marginHorizontal: 32,
    marginVertical: 48,
    fontSize: 48,
    fontFamily: 'PlayfairDisplay_Italic'
  }
});
