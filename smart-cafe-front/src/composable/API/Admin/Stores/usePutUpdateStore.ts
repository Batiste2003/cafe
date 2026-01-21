import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Store } from '@/types/Store'
import { StoreStatusEnum } from '@/enums/StoreStatusEnum'

export interface PutUpdateStoreInterface {
  name?: string
  status?: StoreStatusEnum
  banner?: File | null
  logo?: File | null
  remove_banner?: boolean
  remove_logo?: boolean
  address_line1?: string
  address_line2?: string
  city?: string
  postal_code?: string
  country?: string
}

export interface PutUpdateStoreValidation {
  name: string | null
  status: string | null
  banner: string | null
  logo: string | null
  address_line1: string | null
  address_line2: string | null
  city: string | null
  postal_code: string | null
  country: string | null
}

const NAME_MIN_LENGTH = 2
const NAME_MAX_LENGTH = 255
const ADDRESS_MAX_LENGTH = 255
const CITY_MAX_LENGTH = 100
const POSTAL_CODE_MAX_LENGTH = 20
const COUNTRY_MAX_LENGTH = 100

export function usePutUpdateStore(storeId: number) {
  const { put, putFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<PutUpdateStoreValidation>({
    name: null,
    status: null,
    banner: null,
    logo: null,
    address_line1: null,
    address_line2: null,
    city: null,
    postal_code: null,
    country: null,
  })

  const validateForm = (data: PutUpdateStoreInterface): boolean => {
    let isValid = true
    validationErrors.value = {
      name: null,
      status: null,
      banner: null,
      logo: null,
      address_line1: null,
      address_line2: null,
      city: null,
      postal_code: null,
      country: null,
    }

    // Validate name (if provided)
    if (data.name !== undefined) {
      if (data.name.trim().length === 0) {
        validationErrors.value.name = 'Le nom est requis'
        isValid = false
      } else if (data.name.length < NAME_MIN_LENGTH) {
        validationErrors.value.name = `Le nom doit contenir au moins ${NAME_MIN_LENGTH} caractères`
        isValid = false
      } else if (data.name.length > NAME_MAX_LENGTH) {
        validationErrors.value.name = `Le nom ne doit pas dépasser ${NAME_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate status (if provided)
    if (data.status !== undefined) {
      const allowedStatuses = [
        StoreStatusEnum.ACTIVE,
        StoreStatusEnum.DRAFT,
        StoreStatusEnum.UNPUBLISH,
      ]
      if (!allowedStatuses.includes(data.status)) {
        validationErrors.value.status = 'Le statut sélectionné est invalide'
        isValid = false
      }
    }

    // Validate address_line1 (if provided)
    if (data.address_line1 !== undefined) {
      if (data.address_line1.trim().length === 0) {
        validationErrors.value.address_line1 = "L'adresse est requise"
        isValid = false
      } else if (data.address_line1.length > ADDRESS_MAX_LENGTH) {
        validationErrors.value.address_line1 = `L'adresse ne doit pas dépasser ${ADDRESS_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate address_line2 (if provided)
    if (data.address_line2 !== undefined && data.address_line2.length > ADDRESS_MAX_LENGTH) {
      validationErrors.value.address_line2 = `Le complément d'adresse ne doit pas dépasser ${ADDRESS_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate city (if provided)
    if (data.city !== undefined) {
      if (data.city.trim().length === 0) {
        validationErrors.value.city = 'La ville est requise'
        isValid = false
      } else if (data.city.length > CITY_MAX_LENGTH) {
        validationErrors.value.city = `La ville ne doit pas dépasser ${CITY_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate postal_code (if provided)
    if (data.postal_code !== undefined) {
      if (data.postal_code.trim().length === 0) {
        validationErrors.value.postal_code = 'Le code postal est requis'
        isValid = false
      } else if (data.postal_code.length > POSTAL_CODE_MAX_LENGTH) {
        validationErrors.value.postal_code = `Le code postal ne doit pas dépasser ${POSTAL_CODE_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate country (if provided)
    if (data.country !== undefined) {
      if (data.country.trim().length === 0) {
        validationErrors.value.country = 'Le pays est requis'
        isValid = false
      } else if (data.country.length > COUNTRY_MAX_LENGTH) {
        validationErrors.value.country = `Le pays ne doit pas dépasser ${COUNTRY_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate banner (if provided)
    if (data.banner) {
      const maxSize = 5 * 1024 * 1024 // 5MB
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
      if (!allowedTypes.includes(data.banner.type)) {
        validationErrors.value.banner = 'Le format de la bannière doit être JPG, PNG ou WEBP'
        isValid = false
      } else if (data.banner.size > maxSize) {
        validationErrors.value.banner = 'La bannière ne doit pas dépasser 5MB'
        isValid = false
      }
    }

    // Validate logo (if provided)
    if (data.logo) {
      const maxSize = 2 * 1024 * 1024 // 2MB
      const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']
      if (!allowedTypes.includes(data.logo.type)) {
        validationErrors.value.logo = 'Le format du logo doit être JPG, PNG ou WEBP'
        isValid = false
      } else if (data.logo.size > maxSize) {
        validationErrors.value.logo = 'Le logo ne doit pas dépasser 2MB'
        isValid = false
      }
    }

    return isValid
  }

  const execute = async (data: PutUpdateStoreInterface) => {
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
      // Check if we have files to upload
      const hasFiles = data.banner || data.logo || data.remove_banner || data.remove_logo

      let response

      if (hasFiles) {
        // Use FormData for file uploads
        const formData = new FormData()
        if (data.name !== undefined) formData.append('name', data.name)
        if (data.status !== undefined) formData.append('status', data.status)
        if (data.address_line1 !== undefined) formData.append('address_line1', data.address_line1)
        if (data.address_line2 !== undefined) formData.append('address_line2', data.address_line2)
        if (data.city !== undefined) formData.append('city', data.city)
        if (data.postal_code !== undefined) formData.append('postal_code', data.postal_code)
        if (data.country !== undefined) formData.append('country', data.country)
        if (data.banner) formData.append('banner', data.banner)
        if (data.logo) formData.append('logo', data.logo)
        if (data.remove_banner) formData.append('remove_banner', '1')
        if (data.remove_logo) formData.append('remove_logo', '1')

        response = await putFormData<Store>(`/api/admin/stores/${storeId}`, formData)
      } else {
        // Use regular JSON for simple updates
        const payload: Record<string, string> = {}
        if (data.name !== undefined) payload.name = data.name
        if (data.status !== undefined) payload.status = data.status
        if (data.address_line1 !== undefined) payload.address_line1 = data.address_line1
        if (data.address_line2 !== undefined) payload.address_line2 = data.address_line2
        if (data.city !== undefined) payload.city = data.city
        if (data.postal_code !== undefined) payload.postal_code = data.postal_code
        if (data.country !== undefined) payload.country = data.country

        response = await put<Store>(`/api/admin/stores/${storeId}`, payload)
      }

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la modification du magasin'
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
      status: null,
      banner: null,
      logo: null,
      address_line1: null,
      address_line2: null,
      city: null,
      postal_code: null,
      country: null,
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
