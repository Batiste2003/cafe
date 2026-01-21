import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductVariant } from '@/types/ProductVariant'

const SKU_MIN_LENGTH = 2
const SKU_MAX_LENGTH = 100
const MAX_GALLERY_IMAGES = 10

export interface PostStoreProductVariantInterface {
  sku: string
  price_cent_ht: number
  cost_price_cent_ht?: number
  is_default?: boolean
  images?: File[]
}

interface ValidationErrors {
  sku?: string
  price_cent_ht?: string
  cost_price_cent_ht?: string
  is_default?: string
  images?: string
}

export function usePostStoreProductVariant() {
  const { postFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (productId: number, data: PostStoreProductVariantInterface) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    // Validation côté client
    if (!data.sku || data.sku.length < SKU_MIN_LENGTH || data.sku.length > SKU_MAX_LENGTH) {
      error.value = `Le SKU doit contenir entre ${SKU_MIN_LENGTH} et ${SKU_MAX_LENGTH} caractères`
      validationErrors.value.sku = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    if (data.price_cent_ht < 0) {
      error.value = 'Le prix doit être positif'
      validationErrors.value.price_cent_ht = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    if (data.cost_price_cent_ht !== undefined && data.cost_price_cent_ht < 0) {
      error.value = 'Le prix de revient doit être positif'
      validationErrors.value.cost_price_cent_ht = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    if (data.images && data.images.length > MAX_GALLERY_IMAGES) {
      error.value = `Vous ne pouvez pas ajouter plus de ${MAX_GALLERY_IMAGES} images`
      validationErrors.value.images = error.value
      isLoading.value = false
      return { success: false, data: null, message: error.value }
    }

    try {
      const formData = new FormData()
      formData.append('sku', data.sku)
      formData.append('price_cent_ht', data.price_cent_ht.toString())

      if (data.cost_price_cent_ht !== undefined) {
        formData.append('cost_price_cent_ht', data.cost_price_cent_ht.toString())
      }

      if (data.is_default !== undefined) {
        formData.append('is_default', data.is_default ? '1' : '0')
      }

      if (data.images) {
        data.images.forEach((image, index) => {
          formData.append(`images[${index}]`, image)
        })
      }

      const response = await postFormData<ProductVariant>(
        `/api/admin/products/${productId}/variants`,
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
      error.value = 'Erreur lors de la création de la variante'
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
