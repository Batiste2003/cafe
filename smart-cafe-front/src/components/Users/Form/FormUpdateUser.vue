<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import {
  usePutUpdateUser,
  type PutUpdateUserInterface,
} from '@/composable/API/Admin/Users/usePutUpdateUser'
import { UserRoleEnum, UserRoleLabels } from '@/enums/UserRoleEnum'
import type { User } from '@/types/User'

interface Props {
  user: User
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'user-updated': []
  cancel: []
}>()

const { isLoading, error, validationErrors, execute, resetValidation } = usePutUpdateUser(
  props.user.id,
)

const formData = ref<PutUpdateUserInterface>({
  name: props.user.name,
  email: props.user.email,
  password: '',
  role: props.user.roles?.[0]?.name as UserRoleEnum,
})

const showPassword = ref(false)

const availableRoles = computed(() => [
  { value: UserRoleEnum.ADMIN, label: UserRoleLabels[UserRoleEnum.ADMIN] },
  { value: UserRoleEnum.MANAGER, label: UserRoleLabels[UserRoleEnum.MANAGER] },
  { value: UserRoleEnum.EMPLOYER, label: UserRoleLabels[UserRoleEnum.EMPLOYER] },
])

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const handleSubmit = async () => {
  // Only send fields that have changed or password if provided
  const updateData: PutUpdateUserInterface = {}

  if (formData.value.name !== props.user.name) {
    updateData.name = formData.value.name
  }

  if (formData.value.email !== props.user.email) {
    updateData.email = formData.value.email
  }

  if (formData.value.password && formData.value.password.length > 0) {
    updateData.password = formData.value.password
  }

  if (formData.value.role !== props.user.roles?.[0]?.name) {
    updateData.role = formData.value.role
  }

  // If nothing changed, don't submit
  if (Object.keys(updateData).length === 0) {
    emit('cancel')
    return
  }

  const result = await execute(updateData)

  if (result.success) {
    emit('user-updated')
  }
}

const handleCancel = () => {
  resetForm()
  emit('cancel')
}

const resetForm = () => {
  formData.value = {
    name: props.user.name,
    email: props.user.email,
    password: '',
    role: props.user.roles?.[0]?.name as UserRoleEnum,
  }
  resetValidation()
}

// Reset form when user prop changes
watch(
  () => props.user,
  (newUser) => {
    formData.value = {
      name: newUser.name,
      email: newUser.email,
      password: '',
      role: newUser.roles?.[0]?.name as UserRoleEnum,
    }
  },
)
</script>

<template>
  <div class="bg-white rounded-xl p-6">
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-900">Modifier l'utilisateur</h3>
      <p class="text-sm text-gray-500">
        Modifiez les informations de l'utilisateur {{ user.name }}
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
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
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
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
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
          Nouveau mot de passe (optionnel)
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
                : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
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
        <p class="mt-1 text-xs text-gray-400">
          Laissez vide pour conserver le mot de passe actuel
        </p>
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
              : 'border-gray-300 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]',
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
          class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
          :disabled="isLoading"
        >
          <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-save"></i>
          {{ isLoading ? 'Modification...' : 'Enregistrer' }}
        </button>
      </div>
    </form>
  </div>
</template>
