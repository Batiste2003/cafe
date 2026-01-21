import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductOption } from '@/types/ProductOption'

export function useGetIndexProductOption() {
  const { get } = useRequestApi()

  const options = ref<ProductOption[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<ProductOption[]>(
        `/api/admin/products/${productId}/options`
      )

      if (response.success && response.data) {
        options.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
      error.value = 'Erreur lors de la récupération des options du produit'
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
    options,
    isLoading,
    error,
    execute,
  }
}
