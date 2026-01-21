<script setup lang="ts">
import type { Store } from '@/types/Store'

interface Props {
  store: Store
}

defineProps<Props>()

const emit = defineEmits<{
  detach: []
}>()
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
    <div class="flex items-start justify-between">
      <div class="flex-1">
        <h3 class="font-semibold text-gray-900 text-base mb-1">
          {{ store.name }}
        </h3>
        <p v-if="store.address" class="text-sm text-gray-600">
          <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
          {{ store.address }}
        </p>
      </div>
      <button
        @click.stop="emit('detach')"
        class="ml-3 px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition-colors"
        title="Dissocier ce magasin"
      >
        <i class="fas fa-unlink"></i>
      </button>
    </div>
  </div>
</template>
