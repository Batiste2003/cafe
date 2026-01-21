import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductVariant } from '@/types/ProductVariant'

export function useGetShowProductVariant() {
  const { get } = useRequestApi()

  const variant = ref<ProductVariant | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, variantId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<ProductVariant>(
        `/api/admin/products/${productId}/variants/${variantId}`
      )

      if (response.success && response.data) {
        variant.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération de la variante'
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
    variant,
    isLoading,
    error,
    execute,
  }
}
