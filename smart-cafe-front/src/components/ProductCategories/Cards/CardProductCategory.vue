<script setup lang="ts">
import { RouterLink } from 'vue-router'
import Badge from '@/components/Badge.vue'
import type { ProductCategory } from '@/types/ProductCategory'

interface Props {
  category: ProductCategory
}

defineProps<Props>()
</script>

<template>
  <RouterLink
    :to="{ name: 'admin-product-category-show', params: { id: category.id } }"
    class="block bg-white rounded-xl p-5 hover:shadow-lg transition-all duration-200 border border-gray-100"
  >
    <!-- Header -->
    <div class="flex items-start justify-between mb-3">
      <div class="flex-1">
        <h3 class="text-base font-semibold text-gray-900 mb-1">{{ category.name }}</h3>
        <p v-if="category.slug" class="text-xs text-gray-400 font-mono">{{ category.slug }}</p>
      </div>
      <Badge :variant="category.is_active ? 'success' : 'secondary'">
        {{ category.is_active ? 'Actif' : 'Inactif' }}
      </Badge>
    </div>

    <!-- Description -->
    <p v-if="category.description" class="text-sm text-gray-600 mb-3 line-clamp-2">
      {{ category.description }}
    </p>

    <!-- Footer Info -->
    <div class="flex items-center gap-4 pt-3 border-t border-gray-100">
      <div v-if="category.parent" class="flex items-center gap-1.5 text-xs text-gray-500">
        <i class="fas fa-folder text-gray-400"></i>
        <span>{{ category.parent.name }}</span>
      </div>
      <div v-if="category.products_count !== undefined" class="flex items-center gap-1.5 text-xs text-gray-500">
        <i class="fas fa-box text-gray-400"></i>
        <span>{{ category.products_count }} produit{{ category.products_count > 1 ? 's' : '' }}</span>
      </div>
      <div v-if="category.children && category.children.length > 0" class="flex items-center gap-1.5 text-xs text-gray-500">
        <i class="fas fa-sitemap text-gray-400"></i>
        <span>{{ category.children.length }} sous-catégorie{{ category.children.length > 1 ? 's' : '' }}</span>
      </div>
    </div>
  </RouterLink>
</template>
