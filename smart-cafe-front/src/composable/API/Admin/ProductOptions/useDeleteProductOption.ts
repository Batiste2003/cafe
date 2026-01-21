import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'

export function useDeleteProductOption() {
  const { del } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, optionId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await del(
        `/api/admin/products/${productId}/options/${optionId}`
      )

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        message: response.message,
      }
    } catch (e) {
      error.value = "Erreur lors de la suppression de l'option"
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
