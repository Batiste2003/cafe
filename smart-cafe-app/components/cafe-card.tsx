import * as React from "react";
import { View, Text, Image, Pressable } from "react-native";
import Animated, {
  useAnimatedStyle,
  withSpring,
  withDelay,
  useSharedValue,
  withTiming,
} from "react-native-reanimated";
import { CafeCardStyles } from "@/styles/cafecard.style";
import { CafeCardInterface } from "@/types/cafe.type";
import { Colors } from "@/constants/theme";
import { useColorScheme } from "@/hooks/use-color-scheme";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

export function CafeCard({
  name,
  slug,
  description,
  price,
  image,
  category = ["Arabica", "Medium Roast"],
  index = 0,
}: CafeCardInterface) {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  // Animation values
  const cardScale = useSharedValue(1);
  const cardOpacity = useSharedValue(0);
  const cardTranslateY = useSharedValue(40);

  // Entry animation
  React.useEffect(() => {
    const delay = index * 120;
    cardOpacity.value = withDelay(delay, withTiming(1, { duration: 500 }));
    cardTranslateY.value = withDelay(
      delay,
      withSpring(0, { damping: 20, stiffness: 90 })
    );
  }, []);

  // Press animation
  const handlePressIn = () => {
    cardScale.value = withSpring(0.97, { damping: 15, stiffness: 400 });
  };

  const handlePressOut = () => {
    cardScale.value = withSpring(1, { damping: 15, stiffness: 400 });
  };

  const animatedCardStyle = useAnimatedStyle(() => ({
    opacity: cardOpacity.value,
    transform: [
      { translateY: cardTranslateY.value },
      { scale: cardScale.value },
    ],
  }));

  return (
    <AnimatedPressable
      onPressIn={handlePressIn}
      onPressOut={handlePressOut}
      style={[
        CafeCardStyles.card,
        { backgroundColor: colors.card },
        animatedCardStyle,
      ]}
    >
      {/* Image Section */}
      <View style={CafeCardStyles.imageContainer}>
        {image ? (
          <>
            <Image
              source={{ uri: image }}
              style={CafeCardStyles.image}
              resizeMode="cover"
            />
            <View style={CafeCardStyles.imageOverlay} />
          </>
        ) : (
          <View
            style={[
              CafeCardStyles.imagePlaceholder,
              { backgroundColor: colors.backgroundSecondary },
            ]}
          >
            <Text style={{ fontSize: 64 }}>☕</Text>
          </View>
        )}
      </View>

      {/* Content Section */}
      <View style={CafeCardStyles.content}>
        {/* Header: Title & Price */}
        <View style={CafeCardStyles.header}>
          <View style={CafeCardStyles.titleContainer}>
            <Text style={[CafeCardStyles.title, { color: colors.text }]}>
              {name}
            </Text>
          </View>

          <View style={CafeCardStyles.priceContainer}>
            <Text style={[CafeCardStyles.price, { color: colors.accent }]}>
              {price}
            </Text>
            <Text
              style={[CafeCardStyles.priceLabel, { color: colors.textMuted }]}
            >
              par tasse
            </Text>
          </View>
        </View>

        {/* Divider */}
        <View
          style={[CafeCardStyles.divider, { backgroundColor: colors.cardBorder }]}
        />

        {/* Description */}
        <Text
          style={[CafeCardStyles.description, { color: colors.textSecondary }]}
          numberOfLines={2}
        >
          {description}
        </Text>

        {/* Footer: Tags & Action */}
        <View style={CafeCardStyles.footer}>
          <View style={CafeCardStyles.tagContainer}>
            {category.slice(0, 3).map((category, idx) => (
              <View
                key={idx}
                style={[CafeCardStyles.tag, { backgroundColor: colors.accentLight }]}
              >
                <Text style={[CafeCardStyles.tagText, { color: colors.accent }]}>
                  {category}
                </Text>
              </View>
            ))}
          </View>

          <Pressable
            style={[
              CafeCardStyles.actionButton,
              { backgroundColor: colors.accent },
            ]}
          >
            <Text style={CafeCardStyles.actionButtonIcon}>→</Text>
          </Pressable>
        </View>
      </View>
    </AnimatedPressable>
  );
}
