import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductVariant } from '@/types/ProductVariant'
import type { PagingInfo } from '@/composable/API/Admin/ProductCategories/useGetIndexProductCategory'

export function useGetIndexProductVariant() {
  const { get } = useRequestApi()

  const variants = ref<ProductVariant[]>([])
  const paging = ref<PagingInfo | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, params?: Record<string, any>) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<ProductVariant[]>(
        `/api/admin/products/${productId}/variants`,
        params
      )

      if (response.success && response.data) {
        variants.value = response.data
        paging.value = response.paging || null
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        paging: response.paging,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération des variantes'
      return {
        success: false,
        data: null,
        paging: null,
        message: error.value,
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    variants,
    paging,
    isLoading,
    error,
    execute,
  }
}
