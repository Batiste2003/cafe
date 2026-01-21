import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { User } from '@/types/User'

export function useGetShowUser(userId: number) {
  const { get } = useRequestApi()

  const user = ref<User | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<User>(`/api/admin/users/${userId}`)

      if (response.success && response.data) {
        user.value = response.data
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération des utilisateurs'
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
    user,
    isLoading,
    error,
    execute,
  }
}
