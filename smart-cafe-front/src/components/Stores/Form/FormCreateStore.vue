<script setup lang="ts">
import { ref, computed } from 'vue'
import {
  usePostStoreStore,
  type PostStoreStoreInterface,
} from '@/composable/API/Admin/Stores/usePostStoreStore'
import { StoreStatusEnum, StoreStatusLabels } from '@/enums/StoreStatusEnum'

const emit = defineEmits<{
  'store-created': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePostStoreStore()

const formData = ref<PostStoreStoreInterface>({
  name: '',
  status: StoreStatusEnum.DRAFT,
  address_line1: '',
  address_line2: '',
  city: '',
  postal_code: '',
  country: 'France',
})

const bannerFile = ref<File | undefined>(undefined)
const logoFile = ref<File | undefined>(undefined)
const bannerPreview = ref<string | undefined>(undefined)
const logoPreview = ref<string | undefined>(undefined)

const availableStatuses = computed(() => [
  { value: StoreStatusEnum.ACTIVE, label: StoreStatusLabels[StoreStatusEnum.ACTIVE] },
  { value: StoreStatusEnum.DRAFT, label: StoreStatusLabels[StoreStatusEnum.DRAFT] },
  { value: StoreStatusEnum.UNPUBLISH, label: StoreStatusLabels[StoreStatusEnum.UNPUBLISH] },
])

const handleBannerChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    bannerFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      bannerPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const handleLogoChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    logoFile.value = file
    const reader = new FileReader()
    reader.onload = (e) => {
      logoPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const removeBanner = () => {
  bannerFile.value = undefined
  bannerPreview.value = undefined
}

const removeLogo = () => {
  logoFile.value = undefined
  logoPreview.value = undefined
}

const handleSubmit = async () => {
  const submitData = {
    ...formData.value,
    banner: bannerFile.value,
    logo: logoFile.value,
  }

  const result = await execute(submitData)

  if (result.success) {
    resetForm()
    emit('store-created')
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    status: StoreStatusEnum.DRAFT,
    address_line1: '',
    address_line2: '',
    city: '',
    postal_code: '',
    country: 'France',
  }
  bannerFile.value = undefined
  logoFile.value = undefined
  bannerPreview.value = undefined
  logoPreview.value = undefined
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
      <h3 class="text-lg font-semibold text-gray-900">Créer un magasin</h3>
      <p class="text-sm text-gray-500">
        Remplissez les informations pour créer un nouveau magasin
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-store mr-2 text-gray-400"></i>
          Nom du magasin
        </label>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Café Paris Centre"
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

      <!-- Status -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-toggle-on mr-2 text-gray-400"></i>
          Statut
        </label>
        <select
          v-model="formData.status"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2 bg-white',
            validationErrors.status
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        >
          <option v-for="status in availableStatuses" :key="status.value" :value="status.value">
            {{ status.label }}
          </option>
        </select>
        <p v-if="validationErrors.status" class="mt-1 text-xs text-red-500">
          {{ validationErrors.status }}
        </p>
      </div>

      <!-- Banner Upload -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-image mr-2 text-gray-400"></i>
          Bannière (optionnelle)
        </label>
        <div v-if="bannerPreview" class="relative mb-2">
          <img :src="bannerPreview" alt="Banner preview" class="w-full h-32 object-cover rounded-lg" />
          <button
            type="button"
            @click="removeBanner"
            class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
        <input
          type="file"
          accept="image/jpeg,image/jpg,image/png,image/webp"
          @change="handleBannerChange"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors"
        />
        <p v-if="validationErrors.banner" class="mt-1 text-xs text-red-500">
          {{ validationErrors.banner }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, WEBP. Taille max: 5MB</p>
      </div>

      <!-- Logo Upload -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-image mr-2 text-gray-400"></i>
          Logo (optionnel)
        </label>
        <div v-if="logoPreview" class="relative mb-2 w-24 h-24">
          <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover rounded-lg" />
          <button
            type="button"
            @click="removeLogo"
            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors text-xs"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
        <input
          type="file"
          accept="image/jpeg,image/jpg,image/png,image/webp"
          @change="handleLogoChange"
          class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 transition-colors"
        />
        <p v-if="validationErrors.logo" class="mt-1 text-xs text-red-500">
          {{ validationErrors.logo }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG, WEBP. Taille max: 2MB</p>
      </div>

      <!-- Address Line 1 -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
          Adresse
        </label>
        <input
          v-model="formData.address_line1"
          type="text"
          placeholder="123 Rue de la Paix"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
            validationErrors.address_line1
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        />
        <p v-if="validationErrors.address_line1" class="mt-1 text-xs text-red-500">
          {{ validationErrors.address_line1 }}
        </p>
      </div>

      <!-- Address Line 2 -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
          Complément d'adresse (optionnel)
        </label>
        <input
          v-model="formData.address_line2"
          type="text"
          placeholder="Appartement 4B"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
            validationErrors.address_line2
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        />
        <p v-if="validationErrors.address_line2" class="mt-1 text-xs text-red-500">
          {{ validationErrors.address_line2 }}
        </p>
      </div>

      <!-- City and Postal Code -->
      <div class="grid grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            <i class="fas fa-city mr-2 text-gray-400"></i>
            Ville
          </label>
          <input
            v-model="formData.city"
            type="text"
            placeholder="Paris"
            :class="[
              'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
              validationErrors.city
                ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
                : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
            ]"
          />
          <p v-if="validationErrors.city" class="mt-1 text-xs text-red-500">
            {{ validationErrors.city }}
          </p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            <i class="fas fa-mail-bulk mr-2 text-gray-400"></i>
            Code postal
          </label>
          <input
            v-model="formData.postal_code"
            type="text"
            placeholder="75001"
            :class="[
              'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
              validationErrors.postal_code
                ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
                : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
            ]"
          />
          <p v-if="validationErrors.postal_code" class="mt-1 text-xs text-red-500">
            {{ validationErrors.postal_code }}
          </p>
        </div>
      </div>

      <!-- Country -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-globe mr-2 text-gray-400"></i>
          Pays
        </label>
        <input
          v-model="formData.country"
          type="text"
          placeholder="France"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
            validationErrors.country
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
          ]"
        />
        <p v-if="validationErrors.country" class="mt-1 text-xs text-red-500">
          {{ validationErrors.country }}
        </p>
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
          {{ isLoading ? 'Création...' : 'Créer magasin' }}
        </button>
      </div>
    </form>
  </div>
</template>
