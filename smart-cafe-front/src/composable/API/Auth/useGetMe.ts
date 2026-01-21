import { ref } from "vue";
import { useRequestApi } from "@/composable/API/useRequestApi";
import { useAuthStore } from "@/stores/authStore";
import type { User } from "@/types/User";

interface MeData {
  user: User;
}

export function useGetMe() {
  const { get } = useRequestApi();
  const authStore = useAuthStore();
  const isLoading = ref(false);
  const error = ref<string | null>(null);

  const execute = async () => {
    isLoading.value = true;
    error.value = null;

    try {
      const response = await get<MeData>("/api/auth/me");

      if (response.success && response.data?.user) {
        authStore.setUser(response.data.user);
        return { success: true, user: response.data.user };
      }

      error.value = response.message || "Utilisateur non connecté";
      return { success: false, user: null };
    } catch {
      error.value = "Erreur lors de la récupération de l'utilisateur";
      return { success: false, user: null };
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
