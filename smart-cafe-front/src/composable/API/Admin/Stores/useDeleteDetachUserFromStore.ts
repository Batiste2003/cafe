import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'

export function useDeleteDetachUserFromStore(storeId: number, userId: number) {
  const { del } = useRequestApi()

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await del(`/api/admin/stores/${storeId}/users/${userId}`)

      if (!response.success) {
        error.value = response.message
      }

      return {
        success: response.success,
        message: response.message,
      }
    } catch {
      error.value = "Erreur lors de la dissociation de l'utilisateur"
      return {
        success: false,
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
