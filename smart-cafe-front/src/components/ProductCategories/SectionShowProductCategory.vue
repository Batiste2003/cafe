<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Modal from '@/components/Modal.vue'
import ModalWarning from '@/components/ModalWarning.vue'
import FormUpdateProductCategory from '@/components/ProductCategories/Form/FormUpdateProductCategory.vue'
import { useGetShowProductCategory } from '@/composable/API/Admin/ProductCategories/useGetShowProductCategory'
import { useGetIndexProductCategory } from '@/composable/API/Admin/ProductCategories/useGetIndexProductCategory'
import { useDeleteProductCategory } from '@/composable/API/Admin/ProductCategories/useDeleteProductCategory'

interface Props {
  categoryId: number
}

const props = defineProps<Props>()
const router = useRouter()

const { category, isLoading, execute } = useGetShowProductCategory(props.categoryId)
const { categories: allCategories, execute: fetchAllCategories } = useGetIndexProductCategory()
const { isLoading: isDeleting, execute: executeDelete } = useDeleteProductCategory(props.categoryId)

const isEditModalOpen = ref(false)
const isDeleteModalOpen = ref(false)

onMounted(() => {
  execute()
  fetchAllCategories()
})

const openEditModal = () => {
  isEditModalOpen.value = true
}

const closeEditModal = () => {
  isEditModalOpen.value = false
}

const handleCategoryUpdated = () => {
  closeEditModal()
  execute()
}

const openDeleteModal = () => {
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
}

const handleDelete = async () => {
  const result = await executeDelete()

  if (result.success) {
    router.push({ name: 'admin-product-categories-index' })
  }
}

const availableCategories = computed(() => {
  return allCategories.value.filter((cat) => cat.id !== props.categoryId)
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
        <p class="mt-4 text-sm text-gray-500">Chargement...</p>
      </div>
    </Card>

    <!-- Category Details -->
    <template v-else-if="category">
      <!-- Main Info Card -->
      <Card>
        <div class="flex items-start justify-between mb-6">
          <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ category.name }}</h2>
            <p class="text-sm text-gray-400 font-mono">{{ category.slug }}</p>
          </div>
          <div class="flex items-center gap-2">
            <Badge :variant="category.is_active ? 'success' : 'secondary'">
              {{ category.is_active ? 'Actif' : 'Inactif' }}
            </Badge>
            <button
              @click="openEditModal"
              class="p-2 text-gray-600 hover:text-[var(--cafe-primary)] hover:bg-gray-100 rounded-lg transition-colors"
              title="Modifier"
            >
              <i class="fas fa-edit"></i>
            </button>
            <button
              @click="openDeleteModal"
              class="p-2 text-gray-600 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
              title="Supprimer"
            >
              <i class="fas fa-trash"></i>
            </button>
          </div>
        </div>

        <!-- Description -->
        <div v-if="category.description" class="mb-6">
          <h3 class="text-sm font-semibold text-gray-700 mb-2">Description</h3>
          <p class="text-sm text-gray-600">{{ category.description }}</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Produits</p>
            <p class="text-lg font-semibold text-gray-900">
              {{ category.products_count || 0 }}
            </p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Sous-catégories</p>
            <p class="text-lg font-semibold text-gray-900">
              {{ category.children?.length || 0 }}
            </p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Statut</p>
            <p class="text-lg font-semibold text-gray-900">
              {{ category.is_active ? 'Active' : 'Inactive' }}
            </p>
          </div>
        </div>

        <!-- Metadata -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Créée le</p>
            <p class="text-sm text-gray-900">{{ category.created_at }}</p>
          </div>
          <div>
            <p class="text-xs font-medium text-gray-500 mb-1">Modifiée le</p>
            <p class="text-sm text-gray-900">{{ category.updated_at }}</p>
          </div>
        </div>
      </Card>

      <!-- Parent Category Card -->
      <Card v-if="category.parent">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Catégorie parente</h3>
        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
          <div class="w-10 h-10 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white">
            <i class="fas fa-folder"></i>
          </div>
          <div class="flex-1">
            <p class="text-sm font-medium text-gray-900">{{ category.parent.name }}</p>
            <p class="text-xs text-gray-500">{{ category.parent.slug }}</p>
          </div>
          <Badge :variant="category.parent.is_active ? 'success' : 'secondary'">
            {{ category.parent.is_active ? 'Actif' : 'Inactif' }}
          </Badge>
        </div>
      </Card>

      <!-- Children Categories Card -->
      <Card v-if="category.children && category.children.length > 0">
        <h3 class="text-base font-semibold text-gray-900 mb-4">
          Sous-catégories ({{ category.children.length }})
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <div
            v-for="child in category.children"
            :key="child.id"
            class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg"
          >
            <div class="w-10 h-10 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white">
              <i class="fas fa-sitemap"></i>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900 truncate">{{ child.name }}</p>
              <p class="text-xs text-gray-500 truncate">{{ child.slug }}</p>
            </div>
            <Badge :variant="child.is_active ? 'success' : 'secondary'" class="flex-shrink-0">
              {{ child.is_active ? 'Actif' : 'Inactif' }}
            </Badge>
          </div>
        </div>
      </Card>
    </template>

    <!-- Modal for editing category -->
    <Modal
      v-if="category"
      :is-open="isEditModalOpen"
      title="Modifier la catégorie"
      @close="closeEditModal"
    >
      <FormUpdateProductCategory
        :category="category"
        :categories="availableCategories"
        @category-updated="handleCategoryUpdated"
        @cancel="closeEditModal"
      />
    </Modal>

    <!-- Modal for deleting category -->
    <ModalWarning
      v-if="category"
      :is-open="isDeleteModalOpen"
      title="Supprimer la catégorie"
      :subtitle="`Êtes-vous sûr de vouloir supprimer ${category.name} ?`"
      variant="danger"
      confirm-text="Supprimer"
      :is-loading="isDeleting"
      loading-text="Suppression..."
      @confirm="handleDelete"
      @cancel="closeDeleteModal"
      @close="closeDeleteModal"
    >
      <p class="text-sm text-gray-600">
        Cette action est irréversible. La catégorie
        <strong>{{ category.name }}</strong> sera définitivement supprimée du système.
      </p>
    </ModalWarning>
  </div>
</template>
