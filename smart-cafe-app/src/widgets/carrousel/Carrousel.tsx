import React, { useState } from 'react';
import CarrouselCategory from './CarrouselCategory';
import { View } from 'react-native';
import CarrouselProduct from './CarrouselProduct';

export default function Carrousel() {
  const items = [
    { id: 'coffee', icon: '☕', text: 'coffee' },
    { id: 'pastries', icon: '🥐', text: 'pastries' },
    { id: 'mactha', icon: '🍵', text: 'mactha', },
    { id: 'bakerie', icon: '🧁', text: 'bakerie' }
  ];

  const [selectedIndex, setSelectedIndex] = useState(0);

  return (
    <View>
      <CarrouselCategory
        items={items}
        selectedIndex={selectedIndex}
        onSelect={setSelectedIndex}
      />

      {/* Product carrousel */}
      <CarrouselProduct items={items} selectedIndex={selectedIndex} />
    </View >
  );
}