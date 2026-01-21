<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Modal from '@/components/Modal.vue'
import ModalWarning from '@/components/ModalWarning.vue'
import FormUpdateStore from '@/components/Stores/Form/FormUpdateStore.vue'
import FormAttachUsers from '@/components/Stores/Form/FormAttachUsers.vue'
import { useGetShowStore } from '@/composable/API/Admin/Stores/useGetShowStore'
import { useDeleteStore } from '@/composable/API/Admin/Stores/useDeleteStore'
import { useDeleteDetachUserFromStore } from '@/composable/API/Admin/Stores/useDeleteDetachUserFromStore'
import { useGetStoreVariantStocks } from '@/composable/API/Admin/Stores/useGetStoreVariantStocks'
import CardVariantStock from '@/components/Stores/CardVariantStock.vue'
import ModalAddVariantStock from '@/components/Stores/ModalAddVariantStock.vue'

interface Props {
  storeId: number
}

const props = defineProps<Props>()
const router = useRouter()

const { store, isLoading, execute } = useGetShowStore(props.storeId)
const { isLoading: isDeleting, execute: executeDelete } = useDeleteStore(props.storeId)
const { stocks: variantStocks, isLoading: isLoadingStocks, execute: fetchVariantStocks } = useGetStoreVariantStocks()

const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)
const isAttachUsersModalOpen = ref(false)
const isDetachUserModalOpen = ref(false)
const isAddStockModalOpen = ref(false)
const userToDetach = ref<{ id: number; name: string } | null>(null)
const isDetaching = ref(false)

onMounted(() => {
  execute()
  fetchVariantStocks(props.storeId)
})

const openEditModal = () => {
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
}

const handleStoreUpdated = () => {
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
    router.push({ name: 'dashboard' })
  }
}

const formattedCreatedAt = computed(() => {
  if (!store.value?.created_at) return 'N/A'
  return new Date(store.value.created_at).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})

