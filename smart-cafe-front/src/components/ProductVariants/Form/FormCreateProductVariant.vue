<script setup lang="ts">
import { ref, computed } from 'vue'
import { usePostStoreProductVariant } from '@/composable/API/Admin/ProductVariants/usePostStoreProductVariant'
import type { PostStoreProductVariantInterface } from '@/composable/API/Admin/ProductVariants/usePostStoreProductVariant'

interface Props {
  productId: number
}

const props = defineProps<Props>()

const emit = defineEmits<{
  variantCreated: []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePostStoreProductVariant()

const formData = ref<PostStoreProductVariantInterface>({
  sku: '',
  price_cent_ht: 0,
  cost_price_cent_ht: undefined,
  is_default: false,
  images: [],
})

const priceEuros = ref<string>('0.00')
const costPriceEuros = ref<string>('')

const imageFiles = ref<File[]>([])
const imagePreviews = ref<string[]>([])

const hasCostPrice = ref(false)

// Convertir euros en centimes
const updatePriceFromEuros = () => {
  const euros = parseFloat(priceEuros.value) || 0
  formData.value.price_cent_ht = Math.round(euros * 100)
}

const updateCostPriceFromEuros = () => {
  if (!hasCostPrice.value) {
    formData.value.cost_price_cent_ht = undefined
    costPriceEuros.value = ''
    return
  }
  const euros = parseFloat(costPriceEuros.value) || 0
  formData.value.cost_price_cent_ht = Math.round(euros * 100)
}

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const newFiles = Array.from(target.files)
    imageFiles.value.push(...newFiles)
    formData.value.images = imageFiles.value

    // Generate previews
    newFiles.forEach((file) => {
      const reader = new FileReader()
      reader.onload = (e) => {
        if (e.target?.result) {
          imagePreviews.value.push(e.target.result as string)
        }
      }
      reader.readAsDataURL(file)
    })
  }
}

const removeImage = (index: number) => {
  imageFiles.value.splice(index, 1)
  imagePreviews.value.splice(index, 1)
  formData.value.images = imageFiles.value
}

const handleSubmit = async () => {
  resetValidation()
  updatePriceFromEuros()
  updateCostPriceFromEuros()

  const result = await execute(props.productId, formData.value)

  if (result.success) {
    emit('variantCreated')
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

    <!-- Images -->
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-2">Images (max 10)</label>

      <!-- Image Previews -->
      <div v-if="imagePreviews.length > 0" class="grid grid-cols-4 gap-3 mb-3">
        <div
          v-for="(preview, index) in imagePreviews"
          :key="index"
          class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 group"
        >
          <img :src="preview" alt="Preview" class="w-full h-full object-cover" />
          <button
            type="button"
            @click="removeImage(index)"
            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center"
          >
            <i class="fas fa-trash text-white"></i>
          </button>
        </div>
      </div>

      <!-- Upload Button -->
      <label
        class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
      >
        <div class="flex flex-col items-center justify-center pt-5 pb-6">
          <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
          <p class="text-sm text-gray-500">
            <span class="font-semibold">Cliquez pour sélectionner</span> ou glissez-déposez
          </p>
          <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF ou WEBP (MAX. 5 Mo)</p>
        </div>
        <input
          type="file"
          class="hidden"
          accept="image/jpeg,image/png,image/gif,image/webp"
          multiple
          @change="handleImageChange"
        />
      </label>
      <p v-if="validationErrors.images" class="mt-1 text-sm text-red-500">
        {{ validationErrors.images }}
      </p>
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
        <i v-else class="fas fa-plus"></i>
        {{ isLoading ? 'Création...' : 'Créer la variante' }}
      </button>
    </div>
  </div>
</template>
