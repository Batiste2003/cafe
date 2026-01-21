<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { usePostAttachUsersToStore } from '@/composable/API/Admin/Stores/usePostAttachUsersToStore'
import { useGetIndexUser } from '@/composable/API/Admin/Users/useGetIndexUser'
import type { User } from '@/types/User'

interface Props {
  storeId: number
  attachedUserIds: number[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'users-attached': []
  cancel: []
}>()

const { isLoading: isAttaching, error, execute } = usePostAttachUsersToStore(props.storeId)
const { users, isLoading: isLoadingUsers, execute: fetchUsers } = useGetIndexUser()

const selectedUserIds = ref<number[]>([])
const searchQuery = ref('')

const availableUsers = computed(() => {
  // Filter out users that are already attached to the store
  const filtered = users.value.filter((user) => !props.attachedUserIds.includes(user.id))

  // Apply search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    return filtered.filter(
      (user) =>
        user.name?.toLowerCase().includes(query) || user.email?.toLowerCase().includes(query),
    )
  }

  return filtered
})

const toggleUserSelection = (userId: number) => {
  const index = selectedUserIds.value.indexOf(userId)
  if (index === -1) {
    selectedUserIds.value.push(userId)
  } else {
    selectedUserIds.value.splice(index, 1)
  }
}

const isUserSelected = (userId: number) => {
  return selectedUserIds.value.includes(userId)
}

const handleSubmit = async () => {
  if (selectedUserIds.value.length === 0) {
    return
  }

  const result = await execute(selectedUserIds.value)

  if (result.success) {
    selectedUserIds.value = []
    emit('users-attached')
  }
}

const handleCancel = () => {
  selectedUserIds.value = []
  emit('cancel')
}

onMounted(() => {
  fetchUsers()
})
</script>

<template>
  <div class="bg-white rounded-xl p-6">
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Associer des utilisateurs</h3>
      <p class="text-sm text-gray-500">
        Sélectionnez les utilisateurs à associer au magasin
      </p>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <div class="relative text-gray-500 focus-within:text-gray-900">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
          <i class="fas fa-search text-gray-400 text-sm" />
        </div>
        <input
          v-model="searchQuery"
          type="search"
          class="block w-full pr-3 pl-10 py-2.5 text-sm font-normal shadow-xs text-gray-900 bg-transparent border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
          placeholder="Rechercher un utilisateur..."
        />
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoadingUsers" class="flex justify-center py-8">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[var(--cafe-primary)]"></div>
    </div>

    <!-- No Users Available -->
    <div
      v-else-if="availableUsers.length === 0"
      class="flex flex-col items-center justify-center py-8"
    >
      <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
        <i class="fas fa-users text-gray-400 text-xl" />
      </div>
      <p class="text-sm text-gray-500">
        {{ searchQuery ? 'Aucun utilisateur trouvé' : 'Tous les utilisateurs sont déjà associés' }}
      </p>
    </div>

    <!-- Users List -->
    <div v-else class="space-y-2 max-h-96 overflow-y-auto">
      <div
        v-for="user in availableUsers"
        :key="user.id"
        @click="toggleUserSelection(user.id)"
        :class="[
          'flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-all duration-200',
          isUserSelected(user.id)
            ? 'border-[var(--cafe-primary)] bg-[var(--cafe-primary)]/5'
            : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50',
        ]"
      >
        <div
          :class="[
            'w-5 h-5 rounded border-2 flex items-center justify-center flex-shrink-0 transition-colors',
            isUserSelected(user.id)
              ? 'border-[var(--cafe-primary)] bg-[var(--cafe-primary)]'
              : 'border-gray-300',
          ]"
        >
          <i v-if="isUserSelected(user.id)" class="fas fa-check text-white text-xs"></i>
        </div>
        <div
          class="w-10 h-10 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
        >
          {{ user.name?.charAt(0).toUpperCase() ?? 'U' }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
          <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
        </div>
      </div>
    </div>

    <!-- Selected Count -->
    <div v-if="selectedUserIds.length > 0" class="mt-4 p-3 bg-blue-50 rounded-lg">
      <p class="text-sm text-blue-700">
        <i class="fas fa-info-circle mr-2"></i>
        {{ selectedUserIds.length }} utilisateur{{ selectedUserIds.length > 1 ? 's' : '' }}
        sélectionné{{ selectedUserIds.length > 1 ? 's' : '' }}
      </p>
    </div>

    <!-- Error message -->
    <div v-if="error" class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
      <p class="text-sm text-red-600">
        <i class="fas fa-exclamation-circle mr-2"></i>
        {{ error }}
      </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-4 mt-4 border-t border-gray-100">
      <button
        type="button"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
        :disabled="isAttaching"
        @click="handleCancel"
      >
        Annuler
      </button>
      <button
        type="button"
        class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
        :disabled="isAttaching || selectedUserIds.length === 0"
        @click="handleSubmit"
      >
        <i v-if="isAttaching" class="fas fa-spinner fa-spin"></i>
        <i v-else class="fas fa-link"></i>
        {{ isAttaching ? 'Association...' : 'Associer' }}
      </button>
    </div>
  </div>
</template>
