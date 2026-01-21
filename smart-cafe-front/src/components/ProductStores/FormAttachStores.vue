<script setup lang="ts">
import { ref, onMounted } from 'vue'
import type { Store } from '@/types/Store'
import { useGetIndexStore } from '@/composable/API/Admin/Stores/useGetIndexStore'
import { usePostAttachProductToStores } from '@/composable/API/Admin/Products/usePostAttachProductToStores'

interface Props {
  productId: number
  excludeStoreIds?: number[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  saved: []
  cancel: []
}>()

const { stores: allStores, isLoading: isLoadingStores, execute: fetchStores } = useGetIndexStore()
const { isLoading: isAttaching, validationErrors, execute: attachStores, resetValidation } = usePostAttachProductToStores()

const selectedStoreIds = ref<number[]>([])

const availableStores = ref<Store[]>([])

const loadStores = async () => {
  await fetchStores()

  // Filter out already attached stores
  if (props.excludeStoreIds && props.excludeStoreIds.length > 0) {
    availableStores.value = allStores.value.filter(
      store => !props.excludeStoreIds?.includes(store.id)
    )
  } else {
    availableStores.value = allStores.value
  }
}

const toggleStore = (storeId: number) => {
  const index = selectedStoreIds.value.indexOf(storeId)
  if (index > -1) {
    selectedStoreIds.value.splice(index, 1)
  } else {
    selectedStoreIds.value.push(storeId)
  }
}

const handleSubmit = async () => {
  resetValidation()

  const result = await attachStores(props.productId, {
    store_ids: selectedStoreIds.value
  })

  if (result.success) {
    emit('saved')
  }
}

const handleCancel = () => {
  emit('cancel')
}

onMounted(() => {
  loadStores()
})
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Loading State -->
    <div v-if="isLoadingStores" class="flex items-center justify-center py-8">
      <i class="fas fa-spinner fa-spin text-2xl text-[var(--cafe-primary)]"></i>
    </div>

    <!-- Store Selection -->
    <div v-else>
      <label class="block text-sm font-medium text-gray-700 mb-3">
        Sélectionner les magasins <span class="text-red-500">*</span>
      </label>

      <div v-if="availableStores.length === 0" class="text-center py-8 bg-gray-50 rounded-lg border border-gray-200">
        <i class="fas fa-store-slash text-3xl text-gray-300 mb-2"></i>
        <p class="text-sm text-gray-600">
          Tous les magasins sont déjà associés à ce produit
        </p>
      </div>

      <div v-else class="space-y-2 max-h-96 overflow-y-auto">
        <div
          v-for="store in availableStores"
          :key="store.id"
          @click="toggleStore(store.id)"
          class="flex items-start p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors"
          :class="{
            'bg-[var(--cafe-primary)]/5 border-[var(--cafe-primary)]': selectedStoreIds.includes(store.id)
          }"
        >
          <input
            type="checkbox"
            :checked="selectedStoreIds.includes(store.id)"
            class="mt-0.5 w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
            @click.stop="toggleStore(store.id)"
          />
          <div class="ml-3 flex-1">
            <p class="font-medium text-gray-900">{{ store.name }}</p>
            <p v-if="store.address" class="text-sm text-gray-600 mt-0.5">
              {{ store.address }}
            </p>
          </div>
        </div>
      </div>

      <p v-if="validationErrors.store_ids" class="mt-2 text-sm text-red-600">
        {{ validationErrors.store_ids }}
      </p>

      <p v-if="selectedStoreIds.length > 0" class="mt-2 text-sm text-gray-600">
        {{ selectedStoreIds.length }} magasin(s) sélectionné(s)
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
      <button
        type="button"
        @click="handleCancel"
        :disabled="isAttaching"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
      >
        Annuler
      </button>
      <button
        type="submit"
        :disabled="isAttaching || selectedStoreIds.length === 0"
        class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2"
      >
        <i v-if="isAttaching" class="fas fa-spinner fa-spin"></i>
        <i v-else class="fas fa-link"></i>
        Associer
      </button>
    </div>
  </form>
</template>
