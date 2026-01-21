import AsyncStorage from '@react-native-async-storage/async-storage';
import dotenv from 'dotenv';

dotenv.config();


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
      const response = await fetch(`${process.env.API_URL}/auth/login`, {
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
      const response = await fetch(`${process.env.API_URL}/auth/register`, {
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
      await fetch(`${process.env.API_URL}/auth/logout`, {
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
    const response = await fetch(`${process.env.API_URL}/user`, {
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

  // Products endpoints
  static async getProducts(
    page: number = 1,
    perPage: number = 15,
    filters?: {
      search?: string;
      store_id?: number;
      category_id?: number;
      is_active?: boolean;
      is_featured?: boolean;
    }
  ) {
    try {
      // Build query parameters
      const params = new URLSearchParams({
        page: page.toString(),
        per_page: perPage.toString(),
      });

      if (filters) {
        if (filters.search) params.append('search', filters.search);
        if (filters.store_id) params.append('store_id', filters.store_id.toString());
        if (filters.category_id) params.append('category_id', filters.category_id.toString());
        if (filters.is_active !== undefined) params.append('is_active', filters.is_active ? '1' : '0');
        if (filters.is_featured !== undefined) params.append('is_featured', filters.is_featured ? '1' : '0');
      }

      const response = await fetch(
        `${process.env.API_URL}/products?${params.toString()}`,
        {
          method: 'GET',
          headers: await this.getHeaders(true),
        }
      );

      const result: ApiSuccessResponse<any> | ApiErrorResponse = await response.json();

      if (!response.ok || !result.success) {
        const errorResult = result as ApiErrorResponse;
        const errorMessage = errorResult.errors 
          ? Object.values(errorResult.errors).flat().join(', ')
          : errorResult.message || 'Impossible de récupérer les produits';
        throw new Error(errorMessage);
      }

      return result as ApiSuccessResponse<any>;
    } catch (error: any) {
      console.error('Get products error:', error);
      throw error;
    }
  }

  static async getProduct(productId: number) {
    try {
      const response = await fetch(
        `${process.env.API_URL}/products/${productId}`,
        {
          method: 'GET',
          headers: await this.getHeaders(true),
        }
      );

      const result: ApiSuccessResponse<any> | ApiErrorResponse = await response.json();

      if (!response.ok || !result.success) {
        const errorResult = result as ApiErrorResponse;
        const errorMessage = errorResult.errors 
          ? Object.values(errorResult.errors).flat().join(', ')
          : errorResult.message || 'Impossible de récupérer le produit';
        throw new Error(errorMessage);
      }

      const successResult = result as ApiSuccessResponse<any>;
      return successResult.data;
    } catch (error: any) {
      console.error('Get product error:', error);
      throw error;
    }
  }
}

export default ApiService;
export type { LoginCredentials, RegisterCredentials, AuthResponse };
