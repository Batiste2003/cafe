import axios, { AxiosRequestConfig } from "axios";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";
import type { ApiSuccessResponse } from "@/types/ApiResponse";
import type { User } from "@/types/User";

export type ApiResult<T> = {
  data: T | null | undefined;
  message: string;
  success: boolean;
};

export type LoginResponse = {
  user: User;
  token: string;
};

export function useRequestApi() {
  const authStore = useAuthStore();
  const baseURL = import.meta.env.VITE_API_BACKEND_URL;

  const getHeaders = (token: string | null = null) => {
    const authToken = token || authStore.authToken;
    return {
      "Content-Type": "application/json",
      Authorization: authToken ? `Bearer ${authToken}` : "",
    };
  };

  const delay = (ms: number) =>
    new Promise((resolve) => setTimeout(resolve, ms));

  const handleAuthError = () => {
    const router = useRouter();
    authStore.logout();
    router.push("/login");
  };

  const get = async <T>(
    endpoint: string,
    config: AxiosRequestConfig = {},
    token: string | null = null,
    queryParams: Array<{ key: string; value: string | number | boolean }> = []
  ): Promise<ApiSuccessResponse<T>> => {
    try {
      await delay(500); // Vous pouvez rendre ce délai optionnel si nécessaire

      // Construire la chaîne de requête à l'aide de URLSearchParams
      const params = new URLSearchParams();
      queryParams.forEach(({ key, value }) => {
        params.append(key, String(value));
      });

      const url = `${baseURL}${endpoint}${
        params.toString() ? `?${params.toString()}` : ""
      }`;

      // Préparer les en-têtes
      const headers = {
        ...getHeaders(token),
        ...config.headers,
      };

      // Effectuer la requête GET avec les paramètres et les en-têtes
      const { data } = await axios.get<ApiSuccessResponse<T>>(url, { ...config, headers });

      return data;
    } catch (error) {
      handleError(error);
      throw error; // On relance l'erreur après gestion
    }
  };

  const post = async <T>(
    endpoint: string,
    data: Record<string, unknown> | null | undefined,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<ApiResult<T>> => {
    try {
      await delay(500);
      const response = await axios.post<ApiSuccessResponse<T>>(`${baseURL}${endpoint}`, data, {
        ...config,
        headers: {
          ...getHeaders(token),
          ...config.headers,
        },
      });

      return {
        data: response.data.data,
        message: response.data.message,
        success: response.data.success,
      };
    } catch {
      return {
        data: undefined,
        message: "Erreur lors de la requête",
        success: false,
      };
    }
  };

  const postFormData = async <T>(
    endpoint: string,
    formData: FormData,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<ApiResult<T>> => {
    try {
      await delay(500);
      const authToken = token || authStore.authToken;
      const response = await axios.post<ApiSuccessResponse<T>>(`${baseURL}${endpoint}`, formData, {
        ...config,
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: authToken ? `Bearer ${authToken}` : '',
          ...config.headers,
        },
      });

      return {
        data: response.data.data,
        message: response.data.message,
        success: response.data.success,
      };
    } catch (error) {
      if (axios.isAxiosError(error)) {
        return {
          data: undefined,
          message: error.response?.data?.message || "Erreur lors de l'envoi du formulaire",
          success: false,
        };
      }
      return {
        data: undefined,
        message: "Erreur lors de l'envoi du formulaire",
        success: false,
      };
    }
  };

  const putFormData = async <T>(
    endpoint: string,
    formData: FormData,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<ApiResult<T>> => {
    try {
      await delay(500);
      const authToken = token || authStore.authToken;

      // Laravel needs _method=PUT for multipart/form-data PUT requests
      formData.append('_method', 'PUT');

      const response = await axios.post<ApiSuccessResponse<T>>(`${baseURL}${endpoint}`, formData, {
        ...config,
        headers: {
          'Content-Type': 'multipart/form-data',
          Authorization: authToken ? `Bearer ${authToken}` : '',
          ...config.headers,
        },
      });

      return {
        data: response.data.data,
        message: response.data.message,
        success: response.data.success,
      };
    } catch (error) {
      if (axios.isAxiosError(error)) {
        return {
          data: undefined,
          message: error.response?.data?.message || "Erreur lors de la mise à jour",
          success: false,
        };
      }
      return {
        data: undefined,
        message: "Erreur lors de la mise à jour",
        success: false,
      };
    }
  };

  const put = async <T>(
    endpoint: string,
    data: Record<string, unknown>,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<ApiResult<T>> => {
    try {
      await delay(500);

      const response = await axios.put<ApiSuccessResponse<T>>(`${baseURL}${endpoint}`, data, {
        ...config,
        headers: {
          ...getHeaders(token),
          ...config.headers,
        },
      });

      return {
        data: response.data.data,
        message: response.data.message,
        success: response.data.success,
      };
    } catch (error) {
      if (
        axios.isAxiosError(error) &&
        error.response?.status === 401 &&
        error.response?.data?.error === "UNAUTHENTICATED"
      ) {
        handleAuthError();
      }

      return {
        data: null,
        message:
          axios.isAxiosError(error) && error.response?.data?.message
            ? String(error.response.data.message)
            : "Erreur lors de la requête",
        success: false,
      };
    }
  };

  const patch = async <T>(
    endpoint: string,
    data: Record<string, unknown>,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<ApiResult<T>> => {
    try {
      await delay(500);

      const response = await axios.patch<ApiSuccessResponse<T>>(`${baseURL}${endpoint}`, data, {
        ...config,
        headers: {
          ...getHeaders(token),
          ...config.headers,
        },
      });

      return {
        data: response.data.data,
        message: response.data.message,
        success: response.data.success,
      };
    } catch (error) {
      if (
        axios.isAxiosError(error) &&
        error.response?.status === 401 &&
        error.response?.data?.error === "UNAUTHENTICATED"
      ) {
        handleAuthError();
      }

      return {
        data: null,
        message:
          axios.isAxiosError(error) && error.response?.data?.message
            ? String(error.response.data.message)
            : "Erreur lors de la requête",
        success: false,
      };
    }
  };

  const fetchCsrfToken = async () => {
    try {
      await axios.get(`${baseURL}/sanctum/csrf-cookie`, {
        withCredentials: true,
      });
    } catch (error) {
      console.error("Erreur lors de la récupération du CSRF token:", error);
      throw error;
    }
  };

  const del = async (
    endpoint: string,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<ApiResult<null>> => {
    try {
      await delay(500);
      const response = await axios.delete<ApiSuccessResponse<null>>(`${baseURL}${endpoint}`, {
        ...config,
        headers: {
          ...getHeaders(token),
          ...config.headers,
        },
      });

      return {
        data: null,
        message: response.data.message,
        success: response.data.success,
      };
    } catch (error) {
      if (
        axios.isAxiosError(error) &&
        error.response?.status === 401 &&
        error.response?.data?.error === "UNAUTHENTICATED"
      ) {
        handleAuthError();
      }

      return {
        data: null,
        message:
          axios.isAxiosError(error) && error.response?.data?.message
            ? String(error.response.data.message)
            : "Erreur lors de la suppression",
        success: false,
      };
    }
  };

  const getFile = async (
    endpoint: string,
    config: AxiosRequestConfig = {},
    token: string | null = null
  ): Promise<Blob> => {
    try {
      await delay(500);
      const response = await axios.get(`${baseURL}${endpoint}`, {
        ...config,
        responseType: "blob",
        headers: {
          Authorization: token ? `Bearer ${token}` : "",
          ...config.headers,
        },
      });

      return response.data;
    } catch (error) {
      if (
        axios.isAxiosError(error) &&
        error.response?.status === 401 &&
        error.response?.data?.error === "UNAUTHENTICATED"
      ) {
        handleAuthError();
      }
      throw error;
    }
  };

  // Fonction de gestion des erreurs, centralisée
  const handleError = (error: unknown) => {
    if (axios.isAxiosError(error)) {
      if (
        error.response?.status === 401 &&
        error.response?.data?.error === "UNAUTHENTICATED"
      ) {
        handleAuthError();
      }
    }
  };

  // Méthode de login qui sauvegarde l'utilisateur et le token dans le store
  const login = async (
    endpoint: string,
    credentials: { email: string; password: string }
  ): Promise<ApiResult<LoginResponse>> => {
    try {
      await delay(500);
      const response = await axios.post<ApiSuccessResponse<LoginResponse>>(
        `${baseURL}${endpoint}`,
        credentials,
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      );

      // Si la connexion réussit, sauvegarder l'utilisateur et le token
      if (response.data.success && response.data.data) {
        const { user, token } = response.data.data;
        if (user && token) {
          authStore.login(user, token);
        }
      }

      return {
        data: response.data.data,
        message: response.data.message,
        success: response.data.success,
      };
    } catch (error) {
      if (axios.isAxiosError(error)) {
        return {
          data: undefined,
          message: error.response?.data?.message || "Erreur lors de la connexion",
          success: false,
        };
      }
      return {
        data: undefined,
        message: "Erreur lors de la connexion",
        success: false,
      };
    }
  };

  return {
    get,
    post,
    postFormData,
    put,
    putFormData,
    login,
    fetchCsrfToken,
    del,
    patch,
    getFile,
  };
}
