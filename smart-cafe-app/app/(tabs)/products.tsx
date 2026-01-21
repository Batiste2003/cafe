import { ScrollView, View, Text, StatusBar, ActivityIndicator } from "react-native";
import Animated, {
  useAnimatedStyle,
  useSharedValue,
  withSpring,
  withDelay,
  withTiming,
} from "react-native-reanimated";
import { useEffect, useState } from "react";
import { CafeCard } from "@/components/product-card";
import { ProductScreenStyles } from "@/styles/cafecard.style";
import { Colors } from "@/constants/theme";
import { useColorScheme } from "@/hooks/use-color-scheme";
import ApiService from "@/services/api";
import { mapProductToCardInterface, CafeCardInterface } from "@/types/product.type";

export default function CardScreen() {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  // State management
  const [products, setProducts] = useState<CafeCardInterface[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Header animations
  const headerOpacity = useSharedValue(0);
  const headerTranslateY = useSharedValue(-20);
  const iconScale = useSharedValue(0);

  // Fetch products from API
  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true);
        setError(null);
        
        const response = await ApiService.getProducts(1, 15, {
          is_active: true,
        });
        
        // Transform backend products to card interface
        const transformedProducts = response.data.map(mapProductToCardInterface);
        setProducts(transformedProducts);
      } catch (err: any) {
        console.error('Error fetching products:', err);
        setError(err.message || 'Impossible de charger les produits');
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, []);

  useEffect(() => {
    headerOpacity.value = withTiming(1, { duration: 600 });
    headerTranslateY.value = withSpring(0, { damping: 20, stiffness: 90 });
    iconScale.value = withDelay(
      300,
      withSpring(1, { damping: 12, stiffness: 100 })
    );
  }, [headerOpacity, headerTranslateY, iconScale]);

  const headerAnimatedStyle = useAnimatedStyle(() => ({
    opacity: headerOpacity.value,
    transform: [{ translateY: headerTranslateY.value }],
  }));

  const iconAnimatedStyle = useAnimatedStyle(() => ({
    transform: [{ scale: iconScale.value }],
  }));

  return (
    <View style={[ProductScreenStyles.container, { backgroundColor: colors.background }]}>
      <StatusBar
        barStyle={colorScheme === "dark" ? "light-content" : "dark-content"}
      />

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={ProductScreenStyles.scrollContent}
      >
        {/* Premium Header */}
        <Animated.View style={[ProductScreenStyles.header, headerAnimatedStyle]}>
          <View style={ProductScreenStyles.headerContent}>
            <View>
              <Text style={[ProductScreenStyles.greeting, { color: colors.textMuted }]}>
                Bienvenue chez
              </Text>
              <Text style={[ProductScreenStyles.headerTitle, { color: colors.text }]}>
                Smart{" "}
                <Text style={[ProductScreenStyles.headerAccent, { color: colors.accent }]}>
                  Café
                </Text>
              </Text>
            </View>

            <Animated.View
              style={[
                ProductScreenStyles.headerIcon,
                { backgroundColor: colors.accent },
                iconAnimatedStyle,
              ]}
            >
              <Text style={ProductScreenStyles.headerIconText}>☕</Text>
            </Animated.View>
          </View>
        </Animated.View>

        {/* Section Header */}
        <View style={ProductScreenStyles.sectionHeader}>
          <Text style={[ProductScreenStyles.sectionTitle, { color: colors.text }]}>
            Nos Cafés de Spécialité
          </Text>
          <Text style={[ProductScreenStyles.sectionLink, { color: colors.accent }]}>
            Voir tout
          </Text>
        </View>

        {/* Loading State */}
        {loading && (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <ActivityIndicator size="large" color={colors.accent} />
            <Text style={[{ marginTop: 16, color: colors.textMuted }]}>
              Chargement des produits...
            </Text>
          </View>
        )}

        {/* Error State */}
        {error && !loading && (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <Text style={[{ color: '#EF4444', fontSize: 16, textAlign: 'center' }]}>
              {error}
            </Text>
            <Text 
              style={[{ marginTop: 12, color: colors.accent, fontSize: 14 }]}
              onPress={() => {
                setError(null);
                setLoading(true);
                // Retry fetch
              }}
            >
              Réessayer
            </Text>
          </View>
        )}

        {/* Products List */}
        {!loading && !error && products.length === 0 && (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <Text style={[{ color: colors.textMuted, fontSize: 16 }]}>
              Aucun produit disponible
            </Text>
          </View>
        )}

        {!loading && !error && products.map((product, index) => (
          <CafeCard
            key={product.id}
            id={product.id}
            slug={product.slug}
            name={product.name}
            description={product.description}
            price={product.price}
            imageUrl={product.imageUrl}
            origin={product.origin}
            tags={product.tags}
            badge={product.badge}
            index={index}
          />
        ))}
      </ScrollView>
    </View>
  );
}
