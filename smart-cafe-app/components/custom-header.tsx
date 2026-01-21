import { Text, View, StyleSheet } from "react-native";
import { Colors } from "@/constants/theme";
import { useColorScheme } from "@/hooks/use-color-scheme";

type CustomHeaderProps = {
  title: string;
  subtitle?: string;
  icon?: string;
};

export function CustomHeader({
  subtitle = "Bienvenue chez",
  title,
  icon = "☕",
}: CustomHeaderProps) {
  const colorScheme = useColorScheme() ?? "light";
  const colors = Colors[colorScheme];

  return (
    <View style={[styles.header]}>
      <View>
        <Text style={[styles.greetingText, { color: colors.textMuted }]}>
          {subtitle}
        </Text>
        <Text style={[styles.headerTitle, { color: colors.text }]}>
          {title}
        </Text>
      </View>

      {icon ? (
        <View
          style={[styles.headerIcon, { backgroundColor: colors.accent }]}
        >
          <Text style={styles.headerIconText}>{icon}</Text>
        </View>
      ) : null}
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginHorizontal: 16,
    marginVertical: 8,
  },
  greetingText: {
    fontSize: 15,
    fontWeight: "500",
    letterSpacing: 0.3,
    marginBottom: 4,
  },
  headerTitle: {
    fontSize: 34,
    fontWeight: "800",
    letterSpacing: -1,
  },
  headerIcon: {
    width: 52,
    height: 52,
    borderRadius: 26,
    justifyContent: "center",
    alignItems: "center",
  },
  headerIconText: {
    fontSize: 24,
  },
});