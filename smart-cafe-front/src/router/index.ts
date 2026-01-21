import { createRouter, createWebHistory } from 'vue-router'
import type { RouteRecordRaw } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
import { useGetMe } from '@/composable/API/Auth/useGetMe'

// Import des vues
import LoginView from '@/views/LoginView.vue'
import DashboardView from '@/views/DashboardView.vue'
import ManageOrderView from '@/views/ManageOrderView.vue'
import StoreUserView from '@/views/Admin/User/StoreUserView.vue'

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    redirect: '/login',
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guest: true },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true },
  },
  {
    path: '/manageorder',
    name: 'manageorder',
    component: ManageOrderView,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/users/create',
    name: 'admin-users-create',
    component: StoreUserView,
    meta: { requiresAuth: true },
  },
  {
    path: '/admin/users/:id',
    name: 'admin-user-show',
    component: () => import('@/views/Admin/User/ShowUserView.vue'),
    meta: { requiresAuth: true }, 
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()

  // Initialiser le token depuis le localStorage si nécessaire
  if (!authStore.authToken) {
    authStore.initializeAuth()
  }

  // Si on a un token mais pas d'utilisateur, on tente de récupérer l'utilisateur
  if (authStore.authToken && !authStore.currentUser) {
    const { execute } = useGetMe()
    const result = await execute()

    if (!result.success) {
      // Token invalide, on déconnecte
      authStore.logout()
    }
  }

  const isAuthenticated = authStore.isAuthenticated

  if (to.meta.requiresAuth && !isAuthenticated) {
    // Route protégée mais non connecté -> login
    next({ name: 'login' })
  } else if (to.meta.guest && isAuthenticated) {
    // Page guest (login) mais déjà connecté -> redirection
    next({ name: 'dashboard' })
  } else {
    next()
  }
})

export default router
