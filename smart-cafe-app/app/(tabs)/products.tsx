import { ScrollView, View, Text, StatusBar, ActivityIndicator } from "react-native";
import Animated, {
  useAnimatedStyle,
  useSharedValue,
  withSpring,
  withDelay,
  withTiming,
} from "react-native-reanimated";
import { useEffect } from "react";
import { CafeCard } from "@/components/product-card";
import { ProductScreenStyles } from "@/styles/cafecard.style";
import { Colors } from "@/constants/theme";
import { useColorScheme } from "@/hooks/use-color-scheme";
import { useProducts } from "@/src/hooks/useProduct";

export default function CardScreen() {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  // Header animations
  const headerOpacity = useSharedValue(0);
  const headerTranslateY = useSharedValue(-20);
  const iconScale = useSharedValue(0);

  // Fetch products from API
  const { data: products = [], isLoading, error, refetch } = useProducts({
    page: 1,
    perPage: 15,
    filters: { is_active: true },
  });

  const errorMessage =
    error instanceof Error ? error.message : "Une erreur est survenue.";

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
        {isLoading && (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <ActivityIndicator size="large" color={colors.accent} />
            <Text style={[{ marginTop: 16, color: colors.textMuted }]}>
              Chargement des produits...
            </Text>
          </View>
        )}

        {/* Error State */}
        {error && !isLoading && (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <Text style={[{ color: '#EF4444', fontSize: 16, textAlign: 'center' }]}>
              {errorMessage}
            </Text>
            <Text
              style={[{ marginTop: 12, color: colors.accent, fontSize: 14 }]}
              onPress={() => {
                refetch();
              }}
            >
              Réessayer
            </Text>
          </View>
        )}

        {/* Products List */}
        {!isLoading && !error && products.length === 0 && (
          <View style={{ padding: 40, alignItems: 'center' }}>
            <Text style={[{ color: colors.textMuted, fontSize: 16 }]}>
              Aucun produit disponible
            </Text>
          </View>
        )}

        {!isLoading && !error && products.map((product, index) => (
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
