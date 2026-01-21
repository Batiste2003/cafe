import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { User } from '@/types/User'
import { UserRoleEnum } from '@/enums/UserRoleEnum'

export interface PutUpdateUserInterface {
  name?: string
  email?: string
  password?: string
  role?: UserRoleEnum
}

export interface PutUpdateUserValidation {
  name: string | null
  email: string | null
  password: string | null
  role: string | null
}

const NAME_MIN_LENGTH = 2
const NAME_MAX_LENGTH = 255
const PASSWORD_MIN_LENGTH = 8
const PASSWORD_MAX_LENGTH = 255

export function usePutUpdateUser(userId: number) {
  const { put } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const validationErrors = ref<PutUpdateUserValidation>({
    name: null,
    email: null,
    password: null,
    role: null,
  })

  const validateForm = (data: PutUpdateUserInterface): boolean => {
    let isValid = true
    validationErrors.value = {
      name: null,
      email: null,
      password: null,
      role: null,
    }

    // Validate name if provided
    if (data.name !== undefined) {
      if (data.name.trim().length === 0) {
        validationErrors.value.name = 'Le nom ne peut pas être vide'
        isValid = false
      } else if (data.name.length < NAME_MIN_LENGTH) {
        validationErrors.value.name = `Le nom doit contenir au moins ${NAME_MIN_LENGTH} caractères`
        isValid = false
      } else if (data.name.length > NAME_MAX_LENGTH) {
        validationErrors.value.name = `Le nom ne doit pas dépasser ${NAME_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate email if provided
    if (data.email !== undefined) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
      if (data.email.trim().length === 0) {
        validationErrors.value.email = "L'email ne peut pas être vide"
        isValid = false
      } else if (!emailRegex.test(data.email)) {
        validationErrors.value.email = "L'email n'est pas valide"
        isValid = false
      }
    }

    // Validate password if provided
    if (data.password !== undefined && data.password.length > 0) {
      if (data.password.length < PASSWORD_MIN_LENGTH) {
        validationErrors.value.password = `Le mot de passe doit contenir au moins ${PASSWORD_MIN_LENGTH} caractères`
        isValid = false
      } else if (data.password.length > PASSWORD_MAX_LENGTH) {
        validationErrors.value.password = `Le mot de passe ne doit pas dépasser ${PASSWORD_MAX_LENGTH} caractères`
        isValid = false
      }
    }

    // Validate role if provided
    if (data.role !== undefined) {
      const allowedRoles = [UserRoleEnum.ADMIN, UserRoleEnum.MANAGER, UserRoleEnum.EMPLOYER]
      if (!allowedRoles.includes(data.role)) {
        validationErrors.value.role = 'Le rôle sélectionné est invalide'
        isValid = false
      }
    }

    return isValid
  }

  const execute = async (data: PutUpdateUserInterface) => {
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
      // Only send fields that are defined
      const payload: Record<string, string> = {}
      if (data.name !== undefined) payload.name = data.name
      if (data.email !== undefined) payload.email = data.email
      if (data.password !== undefined && data.password.length > 0) payload.password = data.password
      if (data.role !== undefined) payload.role = data.role

      const response = await put<User>(`/api/admin/users/${userId}`, payload)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = "Erreur lors de la modification de l'utilisateur"
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
