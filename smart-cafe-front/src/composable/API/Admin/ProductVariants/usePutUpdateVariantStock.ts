import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { StoreProductVariant } from '@/types/ProductVariant'

export interface PutUpdateVariantStockInterface {
  stock: number | null // null = unlimited
}

interface ValidationErrors {
  stock?: string
}

export function usePutUpdateVariantStock() {
  const { putFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (
    productId: number,
    variantId: number,
    storeId: number,
    data: PutUpdateVariantStockInterface
  ) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    // Validation côté client
    if (data.stock !== null && data.stock < 0) {
      error.value = 'Le stock doit être positif'
      validationErrors.value.stock = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    try {
      const formData = new FormData()

      if (data.stock === null) {
        formData.append('unlimited', 'true')
      } else {
        formData.append('stock', data.stock.toString())
      }

      const response = await putFormData<StoreProductVariant>(
        `/api/admin/products/${productId}/variants/${variantId}/stocks/${storeId}`,
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
      error.value = 'Erreur lors de la mise à jour du stock'
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
