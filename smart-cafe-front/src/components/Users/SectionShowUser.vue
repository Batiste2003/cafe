<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Modal from '@/components/Modal.vue'
import ModalWarning from '@/components/ModalWarning.vue'
import FormUpdateUser from '@/components/Users/Form/FormUpdateUser.vue'
import { useGetShowUser } from '@/composable/API/Admin/Users/useGetShowUser'
import { useDeleteUser } from '@/composable/API/Admin/Users/useDeleteUser'

interface Props {
  userId: number
}

const props = defineProps<Props>()
const router = useRouter()

const { user, isLoading, execute } = useGetShowUser(props.userId)
const { isLoading: isDeleting, execute: executeDelete } = useDeleteUser(props.userId)

const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)

onMounted(() => {
  execute()
})

const openEditModal = () => {
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
}

const handleUserUpdated = () => {
  closeEditModal()
  execute()
}

const openDeleteModal = () => {
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
}

const handleDeleteConfirm = async () => {
  const result = await executeDelete()

  if (result.success) {
    closeDeleteModal()
    // Redirect to users list after successful deletion
    router.push({ name: 'dashboard' })
  }
}

const formattedCreatedAt = computed(() => {
  if (!user.value?.created_at) return 'N/A'
  return new Date(user.value.created_at).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})

const formattedUpdatedAt = computed(() => {
  if (!user.value?.updated_at) return 'N/A'
  return new Date(user.value.updated_at).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})

const emailVerifiedStatus = computed(() => {
  return user.value?.email_verified_at ? 'Vérifié' : 'Non vérifié'
})

const accountStatus = computed(() => {
  return user.value?.is_deleted ? 'Supprimé' : 'Actif'
})
</script>

<template>
  <div class="space-y-6">
    <!-- Loading State -->
    <Card v-if="isLoading">
      <div class="flex flex-col items-center justify-center py-12">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-[var(--cafe-primary)]"
        ></div>
        <p class="mt-4 text-sm text-gray-500">Chargement des informations...</p>
      </div>
    </Card>

    <!-- User Not Found -->
    <Card v-else-if="!user">
      <div class="flex flex-col items-center justify-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
          <i class="fas fa-user-slash text-gray-400 text-3xl" />
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-1">Utilisateur introuvable</h3>
        <p class="text-sm text-gray-500">Cet utilisateur n'existe pas ou a été supprimé</p>
      </div>
    </Card>

    <!-- User Details -->
    <template v-else-if="user">
      <!-- Header Card -->
      <Card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-start gap-4">
            <div
              class="w-16 h-16 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white text-2xl font-semibold flex-shrink-0"
            >
              {{ user.name.charAt(0).toUpperCase() }}
            </div>
            <div>
              <h2 class="text-xl font-semibold text-gray-900">{{ user.name }}</h2>
              <p class="text-sm text-gray-500 mt-1">{{ user.email }}</p>
              <div class="flex items-center gap-2 mt-2">
                <Badge :variant="user.email_verified_at ? 'success' : 'warning'">
                  {{ emailVerifiedStatus }}
                </Badge>
                <Badge :variant="user.is_deleted ? 'danger' : 'success'">
                  {{ accountStatus }}
                </Badge>
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            <button
              @click="openEditModal"
              class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2"
            >
              <i class="fas fa-edit" />
              Modifier
            </button>
            <button
              v-if="!user.is_deleted"
              @click="openDeleteModal"
              class="px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors duration-200 flex items-center gap-2"
            >
              <i class="fas fa-trash" />
              Supprimer
            </button>
          </div>
        </div>
      </Card>

      <!-- Roles Card -->
      <Card v-if="user.roles && user.roles.length > 0">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Rôles</h3>
        <div class="flex flex-wrap gap-2">
          <Badge v-for="role in user.roles" :key="role.id" variant="primary">
            {{ role.label || role.name }}
          </Badge>
        </div>
      </Card>

      <!-- Permissions Card -->
      <Card v-if="user.permissions && user.permissions.length > 0">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Permissions</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div
            v-for="permission in user.permissions"
            :key="permission.id"
            class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg"
          >
            <i class="fas fa-key text-[var(--cafe-primary)] text-sm" />
            <span class="text-sm text-gray-700">{{ permission.name }}</span>
          </div>
        </div>
      </Card>

      <!-- Additional Information Card -->
      <Card>
        <h3 class="text-base font-semibold text-gray-900 mb-4">Informations supplémentaires</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">ID Utilisateur</p>
            <p class="text-sm text-gray-900">#{{ user.id }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Email vérifié le</p>
            <p class="text-sm text-gray-900">
              {{
                user.email_verified_at
                  ? new Date(user.email_verified_at).toLocaleDateString('fr-FR', {
                      year: 'numeric',
                      month: 'long',
                      day: 'numeric',
                    })
                  : 'Non vérifié'
              }}
            </p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Créé le</p>
            <p class="text-sm text-gray-900">{{ formattedCreatedAt }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Dernière modification</p>
            <p class="text-sm text-gray-900">{{ formattedUpdatedAt }}</p>
          </div>
          <div v-if="user.deleted_at">
            <p class="text-xs font-medium text-gray-500 mb-1">Supprimé le</p>
            <p class="text-sm text-gray-900">
              {{
                new Date(user.deleted_at).toLocaleDateString('fr-FR', {
                  year: 'numeric',
                  month: 'long',
                  day: 'numeric',
                  hour: '2-digit',
                  minute: '2-digit',
                })
              }}
            </p>
          </div>
        </div>
      </Card>
    </template>

    <!-- Modal for editing user -->
    <Modal
      v-if="user"
      :is-open="isEditModalOpen"
      title="Modifier l'utilisateur"
      @close="closeEditModal"
    >
      <FormUpdateUser :user="user" @user-updated="handleUserUpdated" @cancel="closeEditModal" />
    </Modal>

    <!-- Modal for deleting user -->
    <ModalWarning
      v-if="user"
      :is-open="isDeleteModalOpen"
      title="Supprimer l'utilisateur"
      :subtitle="`Êtes-vous sûr de vouloir supprimer ${user.name} ?`"
      variant="danger"
      confirm-text="Supprimer"
      :is-loading="isDeleting"
      loading-text="Suppression..."
      @confirm="handleDeleteConfirm"
      @cancel="closeDeleteModal"
      @close="closeDeleteModal"
    >
      <p class="text-sm text-gray-600">
        Cette action est irréversible. L'utilisateur
        <strong>{{ user.name }}</strong> ({{ user.email }}) sera définitivement supprimé du
        système.
      </p>
    </ModalWarning>
  </div>
</template>
