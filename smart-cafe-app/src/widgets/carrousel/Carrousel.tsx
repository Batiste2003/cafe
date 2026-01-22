import React, { useMemo, useState } from 'react';
import CategoryCarrousel from './CategoryCarrousel';
import { View, StyleSheet, useColorScheme } from 'react-native';
import ProductCarrousel from './ProductCarrousel';
import { CafeCardInterface } from '@/types/product.type';
import { Colors } from '@/constants/theme';

type Props = {
  products: CafeCardInterface[];
};

function getCategoryLabel(product: CafeCardInterface): string {
  const tag = product.tags?.[0];
  return (tag && tag.trim().length > 0) ? tag : 'Autres';
}

// Add icon for each category (better to add this as db input)
function iconForCategory(label: string): string {
  const l = label.toLowerCase();
  if (l.includes('cafés')) return '☕';
  if (l.includes('thés')) return '🍵';
  if (l.includes('viennoiseries')) return '🥐';
  return '✨';
}

export default function Carrousel({ products }: Props) {
  const colorScheme = useColorScheme() ?? 'light';
  const colors = Colors[colorScheme];

  const categories = useMemo(() => {
    const map = new Map<string, { id: string; text: string; icon: string }>();

    for (const product of products) {
      const label = getCategoryLabel(product);
      const id = label.toLowerCase();
      if (!map.has(id)) {
        map.set(id, { id, text: label, icon: iconForCategory(label) });
      }
    }

    return Array.from(map.values());
  }, [products]);

  const [selectedCategoryIndex, setSelectedCategoryIndex] = useState(0);
  const selectedCategory = categories[selectedCategoryIndex];

  // Filter product by category
  const filteredProducts = useMemo(() => {
    if (!selectedCategory) return [];
    return products.filter((p) => {
      const label = getCategoryLabel(p);
      const id = label.toLowerCase().replace(/\s+/g, '-');
      return id === selectedCategory.id;
    });
  }, [products, selectedCategory]);

  if (categories.length === 0) {
    return <View />; // TODO: implement the empty view
  }

  return (
    <View style={[styles.container, { backgroundColor: colors.accent }]}>
      <CategoryCarrousel
        items={categories}
        selectedIndex={selectedCategoryIndex}
        onSelect={setSelectedCategoryIndex}
      />

      {/* Product carrousel */}
      <ProductCarrousel products={filteredProducts} categoryLabel={selectedCategory.text} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    borderTopLeftRadius: 35,
    borderTopRightRadius: 35,
    overflow: 'hidden',
    flex: 1,
  },
});