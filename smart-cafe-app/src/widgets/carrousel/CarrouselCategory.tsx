import { Colors } from "@/constants/theme";
import React from "react";
import { Pressable, ScrollView, useColorScheme, View, StyleSheet, Text } from "react-native";

export type CarrouselItem = {
    id: string;
    icon: React.ReactNode;
    text: string;
};

type CarrouselCategoryProps = {
    items: CarrouselItem[];
    selectedIndex: number;
    onSelect?: (index: number) => void;
};

export default function CarrouselCategory({
    items,
    selectedIndex,
    onSelect,
}: CarrouselCategoryProps) {
    const colorScheme = useColorScheme() ?? "light";
    const colors = Colors[colorScheme];

    return (
        <View style={{ backgroundColor: colors.accent }}>
            <ScrollView
                horizontal
                showsHorizontalScrollIndicator={false}
                contentContainerStyle={styles.carrousel}
            >
                {items.map((item, index) => {
                    const isSelected = index === selectedIndex;

                    return (
                        <Pressable
                            key={item.id}
                            onPress={() => onSelect && onSelect(index)}
                            style={styles.item}
                        >
                            {isSelected &&
                                <View style={[styles.selected, { backgroundColor: colors.background }]}></View>
                            }
                            <View style={[styles.headerIcon, { backgroundColor: colors.background }]}>
                                <Text style={styles.headerIconSize}>{item.icon}</Text>
                            </View>
                            <Text style={[styles.headerIconText, { color: colors.backgroundSecondary }]}>{item.text}</Text>
                        </Pressable>
                    );
                })}
            </ScrollView>
        </View>
    )
}

const styles = StyleSheet.create({
    carrousel: {
        paddingHorizontal: 16,
        flexDirection: 'row',
        gap: 28
    },
    item: {
        gap: 8,
        alignItems: 'center',
        justifyContent: 'flex-end',
        paddingVertical: 8,
    },
    headerIcon: {
        width: 64,
        height: 64,
        borderRadius: 100,
        justifyContent: "center",
        alignItems: "center",
    },
    headerIconSize: {
        fontSize: 32,
    },
    headerIconText: {
        fontSize: 12,
        fontWeight: "800",
        textTransform: "uppercase",
    },
    selected: {
        height: 6,
        width: '100%'
    }
})