import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'

export function useDeleteVariantStock() {
  const { del } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, variantId: number, storeId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await del(
        `/api/admin/products/${productId}/variants/${variantId}/stocks/${storeId}`
      )

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        message: response.message,
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    isLoading,
    error,
    execute,
  }
}
