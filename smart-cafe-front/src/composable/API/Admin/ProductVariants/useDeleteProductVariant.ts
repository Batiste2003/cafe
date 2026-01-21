import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'

export function useDeleteProductVariant() {
  const { del } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, variantId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await del(`/api/admin/products/${productId}/variants/${variantId}`)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la suppression de la variante'
      return {
        success: false,
        message: error.value,
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
