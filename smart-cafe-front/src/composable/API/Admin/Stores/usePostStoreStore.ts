import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Store } from '@/types/Store'
import { StoreStatusEnum } from '@/enums/StoreStatusEnum'

export interface PostStoreStoreInterface {
  name: string
  status: StoreStatusEnum
  banner?: File
  logo?: File
  address_line1: string
  address_line2?: string
  city: string
  postal_code: string
  country: string
}

export interface PostStoreStoreValidation {
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

export function usePostStoreStore() {
  const { postFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<PostStoreStoreValidation>({
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

  const validateForm = (data: PostStoreStoreInterface): boolean => {
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

    // Validate status
    const allowedStatuses = [StoreStatusEnum.ACTIVE, StoreStatusEnum.DRAFT, StoreStatusEnum.UNPUBLISH]
    if (!data.status) {
      validationErrors.value.status = 'Le statut est requis'
      isValid = false
    } else if (!allowedStatuses.includes(data.status)) {
      validationErrors.value.status = 'Le statut sélectionné est invalide'
      isValid = false
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

    // Validate address_line1
    if (!data.address_line1 || data.address_line1.trim().length === 0) {
      validationErrors.value.address_line1 = "L'adresse est requise"
      isValid = false
    } else if (data.address_line1.length > ADDRESS_MAX_LENGTH) {
      validationErrors.value.address_line1 = `L'adresse ne doit pas dépasser ${ADDRESS_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate address_line2 (optional)
    if (data.address_line2 && data.address_line2.length > ADDRESS_MAX_LENGTH) {
      validationErrors.value.address_line2 = `Le complément d'adresse ne doit pas dépasser ${ADDRESS_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate city
    if (!data.city || data.city.trim().length === 0) {
      validationErrors.value.city = 'La ville est requise'
      isValid = false
    } else if (data.city.length > CITY_MAX_LENGTH) {
      validationErrors.value.city = `La ville ne doit pas dépasser ${CITY_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate postal_code
    if (!data.postal_code || data.postal_code.trim().length === 0) {
      validationErrors.value.postal_code = 'Le code postal est requis'
      isValid = false
    } else if (data.postal_code.length > POSTAL_CODE_MAX_LENGTH) {
      validationErrors.value.postal_code = `Le code postal ne doit pas dépasser ${POSTAL_CODE_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate country
    if (!data.country || data.country.trim().length === 0) {
      validationErrors.value.country = 'Le pays est requis'
      isValid = false
    } else if (data.country.length > COUNTRY_MAX_LENGTH) {
      validationErrors.value.country = `Le pays ne doit pas dépasser ${COUNTRY_MAX_LENGTH} caractères`
      isValid = false
    }

    return isValid
  }

  const execute = async (data: PostStoreStoreInterface) => {
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
      const formData = new FormData()
      formData.append('name', data.name)
      formData.append('status', data.status)
      formData.append('address_line1', data.address_line1)
      if (data.address_line2) formData.append('address_line2', data.address_line2)
      formData.append('city', data.city)
      formData.append('postal_code', data.postal_code)
      formData.append('country', data.country)
      if (data.banner) formData.append('banner', data.banner)
      if (data.logo) formData.append('logo', data.logo)

      const response = await postFormData<Store>('/api/admin/stores', formData)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la création du magasin'
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
