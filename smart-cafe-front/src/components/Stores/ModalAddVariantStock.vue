<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import Modal from '@/components/Modal.vue'
import { useGetStoreProducts } from '@/composable/API/Admin/Stores/useGetStoreProducts'
import { usePutUpdateVariantStock } from '@/composable/API/Admin/ProductVariants/usePutUpdateVariantStock'
import type { StoreProductVariant } from '@/types/ProductVariant'

interface Props {
  isOpen: boolean
  storeId: number
  existingStocks: StoreProductVariant[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  close: []
  stockAdded: []
}>()

const { products, isLoading: isLoadingProducts, execute: fetchProducts } = useGetStoreProducts()
const { execute: updateStock, isLoading: isUpdating } = usePutUpdateVariantStock()

const selectedVariantId = ref<number | null>(null)
const selectedProductId = ref<number | null>(null)
const stockValue = ref<number>(0)
const isUnlimited = ref(false)

// Charger les produits quand le modal s'ouvre
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    fetchProducts(props.storeId)
  }
})

// Filtrer les variantes qui n'ont pas encore de stock
const availableVariants = computed(() => {
  const existingVariantIds = props.existingStocks.map(s => s.product_variant_id)
  const variants: Array<{ id: number; sku: string; productId: number; productName: string }> = []

  products.value.forEach(product => {
    product.variants?.forEach(variant => {
      if (!existingVariantIds.includes(variant.id)) {
        variants.push({
          id: variant.id,
          sku: variant.sku,
          productId: product.id,
          productName: product.name
        })
      }
    })
  })

  return variants
})

const handleUnlimitedChange = () => {
  if (isUnlimited.value) {
    stockValue.value = 0
  }
}

const handleSubmit = async () => {
  if (!selectedVariantId.value || !selectedProductId.value) return

  const result = await updateStock(
    selectedProductId.value,
    selectedVariantId.value,
    props.storeId,
    {
      stock: isUnlimited.value ? null : stockValue.value
    }
  )

  if (result.success) {
    selectedVariantId.value = null
    selectedProductId.value = null
    stockValue.value = 0
    isUnlimited.value = false
    emit('stockAdded')
  }
}

const handleCancel = () => {
  selectedVariantId.value = null
  selectedProductId.value = null
  stockValue.value = 0
  isUnlimited.value = false
  emit('close')
}

const handleVariantSelect = (variantId: number, productId: number) => {
  selectedVariantId.value = variantId
  selectedProductId.value = productId
}
</script>

<template>
  <Modal :is-open="isOpen" title="Ajouter un stock de variante" @close="handleCancel">
    <div class="space-y-4">
      <!-- Selection de variante -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          Sélectionner une variante
        </label>

        <div v-if="isLoadingProducts" class="text-center py-4">
          <i class="fas fa-spinner fa-spin text-[var(--cafe-primary)]"></i>
          <p class="text-xs text-gray-500 mt-2">Chargement des variantes...</p>
        </div>

        <div v-else-if="availableVariants.length === 0" class="text-center py-8">
          <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-check-circle text-gray-400 text-xl" />
          </div>
          <p class="text-sm text-gray-500">Toutes les variantes ont déjà un stock configuré</p>
        </div>

        <div v-else class="space-y-2 max-h-64 overflow-y-auto">
          <button
            v-for="variant in availableVariants"
            :key="variant.id"
            @click="handleVariantSelect(variant.id, variant.productId)"
            :class="[
              'w-full text-left p-3 rounded-lg border-2 transition-all',
              selectedVariantId === variant.id
                ? 'border-[var(--cafe-primary)] bg-[var(--cafe-primary)]/5'
                : 'border-gray-200 hover:border-gray-300'
            ]"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-900">{{ variant.sku }}</p>
                <p class="text-xs text-gray-500">{{ variant.productName }}</p>
              </div>
              <i
                v-if="selectedVariantId === variant.id"
                class="fas fa-check-circle text-[var(--cafe-primary)]"
              ></i>
            </div>
          </button>
        </div>
      </div>

      <!-- Configuration du stock -->
      <div v-if="selectedVariantId" class="space-y-4 pt-4 border-t border-gray-200">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Quantité en stock
          </label>
          <input
            v-model.number="stockValue"
            type="number"
            min="0"
            :disabled="isUnlimited"
            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)] disabled:bg-gray-100 disabled:cursor-not-allowed"
            placeholder="Quantité"
          />
        </div>

        <div class="flex items-center">
          <input
            id="unlimited-add"
            v-model="isUnlimited"
            type="checkbox"
            @change="handleUnlimitedChange"
            class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
          />
          <label for="unlimited-add" class="ml-2 block text-sm text-gray-700">
            Stock illimité
          </label>
        </div>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3 pt-4">
        <button
          @click="handleCancel"
          :disabled="isUpdating"
          class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
        >
          Annuler
        </button>
        <button
          @click="handleSubmit"
          :disabled="!selectedVariantId || isUpdating"
          class="flex-1 px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-2"
        >
          <i v-if="isUpdating" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-plus"></i>
          Ajouter
        </button>
      </div>
    </div>
  </Modal>
</template>
