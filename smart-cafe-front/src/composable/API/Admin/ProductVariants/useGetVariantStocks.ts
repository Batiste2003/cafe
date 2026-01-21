import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { StoreProductVariant } from '@/types/ProductVariant'

export function useGetVariantStocks() {
  const { get } = useRequestApi()

  const stocks = ref<StoreProductVariant[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, variantId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<StoreProductVariant[]>(
        `/api/admin/products/${productId}/variants/${variantId}/stocks`
      )

      if (response.success && response.data) {
        stocks.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération des stocks'
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
    stocks,
    isLoading,
    error,
    execute,
  }
}
