import { ScrollView, View, Text, StatusBar } from "react-native";
import Animated, {
  useAnimatedStyle,
  useSharedValue,
  withSpring,
  withDelay,
  withTiming,
} from "react-native-reanimated";
import { useEffect } from "react";
import { CafeCard } from "@/components/cafe-card";
import { CardScreenStyles } from "@/styles/cafecard.style";
import { Colors } from "@/constants/theme";
import { useColorScheme } from "@/hooks/use-color-scheme";

// Demo data with rich coffee details
const CAFE_DATA = [
  {
    id: "1",
    name: "Ethiopian Yirgacheffe",
    description:
      "Notes florales délicates avec des arômes de jasmin et une finale aux agrumes. Un café d'exception pour les amateurs de saveurs subtiles.",
    price: "4.50€",
    origin: "Éthiopie · Sidamo",
    tags: ["Arabica", "Léger", "Floral"],
    badge: "Nouveau",
    imageUrl:
      "https://images.unsplash.com/photo-1497935586351-b67a49e012bf?w=800",
  },
  {
    id: "2",
    name: "Colombian Supremo",
    description:
      "Corps velouté avec des notes de caramel et de noisette. L'équilibre parfait entre douceur et caractère.",
    price: "3.80€",
    origin: "Colombie · Huila",
    tags: ["Arabica", "Moyen", "Caramel"],
    imageUrl:
      "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=800",
  },
  {
    id: "3",
    name: "Sumatra Mandheling",
    description:
      "Profil intense aux notes de terre et de chocolat noir. Pour ceux qui préfèrent un café corsé et mémorable.",
    price: "4.20€",
    origin: "Indonésie · Sumatra",
    tags: ["Robusta", "Corsé", "Chocolat"],
    badge: "Best-seller",
    imageUrl:
      "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=800",
  },
  {
    id: "4",
    name: "Guatemala Antigua",
    description:
      "Arômes de cacao et d'épices douces avec une acidité vive. Un grand cru d'Amérique Centrale.",
    price: "4.00€",
    origin: "Guatemala · Antigua",
    tags: ["Arabica", "Moyen", "Épicé"],
    imageUrl:
      "https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=800",
  },
];

export default function CardScreen() {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  // Header animations
  const headerOpacity = useSharedValue(0);
  const headerTranslateY = useSharedValue(-20);
  const iconScale = useSharedValue(0);

  useEffect(() => {
    headerOpacity.value = withTiming(1, { duration: 600 });
    headerTranslateY.value = withSpring(0, { damping: 20, stiffness: 90 });
    iconScale.value = withDelay(
      300,
      withSpring(1, { damping: 12, stiffness: 100 })
    );
  }, []);

  const headerAnimatedStyle = useAnimatedStyle(() => ({
    opacity: headerOpacity.value,
    transform: [{ translateY: headerTranslateY.value }],
  }));

  const iconAnimatedStyle = useAnimatedStyle(() => ({
    transform: [{ scale: iconScale.value }],
  }));

  return (
    <View style={[CardScreenStyles.container, { backgroundColor: colors.background }]}>
      <StatusBar
        barStyle={colorScheme === "dark" ? "light-content" : "dark-content"}
      />

      <ScrollView
        showsVerticalScrollIndicator={false}
        contentContainerStyle={CardScreenStyles.scrollContent}
      >
        {/* Premium Header */}
        <Animated.View style={[CardScreenStyles.header, headerAnimatedStyle]}>
          <View style={CardScreenStyles.headerContent}>
            <View>
              <Text style={[CardScreenStyles.greeting, { color: colors.textMuted }]}>
                Bienvenue chez
              </Text>
              <Text style={[CardScreenStyles.headerTitle, { color: colors.text }]}>
                Smart{" "}
                <Text style={[CardScreenStyles.headerAccent, { color: colors.accent }]}>
                  Café
                </Text>
              </Text>
            </View>

            <Animated.View
              style={[
                CardScreenStyles.headerIcon,
                { backgroundColor: colors.accent },
                iconAnimatedStyle,
              ]}
            >
              <Text style={CardScreenStyles.headerIconText}>☕</Text>
            </Animated.View>
          </View>
        </Animated.View>

        {/* Section Header */}
        <View style={CardScreenStyles.sectionHeader}>
          <Text style={[CardScreenStyles.sectionTitle, { color: colors.text }]}>
            Nos Cafés de Spécialité
          </Text>
          <Text style={[CardScreenStyles.sectionLink, { color: colors.accent }]}>
            Voir tout
          </Text>
        </View>

        {/* Coffee Cards */}
        {CAFE_DATA.map((cafe, index) => (
          <CafeCard
            key={cafe.id}
            name={cafe.name}
            description={cafe.description}
            price={cafe.price}
            imageUrl={cafe.imageUrl}
            origin={cafe.origin}
            tags={cafe.tags}
            badge={cafe.badge}
            index={index}
          />
        ))}
      </ScrollView>
    </View>
  );
}
