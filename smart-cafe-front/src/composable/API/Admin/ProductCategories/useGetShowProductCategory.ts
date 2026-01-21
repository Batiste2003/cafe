import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductCategory } from '@/types/ProductCategory'

export function useGetShowProductCategory(categoryId: number) {
  const { get } = useRequestApi()

  const category = ref<ProductCategory | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<ProductCategory>(`/api/admin/product-categories/${categoryId}`)

      if (response.success && response.data) {
        category.value = response.data
      } else {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération de la catégorie'
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
    category,
    isLoading,
    error,
    execute,
  }
}
