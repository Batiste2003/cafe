import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

export function useGetStoreProducts() {
  const { get } = useRequestApi()

  const products = ref<Product[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (storeId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<Product[]>(`/api/admin/stores/${storeId}/products`)

      if (response.success && response.data) {
        products.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération des produits'
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
    products,
    isLoading,
    error,
    execute,
  }
}
