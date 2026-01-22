import { Colors } from "@/constants/theme";
import React from "react";
import { Pressable, useColorScheme, View, StyleSheet, Text, Dimensions } from "react-native";
import Svg, { Path } from "react-native-svg";

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

const { width: SCREEN_WIDTH } = Dimensions.get('window');
const ARC_RADIUS = 180;
const START_ANGLE = 225;
const END_ANGLE = 315;
const ITEM_WIDTH = 90;

export default function CarrouselCategory({
    items,
    selectedIndex,
    onSelect,
}: CarrouselCategoryProps) {
    const colorScheme = useColorScheme() ?? "light";
    const colors = Colors[colorScheme];

    // Calcule la position sur l'arc pour chaque item
    const getArcPosition = (index: number, total: number) => {
        const angleRange = END_ANGLE - START_ANGLE;
        const angle = START_ANGLE + (angleRange / (total - 1 || 1)) * index;
        const radians = (angle * Math.PI) / 180;
        
        const centerX = SCREEN_WIDTH / 2;
        const centerY = ARC_RADIUS + 30;
        
        // Position centrée sur l'item
        const left = centerX + Math.cos(radians) * ARC_RADIUS - ITEM_WIDTH / 2;
        const top = centerY + Math.sin(radians) * ARC_RADIUS - 24; // -24 pour centrer l'icône
        
        return { left, top, angle };
    };

    // Génère le path SVG pour l'arc de fond crème
    const generateArcPath = () => {
        const centerX = SCREEN_WIDTH / 2;
        const centerY = 0;
        const outerRadius = ARC_RADIUS + 60; // Plus grand pour englober les icônes
        
        const startRad = (START_ANGLE * Math.PI) / 180;
        const endRad = (END_ANGLE * Math.PI) / 180;
        
        const startX = centerX + Math.cos(startRad) * outerRadius;
        const startY = centerY + Math.sin(startRad) * outerRadius + ARC_RADIUS;
        const endX = centerX + Math.cos(endRad) * outerRadius;
        const endY = centerY + Math.sin(endRad) * outerRadius + ARC_RADIUS;
        
        // Arc path avec une belle courbe
        return `M ${startX} ${startY} A ${outerRadius} ${outerRadius} 0 0 1 ${endX} ${endY}`;
    };

    // Génère le path pour l'indicateur de sélection (petit arc au-dessus de l'icône)
    const generateIndicatorPath = (index: number, total: number) => {
        const centerX = SCREEN_WIDTH / 2;
        const angleRange = END_ANGLE - START_ANGLE;
        const centerAngle = START_ANGLE + (angleRange / (total - 1 || 1)) * index;
        
        // Arc de 15° de chaque côté de l'icône
        const arcSpan = 12;
        const indicatorRadius = ARC_RADIUS - 10; // Légèrement à l'intérieur de l'arc principal
        
        const startAngle = centerAngle - arcSpan;
        const endAngle = centerAngle + arcSpan;
        
        const startRad = (startAngle * Math.PI) / 180;
        const endRad = (endAngle * Math.PI) / 180;
        
        const startX = centerX + Math.cos(startRad) * indicatorRadius;
        const startY = Math.sin(startRad) * indicatorRadius + ARC_RADIUS;
        const endX = centerX + Math.cos(endRad) * indicatorRadius;
        const endY = Math.sin(endRad) * indicatorRadius + ARC_RADIUS;
        
        return `M ${startX} ${startY} A ${indicatorRadius} ${indicatorRadius} 0 0 1 ${endX} ${endY}`;
    };

    return (
        <View style={styles.arcContainer}>
            {/* Arc de fond crème */}
            <Svg 
                width={SCREEN_WIDTH} 
                height={220} 
                style={styles.svgBackground}
            >
                <Path
                    d={generateArcPath()}
                    stroke={colors.backgroundSecondary}
                    strokeWidth={120}
                    fill="none"
                    strokeLinecap="round"
                />
                {/* Indicateur de sélection sur l'arc */}
                <Path
                    d={generateIndicatorPath(selectedIndex, items.length)}
                    stroke={colors.background}
                    strokeWidth={8}
                    fill="none"
                    strokeLinecap="round"
                />
            </Svg>

            {/* Items positionnés sur l'arc */}
            {items.map((item, index) => {
                const isSelected = index === selectedIndex;
                const position = getArcPosition(index, items.length);
                
                return (
                    <Pressable
                        key={item.id}
                        onPress={() => onSelect && onSelect(index)}
                        style={[
                            styles.item,
                            {
                                left: position.left,
                                top: position.top,
                            },
                        ]}
                    >
                        <View style={[
                            styles.headerIcon, 
                            { backgroundColor: colors.background },
                            isSelected && styles.headerIconSelected
                        ]}>
                            <Text style={[
                                styles.headerIconSize,
                                isSelected && styles.headerIconSizeSelected
                            ]}>{item.icon}</Text>
                        </View>
                        <Text
                            numberOfLines={2}
                            style={[styles.headerIconText, { color: colors.backgroundSecondary }]}
                        >
                            {item.text}
                        </Text>
                    </Pressable>
                );
            })}
        </View>
    );
}

const styles = StyleSheet.create({
    arcContainer: {
        height: 220,
        width: '100%',
    },
    svgBackground: {
        position: 'absolute',
        top: 0,
        left: 0,
    },
    item: {
        position: 'absolute',
        gap: 6,
        alignItems: 'center',
        width: ITEM_WIDTH,
    },
    headerIcon: {
        width: 48,
        height: 48,
        borderRadius: 24,
        justifyContent: "center",
        alignItems: "center",
    },
    headerIconSelected: {
        width: 56,
        height: 56,
        borderRadius: 28,
        borderWidth: 3,
        borderColor: '#C69C6D',
        shadowColor: '#000',
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 6,
        elevation: 6,
    },
    headerIconSize: {
        fontSize: 22,
    },
    headerIconSizeSelected: {
        fontSize: 28,
    },
    headerIconText: {
        fontSize: 10,
        fontWeight: "700",
        textTransform: "uppercase",
        textAlign: 'center',
    },
});