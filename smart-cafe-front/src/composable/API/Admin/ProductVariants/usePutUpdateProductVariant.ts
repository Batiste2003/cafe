import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductVariant } from '@/types/ProductVariant'

const SKU_MIN_LENGTH = 2
const SKU_MAX_LENGTH = 100

export interface PutUpdateProductVariantInterface {
  sku?: string
  price_cent_ht?: number
  cost_price_cent_ht?: number | null
  is_default?: boolean
}

interface ValidationErrors {
  sku?: string
  price_cent_ht?: string
  cost_price_cent_ht?: string
  is_default?: string
}

export function usePutUpdateProductVariant() {
  const { putFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (
    productId: number,
    variantId: number,
    data: PutUpdateProductVariantInterface
  ) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    // Validation côté client
    if (data.sku !== undefined && (data.sku.length < SKU_MIN_LENGTH || data.sku.length > SKU_MAX_LENGTH)) {
      error.value = `Le SKU doit contenir entre ${SKU_MIN_LENGTH} et ${SKU_MAX_LENGTH} caractères`
      validationErrors.value.sku = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    if (data.price_cent_ht !== undefined && data.price_cent_ht < 0) {
      error.value = 'Le prix doit être positif'
      validationErrors.value.price_cent_ht = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    if (data.cost_price_cent_ht !== undefined && data.cost_price_cent_ht !== null && data.cost_price_cent_ht < 0) {
      error.value = 'Le prix de revient doit être positif'
      validationErrors.value.cost_price_cent_ht = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    try {
      const formData = new FormData()

      if (data.sku !== undefined) {
        formData.append('sku', data.sku)
      }

      if (data.price_cent_ht !== undefined) {
        formData.append('price_cent_ht', data.price_cent_ht.toString())
      }

      if (data.cost_price_cent_ht !== undefined) {
        if (data.cost_price_cent_ht === null) {
          formData.append('cost_price_cent_ht', '')
        } else {
          formData.append('cost_price_cent_ht', data.cost_price_cent_ht.toString())
        }
      }

      if (data.is_default !== undefined) {
        formData.append('is_default', data.is_default ? '1' : '0')
      }

      const response = await putFormData<ProductVariant>(
        `/api/admin/products/${productId}/variants/${variantId}`,
        formData
      )

      if (!response.success) {
        error.value = response.message

        if (response.errors) {
          validationErrors.value = response.errors as ValidationErrors
        }
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
      error.value = 'Erreur lors de la mise à jour de la variante'
      return {
        success: false,
        data: null,
        message: error.value,
      }
    } finally {
      isLoading.value = false
    }
  }

  const resetValidation = () => {
    validationErrors.value = {}
    error.value = null
  }

  return {
    isLoading,
    error,
    validationErrors,
    execute,
    resetValidation,
  }
}
