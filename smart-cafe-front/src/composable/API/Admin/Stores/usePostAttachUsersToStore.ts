import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Store } from '@/types/Store'

export interface PostAttachUsersToStoreInterface {
  user_ids: number[]
}

export function usePostAttachUsersToStore(storeId: number) {
  const { postFormData } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async (userIds: number[]) => {
    if (userIds.length === 0) {
      return {
        success: false,
        data: null,
        message: 'Veuillez sélectionner au moins un utilisateur',
      }
    }

    isLoading.value = true
    error.value = null

    try {
      const formData = new FormData()
      userIds.forEach((userId, index) => {
        formData.append(`user_ids[${index}]`, userId.toString())
      })

      const response = await postFormData<Store>(`/api/admin/stores/${storeId}/users`, formData)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = "Erreur lors de l'association des utilisateurs"
      return {
        success: false,
        data: null,
        message: error.value,
      }
    } finally {
      isLoading.value = false
    }
  }

  return {
    isLoading,
    error,
    execute,
  }
}
