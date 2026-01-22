// import { Colors } from "@/constants/theme";
import { Text, View, StyleSheet, useColorScheme, ScrollView } from "react-native";
import { CafeCardInterface } from "@/types/product.type";
import { Colors } from "@/constants/theme";
import { ProductCarouselItem } from "./ProductCarouselItem";

type Props = {
    products: CafeCardInterface[];
    categoryLabel: string;
};



export default function CarrouselProduct({ products, categoryLabel }: Props) {
    const colorScheme = useColorScheme() ?? "light";
    const colors = Colors[colorScheme];

    return (
        <View style={styles.container}>
            {products.length === 0 ? (
                <Text style={[styles.empty, { color: colors.textMuted }]}>
                    Aucun produit dans cette catégorie
                </Text>
            ) : (
                <ScrollView
                    horizontal
                    showsHorizontalScrollIndicator={false}
                    contentContainerStyle={styles.scrollContent}
                    pagingEnabled
                    snapToAlignment="center"
                >
                    {products.map((product) => (
                        <ProductCarouselItem
                            key={product.id}
                            icon="☕"
                            name={product.name}
                            price={`${product.price}`}
                        />
                    ))}
                </ScrollView>
            )}
        </View>
    );
}

const styles = StyleSheet.create({
    container: {
        marginTop: -80,
        alignItems: 'center',
    },
    scrollContent: {
        alignItems: 'center',
        justifyContent: 'center',
    },
    title: {
        marginHorizontal: 24,
        marginBottom: 10,
        fontSize: 18,
        fontWeight: '700',
    },
    empty: {
        marginHorizontal: 24,
        marginTop: 8,
        fontSize: 14,
    },
    card: {
        width: 170,
        borderRadius: 16,
        borderWidth: 1,
        padding: 12,
    },
    image: {
        width: '100%',
        height: 110,
        borderRadius: 12,
        marginBottom: 10,
    },
    name: {
        fontSize: 14,
        fontWeight: '700',
        marginBottom: 6,
    },
    price: {
        fontSize: 14,
        fontWeight: '800',
    },
});