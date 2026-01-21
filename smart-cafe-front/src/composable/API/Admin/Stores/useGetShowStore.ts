import { ref } from 'vue'
import { useRequestApi } from '@/composable/API/useRequestApi'
import type { Store } from '@/types/Store'

export function useGetShowStore(storeId: number) {
  const { get } = useRequestApi()

  const store = ref<Store | null>(null)
  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const execute = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await get<Store>(`/api/admin/stores/${storeId}`)

      if (response.success && response.data) {
        store.value = response.data
      }

      return {
        success: response.success,
        data: response.data,
        message: response.message,
      }
    } catch {
      error.value = 'Erreur lors de la récupération du magasin'
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
    store,
    isLoading,
    error,
    execute,
  }
}
