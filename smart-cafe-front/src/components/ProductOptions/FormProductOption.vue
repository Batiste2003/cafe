<script setup lang="ts">
import { ref, watch } from 'vue'
import type { ProductOption } from '@/types/ProductOption'
import { usePostStoreProductOption, OPTION_NAME_MIN_LENGTH, OPTION_NAME_MAX_LENGTH } from '@/composable/API/Admin/ProductOptions/usePostStoreProductOption'
import { usePutUpdateProductOption } from '@/composable/API/Admin/ProductOptions/usePutUpdateProductOption'

interface Props {
  productId: number
  productOption?: ProductOption
}

const props = defineProps<Props>()

const emit = defineEmits<{
  saved: []
  cancel: []
}>()

const { isLoading: isCreating, validationErrors: createErrors, execute: createOption, resetValidation: resetCreateValidation } = usePostStoreProductOption()
const { isLoading: isUpdating, validationErrors: updateErrors, execute: updateOption, resetValidation: resetUpdateValidation } = usePutUpdateProductOption()

const isEditing = !!props.productOption

const formData = ref({
  name: props.productOption?.name || '',
  is_required: props.productOption?.is_required || false,
})

const validationErrors = isEditing ? updateErrors : createErrors
const isLoading = isEditing ? isUpdating : isCreating

watch(() => props.productOption, (newValue) => {
  if (newValue) {
    formData.value = {
      name: newValue.name,
      is_required: newValue.is_required,
    }
  }
})

const handleSubmit = async () => {
  if (isEditing) {
    resetUpdateValidation()
  } else {
    resetCreateValidation()
  }

  let result

  if (isEditing && props.productOption) {
    result = await updateOption(props.productId, props.productOption.id, formData.value)
  } else {
    result = await createOption(props.productId, formData.value)
  }

  if (result.success) {
    emit('saved')
  }
}

const handleCancel = () => {
  emit('cancel')
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-6">
    <!-- Name -->
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
        Nom de l'option <span class="text-red-500">*</span>
      </label>
      <input
        id="name"
        v-model="formData.name"
        type="text"
        :minlength="OPTION_NAME_MIN_LENGTH"
        :maxlength="OPTION_NAME_MAX_LENGTH"
        required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
        :class="{ 'border-red-500': validationErrors.name }"
        placeholder="Ex: Taille, Couleur, Goût..."
      />
      <p v-if="validationErrors.name" class="mt-1 text-sm text-red-600">
        {{ validationErrors.name }}
      </p>
      <p class="mt-1 text-xs text-gray-500">
        Entre {{ OPTION_NAME_MIN_LENGTH }} et {{ OPTION_NAME_MAX_LENGTH }} caractères
      </p>
    </div>

    <!-- Is Required -->
    <div class="flex items-center">
      <input
        id="is_required"
        v-model="formData.is_required"
        type="checkbox"
        class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
      />
      <label for="is_required" class="ml-2 block text-sm text-gray-700">
        Option obligatoire
      </label>
    </div>
    <p class="text-xs text-gray-500 -mt-4 ml-6">
      Si activé, le client devra obligatoirement choisir une valeur pour cette option
    </p>

    <!-- Actions -->
    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
      <button
        type="button"
        @click="handleCancel"
        :disabled="isLoading"
        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
      >
        Annuler
      </button>
      <button
        type="submit"
        :disabled="isLoading"
        class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center gap-2"
      >
        <i v-if="isLoading" class="fas fa-spinner fa-spin"></i>
        <i v-else class="fas fa-save"></i>
        {{ isEditing ? 'Modifier' : 'Créer' }}
      </button>
    </div>
  </form>
</template>
