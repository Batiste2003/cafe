<script setup lang="ts">
import { ref, computed } from 'vue'
import Badge from '@/components/Badge.vue'
import type { StoreProductVariant } from '@/types/ProductVariant'
import { usePutUpdateVariantStock } from '@/composable/API/Admin/ProductVariants/usePutUpdateVariantStock'
import { useDeleteVariantStock } from '@/composable/API/Admin/ProductVariants/useDeleteVariantStock'

interface Props {
  productId: number
  variantId: number
  storeStock: StoreProductVariant
}

const props = defineProps<Props>()

const emit = defineEmits<{
  updated: []
  deleted: []
}>()

const { isLoading: isUpdating, error: updateError, validationErrors, execute: updateStock, resetValidation } = usePutUpdateVariantStock()
const { isLoading: isDeleting, error: deleteError, execute: deleteStock } = useDeleteVariantStock()

const isEditing = ref(false)
const stockValue = ref<number | null>(props.storeStock.stock)
const isUnlimited = ref(props.storeStock.is_unlimited)

const stockBadgeVariant = computed(() => {
  if (props.storeStock.is_unlimited) return 'primary'
  if (props.storeStock.is_in_stock) return 'success'
  return 'danger'
})

const stockBadgeText = computed(() => {
  if (props.storeStock.is_unlimited) return 'Illimité'
  if (props.storeStock.is_in_stock) return 'En stock'
  return 'Rupture'
})

const stockDisplay = computed(() => {
  if (props.storeStock.is_unlimited) return '∞'
  return props.storeStock.stock?.toString() || '0'
})

const startEdit = () => {
  isEditing.value = true
  stockValue.value = props.storeStock.stock
  isUnlimited.value = props.storeStock.is_unlimited
  resetValidation()
}

const cancelEdit = () => {
  isEditing.value = false
  stockValue.value = props.storeStock.stock
  isUnlimited.value = props.storeStock.is_unlimited
  resetValidation()
}

const handleUnlimitedToggle = () => {
  if (isUnlimited.value) {
    stockValue.value = null
  } else {
    stockValue.value = props.storeStock.stock || 0
  }
}

const handleSave = async () => {
  resetValidation()

  const result = await updateStock(
    props.productId,
    props.variantId,
    props.storeStock.store_id,
    {
      stock: isUnlimited.value ? null : stockValue.value,
    }
  )

  if (result.success) {
    isEditing.value = false
    emit('updated')
  }
}

const handleDelete = async () => {
  if (!confirm(`Êtes-vous sûr de vouloir supprimer le stock pour ${props.storeStock.store?.name || 'ce magasin'} ?`)) {
    return
  }

  const result = await deleteStock(props.productId, props.variantId, props.storeStock.store_id)

  if (result.success) {
    emit('deleted')
  }
}
</script>

<template>
  <div class="bg-white rounded-lg border border-gray-200 p-4">
    <!-- Mode affichage -->
    <div v-if="!isEditing" class="space-y-3">
      <div class="flex items-start justify-between">
        <div>
          <h4 class="font-medium text-gray-900">
            {{ storeStock.store?.name || 'Magasin inconnu' }}
          </h4>
          <p class="text-sm text-gray-500 mt-1">
            Stock: <span class="font-medium text-gray-900">{{ stockDisplay }}</span>
          </p>
        </div>
        <Badge :variant="stockBadgeVariant">
          {{ stockBadgeText }}
        </Badge>
      </div>

      <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
        <button
          type="button"
          @click="startEdit"
          class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
        >
          <i class="fas fa-edit mr-2"></i>
          Modifier
        </button>
        <button
          type="button"
          @click="handleDelete"
          :disabled="isDeleting"
          class="px-3 py-2 text-sm font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors disabled:opacity-50"
        >
          <i v-if="isDeleting" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-trash"></i>
        </button>
      </div>

      <p v-if="deleteError" class="text-sm text-red-600">{{ deleteError }}</p>
    </div>

    <!-- Mode édition -->
    <div v-else class="space-y-3">
      <div>
        <h4 class="font-medium text-gray-900 mb-3">
          {{ storeStock.store?.name || 'Magasin inconnu' }}
        </h4>

        <div class="flex items-center mb-3">
          <input
            v-model="isUnlimited"
            type="checkbox"
            class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)]"
            @change="handleUnlimitedToggle"
          />
          <label class="ml-2 text-sm font-medium text-gray-700">
            Stock illimité
          </label>
        </div>

        <input
          v-if="!isUnlimited"
          v-model.number="stockValue"
          type="number"
          min="0"
          step="1"
          class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
          :class="{ 'border-red-500': validationErrors.stock }"
          placeholder="Quantité en stock"
        />
        <p v-if="validationErrors.stock" class="mt-1 text-sm text-red-500">
          {{ validationErrors.stock }}
        </p>
      </div>

      <p v-if="updateError" class="text-sm text-red-600">{{ updateError }}</p>

      <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
        <button
          type="button"
          @click="cancelEdit"
          :disabled="isUpdating"
          class="flex-1 px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
        >
          Annuler
        </button>
        <button
          type="button"
          @click="handleSave"
          :disabled="isUpdating"
          class="flex-1 px-3 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50"
        >
          <i v-if="isUpdating" class="fas fa-spinner fa-spin mr-2"></i>
          <i v-else class="fas fa-save mr-2"></i>
          {{ isUpdating ? 'Sauvegarde...' : 'Sauvegarder' }}
        </button>
      </div>
    </div>
  </div>
</template>
