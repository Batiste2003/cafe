<script setup lang="ts">
import { RouterLink } from 'vue-router'
import Badge from '@/components/Badge.vue'
import type { Product } from '@/types/Product'

interface Props {
  product: Product
}

defineProps<Props>()

const getImageUrl = (url: string | undefined) => {
  return url || ''
}
</script>

<template>
  <RouterLink
    :to="{ name: 'admin-product-show', params: { id: product.id } }"
    class="block bg-white rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200 border border-gray-100"
  >
    <!-- Product Image -->
    <div class="aspect-square bg-gray-100 relative overflow-hidden">
      <img
        v-if="product.gallery && product.gallery.length > 0"
        :src="getImageUrl(product.gallery[0].url)"
        :alt="product.name"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <i class="fas fa-box text-gray-300 text-4xl"></i>
      </div>

      <!-- Featured Badge -->
      <div v-if="product.is_featured" class="absolute top-2 right-2">
        <Badge variant="warning">
          <i class="fas fa-star mr-1"></i>
          Featured
        </Badge>
      </div>

      <!-- Gallery Count -->
      <div v-if="product.gallery && product.gallery.length > 1" class="absolute bottom-2 right-2">
        <div class="bg-black/60 text-white text-xs px-2 py-1 rounded-lg flex items-center gap-1">
          <i class="fas fa-images"></i>
          {{ product.gallery.length }}
        </div>
      </div>
    </div>

    <!-- Product Info -->
    <div class="p-4">
      <!-- Header -->
      <div class="flex items-start justify-between mb-2">
        <div class="flex-1 min-w-0">
          <h3 class="text-base font-semibold text-gray-900 truncate">{{ product.name }}</h3>
          <p v-if="product.slug" class="text-xs text-gray-400 font-mono truncate">{{ product.slug }}</p>
        </div>
        <Badge :variant="product.is_active ? 'success' : 'neutral'" class="ml-2 flex-shrink-0">
          {{ product.is_active ? 'Actif' : 'Inactif' }}
        </Badge>
      </div>

      <!-- Description -->
      <p v-if="product.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
        {{ product.description }}
      </p>

      <!-- Category -->
      <div v-if="product.category" class="flex items-center gap-1.5 text-xs text-gray-500 mb-2">
        <i class="fas fa-tag text-gray-400"></i>
        <span>{{ product.category.name }}</span>
      </div>

      <!-- Footer Info -->
      <div class="flex items-center gap-4 pt-3 border-t border-gray-100">
        <div v-if="product.default_variant" class="flex items-center gap-1.5 text-xs text-gray-700 font-medium">
          <i class="fas fa-euro-sign text-gray-400"></i>
          <span>{{ (product.default_variant.price / 100).toFixed(2) }} €</span>
        </div>
        <div v-if="product.variants_count !== undefined" class="flex items-center gap-1.5 text-xs text-gray-500">
          <i class="fas fa-boxes text-gray-400"></i>
          <span>{{ product.variants_count }} variant{{ product.variants_count > 1 ? 's' : '' }}</span>
        </div>
        <div v-if="product.stores && product.stores.length > 0" class="flex items-center gap-1.5 text-xs text-gray-500">
          <i class="fas fa-store text-gray-400"></i>
          <span>{{ product.stores.length }} magasin{{ product.stores.length > 1 ? 's' : '' }}</span>
        </div>
      </div>
    </div>
  </RouterLink>
</template>
