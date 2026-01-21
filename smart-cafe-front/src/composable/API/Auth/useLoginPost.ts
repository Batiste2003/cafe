import { ref } from "vue";
import { useRequestApi, type ApiResult, type LoginResponse } from "@/composable/API/useRequestApi";

interface LoginRequest {
  email: string;
  password: string;
}

export function useLoginPost() {
  const { login } = useRequestApi();
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const execute = async (credentials: LoginRequest): Promise<ApiResult<LoginResponse>> => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await login("/api/auth/login", credentials);

      if (!response.success) {
        error.value = response.message;
      }

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
