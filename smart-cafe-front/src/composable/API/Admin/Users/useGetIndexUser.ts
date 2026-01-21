import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { User } from '@/types/User'

export function useGetIndexUser() {
  const { get } = useRequestApi()

  const users = ref<User[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<User[]>('/api/admin/users')

      if (response.success && response.data) {
        users.value = response.data
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch (e) {
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
    users,
    isLoading,
    error,
    execute,
  }
}
