<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import Modal from '@/components/Modal.vue'
import CardStoreStock from './CardStoreStock.vue'
import CardAddStoreStock from './CardAddStoreStock.vue'
import { useGetVariantStocks } from '@/composable/API/Admin/ProductVariants/useGetVariantStocks'
import { useGetProductStores } from '@/composable/API/Admin/Products/useGetProductStores'

interface Props {
  productId: number
  variantId: number
  isOpen: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  stocksUpdated: []
}>()

const { stocks, isLoading, error, execute: fetchStocks } = useGetVariantStocks()
const { stores: productStores, isLoading: isLoadingStores, execute: fetchProductStores } = useGetProductStores()

const loadData = async () => {
  await Promise.all([
    fetchStocks(props.productId, props.variantId),
    fetchProductStores(props.productId)
  ])
}

// Stores that don't have stock configured yet
const availableStores = computed(() => {
  const stockedStoreIds = stocks.value.map(s => s.store_id)
  return productStores.value.filter(store => !stockedStoreIds.includes(store.id))
})

const handleStockUpdated = () => {
  loadData()
  emit('stocksUpdated')
}

const handleStockDeleted = () => {
  loadData()
  emit('stocksUpdated')
}

const handleStockCreated = () => {
  loadData()
  emit('stocksUpdated')
}

const handleClose = () => {
  emit('close')
}

// Load data when modal opens
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    loadData()
  }
})
</script>

<template>
  <Modal
    :is-open="isOpen"
    title="Gérer les stocks"
    size="large"
    @close="handleClose"
  >
    <div class="p-6">
      <!-- Loading State -->
      <div v-if="isLoading || isLoadingStores" class="flex items-center justify-center py-12">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin text-3xl text-[var(--cafe-primary)] mb-3"></i>
          <p class="text-sm text-gray-500">Chargement des stocks...</p>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-600">{{ error }}</p>
        <button
          type="button"
          @click="loadData"
          class="mt-3 px-4 py-2 text-sm font-medium text-red-700 bg-white border border-red-300 rounded-lg hover:bg-red-50 transition-colors"
        >
          <i class="fas fa-redo mr-2"></i>
          Réessayer
        </button>
      </div>

      <!-- Content -->
      <div v-else class="space-y-6">
        <!-- Configured Stocks -->
        <div v-if="stocks.length > 0">
          <div class="mb-4">
            <h3 class="text-sm font-medium text-gray-700 mb-1">
              Stocks configurés
            </h3>
            <p class="text-xs text-gray-500">
              {{ stocks.length }} magasin{{ stocks.length > 1 ? 's' : '' }} avec stock défini
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CardStoreStock
              v-for="stock in stocks"
              :key="stock.id"
              :product-id="productId"
              :variant-id="variantId"
              :store-stock="stock"
              @updated="handleStockUpdated"
              @deleted="handleStockDeleted"
            />
          </div>
        </div>

        <!-- Available Stores (not yet configured) -->
        <div v-if="availableStores.length > 0">
          <div class="mb-4" :class="{ 'pt-6 border-t border-gray-200': stocks.length > 0 }">
            <h3 class="text-sm font-medium text-gray-700 mb-1">
              Magasins disponibles
            </h3>
            <p class="text-xs text-gray-500">
              {{ availableStores.length }} magasin{{ availableStores.length > 1 ? 's' : '' }} sans stock configuré
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <CardAddStoreStock
              v-for="store in availableStores"
              :key="store.id"
              :product-id="productId"
              :variant-id="variantId"
              :store="store"
              @created="handleStockCreated"
            />
          </div>
        </div>

        <!-- Empty State (no stores at all) -->
        <div v-if="stocks.length === 0 && availableStores.length === 0" class="text-center py-12">
          <i class="fas fa-store-slash text-4xl text-gray-300 mb-3"></i>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun magasin associé</h3>
          <p class="text-sm text-gray-500">
            Ce produit n'est associé à aucun magasin. Associez d'abord des magasins au produit.
          </p>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
        <button
          type="button"
          @click="handleClose"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
        >
          Fermer
        </button>
      </div>
    </div>
  </Modal>
</template>
