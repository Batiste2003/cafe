<script setup lang="ts">
import { ref, computed } from 'vue'
import {
  usePostStoreUser,
  type PostStoreUserInterface,
} from '@/composable/API/Admin/Users/usePostStoreUser'
import { UserRoleEnum, UserRoleLabels } from '@/enums/UserRoleEnum'

const emit = defineEmits<{
  'user-created': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePostStoreUser()

const formData = ref<PostStoreUserInterface>({
  name: '',
  email: '',
  password: '',
  role: UserRoleEnum.EMPLOYER,
})

const showPassword = ref(false)

const availableRoles = computed(() => [
  { value: UserRoleEnum.MANAGER, label: UserRoleLabels[UserRoleEnum.MANAGER] },
  { value: UserRoleEnum.EMPLOYER, label: UserRoleLabels[UserRoleEnum.EMPLOYER] },
])

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const handleSubmit = async () => {
  const result = await execute(formData.value)

  if (result.success) {
    resetForm()
    emit('user-created')
  }
}

const resetForm = () => {
  formData.value = {
    name: '',
    email: '',
    password: '',
    role: UserRoleEnum.EMPLOYER,
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
      <h3 class="text-lg font-semibold text-gray-900">Créer un utilisateur</h3>
      <p class="text-sm text-gray-500">
        Remplissez les informations pour créer un nouvel utilisateur
      </p>
    </div>

    <form @submit.prevent="handleSubmit" class="space-y-5">
      <!-- Name -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-user mr-2 text-gray-400"></i>
          Nom
        </label>
        <input
          v-model="formData.name"
          type="text"
          placeholder="Jean Dupont"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
            validationErrors.name
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-(--cafe-primary)/20 focus:border-(--cafe-primary)',
          ]"
        />
        <p v-if="validationErrors.name" class="mt-1 text-xs text-red-500">
          {{ validationErrors.name }}
        </p>
      </div>

      <!-- Email -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-envelope mr-2 text-gray-400"></i>
          Email
        </label>
        <input
          v-model="formData.email"
          type="email"
          placeholder="jean.dupont@cafe.com"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
            validationErrors.email
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-(--cafe-primary)/20 focus:border-(--cafe-primary)',
          ]"
        />
        <p v-if="validationErrors.email" class="mt-1 text-xs text-red-500">
          {{ validationErrors.email }}
        </p>
      </div>

      <!-- Password -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-lock mr-2 text-gray-400"></i>
          Mot de passe
        </label>
        <div class="relative">
          <input
            v-model="formData.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="••••••••"
            :class="[
              'w-full px-4 py-2.5 pr-10 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2',
              validationErrors.password
                ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
                : 'border-gray-300 focus:ring-(--cafe-primary)/20 focus:border-(--cafe-primary)',
            ]"
          />
          <button
            type="button"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            @click="togglePasswordVisibility"
          >
            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
          </button>
        </div>
        <p v-if="validationErrors.password" class="mt-1 text-xs text-red-500">
          {{ validationErrors.password }}
        </p>
        <p class="mt-1 text-xs text-gray-400">Minimum 8 caractères</p>
      </div>

      <!-- Role -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          <i class="fas fa-user-tag mr-2 text-gray-400"></i>
          Rôle
        </label>
        <select
          v-model="formData.role"
          :class="[
            'w-full px-4 py-2.5 border rounded-lg text-sm transition-all duration-200 focus:outline-none focus:ring-2 bg-white',
            validationErrors.role
              ? 'border-red-300 focus:ring-red-200 focus:border-red-400'
              : 'border-gray-300 focus:ring-(--cafe-primary)/20 focus:border-(--cafe-primary)',
          ]"
        >
          <option v-for="role in availableRoles" :key="role.value" :value="role.value">
            {{ role.label }}
          </option>
        </select>
        <p v-if="validationErrors.role" class="mt-1 text-xs text-red-500">
          {{ validationErrors.role }}
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
          class="px-4 py-2 text-sm font-medium text-white bg-(--cafe-primary) rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          :disabled="isLoading"
        >
          <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-plus"></i>
          {{ isLoading ? 'Création...' : 'Créer utilisateur' }}
        </button>
      </div>
    </form>
  </div>
</template>
