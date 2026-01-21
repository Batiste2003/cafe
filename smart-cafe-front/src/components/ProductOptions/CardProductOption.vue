<script setup lang="ts">
import type { ProductOption } from '@/types/ProductOption'
import Badge from '@/components/Badge.vue'

interface Props {
  productOption: ProductOption
}

defineProps<Props>()

const emit = defineEmits<{
  edit: []
  delete: []
}>()
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between mb-3">
      <div class="flex-1">
        <h3 class="font-semibold text-gray-900 text-lg mb-1">
          {{ productOption.name }}
        </h3>
        <div class="flex items-center gap-2 mt-2">
          <Badge :variant="productOption.is_required ? 'danger' : 'neutral'">
            {{ productOption.is_required ? 'Obligatoire' : 'Optionnel' }}
          </Badge>
          <span v-if="productOption.values && productOption.values.length > 0" class="text-xs text-gray-500">
            {{ productOption.values.length }} valeur{{ productOption.values.length > 1 ? 's' : '' }}
          </span>
        </div>
      </div>
    </div>

    <div v-if="productOption.values && productOption.values.length > 0" class="mb-3">
      <div class="flex flex-wrap gap-1.5">
        <span
          v-for="value in productOption.values.slice(0, 5)"
          :key="value.id"
          class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700 rounded"
        >
          {{ value.value }}
        </span>
        <span
          v-if="productOption.values.length > 5"
          class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded"
        >
          +{{ productOption.values.length - 5 }}
        </span>
      </div>
    </div>

    <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
      <button
        @click.stop="emit('edit')"
        class="flex-1 px-3 py-1.5 text-sm font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors"
      >
        <i class="fas fa-edit mr-1.5"></i>
        Modifier
      </button>
      <button
        @click.stop="emit('delete')"
        class="flex-1 px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition-colors"
      >
        <i class="fas fa-trash mr-1.5"></i>
        Supprimer
      </button>
    </div>
  </div>
</template>
