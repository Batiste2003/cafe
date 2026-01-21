import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { User } from '@/types/User'
import { UserRoleEnum } from '@/enums/UserRoleEnum'

export interface PostStoreUserInterface {
  name: string
  email: string
  password: string
  role: UserRoleEnum.MANAGER | UserRoleEnum.EMPLOYER
}

export interface PostStoreUserValidation {
  name: string | null
  email: string | null
  password: string | null
  role: string | null
}

const NAME_MIN_LENGTH = 2
const NAME_MAX_LENGTH = 255
const PASSWORD_MIN_LENGTH = 8
const PASSWORD_MAX_LENGTH = 255

export function usePostStoreUser() {
  const { post } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<PostStoreUserValidation>({
    name: null,
    email: null,
    password: null,
    role: null,
  })

  const validateForm = (data: PostStoreUserInterface): boolean => {
    let isValid = true
    validationErrors.value = {
      name: null,
      email: null,
      password: null,
      role: null,
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

    // Validate email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    if (!data.email || data.email.trim().length === 0) {
      validationErrors.value.email = "L'email est requis"
      isValid = false
    } else if (!emailRegex.test(data.email)) {
      validationErrors.value.email = "L'email n'est pas valide"
      isValid = false
    }

    // Validate password
    if (!data.password || data.password.length === 0) {
      validationErrors.value.password = 'Le mot de passe est requis'
      isValid = false
    } else if (data.password.length < PASSWORD_MIN_LENGTH) {
      validationErrors.value.password = `Le mot de passe doit contenir au moins ${PASSWORD_MIN_LENGTH} caractères`
      isValid = false
    } else if (data.password.length > PASSWORD_MAX_LENGTH) {
      validationErrors.value.password = `Le mot de passe ne doit pas dépasser ${PASSWORD_MAX_LENGTH} caractères`
      isValid = false
    }

    // Validate role
    const allowedRoles = [UserRoleEnum.MANAGER, UserRoleEnum.EMPLOYER]
    if (!data.role) {
      validationErrors.value.role = 'Le rôle est requis'
      isValid = false
    } else if (!allowedRoles.includes(data.role)) {
      validationErrors.value.role = 'Le rôle sélectionné est invalide'
      isValid = false
    }

    return isValid
  }

  const execute = async (data: PostStoreUserInterface) => {
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
      const response = await post<User>('/api/admin/users', {
        name: data.name,
        email: data.email,
        password: data.password,
        role: data.role,
      })

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = "Erreur lors de la création de l'utilisateur"
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
      email: null,
      password: null,
      role: null,
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
