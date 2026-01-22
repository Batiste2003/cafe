# 03 - Frontend Web (Vue.js)

> Architecture et bonnes pratiques du dashboard administrateur Vue.js

---

## Table des matières

1. [Choix Technologiques](#1-choix-technologiques)
2. [Architecture Modulaire](#2-architecture-modulaire)
3. [Système de Composants](#3-système-de-composants)
4. [State Management (Pinia)](#4-state-management-pinia)
5. [Composables API](#5-composables-api)
6. [Routing et Navigation](#6-routing-et-navigation)
7. [Responsive Design](#7-responsive-design)
8. [Accessibilité](#8-accessibilité)
9. [Performance](#9-performance)
10. [Gestion des Erreurs](#10-gestion-des-erreurs)

---

## 1. Choix Technologiques

### 1.1 Stack Technique

| Technologie | Version | Rôle |
|-------------|---------|------|
| **Vue.js** | 3.5.26 | Framework frontend |
| **TypeScript** | 5.9.3 | Typage statique |
| **Pinia** | 3.0.4 | State management |
| **Vue Router** | 4.6.4 | Routing SPA |
| **Axios** | 1.13.2 | Client HTTP |
| **Tailwind CSS** | 4.1.18 | Framework CSS |
| **Vite** | - | Build tool |

### 1.2 Justification de Vue 3

```
┌─────────────────────────────────────────────────────────────────┐
│                    POURQUOI VUE 3 ?                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ✅ Composition API                                            │
│      • Meilleure organisation du code                          │
│      • Réutilisabilité via composables                         │
│      • TypeScript natif                                        │
│                                                                 │
│   ✅ Performance                                                │
│      • Virtual DOM optimisé                                    │
│      • Tree-shaking efficace                                   │
│      • Bundle size réduit (~20kb gzipped)                      │
│                                                                 │
│   ✅ Developer Experience                                       │
│      • DevTools excellents                                     │
│      • Hot Module Replacement rapide                           │
│      • Documentation complète                                  │
│                                                                 │
│   ✅ Écosystème                                                 │
│      • Pinia (state management moderne)                        │
│      • Vue Router (routage officiel)                           │
│      • Communauté active                                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.3 Pourquoi TypeScript ?

| Avantage | Impact |
|----------|--------|
| **Détection d'erreurs** | Erreurs détectées à la compilation |
| **Autocomplétion** | IntelliSense dans l'IDE |
| **Refactoring** | Refactoring sécurisé |
| **Documentation** | Types = documentation vivante |
| **Maintenabilité** | Code plus robuste en équipe |

---

## 2. Architecture Modulaire

### 2.1 Structure du Projet

```
smart-cafe-front/
├── src/
│   ├── assets/                     # Images, fonts, CSS global
│   │
│   ├── components/                 # Composants Vue réutilisables
│   │   ├── ui/                     # Composants génériques (Button, Card...)
│   │   ├── Products/               # Composants métier Produits
│   │   ├── ProductVariants/        # Composants Variantes
│   │   ├── ProductCategories/      # Composants Catégories
│   │   ├── Stores/                 # Composants Magasins
│   │   ├── Users/                  # Composants Utilisateurs
│   │   └── Toolbar/                # Composants de navigation
│   │
│   ├── composable/                 # Logique réutilisable (hooks Vue)
│   │   └── API/                    # Composables d'appels API
│   │       ├── Auth/               # Authentification
│   │       ├── Admin/              # Endpoints admin
│   │       │   ├── Users/
│   │       │   ├── Stores/
│   │       │   ├── Products/
│   │       │   ├── ProductVariants/
│   │       │   └── ProductCategories/
│   │       └── ...
│   │
│   ├── stores/                     # State management Pinia
│   │   └── authStore.ts            # Store d'authentification
│   │
│   ├── views/                      # Pages de l'application
│   │   ├── LoginView.vue
│   │   ├── DashboardView.vue
│   │   └── Admin/                  # Pages administration
│   │       ├── User/
│   │       ├── Store/
│   │       ├── Product/
│   │       ├── ProductCategory/
│   │       └── ProductVariant/
│   │
│   ├── router/                     # Configuration Vue Router
│   │   └── index.ts
│   │
│   ├── types/                      # Types TypeScript
│   │   ├── User.ts
│   │   ├── Store.ts
│   │   ├── Product.ts
│   │   └── ...
│   │
│   ├── layout/                     # Layouts de page
│   ├── enums/                      # Énumérations
│   ├── lib/                        # Utilitaires
│   └── services/                   # Services globaux
│
├── public/                         # Assets publics
├── index.html                      # Point d'entrée HTML
├── vite.config.ts                  # Configuration Vite
├── tailwind.config.js              # Configuration Tailwind
└── tsconfig.json                   # Configuration TypeScript
```

### 2.2 Principes d'Organisation

```
┌─────────────────────────────────────────────────────────────────┐
│              SÉPARATION DES RESPONSABILITÉS                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   views/                  → Pages (containers)                  │
│   ├── Orchestrent les composants                               │
│   ├── Gèrent le state local de page                            │
│   └── Appellent les composables                                │
│                                                                 │
│   components/             → UI (presentational)                 │
│   ├── Reçoivent des props                                      │
│   ├── Émettent des events                                      │
│   └── Pas de logique métier                                    │
│                                                                 │
│   composable/             → Logique (logic)                     │
│   ├── Encapsulent la logique réutilisable                      │
│   ├── Gèrent les appels API                                    │
│   └── Retournent des refs réactives                            │
│                                                                 │
│   stores/                 → État global (state)                 │
│   ├── Données partagées entre composants                       │
│   └── Persistance (localStorage)                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. Système de Composants

### 3.1 Hiérarchie des Composants

```
┌─────────────────────────────────────────────────────────────────┐
│                    HIÉRARCHIE COMPOSANTS                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   Layout                                                        │
│   └── View (Page)                                              │
│       ├── Toolbar                                              │
│       ├── Card Container                                       │
│       │   └── Entity Card                                      │
│       │       ├── Card Header                                  │
│       │       ├── Card Content                                 │
│       │       └── Card Actions (Buttons)                       │
│       ├── Form Container                                       │
│       │   └── Entity Form                                      │
│       │       ├── Input Fields                                 │
│       │       └── Submit Button                                │
│       └── Table (pour les listes)                              │
│           ├── Table Header                                     │
│           └── Table Rows                                       │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Exemple de Composant - ProductCard

```vue
<!-- components/Products/ProductCard.vue -->
<script setup lang="ts">
import type { Product } from '@/types/Product'

// Props typées
interface Props {
  product: Product
  loading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  loading: false
})

// Events typés
const emit = defineEmits<{
  (e: 'edit', product: Product): void
  (e: 'delete', id: number): void
}>()

// Méthodes
function handleEdit() {
  emit('edit', props.product)
}

function handleDelete() {
  emit('delete', props.product.id)
}
</script>

<template>
  <div class="product-card">
    <img :src="product.image" :alt="product.name" />
    <h3>{{ product.name }}</h3>
    <p>{{ product.price }} €</p>
    <div class="actions">
      <button @click="handleEdit">Modifier</button>
      <button @click="handleDelete">Supprimer</button>
    </div>
  </div>
</template>
```

### 3.3 Composants UI Génériques

| Composant | Fichier | Usage |
|-----------|---------|-------|
| **Button** | `ui/button.vue` | Actions (submit, cancel...) |
| **Card** | `ui/card.vue` | Container avec ombre |
| **Separator** | `ui/separator.vue` | Ligne de séparation |
| **Skeleton** | `*/Skeleton*.vue` | États de chargement |

### 3.4 Pattern Skeleton Loading

```vue
<!-- Exemple: ProductCardSkeleton.vue -->
<template>
  <div class="product-card skeleton">
    <div class="skeleton-image animate-pulse bg-gray-200" />
    <div class="skeleton-title animate-pulse bg-gray-200" />
    <div class="skeleton-price animate-pulse bg-gray-200" />
  </div>
</template>
```

Utilisation conditionnelle :
```vue
<template>
  <ProductCardSkeleton v-if="loading" />
  <ProductCard v-else :product="product" />
</template>
```

---

## 4. State Management (Pinia)

### 4.1 Architecture du Store

```
┌─────────────────────────────────────────────────────────────────┐
│                    PINIA STORE - authStore                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   STATE (Données réactives)                                     │
│   ─────────────────────────────────────────────────────────    │
│   • user: User | null                                          │
│   • token: string | null                                       │
│                                                                 │
│   GETTERS (Computed)                                            │
│   ─────────────────────────────────────────────────────────    │
│   • isAuthenticated: boolean                                   │
│   • currentUser: User | null                                   │
│   • authToken: string | null                                   │
│                                                                 │
│   ACTIONS (Méthodes)                                            │
│   ─────────────────────────────────────────────────────────    │
│   • login(user, token): void                                   │
│   • logout(): void                                             │
│   • setUser(user): void                                        │
│   • setToken(token): void                                      │
│   • initializeAuth(): void                                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Implémentation authStore

```typescript
// stores/authStore.ts
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/User'

export const useAuthStore = defineStore('auth', () => {
  // State
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)

  // Getters (computed)
  const isAuthenticated = computed(() => !!token.value && !!user.value)
  const currentUser = computed(() => user.value)
  const authToken = computed(() => token.value)

  // Actions
  function setUser(userData: User | null) {
    user.value = userData
  }

  function setToken(authToken: string | null) {
    token.value = authToken
    if (authToken) {
      localStorage.setItem('auth_token', authToken)
    } else {
      localStorage.removeItem('auth_token')
    }
  }

  function login(userData: User, authToken: string) {
    setUser(userData)
    setToken(authToken)
  }

  function logout() {
    setUser(null)
    setToken(null)
  }

  function initializeAuth() {
    const storedToken = localStorage.getItem('auth_token')
    if (storedToken) {
      token.value = storedToken
    }
  }

  return {
    // State
    user,
    token,
    // Getters
    isAuthenticated,
    currentUser,
    authToken,
    // Actions
    setUser,
    setToken,
    login,
    logout,
    initializeAuth,
  }
})
```

### 4.3 Utilisation dans un Composant

```vue
<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'

const authStore = useAuthStore()

// Accès aux getters (réactifs)
const isLoggedIn = authStore.isAuthenticated
const user = authStore.currentUser

// Appel d'actions
function handleLogout() {
  authStore.logout()
}
</script>
```

---

## 5. Composables API

### 5.1 Pattern des Composables

```
┌─────────────────────────────────────────────────────────────────┐
│                    PATTERN COMPOSABLE API                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   Chaque endpoint = 1 composable                                │
│   ─────────────────────────────────────────────────────────    │
│                                                                 │
│   useGetIndexProduct()     → GET /api/admin/products            │
│   useGetShowProduct()      → GET /api/admin/products/:id        │
│   usePostStoreProduct()    → POST /api/admin/products           │
│   usePutUpdateProduct()    → PUT /api/admin/products/:id        │
│   useDeleteProduct()       → DELETE /api/admin/products/:id     │
│                                                                 │
│   Retourne:                                                     │
│   ─────────────────────────────────────────────────────────    │
│   {                                                             │
│     data: Ref<T | null>,        // Données de réponse          │
│     loading: Ref<boolean>,      // État de chargement          │
│     error: Ref<string | null>,  // Message d'erreur            │
│     execute: () => Promise<R>   // Fonction d'exécution        │
│   }                                                             │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Exemple de Composable

```typescript
// composable/API/Admin/Products/useGetIndexProduct.ts
import { ref } from 'vue'
import axios from 'axios'
import type { Product } from '@/types/Product'
import { useAuthStore } from '@/stores/authStore'

export function useGetIndexProduct() {
  const data = ref<Product[] | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function execute(params?: { page?: number; per_page?: number }) {
    loading.value = true
    error.value = null

    const authStore = useAuthStore()

    try {
      const response = await axios.get('/api/admin/products', {
        params,
        headers: {
          Authorization: `Bearer ${authStore.authToken}`
        }
      })

      if (response.data.success) {
        data.value = response.data.data
        return { success: true, data: response.data.data }
      } else {
        error.value = response.data.error
        return { success: false, error: response.data.error }
      }
    } catch (err) {
      error.value = 'Erreur de connexion'
      return { success: false, error: 'Erreur de connexion' }
    } finally {
      loading.value = false
    }
  }

  return { data, loading, error, execute }
}
```

### 5.3 Liste des Composables Disponibles

| Module | Composable | Méthode | Endpoint |
|--------|------------|---------|----------|
| **Auth** | `useLoginPost` | POST | `/api/login` |
| **Auth** | `useLogoutPost` | POST | `/api/logout` |
| **Auth** | `useGetMe` | GET | `/api/me` |
| **Users** | `useGetIndexUser` | GET | `/api/admin/users` |
| **Users** | `useGetShowUser` | GET | `/api/admin/users/:id` |
| **Users** | `usePostStoreUser` | POST | `/api/admin/users` |
| **Users** | `usePutUpdateUser` | PUT | `/api/admin/users/:id` |
| **Users** | `useDeleteUser` | DELETE | `/api/admin/users/:id` |
| **Stores** | `useGetIndexStore` | GET | `/api/admin/stores` |
| **Stores** | `useGetShowStore` | GET | `/api/admin/stores/:id` |
| **Stores** | `usePostStoreStore` | POST | `/api/admin/stores` |
| **Products** | `useGetIndexProduct` | GET | `/api/admin/products` |
| **Products** | `useGetShowProduct` | GET | `/api/admin/products/:id` |
| **Products** | `usePostStoreProduct` | POST | `/api/admin/products` |
| ... | ... | ... | ... |

---

## 6. Routing et Navigation

### 6.1 Configuration des Routes

```typescript
// router/index.ts
const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guest: true },  // Accessible uniquement si non connecté
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },  // Nécessite authentification
  },
  // Routes admin avec lazy loading
  {
    path: '/admin/products',
    name: 'admin-products-index',
    component: () => import('@/views/Admin/Product/IndexProductView.vue'),
    meta: { requiresAuth: true },
  },
  // ...
]
```

### 6.2 Navigation Guards

```typescript
// Protection des routes
router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  // Initialiser le token depuis localStorage
  if (!authStore.authToken) {
    authStore.initializeAuth()
  }

  // Récupérer l'utilisateur si token présent
  if (authStore.authToken && !authStore.currentUser) {
    const { execute } = useGetMe()
    const result = await execute()

    if (!result.success) {
      authStore.logout()  // Token invalide
    }
  }

  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' })  // Redirection vers login
  } else if (to.meta.guest && isAuthenticated) {
    next({ name: 'dashboard' })  // Déjà connecté
  } else {
    next()
  }
})
```

### 6.3 Structure des Routes

```
/                           → Redirect vers /login
/login                      → Page de connexion (guest only)
/dashboard                  → Tableau de bord
/manageorder                → Gestion des commandes
/admin/users/create         → Création utilisateur
/admin/users/:id            → Détail utilisateur
/admin/stores               → Liste des magasins
/admin/stores/create        → Création magasin
/admin/stores/:id           → Détail magasin
/admin/product-categories   → Liste catégories
/admin/product-categories/:id → Détail catégorie
/admin/products             → Liste produits
/admin/products/:id         → Détail produit
/admin/products/:productId/variants → Variantes d'un produit
/admin/products/:productId/variants/:id → Détail variante
```

---

## 7. Responsive Design

### 7.1 Stratégie Mobile-First

```css
/* Tailwind utilise mobile-first par défaut */

/* Mobile (< 640px) - Style de base */
.card {
  @apply w-full p-4;
}

/* Tablette (>= 768px) */
@screen md {
  .card {
    @apply w-1/2 p-6;
  }
}

/* Desktop (>= 1024px) */
@screen lg {
  .card {
    @apply w-1/3 p-8;
  }
}
```

### 7.2 Breakpoints Tailwind

| Breakpoint | Taille | Usage |
|------------|--------|-------|
| `sm` | ≥ 640px | Grands téléphones |
| `md` | ≥ 768px | Tablettes portrait |
| `lg` | ≥ 1024px | Tablettes paysage / Laptops |
| `xl` | ≥ 1280px | Desktops |
| `2xl` | ≥ 1536px | Grands écrans |

### 7.3 Exemple Responsive Grid

```vue
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <ProductCard
      v-for="product in products"
      :key="product.id"
      :product="product"
    />
  </div>
</template>
```

---

## 8. Accessibilité

### 8.1 Normes WCAG 2.1

```
┌─────────────────────────────────────────────────────────────────┐
│                    CRITÈRES WCAG RESPECTÉS                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   PERCEPTIBLE                                                   │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Textes alternatifs sur les images                         │
│   ✅ Contrastes de couleur suffisants (ratio 4.5:1)            │
│   ✅ Pas d'information uniquement par la couleur               │
│                                                                 │
│   UTILISABLE                                                    │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Navigation au clavier complète                            │
│   ✅ Focus visible sur tous les éléments interactifs           │
│   ✅ Pas de piège au clavier                                   │
│                                                                 │
│   COMPRÉHENSIBLE                                                │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Labels sur les champs de formulaire                       │
│   ✅ Messages d'erreur explicites                              │
│   ✅ Langue de la page définie                                 │
│                                                                 │
│   ROBUSTE                                                       │
│   ─────────────────────────────────────────────────────────    │
│   ✅ HTML sémantique                                           │
│   ✅ Attributs ARIA où nécessaire                              │
│   ✅ Compatible lecteurs d'écran                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 8.2 Bonnes Pratiques Implémentées

```vue
<!-- Exemple de formulaire accessible -->
<template>
  <form @submit.prevent="handleSubmit" role="form">
    <div class="form-group">
      <label for="email" class="form-label">
        Adresse email
        <span class="text-red-500" aria-label="requis">*</span>
      </label>
      <input
        id="email"
        v-model="email"
        type="email"
        required
        aria-describedby="email-error"
        :aria-invalid="!!errors.email"
        class="form-input"
      />
      <p
        v-if="errors.email"
        id="email-error"
        class="error-message"
        role="alert"
      >
        {{ errors.email }}
      </p>
    </div>

    <button type="submit" :disabled="loading">
      <span v-if="loading" aria-hidden="true">⏳</span>
      {{ loading ? 'Chargement...' : 'Envoyer' }}
    </button>
  </form>
</template>
```

### 8.3 Checklist Accessibilité

- [ ] Toutes les images ont un attribut `alt`
- [ ] Les formulaires ont des `label` associés
- [ ] Les erreurs sont annoncées avec `role="alert"`
- [ ] La navigation au clavier fonctionne
- [ ] Le focus est visible
- [ ] Les couleurs ont un contraste suffisant
- [ ] La page a une langue définie (`<html lang="fr">`)

---

## 9. Performance

### 9.1 Optimisations Implémentées

```
┌─────────────────────────────────────────────────────────────────┐
│                    OPTIMISATIONS PERFORMANCE                    │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   BUILD                                                         │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Tree-shaking (Vite)                                       │
│   ✅ Code splitting automatique                                │
│   ✅ Minification CSS/JS                                       │
│   ✅ Compression Gzip/Brotli                                   │
│                                                                 │
│   RUNTIME                                                       │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Lazy loading des routes                                   │
│   ✅ Skeleton loaders (UX perçue)                              │
│   ✅ Debounce sur les recherches                               │
│   ✅ Pagination côté serveur                                   │
│                                                                 │
│   ASSETS                                                        │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Images optimisées                                         │
│   ✅ Fonts préchargées                                         │
│   ✅ Cache HTTP configuré                                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 9.2 Lazy Loading des Routes

```typescript
// Chargement différé des composants volumineux
{
  path: '/admin/products',
  component: () => import('@/views/Admin/Product/IndexProductView.vue')
  // Le bundle sera chargé uniquement quand la route est visitée
}
```

### 9.3 Métriques Cibles

| Métrique | Cible | Description |
|----------|-------|-------------|
| **FCP** | < 1.8s | First Contentful Paint |
| **LCP** | < 2.5s | Largest Contentful Paint |
| **TTI** | < 3.5s | Time to Interactive |
| **CLS** | < 0.1 | Cumulative Layout Shift |
| **Bundle** | < 200kb | Taille initiale gzippée |

---

## 10. Gestion des Erreurs

### 10.1 Stratégie de Gestion des Erreurs

```
┌─────────────────────────────────────────────────────────────────┐
│                    GESTION DES ERREURS                          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   Niveau Composable (API)                                       │
│   ─────────────────────────────────────────────────────────    │
│   • Capture des erreurs HTTP                                   │
│   • Retour de messages d'erreur typés                          │
│   • Logging en console (dev)                                   │
│                                                                 │
│   Niveau Composant                                              │
│   ─────────────────────────────────────────────────────────    │
│   • Affichage de messages d'erreur                             │
│   • États de chargement                                        │
│   • Boutons de retry                                           │
│                                                                 │
│   Niveau Global                                                 │
│   ─────────────────────────────────────────────────────────    │
│   • Error boundary (vue-error-boundary)                        │
│   • Intercepteurs Axios                                        │
│   • Page 404/500                                               │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.2 Affichage des Erreurs

```vue
<template>
  <div>
    <!-- État de chargement -->
    <ProductCardSkeleton v-if="loading" />

    <!-- Erreur -->
    <div v-else-if="error" class="error-container" role="alert">
      <p class="error-message">{{ error }}</p>
      <button @click="retry" class="btn-retry">
        Réessayer
      </button>
    </div>

    <!-- Succès -->
    <ProductCard v-else :product="product" />
  </div>
</template>
```

### 10.3 Feedback Utilisateur

| Type | Composant | Usage |
|------|-----------|-------|
| **Success** | Toast vert | Création/modification réussie |
| **Error** | Alert rouge | Erreur de validation/serveur |
| **Warning** | Alert orange | Action irréversible |
| **Info** | Toast bleu | Information contextuelle |
| **Loading** | Skeleton/Spinner | Chargement en cours |

---

## Ressources

- [Vue.js Documentation](https://vuejs.org/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Vue Router Documentation](https://router.vuejs.org/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---
