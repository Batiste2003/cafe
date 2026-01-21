<script setup lang="ts">
import { ref, watch } from 'vue'
import {
  usePostStoreProductCategory,
  type PostStoreProductCategoryInterface,
} from '@/composable/API/Admin/ProductCategories/usePostStoreProductCategory'
import type { ProductCategory } from '@/types/ProductCategory'

interface Props {
  categories?: ProductCategory[]
  preselectedParent?: ProductCategory | null
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'category-created': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } =
  usePostStoreProductCategory()

const formData = ref<PostStoreProductCategoryInterface>({
  name: '',
  description: '',
  parent_id: props.preselectedParent?.id || null,
  is_active: true,
})

// Watch for preselected parent changes
watch(
  () => props.preselectedParent,
  (newParent) => {
    if (newParent) {
      formData.value.parent_id = newParent.id
    }
  },
  { immediate: true }
)

const handleSubmit = async () => {
  const result = await execute(formData.value)

  if (result.success) {
    resetForm()
    emit('category-created')
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    description: '',
    parent_id: null,
    is_active: true,
  }
  resetValidation()
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}
</script>

<template>
  <div class="bg-white rounded-xl p-6">
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Créer une catégorie</h3>
      <p class="text-sm text-gray-500">
        Remplissez les informations pour créer une nouvelle catégorie
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-tag mr-2 text-gray-400"></i>
          Nom de la catégorie
        </label>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Boissons chaudes"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
            validationErrors.name
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        />
        <p v-if="validationErrors.name" class="mt-1 text-xs text-red-500">
          {{ validationErrors.name }}
        </p>
      </div>

      <!-- Description -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-align-left mr-2 text-gray-400"></i>
          Description (optionnelle)
        </label>
        <textarea
          v-model="formData.description"
          rows="3"
          placeholder="Description de la catégorie..."
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2 resize-none',
            validationErrors.description
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        ></textarea>
        <p v-if="validationErrors.description" class="mt-1 text-xs text-red-500">
          {{ validationErrors.description }}
        </p>
      </div>

      <!-- Parent Category -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-folder mr-2 text-gray-400"></i>
          Catégorie parente
          <span v-if="!preselectedParent" class="text-gray-500 font-normal">(optionnelle)</span>
        </label>

        <!-- Show preselected parent as badge if set -->
        <div v-if="preselectedParent" class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <i class="fas fa-folder text-blue-600"></i>
              <span class="text-sm font-medium text-blue-900">{{ preselectedParent.name }}</span>
            </div>
            <button
              type="button"
              @click="formData.parent_id = null"
              class="text-xs text-blue-600 hover:text-blue-800"
            >
              Retirer
            </button>
          </div>
        </div>

        <select
          v-else
          v-model="formData.parent_id"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2 bg-white',
            validationErrors.parent_id
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        >
          <option :value="null">Aucune (catégorie racine)</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <p v-if="validationErrors.parent_id" class="mt-1 text-xs text-red-500">
          {{ validationErrors.parent_id }}
        </p>
      </div>

      <!-- Is Active -->
      <div class="flex items-center gap-3">
        <input
          v-model="formData.is_active"
          type="checkbox"
          id="is_active"
          class="w-4 h-4 text-[var(--cafe-primary)] bg-gray-100 border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
        />
        <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
          Catégorie active
        </label>
      </div>

      <!-- Error message -->
      <div v-if="error" class="p-3 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-sm text-red-600">
          <i class="fas fa-exclamation-circle mr-2"></i>
          {{ error }}
        </p>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
        <button
          type="button"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
          :disabled="isLoading"
          @click="handleCancel"
        >
          Annuler
        </button>
        <button
          type="submit"
          class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          :disabled="isLoading"
        >
          <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-plus"></i>
          {{ isLoading ? 'Création...' : 'Créer catégorie' }}
        </button>
      </div>
    </form>
  </div>
</template>
