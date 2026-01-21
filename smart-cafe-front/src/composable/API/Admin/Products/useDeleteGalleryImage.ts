import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

export function useDeleteGalleryImage() {
  const { del } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (productId: number, imageId: number) => {
    isLoading.value = true
    error.value = null

    try {
      const response = await del<Product>(`/api/admin/products/${productId}/gallery/${imageId}`)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = "Erreur lors de la suppression de l'image"
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
    isLoading,
    error,
    execute,
  }
}
