<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink } from 'vue-router'
import Badge from '@/components/Badge.vue'
import type { ProductVariant } from '@/types/ProductVariant'

interface Props {
  productVariant: ProductVariant
  productId: number
}

const props = defineProps<Props>()

const getImageUrl = (url: string | undefined) => {
  return url || ''
}

const totalStock = computed(() => {
  if (!props.productVariant.store_stocks) return 0

  return props.productVariant.store_stocks.reduce((total, stock) => {
    if (stock.is_unlimited) return Infinity
    return total + (stock.stock || 0)
  }, 0)
})

const hasUnlimitedStock = computed(() => {
  return props.productVariant.store_stocks?.some(stock => stock.is_unlimited) || false
})
</script>

<template>
  <RouterLink
    :to="{ name: 'admin-product-variant-show', params: { productId: productId, id: productVariant.id } }"
    class="block bg-white rounded-xl overflow-hidden hover:shadow-lg transition-all duration-200 border border-gray-100"
  >
    <!-- Variant Image -->
    <div class="aspect-square bg-gray-100 relative overflow-hidden">
      <img
        v-if="productVariant.gallery && productVariant.gallery.length > 0"
        :src="getImageUrl(productVariant.gallery[0].url)"
        :alt="productVariant.sku"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <i class="fas fa-box text-gray-300 text-4xl"></i>
      </div>

      <!-- Default Badge -->
      <div v-if="productVariant.is_default" class="absolute top-2 right-2">
        <Badge variant="primary">
          <i class="fas fa-star mr-1"></i>
          Par défaut
        </Badge>
      </div>

      <!-- Gallery Count -->
      <div v-if="productVariant.gallery && productVariant.gallery.length > 1" class="absolute bottom-2 right-2">
        <div class="bg-black/60 text-white text-xs px-2 py-1 rounded-lg flex items-center gap-1">
          <i class="fas fa-images"></i>
          {{ productVariant.gallery.length }}
        </div>
      </div>
    </div>

    <!-- Variant Info -->
    <div class="p-4">
      <!-- Header -->
      <div class="flex items-start justify-between mb-2">
        <div class="flex-1 min-w-0">
          <h3 class="text-base font-semibold text-gray-900 truncate">{{ productVariant.sku }}</h3>
        </div>
      </div>

      <!-- Price -->
      <div class="flex items-center gap-4 mb-3">
        <div class="flex items-center gap-1.5 text-sm">
          <span class="text-gray-500">Prix HT:</span>
          <span class="font-semibold text-gray-900">
            {{ (productVariant.price_cent_ht / 100).toFixed(2) }} €
          </span>
        </div>
        <div v-if="productVariant.cost_price_cent_ht" class="flex items-center gap-1.5 text-xs text-gray-500">
          <span>Coût:</span>
          <span>{{ (productVariant.cost_price_cent_ht / 100).toFixed(2) }} €</span>
        </div>
      </div>

      <!-- Stock Info -->
      <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
        <div class="flex items-center gap-1.5 text-xs">
          <i class="fas fa-boxes text-gray-400"></i>
          <span v-if="hasUnlimitedStock" class="text-green-600 font-medium">Stock illimité</span>
          <span v-else-if="totalStock > 0" class="text-green-600 font-medium">
            {{ totalStock }} en stock
          </span>
          <span v-else class="text-red-600 font-medium">Rupture</span>
        </div>

        <div v-if="productVariant.store_stocks && productVariant.store_stocks.length > 0" class="flex items-center gap-1.5 text-xs text-gray-500">
          <i class="fas fa-store text-gray-400"></i>
          <span>{{ productVariant.store_stocks.length }} magasin{{ productVariant.store_stocks.length > 1 ? 's' : '' }}</span>
        </div>
      </div>
    </div>
  </RouterLink>
</template>
