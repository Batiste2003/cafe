import React, { useEffect, useState, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  Image,
  Pressable,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { SafeAreaView } from 'react-native-safe-area-context';
import ApiService from '@/services/api';
import { Product } from '@/types/product.type';
import { Colors } from '@/constants/theme';
import { useColorScheme } from '@/hooks/use-color-scheme';

export default function ProductDetailScreen() {
  const { id } = useLocalSearchParams<{ id: string }>();
  const router = useRouter();
  const colorScheme = useColorScheme() ?? 'light';
  const colors = Colors[colorScheme];
  const [product, setProduct] = useState<Product | null>(null);
  const [loading, setLoading] = useState(true);
  const [quantity, setQuantity] = useState(1);
  const [selectedOptions, setSelectedOptions] = useState<{
    [optionId: number]: number;
  }>({});

  const loadProduct = useCallback(async () => {
    try {
      setLoading(true);
      const response = await ApiService.getProduct(parseInt(id || '0'));
      setProduct(response);
    } catch (error) {
      console.error('Error loading product:', error);
      Alert.alert('Erreur', 'Impossible de charger le produit');
      router.back();
    } finally {
      setLoading(false);
    }
  }, [id, router]);

  useEffect(() => {
    loadProduct();
  }, [loadProduct]);

  const handleSelectOption = (optionId: number, valueId: number) => {
    setSelectedOptions((prev) => ({
      ...prev,
      [optionId]: valueId,
    }));
  };

  const increaseQuantity = () => {
    setQuantity((prev) => prev + 1);
  };

  const decreaseQuantity = () => {
    setQuantity((prev) => (prev > 1 ? prev - 1 : 1));
  };

  const calculateTotalPrice = (): number => {
    if (!product?.default_variant) return 0;

    let unitPrice = product.default_variant.price_euros;

    // Ajouter les prix additionnels des options sélectionnées
    if (product.options) {
      product.options.forEach((option) => {
        const selectedValueId = selectedOptions[option.id];
        if (selectedValueId) {
          const selectedValue = option.values.find((v) => v.id === selectedValueId);
          if (selectedValue) {
            unitPrice += selectedValue.price_add_euros;
          }
        }
      });
    }

    return unitPrice * quantity;
  };

  const areAllRequiredOptionsSelected = (): boolean => {
    if (!product?.options) return true;

    return product.options
      .filter((option) => option.is_required)
      .every((option) => selectedOptions[option.id] !== undefined);
  };

  const handleAddToCart = () => {
    if (!areAllRequiredOptionsSelected()) {
      Alert.alert('Attention', 'Veuillez sélectionner toutes les options requises');
      return;
    }

    // TODO: Implémenter l'ajout au panier
    Alert.alert(
      'Succès', 
      `${quantity} produit(s) ajouté(s) au panier pour ${calculateTotalPrice().toFixed(2)}€`
    );
    router.back();
  };

  if (loading) {
    return (
      <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]}>
        <View style={styles.loadingContainer}>
          <ActivityIndicator size="large" color={colors.accent} />
        </View>
      </SafeAreaView>
    );
  }

  if (!product) {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.loadingContainer}>
          <Text style={styles.errorText}>Produit introuvable</Text>
        </View>
      </SafeAreaView>
    );
  }

  const imageUrl =
    product.gallery && product.gallery.length > 0 ? product.gallery[0].url : null;

  return (
    <SafeAreaView style={[styles.container, { backgroundColor: colors.background }]} edges={['bottom']}>
      <ScrollView style={styles.scrollView} showsVerticalScrollIndicator={false}>
        {/* Image du produit */}
        {imageUrl ? (
          <Image source={{ uri: imageUrl }} style={styles.image} resizeMode="cover" />
        ) : (
          <View style={[styles.image, styles.imagePlaceholder, { backgroundColor: colors.backgroundSecondary }]}>
            <Text style={[styles.imagePlaceholderText, { color: colors.textSecondary }]}>💁</Text>
          </View>
        )}

        {/* Informations du produit */}
        <View style={[styles.content, { backgroundColor: colors.background }]}>
          {/* En-tête produit */}
          <View style={styles.header}>
            <View style={styles.headerInfo}>
              {product.category && (
                <Text style={[styles.category, { color: colors.textMuted }]}>{product.category.name}</Text>
              )}
              <Text style={[styles.productName, { color: colors.text }]}>{product.name}</Text>
              {product.description && (
                <Text style={[styles.description, { color: colors.textSecondary }]}>{product.description}</Text>
              )}
            </View>
          </View>

          {/* Séparateur */}
          <View style={[styles.divider, { backgroundColor: colors.cardBorder }]} />

          {/* Options du produit */}
          {product.options && product.options.length > 0 && (
            <View style={styles.optionsSection}>
              {product.options.map((option) => (
                <View key={option.id} style={[styles.optionCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
                  <View style={styles.optionHeader}>
                    <Text style={[styles.optionName, { color: colors.text }]}>  
                      {option.name}
                    </Text>
                    {option.is_required && (
                      <View style={[styles.requiredBadge, { backgroundColor: colors.accentLight }]}>
                        <Text style={[styles.requiredBadgeText, { color: colors.accent }]}>Requis</Text>
                      </View>
                    )}
                  </View>

                  <View style={styles.optionValues}>
                    {option.values.map((value) => {
                      const isSelected = selectedOptions[option.id] === value.id;

                      return (
                        <Pressable
                          key={value.id}
                          style={[
                            styles.optionButton,
                            { 
                              borderColor: isSelected ? colors.accent : colors.cardBorder,
                              backgroundColor: isSelected ? colors.accent : colors.background,
                              shadowColor: isSelected ? colors.accent : 'transparent',
                            },
                            isSelected && styles.optionButtonSelected,
                          ]}
                          onPress={() => handleSelectOption(option.id, value.id)}
                        >
                          <Text
                            style={[
                              styles.optionButtonText,
                              { color: isSelected ? '#FFFFFF' : colors.text },
                            ]}
                          >
                            {value.value}
                          </Text>
                          {value.price_add_euros > 0 && (
                            <Text
                              style={[
                                styles.optionPriceAdd,
                                { color: isSelected ? 'rgba(255,255,255,0.9)' : colors.textSecondary },
                              ]}
                            >
                              +{value.price_add_euros.toFixed(2)}€
                            </Text>
                          )}
                        </Pressable>
                      );
                    })}
                  </View>
                </View>
              ))}
            </View>
          )}

          {/* Sélecteur de quantité */}
          <View style={[styles.quantityCard, { backgroundColor: colors.card, borderColor: colors.cardBorder }]}>
            <Text style={[styles.quantityLabel, { color: colors.text }]}>Quantité</Text>
            <View style={[styles.quantityControls, { backgroundColor: colors.background, borderColor: colors.cardBorder }]}>
              <Pressable
                style={[styles.quantityButton, { borderRightColor: colors.cardBorder }]}
                onPress={decreaseQuantity}
              >
                <Text style={[styles.quantityButtonText, { color: colors.accent }]}>−</Text>
              </Pressable>
              <View style={styles.quantityValueContainer}>
                <Text style={[styles.quantityValue, { color: colors.text }]}>{quantity}</Text>
              </View>
              <Pressable
                style={[styles.quantityButton, { borderLeftColor: colors.cardBorder }]}
                onPress={increaseQuantity}
              >
                <Text style={[styles.quantityButtonText, { color: colors.accent }]}>+</Text>
              </Pressable>
            </View>
          </View>

          {/* Espacement pour le footer */}
          <View style={{ height: 20 }} />
        </View>
      </ScrollView>

      {/* Footer avec prix et bouton */}
      <View style={[styles.footer, { backgroundColor: colors.card, borderTopColor: colors.cardBorder, shadowColor: '#000' }]}>
        <View style={styles.footerContent}>
          <View style={styles.priceSection}>
            <Text style={[styles.totalLabel, { color: colors.textSecondary }]}>Total</Text>
            <Text style={[styles.totalPrice, { color: colors.accent }]}>{calculateTotalPrice().toFixed(2)}€</Text>
          </View>
          <Pressable
            style={[
              styles.addButton,
              { backgroundColor: areAllRequiredOptionsSelected() ? colors.accent : colors.cardBorder },
            ]}
            onPress={handleAddToCart}
            disabled={!areAllRequiredOptionsSelected()}
          >
            <Text style={[styles.addButtonText, { opacity: areAllRequiredOptionsSelected() ? 1 : 0.5 }]}>
              Payer
            </Text>
          </Pressable>
        </View>
      </View>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
  },
  loadingContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
  },
  errorText: {
    fontSize: 16,
  },
  scrollView: {
    flex: 1,
  },
  image: {
    width: '100%',
    height: 320,
  },
  imagePlaceholder: {
    justifyContent: 'center',
    alignItems: 'center',
  },
  imagePlaceholderText: {
    fontSize: 80,
  },
  content: {
    flex: 1,
    padding: 20,
    paddingTop: 24,
  },
  header: {
    marginBottom: 20,
  },
  headerInfo: {
    gap: 8,
  },
  category: {
    fontSize: 12,
    fontWeight: '600',
    textTransform: 'uppercase',
    letterSpacing: 1,
  },
  productName: {
    fontSize: 32,
    fontWeight: 'bold',
    lineHeight: 38,
  },
  description: {
    fontSize: 15,
    lineHeight: 22,
    marginTop: 4,
  },
  divider: {
    height: 1,
    marginVertical: 20,
  },
  optionsSection: {
    gap: 16,
  },
  optionCard: {
    padding: 16,
    borderRadius: 16,
    borderWidth: 1,
  },
  optionHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  optionName: {
    fontSize: 18,
    fontWeight: '700',
  },
  requiredBadge: {
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 8,
  },
  requiredBadgeText: {
    fontSize: 11,
    fontWeight: '700',
    textTransform: 'uppercase',
  },
  optionValues: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 10,
  },
  optionButton: {
    paddingVertical: 14,
    paddingHorizontal: 24,
    borderRadius: 14,
    borderWidth: 2,
    minWidth: 90,
    alignItems: 'center',
  },
  optionButtonSelected: {
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 4,
  },
  optionButtonText: {
    fontSize: 16,
    fontWeight: '700',
  },
  optionPriceAdd: {
    fontSize: 12,
    marginTop: 2,
    fontWeight: '600',
  },
  quantityCard: {
    marginTop: 16,
    padding: 16,
    borderRadius: 16,
    borderWidth: 1,
  },
  quantityLabel: {
    fontSize: 18,
    fontWeight: '700',
    marginBottom: 12,
  },
  quantityControls: {
    flexDirection: 'row',
    borderRadius: 14,
    borderWidth: 1,
    overflow: 'hidden',
  },
  quantityButton: {
    flex: 1,
    paddingVertical: 16,
    alignItems: 'center',
    justifyContent: 'center',
  },
  quantityButtonText: {
    fontSize: 26,
    fontWeight: '600',
  },
  quantityValueContainer: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  quantityValue: {
    fontSize: 22,
    fontWeight: '700',
  },
  footer: {
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderTopWidth: 1,
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.1,
    shadowRadius: 8,
    elevation: 10,
  },
  footerContent: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 16,
  },
  priceSection: {
    flex: 1,
  },
  totalLabel: {
    fontSize: 13,
    fontWeight: '600',
    marginBottom: 2,
  },
  totalPrice: {
    fontSize: 28,
    fontWeight: 'bold',
  },
  addButton: {
    flex: 1.5,
    paddingVertical: 18,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
  },
  addButtonText: {
    fontSize: 17,
    fontWeight: 'bold',
    color: '#FFFFFF',
  },
});
