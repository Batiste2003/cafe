import React from 'react';
import { StyleSheet, useColorScheme, Text } from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Carrousel from '@/src/widgets/carrousel/Carrousel';
import { CustomHeader } from '@/components/custom-header';
import { Colors } from '@/constants/theme';
import { useProducts } from '@/src/hooks/useProduct';

export default function HomeScreen() {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  const { data: products = [] } = useProducts({
    page: 1,
    perPage: 15,
    filters: { is_active: true },
  });


  return (
    <SafeAreaView style={[styles.safeArea, { backgroundColor: colors.background }]}>
      <CustomHeader title={'Smart Coffee'} />
      <Text style={[styles.heroText, { color: colors.textMuted }]}>
        Le plaisir, en toute simplicité.
      </Text>
      <Carrousel products={products} />
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
