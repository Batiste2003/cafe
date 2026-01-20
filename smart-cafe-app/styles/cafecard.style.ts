import { StyleSheet, Dimensions, Platform } from "react-native";
import { CoffeeColors } from "@/constants/theme";


// Premium Card Styles
export const CafeCardStyles = StyleSheet.create({
  card: {
    width: Dimensions.get("window").width - 40,
    marginHorizontal: 20,
    marginBottom: 24,
    borderRadius: 24,
    overflow: "hidden",
    backgroundColor: '#fff8e1',
    ...Platform.select({
      ios: {
        shadowColor: CoffeeColors.espresso,
        shadowOffset: { width: 0, height: 8 },
        shadowOpacity: 0.12,
        shadowRadius: 24,
      },
      android: {
        elevation: 8,
      },
    }),
  },
  imageContainer: {
    position: "relative",
    width: "100%",
    height: 220,
  },
  image: {
    width: "100%",
    height: "100%",
  },
  imageOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: "rgba(28, 18, 16, 0.08)",
  },
  imagePlaceholder: {
    width: "100%",
    height: "100%",
    justifyContent: "center",
    alignItems: "center",
  },
  badge: {
    position: "absolute",
    top: 16,
    left: 16,
    paddingHorizontal: 14,
    paddingVertical: 6,
    borderRadius: 20,
  },
  badgeText: {
    fontSize: 12,
    fontWeight: "700",
    letterSpacing: 0.5,
    textTransform: "uppercase",
  },
  content: {
    padding: 20,
  },
  header: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
    marginBottom: 10,
  },
  titleContainer: {
    flex: 1,
    marginRight: 12,
  },
  title: {
    fontSize: 22,
    fontWeight: "700",
    letterSpacing: -0.3,
    marginBottom: 4,
    color: '#000000',
  },
  origin: {
    fontSize: 13,
    fontWeight: "500",
    letterSpacing: 0.3,
    textTransform: "uppercase",
  },
  priceContainer: {
    alignItems: "flex-end",
  },
  price: {
    fontSize: 24,
    fontWeight: "800",
    letterSpacing: -0.5,
    color: '#2e7d32',
  },
  priceLabel: {
    fontSize: 11,
    fontWeight: "500",
    letterSpacing: 0.2,
    marginTop: 2,
  },
  divider: {
    height: 1,
    marginVertical: 16,
    opacity: 0.6,
  },
  description: {
    fontSize: 15,
    lineHeight: 24,
    fontWeight: "400",
    letterSpacing: 0.1,
    color: '#666',
  },
  footer: {
    flexDirection: "row",
    alignItems: "center",
    marginTop: 18,
  },
  tagContainer: {
    flexDirection: "row",
    flexWrap: "wrap",
    flex: 1,
    gap: 8,
  },
  tag: {
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 16,
  },
  tagText: {
    fontSize: 12,
    fontWeight: "600",
    letterSpacing: 0.2,
  },
  actionButton: {
    width: 48,
    height: 48,
    borderRadius: 24,
    justifyContent: "center",
    alignItems: "center",
    marginLeft: 12,
  },
  actionButtonIcon: {
    fontSize: 20,
  },
});

// Screen Container Styles
export const CardScreenStyles = StyleSheet.create({
  container: {
    flex: 1,
  },
  scrollContent: {
    paddingBottom: 40,
  },
  header: {
    paddingHorizontal: 20,
    paddingTop: Platform.OS === "ios" ? 60 : 40,
    paddingBottom: 28,
  },
  headerContent: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-end",
  },
  greeting: {
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
  headerAccent: {
    fontWeight: "800",
  },
  headerIcon: {
    width: 52,
    height: 52,
    borderRadius: 26,
    justifyContent: "center",
    alignItems: "center",
    ...Platform.select({
      ios: {
        shadowColor: CoffeeColors.caramel,
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 8,
      },
      android: {
        elevation: 4,
      },
    }),
  },
  headerIconText: {
    fontSize: 24,
  },
  sectionHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    paddingHorizontal: 20,
    marginBottom: 20,
    marginTop: 8,
  },
  sectionTitle: {
    fontSize: 18,
    fontWeight: "700",
    letterSpacing: -0.2,
  },
  sectionLink: {
    fontSize: 14,
    fontWeight: "600",
  },
  emptyState: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
    paddingVertical: 60,
  },
  emptyIcon: {
    fontSize: 48,
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 20,
    fontWeight: "700",
    marginBottom: 8,
  },
  emptyText: {
    fontSize: 15,
    textAlign: "center",
    maxWidth: 260,
    lineHeight: 22,
  },
});