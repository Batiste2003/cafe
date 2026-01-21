import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { StoreProductVariant } from '@/types/ProductVariant'

export interface PostCreateVariantStockInterface {
  store_id: number
  stock: number | null  // null = unlimited
}

interface ValidationErrors {
  store_id?: string
  stock?: string
}

export function usePostCreateVariantStock() {
  const { putFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (
    productId: number,
    variantId: number,
    data: PostCreateVariantStockInterface
  ) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    try {
      const formData = new FormData()

      if (data.stock === null) {
        formData.append('unlimited', 'true')
      } else {
        formData.append('stock', data.stock.toString())
      }

      const response = await putFormData<StoreProductVariant>(
        `/api/admin/products/${productId}/variants/${variantId}/stocks/${data.store_id}`,
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
      error.value = 'Erreur lors de la création du stock'
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
