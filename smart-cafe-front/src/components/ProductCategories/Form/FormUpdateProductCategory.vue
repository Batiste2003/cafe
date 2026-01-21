<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {
  usePutUpdateProductCategory,
  type PutUpdateProductCategoryInterface,
} from '@/composable/API/Admin/ProductCategories/usePutUpdateProductCategory'
import type { ProductCategory } from '@/types/ProductCategory'

interface Props {
  category: ProductCategory
  categories?: ProductCategory[]
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'category-updated': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } =
  usePutUpdateProductCategory(props.category.id)

const formData = ref<{
  name: string
  description: string
  parent_id: number | null
  is_active: boolean
}>({
  name: props.category.name,
  description: props.category.description || '',
  parent_id: props.category.parent?.id || null,
  is_active: props.category.is_active,
})

onMounted(() => {
  resetValidation()
})

const handleSubmit = async () => {
  const updateData: PutUpdateProductCategoryInterface = {}

  // Only include changed fields
  if (formData.value.name !== props.category.name) {
    updateData.name = formData.value.name
  }
  if (formData.value.description !== (props.category.description || '')) {
    updateData.description = formData.value.description || null
  }
  if (formData.value.parent_id !== (props.category.parent?.id || null)) {
    updateData.parent_id = formData.value.parent_id
  }
  if (formData.value.is_active !== props.category.is_active) {
    updateData.is_active = formData.value.is_active
  }

  // If no changes, just close the modal
  if (Object.keys(updateData).length === 0) {
    emit('cancel')
    return
  }

  const result = await execute(updateData)

  if (result.success) {
    emit('category-updated')
  }
}

const handleCancel = () => {
  resetValidation()
  emit('cancel')
}
</script>

<template>
  <div class="bg-white rounded-xl p-6">
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Modifier la catégorie</h3>
      <p class="text-sm text-gray-500">Modifiez les informations de la catégorie</p>
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
          Catégorie parente (optionnelle)
        </label>
        <select
          v-model="formData.parent_id"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2 bg-white',
            validationErrors.parent_id
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        >
          <option :value="null">Aucune (catégorie racine)</option>
          <option
            v-for="cat in categories"
            :key="cat.id"
            :value="cat.id"
            :disabled="cat.id === category.id"
          >
            {{ cat.name }}
            {{ cat.id === category.id ? ' (cette catégorie)' : '' }}
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
          id="is_active_update"
          class="w-4 h-4 text-[var(--cafe-primary)] bg-gray-100 border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
        />
        <label for="is_active_update" class="text-sm font-medium text-gray-700 cursor-pointer">
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
          <i v-else class="fas fa-save"></i>
          {{ isLoading ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </div>
    </form>
  </div>
</template>
