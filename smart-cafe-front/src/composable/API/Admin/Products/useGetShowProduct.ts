import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

export function useGetShowProduct() {
  const { get } = useRequestApi()

  const product = ref<Product | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (id: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<Product>(`/api/admin/products/${id}`)

      if (response.success && response.data) {
        product.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération du produit'
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
    product,
    isLoading,
    error,
    execute,
  }
}
