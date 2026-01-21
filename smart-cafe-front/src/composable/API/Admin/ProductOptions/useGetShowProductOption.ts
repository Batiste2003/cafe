import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductOption } from '@/types/ProductOption'

export function useGetShowProductOption() {
  const { get } = useRequestApi()

  const option = ref<ProductOption | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, optionId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<ProductOption>(
        `/api/admin/products/${productId}/options/${optionId}`
      )

      if (response.success && response.data) {
        option.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
      error.value = "Erreur lors de la récupération de l'option"
      return {
        success: false,
        data: null,
        message: error.value,
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    option,
    isLoading,
    error,
    execute,
  }
}
