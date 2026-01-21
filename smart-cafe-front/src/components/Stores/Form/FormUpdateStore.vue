<script setup lang="ts">
import { ref, onMounted } from 'vue'
import {
  usePutUpdateStore,
  type PutUpdateStoreInterface,
} from '@/composable/API/Admin/Stores/usePutUpdateStore'
import { StoreStatusEnum, StoreStatusLabels } from '@/enums/StoreStatusEnum'
import type { Store } from '@/types/Store'

interface Props {
  store: Store
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'store-updated': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePutUpdateStore(
  props.store.id,
)

const formData = ref<{
  name: string
  status: StoreStatusEnum
  address_line1: string
  address_line2: string
  city: string
  postal_code: string
  country: string
}>({
  name: props.store.name,
  status: props.store.status || StoreStatusEnum.DRAFT,
  address_line1: props.store.address?.address_line1 || '',
  address_line2: props.store.address?.address_line2 || '',
  city: props.store.address?.city || '',
  postal_code: props.store.address?.postal_code || '',
  country: props.store.address?.country || 'France',
})

const bannerFile = ref<File | undefined>(undefined)
const logoFile = ref<File | undefined>(undefined)
const bannerPreview = ref<string | undefined>(undefined)
const logoPreview = ref<string | undefined>(undefined)
const removeBannerFlag = ref(false)
const removeLogoFlag = ref(false)

const availableStatuses = [
  { value: StoreStatusEnum.ACTIVE, label: StoreStatusLabels[StoreStatusEnum.ACTIVE] },
  { value: StoreStatusEnum.DRAFT, label: StoreStatusLabels[StoreStatusEnum.DRAFT] },
  { value: StoreStatusEnum.UNPUBLISH, label: StoreStatusLabels[StoreStatusEnum.UNPUBLISH] },
]

onMounted(() => {
  resetValidation()
})

const handleBannerChange = (event: Event) => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    bannerFile.value = file
    removeBannerFlag.value = false
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
    removeLogoFlag.value = false
    const reader = new FileReader()
    reader.onload = (e) => {
      logoPreview.value = e.target?.result as string
    }
    reader.readAsDataURL(file)
  }
}

const removeNewBanner = () => {
  bannerFile.value = undefined
  bannerPreview.value = undefined
}

const removeNewLogo = () => {
  logoFile.value = undefined
  logoPreview.value = undefined
}

const removeCurrentBanner = () => {
  removeBannerFlag.value = true
}

const removeCurrentLogo = () => {
  removeLogoFlag.value = true
}

const handleSubmit = async () => {
  const updateData: PutUpdateStoreInterface = {}

  // Only include changed fields
  if (formData.value.name !== props.store.name) {
    updateData.name = formData.value.name
  }
  if (formData.value.status !== props.store.status) {
    updateData.status = formData.value.status
  }
  if (formData.value.address_line1 !== props.store.address?.address_line1) {
    updateData.address_line1 = formData.value.address_line1
  }
  if (formData.value.address_line2 !== (props.store.address?.address_line2 || '')) {
    updateData.address_line2 = formData.value.address_line2
  }
  if (formData.value.city !== props.store.address?.city) {
    updateData.city = formData.value.city
  }
  if (formData.value.postal_code !== props.store.address?.postal_code) {
    updateData.postal_code = formData.value.postal_code
  }
  if (formData.value.country !== props.store.address?.country) {
    updateData.country = formData.value.country
  }

  // Handle file uploads and removals
  if (bannerFile.value) {
    updateData.banner = bannerFile.value
  }
  if (logoFile.value) {
    updateData.logo = logoFile.value
  }
  if (removeBannerFlag.value) {
    updateData.remove_banner = true
  }
  if (removeLogoFlag.value) {
    updateData.remove_logo = true
  }

  // If no changes, just close the modal
  if (Object.keys(updateData).length === 0) {
    emit('cancel')
    return
  }

  const result = await execute(updateData)

  if (result.success) {
    emit('store-updated')
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
      <h3 class="text-lg font-semibold text-gray-900">Modifier le magasin</h3>
      <p class="text-sm text-gray-500">Modifiez les informations du magasin</p>
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
          Bannière
        </label>

        <!-- Current banner -->
        <div v-if="store.banner && !removeBannerFlag && !bannerPreview" class="relative mb-2">
          <img
            :src="store.banner.url"
            alt="Current banner"
            class="w-full h-32 object-cover rounded-lg"
          />
          <button
            type="button"
            @click="removeCurrentBanner"
            class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- New banner preview -->
        <div v-if="bannerPreview" class="relative mb-2">
          <img :src="bannerPreview" alt="New banner preview" class="w-full h-32 object-cover rounded-lg" />
          <button
            type="button"
            @click="removeNewBanner"
            class="absolute top-2 right-2 w-8 h-8 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Removed banner indicator -->
        <div
          v-if="removeBannerFlag && !bannerPreview"
          class="mb-2 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between"
        >
          <p class="text-sm text-red-600">
            <i class="fas fa-trash mr-2"></i>
            La bannière sera supprimée
          </p>
          <button
            type="button"
            @click="removeBannerFlag = false"
            class="text-xs text-red-500 hover:text-red-700"
          >
            Annuler
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
          Logo
        </label>

        <!-- Current logo -->
        <div v-if="store.logo && !removeLogoFlag && !logoPreview" class="relative mb-2 w-24 h-24">
          <img
            :src="store.logo.url"
            alt="Current logo"
            class="w-full h-full object-cover rounded-lg"
          />
          <button
            type="button"
            @click="removeCurrentLogo"
            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors text-xs"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- New logo preview -->
        <div v-if="logoPreview" class="relative mb-2 w-24 h-24">
          <img :src="logoPreview" alt="New logo preview" class="w-full h-full object-cover rounded-lg" />
          <button
            type="button"
            @click="removeNewLogo"
            class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors text-xs"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>

        <!-- Removed logo indicator -->
        <div
          v-if="removeLogoFlag && !logoPreview"
          class="mb-2 p-3 bg-red-50 border border-red-200 rounded-lg flex items-center justify-between"
        >
          <p class="text-sm text-red-600">
            <i class="fas fa-trash mr-2"></i>
            Le logo sera supprimé
          </p>
          <button
            type="button"
            @click="removeLogoFlag = false"
            class="text-xs text-red-500 hover:text-red-700"
          >
            Annuler
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
          <i v-else class="fas fa-save"></i>
          {{ isLoading ? 'Enregistrement...' : 'Enregistrer' }}
        </button>
      </div>
    </form>
  </div>
</template>
