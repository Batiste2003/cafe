<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import Card from '@/components/Card.vue'
import Modal from '@/components/Modal.vue'
import ToolbarFilter from '@/components/Toolbar/ToolbarFilter.vue'
import CardUser from '@/components/Users/Cards/CardUser.vue'
import CardSkeletonUser from '@/components/Users/Skeleton/CardSkeletonUser.vue'
import TableUsers from '@/components/Users/Tables/TableUsers.vue'
import FormCreateUser from '@/components/Users/Form/FormCreateUser.vue'
import { useGetIndexUser } from '@/composable/API/Admin/Users/useGetIndexUser'
import type { User } from '@/types/User'

const { users, isLoading, execute } = useGetIndexUser()
const isModalOpen = ref(false)

const expandedUserId = ref<number | null>(null)
const activeFilter = ref('all')
const searchQuery = ref('')
const viewMode = ref<'cards' | 'table'>('cards')

const userFilters = [
  { value: 'all', label: 'Tous' },
  { value: 'verified', label: 'Email vérifié' },
  { value: 'unverified', label: 'Email non vérifié' },
  { value: 'active', label: 'Actifs' },
  { value: 'deleted', label: 'Supprimés' },
]

const filteredUsers = computed<User[]>(() => {
  let result = users.value

  // Apply active filter
  if (activeFilter.value !== 'all') {
    result = result.filter((user) => {
      switch (activeFilter.value) {
        case 'verified':
          return !!user.email_verified_at
        case 'unverified':
          return !user.email_verified_at
        case 'active':
          return !user.is_deleted
        case 'deleted':
          return user.is_deleted
        default:
          return true
      }
    })
  }

  // Apply search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(
      (user) =>
        user.id.toString().includes(query) ||
        (user.name && user.name.toLowerCase().includes(query)) ||
        (user.email && user.email.toLowerCase().includes(query)),
    )
  }

  return result
})

const toggleUserDetails = (userId: number) => {
  expandedUserId.value = expandedUserId.value === userId ? null : userId
}

const openModal = () => {
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const handleUserCreated = () => {
  closeModal()
  execute()
}

onMounted(() => {
  execute()
})
</script>

<template>
  <Card>
    <div
      class="flex flex-col lg:flex-row items-center justify-between gap-3 pb-4 border-b border-solid border-gray-200"
    >
      <div class="block">
        <h3 class="text-base font-semibold text-black mb-1">Utilisateurs</h3>
        <p class="text-xs font-normal text-gray-500">Gérez et suivez tous les utilisateurs</p>
      </div>
      <div class="flex flex-col min-[470px]:flex-row items-center gap-3">
        <button
          @click="openModal"
          class="px-4 py-2 bg--cafe-primary) text-white text-xs font-medium rounded-lg hover:bg-[#4a0000] transition-colors duration-200 flex items-center gap-2"
        >
          <i class="fas fa-user-plus" />
          Ajouter un utilisateur
        </button>
        <div class="relative text-gray-500 focus-within:text-gray-900">
          <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
            <i class="fas fa-search text-gray-400 text-xs" />
          </div>
          <input
            v-model="searchQuery"
            type="search"
            class="block w-full max-w-sm pr-3 pl-7 py-1.5 text-xs font-normal shadow-xs text-gray-900 bg-transparent border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none"
            placeholder="Rechercher"
          />
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row items-center justify-between my-4">
      <ToolbarFilter v-model="activeFilter" :filters="userFilters" />
      <ul class="p-0.5 rounded-md bg-gray-100 flex items-center flex-shrink-0">
        <button
          :class="{
            'bg-white': viewMode === 'cards',
            'bg-gray-100': viewMode !== 'cards',
          }"
          class="py-2 px-2.5 rounded-lg text-xs font-medium text-gray-900 transition-all duration-300 hover:bg-white"
          @click="viewMode = 'cards'"
        >
          <i class="fas fa-th-large" />
        </button>
        <button
          :class="{
            'bg-white': viewMode === 'table',
            'bg-gray-100': viewMode !== 'table',
          }"
          class="py-2 px-2.5 rounded-lg text-xs font-medium text-gray-900 transition-all duration-300 hover:bg-white"
          @click="viewMode = 'table'"
        >
          <i class="fas fa-bars" />
        </button>
      </ul>
    </div>

    <!-- Loading State with Skeleton -->
    <div v-if="isLoading" class="space-y-3">
      <CardSkeletonUser v-for="i in 5" :key="i" />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredUsers.length === 0"
      class="flex flex-col items-center justify-center py-12"
    >
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
        <i class="fas fa-users text-gray-400 text-3xl" />
      </div>
      <h3 class="text-base font-semibold text-gray-900 mb-1">Aucun utilisateur</h3>
      <p class="text-sm text-gray-500">Les utilisateurs apparaîtront ici</p>
    </div>

    <!-- Users List - Cards View -->
    <div v-else-if="viewMode === 'cards'" class="space-y-3">
      <CardUser v-for="user in filteredUsers" :key="user.id" :user="user" />
    </div>

    <!-- Users List - Table View -->
    <TableUsers
      v-else
      :users="filteredUsers"
      :expanded-user-id="expandedUserId"
      @toggle-user="toggleUserDetails"
    />
  </Card>

  <!-- Modal for creating user -->
  <Modal :is-open="isModalOpen" title="Ajouter un utilisateur" @close="closeModal">
    <FormCreateUser @user-created="handleUserCreated" @cancel="closeModal" />
  </Modal>
</template>
