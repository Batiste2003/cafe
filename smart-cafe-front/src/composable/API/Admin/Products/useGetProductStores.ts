import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Store } from '@/types/Store'

export function useGetProductStores() {
  const { get } = useRequestApi()

  const stores = ref<Store[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<Store[]>(
        `/api/admin/products/${productId}/stores`
      )

      if (response.success && response.data) {
        stores.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
      error.value = 'Erreur lors de la récupération des magasins associés'
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
    stores,
    isLoading,
    error,
    execute,
  }
}
