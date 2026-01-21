<script setup lang="ts">
import { ref, computed } from 'vue'
import type { Store } from '@/types/Store'
import { usePostCreateVariantStock } from '@/composable/API/Admin/ProductVariants/usePostCreateVariantStock'

interface Props {
  productId: number
  variantId: number
  store: Store
}

const props = defineProps<Props>()

const emit = defineEmits<{
  created: []
}>()

const { isLoading, execute } = usePostCreateVariantStock()

const stock = ref<number | null>(0)
const isUnlimited = ref(false)

const isAdding = ref(false)

const displayStock = computed({
  get: () => (isUnlimited.value ? '' : stock.value?.toString() || '0'),
  set: (value: string) => {
    const parsed = parseInt(value)
    stock.value = isNaN(parsed) ? 0 : parsed
  }
})

const handleUnlimitedChange = () => {
  if (isUnlimited.value) {
    stock.value = null
  } else {
    stock.value = 0
  }
}

const handleAdd = async () => {
  isAdding.value = true

  const result = await execute(props.productId, props.variantId, {
    store_id: props.store.id,
    stock: isUnlimited.value ? null : stock.value
  })

  if (result.success) {
    emit('created')
    // Reset
    stock.value = 0
    isUnlimited.value = false
    isAdding.value = false
  } else {
    isAdding.value = false
  }
}

const handleCancel = () => {
  isAdding.value = false
  stock.value = 0
  isUnlimited.value = false
}
</script>

<template>
  <div class="bg-white border-2 border-dashed border-gray-300 rounded-lg p-4">
    <div v-if="!isAdding" class="text-center py-4">
      <div class="mb-3">
        <i class="fas fa-store text-2xl text-gray-400"></i>
      </div>
      <h3 class="font-semibold text-gray-900 mb-1">{{ store.name }}</h3>
      <p v-if="store.address" class="text-xs text-gray-500 mb-3">
        {{ store.address }}
      </p>
      <button
        @click="isAdding = true"
        class="px-3 py-1.5 text-sm font-medium text-[var(--cafe-primary)] bg-[var(--cafe-primary)]/10 rounded-lg hover:bg-[var(--cafe-primary)]/20 transition-colors"
      >
        <i class="fas fa-plus mr-1.5"></i>
        Configurer
      </button>
    </div>

    <div v-else class="space-y-3">
      <div>
        <h3 class="font-semibold text-gray-900 mb-1 text-sm">{{ store.name }}</h3>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1.5">
          Stock
        </label>
        <input
          v-model="displayStock"
          type="number"
          min="0"
          :disabled="isUnlimited || isLoading"
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)] disabled:bg-gray-100 disabled:cursor-not-allowed"
          placeholder="Quantité"
        />
      </div>

      <div class="flex items-center">
        <input
          id="unlimited-new"
          v-model="isUnlimited"
          type="checkbox"
          :disabled="isLoading"
          @change="handleUnlimitedChange"
          class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
        />
        <label for="unlimited-new" class="ml-2 block text-xs text-gray-700">
          Stock illimité
        </label>
      </div>

      <div class="flex items-center gap-2 pt-2 border-t border-gray-200">
        <button
          @click="handleCancel"
          :disabled="isLoading"
          class="flex-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
        >
          Annuler
        </button>
        <button
          @click="handleAdd"
          :disabled="isLoading"
          class="flex-1 px-3 py-1.5 text-xs font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-1.5"
        >
          <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-check"></i>
          Ajouter
        </button>
      </div>
    </div>
  </div>
</template>
