<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const mobileMenuOpen = ref(false)

const toggleMobileMenu = () => {
  mobileMenuOpen.value = !mobileMenuOpen.value
}

const navItems = [
  { name: 'Dashboard', path: '/dashboard', icon: 'fa-solid fa-house' },
  { name: 'Gérer commandes', path: '/manageorder', icon: 'fa-solid fa-list' },
  { name: 'Créer utilisateur', path: '/admin/users/create', icon: 'fa-solid fa-user-plus' },
]

const isActive = (path: string) => route.path === path

const logout = () => {
  authStore.logout()
  router.push('/login')
}
</script>

<template>
  <div class="relative bg-(--cafe-primary) min-h-screen">
    <!-- Header -->
    <nav
      class="py-3.5 px-6 bg-(--cafe-primary) border-b border-solid border-white/20 fixed w-full top-0 z-20"
    >
      <div class="flex items-center justify-between gap-1 sm:gap-6 lg:flex-row flex-col">
        <div class="flex justify-between items-center lg:w-auto w-full">
          <router-link to="/dashboard" class="block">
            <h1 class="text-white text-2xl font-bold font-asset">Smart Café</h1>
          </router-link>
          <button
            type="button"
            class="inline-flex items-center p-2 ml-3 text-white rounded-lg lg:hidden hover:bg-white/10 focus:outline-none"
            @click="toggleMobileMenu"
          >
            <span class="sr-only">Ouvrir le menu</span>
            <i class="fa-solid fa-bars text-xl"></i>
          </button>
        </div>

        <div
          :class="[
            'lg:flex flex-row w-full flex-1 shadow-sm lg:shadow-none lg:bg-transparent rounded-xl py-4 lg:py-0',
            mobileMenuOpen ? 'flex' : 'hidden',
          ]"
        >
          <ul
            class="text-center flex lg:flex-row flex-col xl:gap-1 gap-2 lg:ml-auto lg:flex lg:bg-white/10 items-center p-1 rounded-xl"
          >
            <li v-for="item in navItems" :key="item.path">
              <router-link
                :to="item.path"
                :class="[
                  'py-2 px-5 flex justify-center items-center gap-2 transition-all duration-300 text-xs font-semibold rounded-lg',
                  isActive(item.path)
                    ? 'bg-white text-gray-900'
                    : 'bg-transparent text-white hover:bg-white hover:text-gray-900',
                ]"
              >
                <i :class="item.icon"></i>
                {{ item.name }}
              </router-link>
            </li>
          </ul>

          <div class="text-center lg:flex items-center gap-1 sm:gap-4 lg:ml-auto">
            <div class="flex items-center gap-1 sm:gap-2 lg:justify-start justify-center">
              <p class="text-white/40 font-normal">|</p>
              <span class="text-white text-sm font-medium px-3">
                {{ authStore.currentUser?.name ?? 'Utilisateur' }}
              </span>
            </div>

            <button
              class="group py-2 px-2 lg:pr-5 lg:pl-3.5 lg:mx-0 mx-auto flex items-center whitespace-nowrap gap-1.5 font-medium text-sm text-(--cafe-primary) border border-solid bg-(--cafe-secondary) rounded-lg transition-all duration-300 hover:bg-white"
              @click="logout"
            >
              <i class="fa-solid fa-right-from-bracket"></i>
              <span class="max-lg:hidden">Déconnexion</span>
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <div class="py-3.5 px-8 lg:mt-[72px] mt-[68px]">
      <div class="block max-lg:pl-6">
        <h6 class="text-sm sm:text-lg font-semibold text-white whitespace-nowrap mb-1.5">
          Bienvenue,
          <span class="text-white text-base sm:text-lg font-semibold">
            {{ authStore.currentUser?.name ?? 'Utilisateur' }} !
          </span>
        </h6>
        <p class="text-xs font-medium text-white/70">
          <slot name="breadcrumb">Dashboard</slot>
        </p>
      </div>
    </div>

    <div class="p-4">
      <div class="rounded-2xl bg-white overflow-hidden p-8 min-h-[calc(100vh-200px)]">
        <slot />
      </div>
    </div>
  </div>
</template>
