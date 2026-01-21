import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://localhost:8000/api';

interface LoginCredentials {
  email: string;
  password: string;
}

interface RegisterCredentials {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

interface AuthResponse {
  user: {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
  };
  token: string;
}

interface ApiSuccessResponse<T> {
  success: boolean;
  message: string;
  data: T;
}

interface ApiErrorResponse {
  success: boolean;
  message: string;
  errors?: Record<string, string[]>;
}

class ApiService {
  private static async getHeaders(includeAuth: boolean = false): Promise<HeadersInit> {
    const headers: HeadersInit = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    };

    if (includeAuth) {
      const token = await AsyncStorage.getItem('auth_token');
      if (token) {
        headers['Authorization'] = `Bearer ${token}`;
      }
    }

    return headers;
  }

  static async login(credentials: LoginCredentials): Promise<AuthResponse> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/login`, {
        method: 'POST',
        headers: await this.getHeaders(),
        body: JSON.stringify(credentials),
      });

      const result: ApiSuccessResponse<AuthResponse> | ApiErrorResponse = await response.json();
      console.log('Login response:', result);

      if (!response.ok || !result.success) {
        const errorResult = result as ApiErrorResponse;
        const errorMessage = errorResult.errors 
          ? Object.values(errorResult.errors).flat().join(', ')
          : errorResult.message || 'Échec de la connexion';
        throw new Error(errorMessage);
      }

      const successResult = result as ApiSuccessResponse<AuthResponse>;
      const { token } = successResult.data;

      if (!token) {
        throw new Error('Token non reçu du serveur');
      }

      await AsyncStorage.setItem('auth_token', token);
      return successResult.data;
    } catch (error: any) {
      console.error('Login error:', error);
      throw error;
    }
  }

  static async register(credentials: RegisterCredentials): Promise<AuthResponse> {
    try {
      const response = await fetch(`${API_BASE_URL}/auth/register`, {
        method: 'POST',
        headers: await this.getHeaders(),
        body: JSON.stringify(credentials),
      });

      const result: ApiSuccessResponse<AuthResponse> | ApiErrorResponse = await response.json();
      console.log('Register response:', result);

      if (!response.ok || !result.success) {
        const errorResult = result as ApiErrorResponse;
        const errorMessage = errorResult.errors 
          ? Object.values(errorResult.errors).flat().join(', ')
          : errorResult.message || 'Échec de l\'inscription';
        throw new Error(errorMessage);
      }

      const successResult = result as ApiSuccessResponse<AuthResponse>;
      const { token } = successResult.data;

      if (!token) {
        throw new Error('Token non reçu du serveur');
      }

      await AsyncStorage.setItem('auth_token', token);
      return successResult.data;
    } catch (error: any) {
      console.error('Register error:', error);
      throw error;
    }
  }

  static async logout(): Promise<void> {
    try {
      await fetch(`${API_BASE_URL}/auth/logout`, {
        method: 'POST',
        headers: await this.getHeaders(true),
      });
    } catch (error) {
      console.error('Erreur lors de la déconnexion:', error);
    } finally {
      await AsyncStorage.removeItem('auth_token');
    }
  }

  static async getCurrentUser() {
    const response = await fetch(`${API_BASE_URL}/user`, {
      method: 'GET',
      headers: await this.getHeaders(true),
    });

    if (!response.ok) {
      throw new Error('Impossible de récupérer l\'utilisateur');
    }

    return await response.json();
  }

  static async getToken(): Promise<string | null> {
    return await AsyncStorage.getItem('auth_token');
  }
}

export default ApiService;
export type { LoginCredentials, RegisterCredentials, AuthResponse };
