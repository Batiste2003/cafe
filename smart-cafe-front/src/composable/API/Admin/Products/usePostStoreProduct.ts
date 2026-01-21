import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Product } from '@/types/Product'

const NAME_MIN_LENGTH = 2
const NAME_MAX_LENGTH = 255
const DESCRIPTION_MAX_LENGTH = 2000
const MAX_GALLERY_IMAGES = 10

export interface PostStoreProductInterface {
  name: string
  description?: string
  product_category_id: number
  store_ids: number[]
  is_active?: boolean
  is_featured?: boolean
  images?: File[]
}

interface ValidationErrors {
  name?: string
  description?: string
  product_category_id?: string
  store_ids?: string
  is_active?: string
  is_featured?: string
  images?: string
}

export function usePostStoreProduct() {
  const { postForm } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const validate = (data: PostStoreProductInterface): boolean => {
    validationErrors.value = {}

    if (!data.name || data.name.trim().length < NAME_MIN_LENGTH) {
      validationErrors.value.name = `Le nom doit contenir au moins ${NAME_MIN_LENGTH} caractères`
    }

    if (data.name && data.name.length > NAME_MAX_LENGTH) {
      validationErrors.value.name = `Le nom ne doit pas dépasser ${NAME_MAX_LENGTH} caractères`
    }

    if (data.description && data.description.length > DESCRIPTION_MAX_LENGTH) {
      validationErrors.value.description = `La description ne doit pas dépasser ${DESCRIPTION_MAX_LENGTH} caractères`
    }

    if (!data.product_category_id) {
      validationErrors.value.product_category_id = 'La catégorie est requise'
    }

    if (!data.store_ids || data.store_ids.length === 0) {
      validationErrors.value.store_ids = 'Au moins un magasin doit être sélectionné'
    }

    if (data.images && data.images.length > MAX_GALLERY_IMAGES) {
      validationErrors.value.images = `Vous ne pouvez pas ajouter plus de ${MAX_GALLERY_IMAGES} images`
    }

    return Object.keys(validationErrors.value).length === 0
  }

  const execute = async (data: PostStoreProductInterface) => {
    if (!validate(data)) {
      return { success: false, data: null, message: 'Validation échouée' }
    }

    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    try {
      const formData = new FormData()
      formData.append('name', data.name)

      if (data.description) {
        formData.append('description', data.description)
      }

      formData.append('product_category_id', data.product_category_id.toString())

      data.store_ids.forEach((storeId, index) => {
        formData.append(`store_ids[${index}]`, storeId.toString())
      })

      if (data.is_active !== undefined) {
        formData.append('is_active', data.is_active ? '1' : '0')
      }

      if (data.is_featured !== undefined) {
        formData.append('is_featured', data.is_featured ? '1' : '0')
      }

      if (data.images && data.images.length > 0) {
        data.images.forEach((image, index) => {
          formData.append(`images[${index}]`, image)
        })
      }

      const response = await postForm<Product>('/api/admin/products', formData)

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
      error.value = 'Erreur lors de la création du produit'
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