const formattedUpdatedAt = computed(() => {
  if (!store.value?.updated_at) return 'N/A'
  return new Date(store.value.updated_at).toLocaleDateString('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
})

const statusVariant = computed(() => {
  if (!store.value?.status) return 'secondary'
  switch (store.value.status) {
    case 'active':
      return 'success'
    case 'draft':
      return 'warning'
    case 'unpublish':
      return 'secondary'
    default:
      return 'secondary'
  }
})

const attachedUserIds = computed(() => {
  return store.value?.users?.map((user) => user.id) || []
})

const openAttachUsersModal = () => {
  isAttachUsersModalOpen.value = true
}

const closeAttachUsersModal = () => {
  isAttachUsersModalOpen.value = false
}

const handleUsersAttached = () => {
  closeAttachUsersModal()
  execute()
}

const openDetachUserModal = (userId: number, userName: string) => {
  userToDetach.value = { id: userId, name: userName }
  isDetachUserModalOpen.value = true
}

const closeDetachUserModal = () => {
  isDetachUserModalOpen.value = false
  userToDetach.value = null
}

const handleDetachUserConfirm = async () => {
  if (!userToDetach.value) return

  isDetaching.value = true
  const { execute: executeDetach } = useDeleteDetachUserFromStore(
    props.storeId,
    userToDetach.value.id,
  )

  const result = await executeDetach()

  if (result.success) {
    closeDetachUserModal()
    execute()
  }
  isDetaching.value = false
}

const handleStockUpdated = () => {
  fetchVariantStocks(props.storeId)
}

const handleStockDeleted = () => {
  fetchVariantStocks(props.storeId)
}

const openAddStockModal = () => {
  isAddStockModalOpen.value = true
}

const closeAddStockModal = () => {
  isAddStockModalOpen.value = false
}

const handleStockAdded = () => {
  closeAddStockModal()
  fetchVariantStocks(props.storeId)
}
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

    <!-- Store Not Found -->
    <Card v-else-if="!store">
      <div class="flex flex-col items-center justify-center py-12">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
          <i class="fas fa-store-slash text-gray-400 text-3xl" />
        </div>
        <h3 class="text-base font-semibold text-gray-900 mb-1">Magasin introuvable</h3>
        <p class="text-sm text-gray-500">Ce magasin n'existe pas ou a été supprimé</p>
      </div>
    </Card>

    <!-- Store Details -->
    <template v-else-if="store">
      <!-- Banner Card -->
      <Card v-if="store.banner?.url">
        <div class="w-full h-64 rounded-lg overflow-hidden">
          <img :src="store.banner.url" :alt="store.name" class="w-full h-full object-cover" />
        </div>
      </Card>

      <!-- Header Card -->
      <Card>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <div class="flex items-start gap-4">
            <div
              v-if="store.logo?.url"
              class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0 border-2 border-gray-200"
            >
              <img :src="store.logo.url" :alt="store.name" class="w-full h-full object-cover" />
            </div>
            <div
              v-else
              class="w-16 h-16 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white text-2xl font-semibold flex-shrink-0"
            >
              {{ store.name.charAt(0).toUpperCase() }}
            </div>
            <div>
              <h2 class="text-xl font-semibold text-gray-900">{{ store.name }}</h2>
              <p v-if="store.address" class="text-sm text-gray-500 mt-1">
                <i class="fas fa-map-marker-alt mr-1"></i>
                {{ store.address.address_line1 }}
                <span v-if="store.address.address_line2">, {{ store.address.address_line2 }}</span>
              </p>
              <div class="flex items-center gap-2 mt-2">
                <Badge :variant="statusVariant">
                  {{ store.status_label }}
                </Badge>
                <Badge v-if="store.is_deleted" variant="danger">
                  <i class="fas fa-trash mr-1"></i>
                  Supprimé
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
              v-if="!store.is_deleted"
              @click="openDeleteModal"
              class="px-4 py-2 bg-red-100 text-red-700 text-sm font-medium rounded-lg hover:bg-red-200 transition-colors duration-200 flex items-center gap-2"
            >
              <i class="fas fa-trash" />
              Supprimer
            </button>
          </div>
        </div>
      </Card>

      <!-- Address Card -->
      <Card v-if="store.address">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Adresse</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Adresse</p>
            <p class="text-sm text-gray-900">{{ store.address.address_line1 }}</p>
            <p v-if="store.address.address_line2" class="text-sm text-gray-900">
              {{ store.address.address_line2 }}
            </p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Ville</p>
            <p class="text-sm text-gray-900">{{ store.address.city }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Code postal</p>
            <p class="text-sm text-gray-900">{{ store.address.postal_code }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Pays</p>
            <p class="text-sm text-gray-900">{{ store.address.country }}</p>
          </div>
        </div>
      </Card>

      <!-- Users Card -->
      <Card>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-semibold text-gray-900">
            Employés ({{ store.users?.length || 0 }})
          </h3>
          <button
            @click="openAttachUsersModal"
            class="px-3 py-1.5 bg-[var(--cafe-primary)] text-white text-xs font-medium rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
          >
            <i class="fas fa-user-plus" />
            Associer des utilisateurs
          </button>
        </div>

        <div v-if="!store.users || store.users.length === 0" class="text-center py-8">
          <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-users text-gray-400 text-xl" />
          </div>
          <p class="text-sm text-gray-500">Aucun employé associé</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div
            v-for="user in store.users"
            :key="user.id"
            class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg relative"
          >
            <div
              class="w-10 h-10 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
            >
              {{ user.name?.charAt(0).toUpperCase() ?? 'U' }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ user.name }}</p>
              <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
            </div>
            <button
              @click="openDetachUserModal(user.id, user.name)"
              class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
              title="Dissocier l'utilisateur"
            >
              <i class="fas fa-unlink text-xs"></i>
            </button>
          </div>
        </div>
      </Card>

      <!-- Variant Stocks Card -->
      <Card>
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-base font-semibold text-gray-900">
            Stocks des variantes ({{ variantStocks.length || 0 }})
          </h3>
          <button
            @click="openAddStockModal"
            class="px-3 py-1.5 bg-[var(--cafe-primary)] text-white text-xs font-medium rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
          >
            <i class="fas fa-plus" />
            Ajouter un stock
          </button>
        </div>

        <div v-if="isLoadingStocks" class="flex items-center justify-center py-8">
          <div class="text-center">
            <i class="fas fa-spinner fa-spin text-2xl text-[var(--cafe-primary)] mb-2"></i>
            <p class="text-xs text-gray-500">Chargement des stocks...</p>
          </div>
        </div>

        <div v-else-if="!variantStocks || variantStocks.length === 0" class="text-center py-8">
          <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-boxes text-gray-400 text-xl" />
          </div>
          <p class="text-sm text-gray-500">Aucun stock de variante configuré</p>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <CardVariantStock
            v-for="stock in variantStocks"
            :key="stock.id"
            :store-stock="stock"
            @updated="handleStockUpdated"
            @deleted="handleStockDeleted"
          />
        </div>
      </Card>

      <!-- Creator Card -->
      <Card v-if="store.creator">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Créateur</h3>
        <div class="flex items-center gap-3">
          <div
            class="w-12 h-12 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white font-semibold"
          >
            {{ store.creator.name?.charAt(0).toUpperCase() ?? 'U' }}
          </div>
          <div>
            <p class="text-sm font-medium text-gray-900">{{ store.creator.name }}</p>
            <p class="text-xs text-gray-500">{{ store.creator.email }}</p>
          </div>
        </div>
      </Card>

      <!-- Additional Information Card -->
      <Card>
        <h3 class="text-base font-semibold text-gray-900 mb-4">Informations supplémentaires</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">ID Magasin</p>
            <p class="text-sm text-gray-900">#{{ store.id }}</p>
          </div>
          <div v-if="store.users_count !== undefined">
            <p class="text-xs font-medium text-gray-500 mb-1">Nombre d'employés</p>
            <p class="text-sm text-gray-900">{{ store.users_count }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Créé le</p>
            <p class="text-sm text-gray-900">{{ formattedCreatedAt }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Dernière modification</p>
            <p class="text-sm text-gray-900">{{ formattedUpdatedAt }}</p>
          </div>
          <div v-if="store.deleted_at">
            <p class="text-xs font-medium text-gray-500 mb-1">Supprimé le</p>
            <p class="text-sm text-gray-900">
              {{
                new Date(store.deleted_at).toLocaleDateString('fr-FR', {
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

    <!-- Modal for editing store -->
    <Modal
      v-if="store"
      :is-open="isEditModalOpen"
      title="Modifier le magasin"
      @close="closeEditModal"
    >
      <FormUpdateStore
        :store="store"
        @store-updated="handleStoreUpdated"
        @cancel="closeEditModal"
      />
    </Modal>

    <!-- Modal for deleting store -->
    <ModalWarning
      v-if="store"
      :is-open="isDeleteModalOpen"
      title="Supprimer le magasin"
      :subtitle="`Êtes-vous sûr de vouloir supprimer ${store.name} ?`"
      variant="danger"
      confirm-text="Supprimer"
      :is-loading="isDeleting"
      loading-text="Suppression..."
      @confirm="handleDeleteConfirm"
      @cancel="closeDeleteModal"
      @close="closeDeleteModal"
    >
      <p class="text-sm text-gray-600">
        Cette action est irréversible. Le magasin
        <strong>{{ store.name }}</strong> sera définitivement supprimé du système.
      </p>
    </ModalWarning>

    <!-- Modal for attaching users -->
    <Modal
      v-if="store"
      :is-open="isAttachUsersModalOpen"
      title="Associer des utilisateurs"
      @close="closeAttachUsersModal"
    >
      <FormAttachUsers
        :store-id="store.id"
        :attached-user-ids="attachedUserIds"
        @users-attached="handleUsersAttached"
        @cancel="closeAttachUsersModal"
      />
    </Modal>

    <!-- Modal for detaching user -->
    <ModalWarning
      v-if="userToDetach"
      :is-open="isDetachUserModalOpen"
      title="Dissocier l'utilisateur"
      :subtitle="`Êtes-vous sûr de vouloir dissocier ${userToDetach.name} ?`"
      variant="warning"
      confirm-text="Dissocier"
      :is-loading="isDetaching"
      loading-text="Dissociation..."
      @confirm="handleDetachUserConfirm"
      @cancel="closeDetachUserModal"
      @close="closeDetachUserModal"
    >
      <p class="text-sm text-gray-600">
        L'utilisateur <strong>{{ userToDetach.name }}</strong> ne sera plus associé à ce magasin.
        Cette action peut être annulée en réassociant l'utilisateur.
      </p>
    </ModalWarning>

    <!-- Modal for adding variant stock -->
    <ModalAddVariantStock
      v-if="store"
      :is-open="isAddStockModalOpen"
      :store-id="store.id"
      :existing-stocks="variantStocks"
      @close="closeAddStockModal"
      @stock-added="handleStockAdded"
    />
  </div>
</template>
