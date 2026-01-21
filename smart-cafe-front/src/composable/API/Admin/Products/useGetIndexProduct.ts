import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

export interface PagingInfo {
  total: number
  count: number
  per_page: number
  current_page: number
  total_pages: number
  has_more_pages: boolean
}

export function useGetIndexProduct() {
  const { get } = useRequestApi()

  const products = ref<Product[]>([])
  const paging = ref<PagingInfo | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (params?: Record<string, any>) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<Product[]>('/api/admin/products', params)

      if (response.success && response.data) {
        products.value = response.data
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
      error.value = 'Erreur lors de la récupération des produits'
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
    products,
    paging,
    isLoading,
    error,
    execute,
  }
}
