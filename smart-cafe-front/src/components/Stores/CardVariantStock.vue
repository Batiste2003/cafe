<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import type { StoreProductVariant } from '@/types/ProductVariant'
import Badge from '@/components/Badge.vue'
import { usePutUpdateVariantStock } from '@/composable/API/Admin/ProductVariants/usePutUpdateVariantStock'
import { useDeleteVariantStock } from '@/composable/API/Admin/ProductVariants/useDeleteVariantStock'

interface Props {
  storeStock: StoreProductVariant
}

const props = defineProps<Props>()

const emit = defineEmits<{
  updated: []
  deleted: []
}>()

const router = useRouter()

const { isLoading: isUpdating, execute: updateStock } = usePutUpdateVariantStock()
const { isLoading: isDeleting, execute: deleteStock } = useDeleteVariantStock()

const isEditing = ref(false)
const editStock = ref<number | null>(props.storeStock.stock)
const editIsUnlimited = ref(props.storeStock.is_unlimited)

const displayStock = computed({
  get: () => (editIsUnlimited.value ? '' : editStock.value?.toString() || '0'),
  set: (value: string) => {
    const parsed = parseInt(value)
    editStock.value = isNaN(parsed) ? 0 : parsed
  }
})

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

const handleEdit = () => {
  editStock.value = props.storeStock.stock
  editIsUnlimited.value = props.storeStock.is_unlimited
  isEditing.value = true
}

const handleCancel = () => {
  isEditing.value = false
  editStock.value = props.storeStock.stock
  editIsUnlimited.value = props.storeStock.is_unlimited
}

const handleUnlimitedChange = () => {
  if (editIsUnlimited.value) {
    editStock.value = null
  } else {
    editStock.value = 0
  }
}

const handleSave = async () => {
  if (!props.storeStock.variant || !props.storeStock.variant.product) return

  const result = await updateStock(
    props.storeStock.variant.product.id,
    props.storeStock.product_variant_id,
    props.storeStock.store_id,
    {
      stock: editIsUnlimited.value ? null : editStock.value
    }
  )

  if (result.success) {
    isEditing.value = false
    emit('updated')
  }
}

const handleDelete = async () => {
  if (!props.storeStock.variant || !props.storeStock.variant.product) return

  if (!confirm('Êtes-vous sûr de vouloir supprimer ce stock ?')) return

  const result = await deleteStock(
    props.storeStock.variant.product.id,
    props.storeStock.product_variant_id,
    props.storeStock.store_id
  )

  if (result.success) {
    emit('deleted')
  }
}

const goToVariant = () => {
  if (!props.storeStock.variant || !props.storeStock.variant.product) return

  router.push({
    name: 'admin-product-variant-show',
    params: {
      productId: props.storeStock.variant.product.id,
      id: props.storeStock.product_variant_id
    }
  })
}
</script>

<template>
  <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
    <div v-if="!isEditing" class="space-y-3">
      <!-- Header -->
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <button
            v-if="storeStock.variant"
            @click="goToVariant"
            class="text-sm font-semibold text-gray-900 hover:text-[var(--cafe-primary)] truncate block"
          >
            {{ storeStock.variant.sku }}
          </button>
          <p v-else class="text-sm font-semibold text-gray-900 truncate">
            Variante #{{ storeStock.product_variant_id }}
          </p>
          <p v-if="storeStock.variant?.product" class="text-xs text-gray-500 truncate mt-0.5">
            {{ storeStock.variant.product.name }}
          </p>
        </div>
        <Badge :variant="stockBadgeVariant" class="ml-2">
          {{ stockBadgeText }}
        </Badge>
      </div>

      <!-- Stock Info -->
      <div class="flex items-center justify-between text-sm">
        <span class="text-gray-600">Stock:</span>
        <span class="font-medium text-gray-900">
          {{ storeStock.is_unlimited ? '∞ Illimité' : storeStock.stock }}
        </span>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
        <button
          @click="handleEdit"
          class="flex-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors"
        >
          <i class="fas fa-edit mr-1"></i>
          Modifier
        </button>
        <button
          @click="handleDelete"
          :disabled="isDeleting"
          class="flex-1 px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100 transition-colors disabled:opacity-50"
        >
          <i v-if="isDeleting" class="fas fa-spinner fa-spin mr-1"></i>
          <i v-else class="fas fa-trash mr-1"></i>
          Supprimer
        </button>
      </div>
    </div>

    <!-- Edit Mode -->
    <div v-else class="space-y-3">
      <div>
        <p class="text-sm font-semibold text-gray-900 truncate">
          {{ storeStock.variant?.sku || `Variante #${storeStock.product_variant_id}` }}
        </p>
      </div>

      <div>
        <label class="block text-xs font-medium text-gray-700 mb-1.5">
          Stock
        </label>
        <input
          v-model="displayStock"
          type="number"
          min="0"
          :disabled="editIsUnlimited || isUpdating"
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)] disabled:bg-gray-100 disabled:cursor-not-allowed"
          placeholder="Quantité"
        />
      </div>

      <div class="flex items-center">
        <input
          id="unlimited-edit"
          v-model="editIsUnlimited"
          type="checkbox"
          :disabled="isUpdating"
          @change="handleUnlimitedChange"
          class="w-4 h-4 text-[var(--cafe-primary)] border-gray-300 rounded focus:ring-[var(--cafe-primary)] focus:ring-2"
        />
        <label for="unlimited-edit" class="ml-2 block text-xs text-gray-700">
          Stock illimité
        </label>
      </div>

      <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
        <button
          @click="handleCancel"
          :disabled="isUpdating"
          class="flex-1 px-3 py-1.5 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200 transition-colors disabled:opacity-50"
        >
          Annuler
        </button>
        <button
          @click="handleSave"
          :disabled="isUpdating"
          class="flex-1 px-3 py-1.5 text-xs font-medium text-white bg-[var(--cafe-primary)] rounded hover:opacity-90 transition-opacity disabled:opacity-50 flex items-center justify-center gap-1"
        >
          <i v-if="isUpdating" class="fas fa-spinner fa-spin"></i>
          <i v-else class="fas fa-save"></i>
          Sauvegarder
        </button>
      </div>
    </div>
  </div>
</template>
