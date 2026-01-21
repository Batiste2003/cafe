import { ref } from "vue";
import { useRequestApi, type ApiResult } from "@/composable/API/useRequestApi";
import { useAuthStore } from '@/stores/authStore'

export function useLogoutPost() {
  const { post } = useRequestApi();
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const execute = async (): Promise<ApiResult> => {
    const authStore = useAuthStore();
    const token = authStore.token;
    isLoading.value = true;
    error.value = null;

    try {
      const response = await post("/api/auth/logout", null, {}, token);

      if (!response.success) {
        error.value = response.message;
      }

      await authStore.logout()
      return response;
    } catch (e) {
      error.value = "Une erreur est survenue lors de la connexion";
      throw e;
    } finally {
      isLoading.value = false;
    }
  };

  return {
    execute,
    isLoading,
    error,
  };
}
