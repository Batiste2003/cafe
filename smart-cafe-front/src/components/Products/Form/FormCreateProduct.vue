<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {
  usePostStoreProduct,
  type PostStoreProductInterface,
} from '@/composable/API/Admin/Products/usePostStoreProduct'
import { useGetIndexProductCategory } from '@/composable/API/Admin/ProductCategories/useGetIndexProductCategory'
import { useGetIndexStore } from '@/composable/API/Admin/Stores/useGetIndexStore'

const emit = defineEmits<{
  'product-created': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePostStoreProduct()
const { categories, execute: fetchCategories } = useGetIndexProductCategory()
const { stores, execute: fetchStores } = useGetIndexStore()

const formData = ref<PostStoreProductInterface>({
  name: '',
  description: '',
  product_category_id: 0,
  store_ids: [],
  is_active: true,
  is_featured: false,
  images: [],
})

const imageFiles = ref<File[]>([])
const imagePreviews = ref<string[]>([])

onMounted(async () => {
  await Promise.all([fetchCategories({ per_page: 1000 }), fetchStores({ per_page: 1000 })])
})

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    const newFiles = Array.from(target.files)
    imageFiles.value.push(...newFiles)
    formData.value.images = imageFiles.value

    // Generate previews
    newFiles.forEach((file) => {
      const reader = new FileReader()
      reader.onload = (e) => {
        if (e.target?.result) {
          imagePreviews.value.push(e.target.result as string)
        }
      }
      reader.readAsDataURL(file)
    })
  }
}

const removeImage = (index: number) => {
  imageFiles.value.splice(index, 1)
  imagePreviews.value.splice(index, 1)
  formData.value.images = imageFiles.value
}

const handleSubmit = async () => {
  const result = await execute(formData.value)

  if (result.success) {
    resetForm()
    emit('product-created')
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    description: '',
    product_category_id: 0,
    store_ids: [],
    is_active: true,
    is_featured: false,
    images: [],
  }
  imageFiles.value = []
  imagePreviews.value = []
  resetValidation()
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}

const toggleStore = (storeId: number) => {
  const index = formData.value.store_ids.indexOf(storeId)
  if (index > -1) {
    formData.value.store_ids.splice(index, 1)
  } else {
    formData.value.store_ids.push(storeId)
  }
}
</script>

<template>
  <div class="bg-white rounded-xl p-6">
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Créer un produit</h3>
      <p class="text-sm text-gray-500">Remplissez les informations pour créer un nouveau produit</p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-box mr-2 text-gray-400"></i>
          Nom du produit
        </label>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Café Latte"
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
          placeholder="Description du produit..."
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

      <!-- Category -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-tag mr-2 text-gray-400"></i>
          Catégorie
        </label>
        <select
          v-model="formData.product_category_id"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2 bg-white',
            validationErrors.product_category_id
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        >
          <option :value="0">Sélectionner une catégorie</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>
        <p v-if="validationErrors.product_category_id" class="mt-1 text-xs text-red-500">
          {{ validationErrors.product_category_id }}
        </p>
      </div>

      <!-- Stores -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fas fa-store mr-2 text-gray-400"></i>
          Magasins
        </label>
        <div class="space-y-2">
          <label
            v-for="store in stores"
            :key="store.id"
            class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors"
          >
            <input
              type="checkbox"
              :checked="formData.store_ids.includes(store.id)"
              @change="toggleStore(store.id)"
              class="w-4 h-4 text-[var(--cafe-primary)] bg-gray-100 border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
            />
            <span class="text-sm font-medium text-gray-700">{{ store.name }}</span>
          </label>
        </div>
        <p v-if="validationErrors.store_ids" class="mt-1 text-xs text-red-500">
          {{ validationErrors.store_ids }}
        </p>
      </div>

      <!-- Images -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
          <i class="fas fa-images mr-2 text-gray-400"></i>
          Images (optionnelles)
        </label>

        <div v-if="imagePreviews.length > 0" class="grid grid-cols-3 gap-3 mb-3">
          <div
            v-for="(preview, index) in imagePreviews"
            :key="index"
            class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 group"
          >
            <img :src="preview" alt="Preview" class="w-full h-full object-cover" />
            <button
              type="button"
              @click="removeImage(index)"
              class="absolute top-2 right-2 bg-red-500 text-white p-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
            >
              <i class="fas fa-times text-xs"></i>
            </button>
          </div>
        </div>

        <label
          class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
        >
          <div class="flex flex-col items-center justify-center pt-5 pb-6">
            <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
            <p class="text-sm text-gray-500">
              <span class="font-semibold">Cliquez pour ajouter</span> ou glissez-déposez
            </p>
            <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF ou WEBP (MAX. 5 Mo)</p>
          </div>
          <input
            type="file"
            class="hidden"
            accept="image/jpeg,image/png,image/gif,image/webp"
            multiple
            @change="handleImageChange"
          />
        </label>
        <p v-if="validationErrors.images" class="mt-1 text-xs text-red-500">
          {{ validationErrors.images }}
        </p>
      </div>

      <!-- Checkboxes -->
      <div class="space-y-3">
        <div class="flex items-center gap-3">
          <input
            v-model="formData.is_active"
            type="checkbox"
            id="is_active"
            class="w-4 h-4 text-[var(--cafe-primary)] bg-gray-100 border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
          />
          <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
            Produit actif
          </label>
        </div>

        <div class="flex items-center gap-3">
          <input
            v-model="formData.is_featured"
            type="checkbox"
            id="is_featured"
            class="w-4 h-4 text-[var(--cafe-primary)] bg-gray-100 border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
          />
          <label for="is_featured" class="text-sm font-medium text-gray-700 cursor-pointer">
            Produit mis en avant
          </label>
        </div>
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
          {{ isLoading ? 'Création...' : 'Créer produit' }}
        </button>
      </div>
    </form>
  </div>
</template>
