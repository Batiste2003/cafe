import React from 'react';
import { View, Text, StyleSheet, Dimensions } from 'react-native';
import { useColorScheme } from '@/hooks/use-color-scheme';
import { Colors } from '@/constants/theme';

type ProductCarouselItemProps = {
    icon: React.ReactNode;
    name: string;
    price: string;
};

const screenWidth = Dimensions.get('window').width;

export function ProductCarouselItem({
    icon,
    name,
    price,
}: ProductCarouselItemProps) {
    const colorScheme = useColorScheme() ?? 'light';
    const colors = Colors[colorScheme];

    return (
        <View style={[styles.card, { width: screenWidth }]}>
            <View style={[styles.iconWrapper, { backgroundColor: colors.accent }]}>
                {/* TODO: Replace by imageUrl */}
                <Text style={{ fontSize: 100 }}>{icon}</Text>
            </View>

            <View style={{ alignItems: 'center', gap: 8 }}>
                <Text style={[styles.name, { color: colors.text }]} numberOfLines={2}>
                    {name}
                </Text>
                <Text style={{ fontSize: 14, color: colors.textMuted }}>
                    {price}
                </Text>
            </View>
        </View >
    );
}

const styles = StyleSheet.create({
    card: {
        alignItems: 'center',
        gap: 12,
        paddingVertical: 32,
        overflow: 'hidden',
    },
    iconWrapper: {
        width: 140,
        height: 140,
        borderRadius: 70,
        justifyContent: 'center',
        alignItems: 'center',
    },
    name: {
        fontSize: 24,
        fontWeight: '600',
        textAlign: 'center',
        letterSpacing: 0.5,
    },
});