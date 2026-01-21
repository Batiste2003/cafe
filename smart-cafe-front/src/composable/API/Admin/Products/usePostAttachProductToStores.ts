import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

export interface PostAttachProductToStoresInterface {
  store_ids: number[]
}

interface ValidationErrors {
  store_ids?: string
}

export function usePostAttachProductToStores() {
  const { postFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (
    productId: number,
    data: PostAttachProductToStoresInterface
  ) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    // Client-side validation
    if (!data.store_ids || data.store_ids.length === 0) {
      validationErrors.value.store_ids = 'Vous devez sélectionner au moins un magasin'
      isLoading.value = false
      return {
        success: false,
        data: null,
        message: 'Erreur de validation',
      }
    }

    try {
      const formData = new FormData()
      data.store_ids.forEach(storeId => {
        formData.append('store_ids[]', storeId.toString())
      })

      const response = await postFormData<Product>(
        `/api/admin/products/${productId}/stores`,
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
      error.value = 'Erreur lors de l\'association des magasins'
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
