<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import { usePutUpdateProductVariant } from '@/composable/API/Admin/ProductVariants/usePutUpdateProductVariant'
import type { PutUpdateProductVariantInterface } from '@/composable/API/Admin/ProductVariants/usePutUpdateProductVariant'
import type { ProductVariant } from '@/types/ProductVariant'

interface Props {
  productId: number
  variant: ProductVariant
}

const props = defineProps<Props>()

const emit = defineEmits<{
  variantUpdated: []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePutUpdateProductVariant()

const formData = ref<PutUpdateProductVariantInterface>({
  sku: props.variant.sku,
  price_cent_ht: props.variant.price_cent_ht,
  cost_price_cent_ht: props.variant.cost_price_cent_ht,
  is_default: props.variant.is_default,
})

const priceEuros = ref<string>((props.variant.price_cent_ht / 100).toFixed(2))
const costPriceEuros = ref<string>(
  props.variant.cost_price_cent_ht ? (props.variant.cost_price_cent_ht / 100).toFixed(2) : ''
)

const hasCostPrice = ref(props.variant.cost_price_cent_ht !== null)

// Convertir euros en centimes
const updatePriceFromEuros = () => {
  const euros = parseFloat(priceEuros.value) || 0
  formData.value.price_cent_ht = Math.round(euros * 100)
}

const updateCostPriceFromEuros = () => {
  if (!hasCostPrice.value) {
    formData.value.cost_price_cent_ht = null
    costPriceEuros.value = ''
    return
  }
  const euros = parseFloat(costPriceEuros.value) || 0
  formData.value.cost_price_cent_ht = Math.round(euros * 100)
}

const handleSubmit = async () => {
  resetValidation()
  updatePriceFromEuros()
  updateCostPriceFromEuros()

  const result = await execute(props.productId, props.variant.id, formData.value)

  if (result.success) {
    emit('variantUpdated')
  }
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <div class="space-y-4">
    <!-- SKU -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        SKU <span class="text-red-500">*</span>
      </label>
      <input
        v-model="formData.sku"
        type="text"
        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
        :class="{ 'border-red-500': validationErrors.sku }"
        placeholder="SKU-12345"
      />
      <p v-if="validationErrors.sku" class="mt-1 text-sm text-red-500">
        {{ validationErrors.sku }}
      </p>
    </div>

    <!-- Prix HT -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">
        Prix HT (€) <span class="text-red-500">*</span>
      </label>
      <input
        v-model="priceEuros"
        type="number"
        step="0.01"
        min="0"
        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
        :class="{ 'border-red-500': validationErrors.price_cent_ht }"
        placeholder="0.00"
      />
      <p v-if="validationErrors.price_cent_ht" class="mt-1 text-sm text-red-500">
        {{ validationErrors.price_cent_ht }}
      </p>
    </div>

    <!-- Prix de revient -->
    <div>
      <div class="flex items-center mb-2">
        <input
          v-model="hasCostPrice"
          type="checkbox"
          class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)]"
          @change="updateCostPriceFromEuros"
        />
        <label class="ml-2 text-sm font-medium text-gray-700">
          Définir un prix de revient
        </label>
      </div>
      <input
        v-if="hasCostPrice"
        v-model="costPriceEuros"
        type="number"
        step="0.01"
        min="0"
        class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
        :class="{ 'border-red-500': validationErrors.cost_price_cent_ht }"
        placeholder="0.00"
      />
      <p v-if="validationErrors.cost_price_cent_ht" class="mt-1 text-sm text-red-500">
        {{ validationErrors.cost_price_cent_ht }}
      </p>
    </div>

    <!-- Variante par défaut -->
    <div class="flex items-center">
      <input
        v-model="formData.is_default"
        type="checkbox"
        class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)]"
      />
      <label class="ml-2 text-sm font-medium text-gray-700">
        Définir comme variante par défaut
      </label>
    </div>

    <!-- Error Message -->
    <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
      <p class="text-sm text-red-600">{{ error }}</p>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
      <button
        type="button"
        @click="handleCancel"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
        :disabled="isLoading"
      >
        Annuler
      </button>
      <button
        type="button"
        @click="handleSubmit"
        class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        :disabled="isLoading"
      >
        <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
        <i v-else class="fas fa-save"></i>
        {{ isLoading ? 'Enregistrement...' : 'Enregistrer' }}
      </button>
    </div>
  </div>
</template>
