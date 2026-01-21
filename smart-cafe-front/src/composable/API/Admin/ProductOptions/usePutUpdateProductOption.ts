import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { ProductOption } from '@/types/ProductOption'

export const OPTION_NAME_MIN_LENGTH = 2
export const OPTION_NAME_MAX_LENGTH = 100

export interface PutUpdateProductOptionInterface {
  name?: string
  is_required?: boolean
}

interface ValidationErrors {
  name?: string
  is_required?: string
}

export function usePutUpdateProductOption() {
  const { putFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<ValidationErrors>({})

  const execute = async (
    productId: number,
    optionId: number,
    data: PutUpdateProductOptionInterface
  ) => {
    isLoading.value = true
    error.value = null
    validationErrors.value = {}

    // Client-side validation
    if (data.name !== undefined) {
      if (data.name.length < OPTION_NAME_MIN_LENGTH || data.name.length > OPTION_NAME_MAX_LENGTH) {
        validationErrors.value.name = `Le nom doit contenir entre ${OPTION_NAME_MIN_LENGTH} et ${OPTION_NAME_MAX_LENGTH} caractères`
        isLoading.value = false
        return {
          success: false,
          data: null,
          message: 'Erreur de validation',
        }
      }
    }

    try {
      const formData = new FormData()

      if (data.name !== undefined) {
        formData.append('name', data.name)
      }

      if (data.is_required !== undefined) {
        formData.append('is_required', data.is_required ? '1' : '0')
      }

      const response = await putFormData<ProductOption>(
        `/api/admin/products/${productId}/options/${optionId}`,
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
      error.value = "Erreur lors de la modification de l'option"
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
