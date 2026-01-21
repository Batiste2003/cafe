import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User } from '@/types/User'

export type { User }

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)

  // Computed
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
    // Computed
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
