import * as React from "react";
import { View, Text, Image, Pressable } from "react-native";
import Animated, {
  useAnimatedStyle,
  withSpring,
  withDelay,
  useSharedValue,
  withTiming,
} from "react-native-reanimated";
import { useRouter } from "expo-router";
import { ProductStyles } from "@/styles/cafecard.style";
import { CafeCardInterface } from "@/types/product.type";
import { Colors } from "@/constants/theme";
import { useColorScheme } from "@/hooks/use-color-scheme";

const AnimatedPressable = Animated.createAnimatedComponent(Pressable);

export function CafeCard({
  id,
  name,
  description,
  price,
  imageUrl,
  origin,
  tags = [],
  badge,
  slug,
  index = 0,
}: CafeCardInterface) {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];
  const router = useRouter();

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
  }, [index, cardOpacity, cardTranslateY]);

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
        ProductStyles.card,
        { backgroundColor: colors.card },
        animatedCardStyle,
      ]}
    >
      {/* Image Section */}
      <View style={ProductStyles.imageContainer}>
        {imageUrl ? (
          <>
            <Image
              source={{ uri: imageUrl }}
              style={ProductStyles.image}
              resizeMode="cover"
            />
            <View style={ProductStyles.imageOverlay} />
            {badge && (
              <View style={[ProductStyles.badge, { backgroundColor: colors.accent }]}>
                <Text style={ProductStyles.badgeText}>{badge}</Text>
              </View>
            )}
          </>
        ) : (
          <View
            style={[
              ProductStyles.imagePlaceholder,
              { backgroundColor: colors.backgroundSecondary },
            ]}
          >
            <Text style={{ fontSize: 64 }}>☕</Text>
          </View>
        )}
      </View>

      {/* Content Section */}
      <View style={ProductStyles.content}>
        {/* Header: Title & Price */}
        <View style={ProductStyles.header}>
          <View style={ProductStyles.titleContainer}>
            <Text style={[ProductStyles.title, { color: colors.text }]}>
              {name}
            </Text>
            {origin && (
              <Text style={[ProductStyles.origin, { color: colors.textMuted }]}>
                {origin}
              </Text>
            )}
          </View>

          <View style={ProductStyles.priceContainer}>
            <Text style={[ProductStyles.price, { color: colors.accent }]}>
              {price}
            </Text>
            <Text
              style={[ProductStyles.priceLabel, { color: colors.textMuted }]}
            >
              par tasse
            </Text>
          </View>
        </View>

        {/* Divider */}
        <View
          style={[ProductStyles.divider, { backgroundColor: colors.cardBorder }]}
        />

        {/* Description */}
        <Text
          style={[ProductStyles.description, { color: colors.textSecondary }]}
          numberOfLines={2}
        >
          {description}
        </Text>

        {/* Footer: Tags & Action */}
        <View style={ProductStyles.footer}>
          <View style={ProductStyles.tagContainer}>
            {tags.slice(0, 3).map((tag, idx) => (
              <View
                key={idx}
                style={[ProductStyles.tag, { backgroundColor: colors.accentLight }]}
              >
                <Text style={[ProductStyles.tagText, { color: colors.accent }]}>
                  {tag}
                </Text>
              </View>
            ))}
          </View>

          <Pressable
            style={[
              ProductStyles.actionButton,
              { backgroundColor: colors.accent },
            ]}
            onPress={() => router.push(`/product/options?id=${id}`)}
          >
            <Text style={ProductStyles.actionButtonIcon}>→</Text>
          </Pressable>
        </View>
      </View>
    </AnimatedPressable>
  );
}
