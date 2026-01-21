import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductVariant } from '@/types/ProductVariant'

export interface PostAttachVariantGalleryInterface {
  image: File
  position?: number
}

interface ValidationErrors {
  image?: string
  position?: string
}

export function usePostAttachVariantGallery() {
  const { postFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (
    productId: number,
    variantId: number,
    data: PostAttachVariantGalleryInterface
  ) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    try {
      const formData = new FormData()
      formData.append('image', data.image)

      if (data.position !== undefined) {
        formData.append('position', data.position.toString())
      }

      const response = await postFormData<ProductVariant>(
        `/api/admin/products/${productId}/variants/${variantId}/gallery`,
        formData
      )

      if (!response.success) {
        error.value = response.message

        if (response.errors) {
          validationErrors.value = response.errors as ValidationErrors
        }
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
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
