import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductCategory } from '@/types/ProductCategory'

export interface PostStoreProductCategoryInterface {
  name: string
  description?: string
  parent_id?: number | null
  is_active?: boolean
}

export interface PostStoreProductCategoryValidation {
  name: string | null
  description: string | null
  parent_id: string | null
}

const NAME_MIN_LENGTH = 2
const NAME_MAX_LENGTH = 255
const DESCRIPTION_MAX_LENGTH = 1000

export function usePostStoreProductCategory() {
  const { post } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<PostStoreProductCategoryValidation>({
    name: null,
    description: null,
    parent_id: null,
  })

  const validateForm = (data: PostStoreProductCategoryInterface): boolean => {
    let isValid = true
    validationErrors.value = {
      name: null,
      description: null,
      parent_id: null,
    }

    // Validate name
    if (!data.name || data.name.trim().length === 0) {
      validationErrors.value.name = 'Le nom est requis'
      isValid = false
    } else if (data.name.length < NAME_MIN_LENGTH) {
      validationErrors.value.name = `Le nom doit contenir au moins ${NAME_MIN_LENGTH} caractères`
      isValid = false
    } else if (data.name.length > NAME_MAX_LENGTH) {
      validationErrors.value.name = `Le nom ne doit pas dépasser ${NAME_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate description (optional)
    if (data.description && data.description.length > DESCRIPTION_MAX_LENGTH) {
      validationErrors.value.description = `La description ne doit pas dépasser ${DESCRIPTION_MAX_LENGTH} caractères`
      isValid = false
    }

    return isValid
  }

  const execute = async (data: PostStoreProductCategoryInterface) => {
    if (!validateForm(data)) {
      return {
        success: false,
        data: null,
        message: 'Veuillez corriger les erreurs du formulaire',
      }
    }

    isLoading.value = true
    error.value = null

    try {
      const payload: Record<string, string | number | boolean> = {
        name: data.name,
      }

      if (data.description) {
        payload.description = data.description
      }

      if (data.parent_id !== undefined && data.parent_id !== null) {
        payload.parent_id = data.parent_id
      }

      if (data.is_active !== undefined) {
        payload.is_active = data.is_active
      }

      const response = await post<ProductCategory>('/api/admin/product-categories', payload)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la création de la catégorie'
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
    validationErrors.value = {
      name: null,
      description: null,
      parent_id: null,
    }
    error.value = null
  }

  return {
    isLoading,
    error,
    validationErrors,
    execute,
    validateForm,
    resetValidation,
  }
}
