# 05 - Application Mobile (React Native)

> Architecture et bonnes pratiques de l'application mobile client

---

## Table des matières

1. [Choix Technologiques](#1-choix-technologiques)
2. [Architecture Expo](#2-architecture-expo)
3. [Navigation (Expo Router)](#3-navigation-expo-router)
4. [Gestion d'État](#4-gestion-détat)
5. [Services API](#5-services-api)
6. [Composants](#6-composants)
7. [Hooks Personnalisés](#7-hooks-personnalisés)
8. [Theming et Design](#8-theming-et-design)
9. [Gestion des Erreurs](#9-gestion-des-erreurs)
10. [Performance](#10-performance)

---

## 1. Choix Technologiques

### 1.1 Stack Technique

| Technologie | Version | Rôle |
|-------------|---------|------|
| **React Native** | 0.81.5 | Framework mobile |
| **Expo** | 54.0.31 | Plateforme de développement |
| **Expo Router** | 6.0.21 | Navigation file-based |
| **React Query** | 5.90.19 | Data fetching & cache |
| **AsyncStorage** | 2.2.0 | Stockage local |
| **Reanimated** | 4.1.1 | Animations fluides |

### 1.2 Justification de React Native + Expo

```
┌─────────────────────────────────────────────────────────────────┐
│                    POURQUOI REACT NATIVE + EXPO ?               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ✅ Cross-Platform                                             │
│      • Un seul code pour iOS et Android                        │
│      • Cohérence UI entre plateformes                          │
│      • Réduction des coûts de développement                    │
│                                                                 │
│   ✅ Expo Managed Workflow                                      │
│      • Configuration simplifiée                                │
│      • Build dans le cloud (EAS)                               │
│      • Over-the-Air updates                                    │
│      • Pas besoin de Xcode/Android Studio au quotidien         │
│                                                                 │
│   ✅ Expo Router                                                │
│      • Navigation file-based (comme Next.js)                   │
│      • Deep linking automatique                                │
│      • Routes typées                                           │
│                                                                 │
│   ✅ Performance                                                │
│      • Hermes JavaScript Engine                                │
│      • Animations natives (Reanimated)                         │
│      • Proche des performances natives                         │
│                                                                 │
│   ✅ Écosystème React                                           │
│      • Réutilisation des compétences React web                 │
│      • NPM packages compatibles                                │
│      • Communauté large et active                              │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.3 Comparaison avec Alternatives

| Critère | React Native | Flutter | Natif |
|---------|--------------|---------|-------|
| Performance | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| Time-to-market | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| Coût | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐ |
| Hot Reload | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐ |
| Compétences JS | ✅ | ❌ (Dart) | ❌ |

---

## 2. Architecture Expo

### 2.1 Structure du Projet

```
smart-cafe-app/
├── app/                          # Pages (Expo Router file-based)
│   ├── _layout.tsx               # Layout racine
│   ├── modal.tsx                 # Modal global
│   │
│   ├── auth/                     # Groupe authentification
│   │   ├── _layout.tsx           # Layout auth
│   │   ├── index.tsx             # Page d'accueil auth
│   │   ├── login.tsx             # Écran connexion
│   │   └── register.tsx          # Écran inscription
│   │
│   ├── (tabs)/                   # Navigation par onglets
│   │   ├── _layout.tsx           # Configuration tabs
│   │   ├── index.tsx             # Tab Accueil
│   │   ├── products.tsx          # Tab Carte/Produits
│   │   └── explore.tsx           # Tab Explorer
│   │
│   └── product/                  # Détails produit
│       └── options.tsx           # Options de personnalisation
│
├── components/                   # Composants réutilisables
│   ├── product-card.tsx          # Carte produit
│   ├── themed-text.tsx           # Texte thématisé
│   ├── themed-view.tsx           # Vue thématisée
│   ├── custom-header.tsx         # Header personnalisé
│   ├── parallax-scroll-view.tsx  # ScrollView parallax
│   └── ui/                       # Composants UI génériques
│       ├── icon-symbol.tsx
│       └── collapsible.tsx
│
├── contexts/                     # Context API
│   └── AuthContext.tsx           # Contexte d'authentification
│
├── hooks/                        # Custom hooks
│   ├── use-color-scheme.ts       # Détection du thème
│   ├── use-protected-route.ts    # Protection des routes
│   └── use-theme-color.ts        # Couleurs du thème
│
├── services/                     # Services API
│   └── api.ts                    # Client API centralisé
│
├── constants/                    # Constantes
│   └── Colors.ts                 # Palette de couleurs
│
├── types/                        # Types TypeScript
│   └── index.ts
│
├── assets/                       # Images et médias
├── styles/                       # Styles globaux
│
├── app.json                      # Configuration Expo
├── package.json                  # Dépendances
└── tsconfig.json                 # Configuration TypeScript
```

### 2.2 Configuration Expo (app.json)

```json
{
  "expo": {
    "name": "Smart Cafe",
    "slug": "smart-cafe-app",
    "version": "1.0.0",
    "orientation": "portrait",
    "scheme": "smartcafe",
    "platforms": ["ios", "android"],
    "ios": {
      "supportsTablet": true,
      "bundleIdentifier": "com.smartcafe.app"
    },
    "android": {
      "adaptiveIcon": { ... },
      "package": "com.smartcafe.app"
    }
  }
}
```

---

## 3. Navigation (Expo Router)

### 3.1 File-Based Routing

Expo Router utilise le **file-based routing**, similaire à Next.js :

| Fichier | Route |
|---------|-------|
| `app/index.tsx` | `/` |
| `app/auth/login.tsx` | `/auth/login` |
| `app/auth/register.tsx` | `/auth/register` |
| `app/(tabs)/index.tsx` | `/` (tab Home) |
| `app/(tabs)/products.tsx` | `/products` |
| `app/product/options.tsx` | `/product/options` |

### 3.2 Layout Racine

```tsx
// app/_layout.tsx
import { Stack } from 'expo-router';
import { AuthProvider } from '@/contexts/AuthContext';
import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { GestureHandlerRootView } from 'react-native-gesture-handler';

const queryClient = new QueryClient();

export default function RootLayout() {
  return (
    <GestureHandlerRootView style={{ flex: 1 }}>
      <QueryClientProvider client={queryClient}>
        <AuthProvider>
          <Stack>
            <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
            <Stack.Screen name="auth" options={{ headerShown: false }} />
            <Stack.Screen name="modal" options={{ presentation: 'modal' }} />
          </Stack>
        </AuthProvider>
      </QueryClientProvider>
    </GestureHandlerRootView>
  );
}
```

### 3.3 Navigation par Onglets

```tsx
// app/(tabs)/_layout.tsx
import { Tabs } from 'expo-router';
import { IconSymbol } from '@/components/ui/icon-symbol';

export default function TabLayout() {
  return (
    <Tabs
      screenOptions={{
        tabBarActiveTintColor: '#007AFF',
        headerShown: false,
      }}
    >
      <Tabs.Screen
        name="index"
        options={{
          title: 'Accueil',
          tabBarIcon: ({ color }) => (
            <IconSymbol name="house.fill" color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="products"
        options={{
          title: 'Carte',
          tabBarIcon: ({ color }) => (
            <IconSymbol name="menucard.fill" color={color} />
          ),
        }}
      />
      <Tabs.Screen
        name="explore"
        options={{
          title: 'Explorer',
          tabBarIcon: ({ color }) => (
            <IconSymbol name="safari.fill" color={color} />
          ),
        }}
      />
    </Tabs>
  );
}
```

### 3.4 Protection des Routes

```tsx
// hooks/use-protected-route.ts
import { useEffect } from 'react';
import { useRouter, useSegments } from 'expo-router';
import { useAuth } from '@/contexts/AuthContext';

export function useProtectedRoute() {
  const { isAuthenticated, loading } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (loading) return;

    const inAuthGroup = segments[0] === 'auth';

    if (!isAuthenticated && !inAuthGroup) {
      // Rediriger vers login
      router.replace('/auth/login');
    } else if (isAuthenticated && inAuthGroup) {
      // Rediriger vers l'app
      router.replace('/');
    }
  }, [isAuthenticated, loading, segments]);
}
```

---

## 4. Gestion d'État

### 4.1 Architecture d'État

```
┌─────────────────────────────────────────────────────────────────┐
│                    GESTION D'ÉTAT MOBILE                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   Context API (Authentification)                                │
│   ─────────────────────────────────────────────────────────    │
│   • User courant                                               │
│   • Token d'authentification                                   │
│   • Actions login/logout/register                              │
│                                                                 │
│   React Query (Data Fetching)                                   │
│   ─────────────────────────────────────────────────────────    │
│   • Cache automatique                                          │
│   • Refetch en arrière-plan                                    │
│   • Gestion du loading/error                                   │
│   • Invalidation intelligente                                  │
│                                                                 │
│   AsyncStorage (Persistance)                                    │
│   ─────────────────────────────────────────────────────────    │
│   • Token d'authentification                                   │
│   • Préférences utilisateur                                    │
│   • Cache hors-ligne                                           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 AuthContext

```tsx
// contexts/AuthContext.tsx
import React, { createContext, useState, useContext, useEffect, ReactNode } from 'react';
import ApiService from '@/services/api';

interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at?: string;
}

interface AuthContextType {
  user: User | null;
  loading: boolean;
  login: (email: string, password: string) => Promise<void>;
  register: (name: string, email: string, password: string, passwordConfirmation: string) => Promise<void>;
  logout: () => Promise<void>;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<User | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    checkAuth();
  }, []);

  const checkAuth = async () => {
    try {
      const token = await ApiService.getToken();

      if (!token) {
        setLoading(false);
        return;
      }

      const currentUser = await ApiService.getCurrentUser();
      setUser(currentUser.user);
    } catch (error) {
      await ApiService.logout();
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  const login = async (email: string, password: string) => {
    const response = await ApiService.login({ email, password });
    setUser(response.user);
  };

  const register = async (
    name: string,
    email: string,
    password: string,
    passwordConfirmation: string
  ) => {
    const response = await ApiService.register({
      name,
      email,
      password,
      password_confirmation: passwordConfirmation,
    });
    setUser(response.user);
  };

  const logout = async () => {
    await ApiService.logout();
    setUser(null);
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        login,
        register,
        logout,
        isAuthenticated: !!user,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);
  if (context === undefined) {
    throw new Error('useAuth doit être utilisé dans un AuthProvider');
  }
  return context;
}
```

### 4.3 Utilisation avec React Query

```tsx
// Exemple d'utilisation de React Query
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import ApiService from '@/services/api';

// Hook pour récupérer les produits
export function useProducts(page = 1, filters = {}) {
  return useQuery({
    queryKey: ['products', page, filters],
    queryFn: () => ApiService.getProducts(page, 15, filters),
    staleTime: 5 * 60 * 1000, // 5 minutes
  });
}

// Hook pour récupérer un produit
export function useProduct(productId: number) {
  return useQuery({
    queryKey: ['product', productId],
    queryFn: () => ApiService.getProduct(productId),
    enabled: !!productId,
  });
}
```

---

## 5. Services API

### 5.1 Structure du Service API

```typescript
// services/api.ts
import AsyncStorage from '@react-native-async-storage/async-storage';

const API_BASE_URL = 'http://localhost:8000/api';

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
  /**
   * Génère les headers avec ou sans authentification.
   */
  private static async getHeaders(includeAuth = false): Promise<HeadersInit> {
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

  /**
   * Connexion utilisateur.
   */
  static async login(credentials: LoginCredentials): Promise<AuthResponse> {
    const response = await fetch(`${API_BASE_URL}/auth/login`, {
      method: 'POST',
      headers: await this.getHeaders(),
      body: JSON.stringify(credentials),
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      const errorMessage = result.errors
        ? Object.values(result.errors).flat().join(', ')
        : result.message || 'Échec de la connexion';
      throw new Error(errorMessage);
    }

    await AsyncStorage.setItem('auth_token', result.data.token);
    return result.data;
  }

  /**
   * Inscription utilisateur.
   */
  static async register(credentials: RegisterCredentials): Promise<AuthResponse> {
    const response = await fetch(`${API_BASE_URL}/auth/register`, {
      method: 'POST',
      headers: await this.getHeaders(),
      body: JSON.stringify(credentials),
    });

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || "Échec de l'inscription");
    }

    await AsyncStorage.setItem('auth_token', result.data.token);
    return result.data;
  }

  /**
   * Déconnexion.
   */
  static async logout(): Promise<void> {
    try {
      await fetch(`${API_BASE_URL}/auth/logout`, {
        method: 'POST',
        headers: await this.getHeaders(true),
      });
    } finally {
      await AsyncStorage.removeItem('auth_token');
    }
  }

  /**
   * Récupère l'utilisateur courant.
   */
  static async getCurrentUser() {
    const response = await fetch(`${API_BASE_URL}/auth/me`, {
      method: 'GET',
      headers: await this.getHeaders(true),
    });

    if (!response.ok) {
      throw new Error("Impossible de récupérer l'utilisateur");
    }

    return await response.json();
  }

  /**
   * Récupère le token stocké.
   */
  static async getToken(): Promise<string | null> {
    return await AsyncStorage.getItem('auth_token');
  }

  /**
   * Liste des produits avec pagination et filtres.
   */
  static async getProducts(page = 1, perPage = 15, filters = {}) {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: perPage.toString(),
    });

    // Ajouter les filtres
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        params.append(key, String(value));
      }
    });

    const response = await fetch(
      `${API_BASE_URL}/products?${params.toString()}`,
      {
        method: 'GET',
        headers: await this.getHeaders(true),
      }
    );

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Impossible de récupérer les produits');
    }

    return result;
  }

  /**
   * Récupère un produit par ID.
   */
  static async getProduct(productId: number) {
    const response = await fetch(
      `${API_BASE_URL}/products/${productId}`,
      {
        method: 'GET',
        headers: await this.getHeaders(true),
      }
    );

    const result = await response.json();

    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Impossible de récupérer le produit');
    }

    return result.data;
  }
}

export default ApiService;
```

### 5.2 Endpoints Disponibles

| Méthode | Endpoint | Description |
|---------|----------|-------------|
| `login()` | POST `/auth/login` | Connexion |
| `register()` | POST `/auth/register` | Inscription |
| `logout()` | POST `/auth/logout` | Déconnexion |
| `getCurrentUser()` | GET `/auth/me` | Utilisateur courant |
| `getProducts()` | GET `/products` | Liste des produits |
| `getProduct()` | GET `/products/:id` | Détail d'un produit |

---

## 6. Composants

### 6.1 ProductCard

```tsx
// components/product-card.tsx
import { View, Text, Image, TouchableOpacity, StyleSheet } from 'react-native';
import { useRouter } from 'expo-router';

interface ProductCardProps {
  product: {
    id: number;
    name: string;
    description?: string;
    image_url?: string;
    price: number;
  };
}

export function ProductCard({ product }: ProductCardProps) {
  const router = useRouter();

  const handlePress = () => {
    router.push(`/product/options?id=${product.id}`);
  };

  return (
    <TouchableOpacity onPress={handlePress} style={styles.card}>
      <Image
        source={{ uri: product.image_url || 'https://placeholder.com/image' }}
        style={styles.image}
      />
      <View style={styles.content}>
        <Text style={styles.name}>{product.name}</Text>
        {product.description && (
          <Text style={styles.description} numberOfLines={2}>
            {product.description}
          </Text>
        )}
        <Text style={styles.price}>{product.price.toFixed(2)} €</Text>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: {
    backgroundColor: '#fff',
    borderRadius: 12,
    overflow: 'hidden',
    marginBottom: 16,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.1,
    shadowRadius: 4,
    elevation: 3,
  },
  image: {
    width: '100%',
    height: 150,
    resizeMode: 'cover',
  },
  content: {
    padding: 12,
  },
  name: {
    fontSize: 18,
    fontWeight: '600',
    marginBottom: 4,
  },
  description: {
    fontSize: 14,
    color: '#666',
    marginBottom: 8,
  },
  price: {
    fontSize: 16,
    fontWeight: '700',
    color: '#007AFF',
  },
});
```

### 6.2 ThemedComponents

```tsx
// components/themed-text.tsx
import { Text, TextProps, useColorScheme } from 'react-native';
import { Colors } from '@/constants/Colors';

export function ThemedText({ style, ...props }: TextProps) {
  const colorScheme = useColorScheme() ?? 'light';
  const color = Colors[colorScheme].text;

  return <Text style={[{ color }, style]} {...props} />;
}

// components/themed-view.tsx
import { View, ViewProps, useColorScheme } from 'react-native';
import { Colors } from '@/constants/Colors';

export function ThemedView({ style, ...props }: ViewProps) {
  const colorScheme = useColorScheme() ?? 'light';
  const backgroundColor = Colors[colorScheme].background;

  return <View style={[{ backgroundColor }, style]} {...props} />;
}
```

### 6.3 Liste des Composants

| Composant | Description |
|-----------|-------------|
| `ProductCard` | Carte de présentation produit |
| `ThemedText` | Texte avec support du thème |
| `ThemedView` | Container avec support du thème |
| `CustomHeader` | Header personnalisé |
| `ParallaxScrollView` | ScrollView avec effet parallax |
| `IconSymbol` | Icônes SF Symbols (iOS) / Material (Android) |
| `Collapsible` | Section dépliable |
| `HapticTab` | Tab avec retour haptique |
| `ExternalLink` | Lien vers navigateur externe |

---

## 7. Hooks Personnalisés

### 7.1 useColorScheme

```tsx
// hooks/use-color-scheme.ts
import { useColorScheme as useRNColorScheme } from 'react-native';

export function useColorScheme() {
  return useRNColorScheme() ?? 'light';
}
```

### 7.2 useThemeColor

```tsx
// hooks/use-theme-color.ts
import { Colors } from '@/constants/Colors';
import { useColorScheme } from './use-color-scheme';

export function useThemeColor(
  colorName: keyof typeof Colors.light & keyof typeof Colors.dark
) {
  const theme = useColorScheme();
  return Colors[theme][colorName];
}
```

### 7.3 useProtectedRoute

```tsx
// hooks/use-protected-route.ts
import { useEffect } from 'react';
import { useRouter, useSegments } from 'expo-router';
import { useAuth } from '@/contexts/AuthContext';

export function useProtectedRoute() {
  const { isAuthenticated, loading } = useAuth();
  const segments = useSegments();
  const router = useRouter();

  useEffect(() => {
    if (loading) return;

    const inAuthGroup = segments[0] === 'auth';

    if (!isAuthenticated && !inAuthGroup) {
      router.replace('/auth/login');
    } else if (isAuthenticated && inAuthGroup) {
      router.replace('/');
    }
  }, [isAuthenticated, loading, segments]);

  return { isAuthenticated, loading };
}
```

---

## 8. Theming et Design

### 8.1 Constantes de Couleurs

```typescript
// constants/Colors.ts
const tintColorLight = '#0a7ea4';
const tintColorDark = '#fff';

export const Colors = {
  light: {
    text: '#11181C',
    background: '#fff',
    tint: tintColorLight,
    icon: '#687076',
    tabIconDefault: '#687076',
    tabIconSelected: tintColorLight,
  },
  dark: {
    text: '#ECEDEE',
    background: '#151718',
    tint: tintColorDark,
    icon: '#9BA1A6',
    tabIconDefault: '#9BA1A6',
    tabIconSelected: tintColorDark,
  },
};
```

### 8.2 Support Dark Mode

```tsx
// Utilisation automatique du thème système
import { useColorScheme } from 'react-native';

function MyComponent() {
  const colorScheme = useColorScheme();

  const backgroundColor = colorScheme === 'dark'
    ? Colors.dark.background
    : Colors.light.background;

  return (
    <View style={{ backgroundColor }}>
      {/* ... */}
    </View>
  );
}
```

### 8.3 Cohérence UI/UX avec le Web

| Aspect | Mobile | Web |
|--------|--------|-----|
| Couleurs | `Colors.ts` | Tailwind config |
| Typographie | System fonts | Tailwind typography |
| Espacements | 4, 8, 12, 16, 24, 32 | Tailwind spacing |
| Radius | 4, 8, 12, 16 | Tailwind rounded |
| Shadows | StyleSheet | Tailwind shadow |

---

## 9. Gestion des Erreurs

### 9.1 Gestion des Erreurs API

```typescript
// Dans ApiService
static async handleResponse<T>(response: Response): Promise<T> {
  const result = await response.json();

  if (!response.ok || !result.success) {
    // Extraire le message d'erreur
    const errorMessage = result.errors
      ? Object.values(result.errors).flat().join(', ')
      : result.message || 'Une erreur est survenue';

    throw new Error(errorMessage);
  }

  return result.data;
}
```

### 9.2 Affichage des Erreurs

```tsx
// Composant d'écran avec gestion d'erreur
import { useProducts } from '@/hooks/useProducts';

function ProductsScreen() {
  const { data, isLoading, error, refetch } = useProducts();

  if (isLoading) {
    return <ActivityIndicator />;
  }

  if (error) {
    return (
      <View style={styles.errorContainer}>
        <Text style={styles.errorText}>{error.message}</Text>
        <TouchableOpacity onPress={() => refetch()}>
          <Text style={styles.retryButton}>Réessayer</Text>
        </TouchableOpacity>
      </View>
    );
  }

  return (
    <FlatList
      data={data}
      renderItem={({ item }) => <ProductCard product={item} />}
    />
  );
}
```

### 9.3 Toast/Notifications

```tsx
// Utilisation d'un système de notification
import Toast from 'react-native-toast-message';

// Afficher un succès
Toast.show({
  type: 'success',
  text1: 'Commande validée',
  text2: 'Votre commande est en préparation',
});

// Afficher une erreur
Toast.show({
  type: 'error',
  text1: 'Erreur',
  text2: 'Impossible de se connecter',
});
```

---

## 10. Performance

### 10.1 Optimisations Implémentées

```
┌─────────────────────────────────────────────────────────────────┐
│                    OPTIMISATIONS MOBILE                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   RENDU                                                         │
│   ─────────────────────────────────────────────────────────    │
│   ✅ FlatList pour les listes longues                          │
│   ✅ memo() pour les composants coûteux                        │
│   ✅ useCallback/useMemo pour éviter re-renders                │
│                                                                 │
│   IMAGES                                                        │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Lazy loading des images                                   │
│   ✅ Cache d'images (FastImage)                                │
│   ✅ Placeholders pendant le chargement                        │
│                                                                 │
│   DONNÉES                                                       │
│   ─────────────────────────────────────────────────────────    │
│   ✅ React Query cache                                         │
│   ✅ Pagination des listes                                     │
│   ✅ Background refetch                                        │
│                                                                 │
│   ANIMATIONS                                                    │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Reanimated sur le UI thread                               │
│   ✅ Gesture Handler natif                                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.2 Bonnes Pratiques FlatList

```tsx
// ✅ Optimisé
<FlatList
  data={products}
  renderItem={renderItem}
  keyExtractor={(item) => item.id.toString()}
  initialNumToRender={10}
  maxToRenderPerBatch={10}
  windowSize={5}
  removeClippedSubviews={true}
  getItemLayout={(data, index) => ({
    length: ITEM_HEIGHT,
    offset: ITEM_HEIGHT * index,
    index,
  })}
/>

// Composant mémorisé
const ProductCard = memo(({ product }: Props) => {
  // ...
});
```

### 10.3 Métriques Cibles

| Métrique | Cible |
|----------|-------|
| Time to Interactive | < 3s |
| Frame rate | 60 FPS |
| Memory usage | < 150MB |
| Bundle size | < 20MB |
| Startup time | < 2s |

---

## Ressources

- [Expo Documentation](https://docs.expo.dev/)
- [React Native Documentation](https://reactnative.dev/docs)
- [Expo Router](https://docs.expo.dev/router)
- [React Query](https://tanstack.com/query)
- [Reanimated](https://docs.swmansion.com/react-native-reanimated/)

---
