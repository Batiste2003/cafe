// import { Colors } from "@/constants/theme";
import { Text, View, StyleSheet } from "react-native";
import { CarrouselItem } from "./CarrouselCategory";

type CarrouselCategoryProps = {
    items: CarrouselItem[];
    selectedIndex: number;
}


export default function CarrouselProduct({
    items,
    selectedIndex,
}: CarrouselCategoryProps) {
    // const colorScheme = useColorScheme() ?? "light";
    // const colors = Colors[colorScheme];

    return (
        <View
            style={[
                styles.detailSection,
                // { backgroundColor: items[selectedIndex]?.icon },
            ]}
        >
            <Text style={styles.detailText}>
                Contenu pour {items[selectedIndex].id}
            </Text>
        </View>
    );
}

const styles = StyleSheet.create({
    detailSection: {
        marginTop: 16,
        marginHorizontal: 16,
        padding: 16,
        borderRadius: 16,
    },

    detailText: {
        fontSize: 16,
        fontWeight: '600',
    },
})