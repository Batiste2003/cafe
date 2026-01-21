import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

export interface PostAttachGalleryInterface {
  image: File
  position?: number
}

interface ValidationErrors {
  image?: string
  position?: string
}

export function usePostAttachGallery() {
  const { postFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (productId: number, data: PostAttachGalleryInterface) => {
    console.log('usePostAttachGallery.execute called with:', productId, data)
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    try {
      const formData = new FormData()
      formData.append('image', data.image)

      if (data.position !== undefined) {
        formData.append('position', data.position.toString())
      }

      console.log('FormData created, calling API...')
      const response = await postFormData<Product>(`/api/admin/products/${productId}/gallery`, formData)
      console.log('API response:', response)

      if (!response.success) {
        console.error('API error:', response.message)
        error.value = response.message

        if (response.errors) {
          console.error('Validation errors:', response.errors)
          validationErrors.value = response.errors as ValidationErrors
        }
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
      console.error('Exception in usePostAttachGallery:', e)
      error.value = "Erreur lors de l'ajout de l'image à la galerie"
      return {
        success: false,
        data: null,
        message: error.value,
      }
    } finally {
      isLoading.value = false
    }
  }

  const resetValidation = () => {
    validationErrors.value = {}
    error.value = null
  }

  return {
    isLoading,
    error,
    validationErrors,
    execute,
    resetValidation,
  }
}
