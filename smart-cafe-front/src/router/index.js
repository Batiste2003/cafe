import { createRouter, createWebHistory } from 'vue-router'

// Import des vues
import LoginView from '@/views/LoginView.vue'
import ManageOrderView from '@/views/ManageOrderView.vue'
import TakeOrderView from '@/views/TakeOrderView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      redirect: '/login',
    },
    {
      path: '/login',
      name: 'login',
      component: LoginView,
    },
    {
      path: '/takeorder',
      name: 'takeorder',
      component: TakeOrderView,
      meta: { requiresAuth: true },
    },
    {
      path: '/manageorder',
      name: 'manageorder',
      component: ManageOrderView,
      meta: { requiresAuth: true },
    },
  ],
})

export default router
