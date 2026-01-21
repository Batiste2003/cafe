<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import { useGetShowProduct } from '@/composable/API/Admin/Products/useGetShowProduct'
import { useGetShowProductVariant } from '@/composable/API/Admin/ProductVariants/useGetShowProductVariant'
import { useDeleteProductVariant } from '@/composable/API/Admin/ProductVariants/useDeleteProductVariant'
import { usePostAttachVariantGallery } from '@/composable/API/Admin/ProductVariants/usePostAttachVariantGallery'
import { useDeleteVariantGalleryImage } from '@/composable/API/Admin/ProductVariants/useDeleteVariantGalleryImage'
import Badge from '@/components/Badge.vue'
import Card from '@/components/Card.vue'
import Modal from '@/components/Modal.vue'
import FormUpdateProductVariant from '@/components/ProductVariants/Form/FormUpdateProductVariant.vue'
import ModalManageStocks from '@/components/ProductVariants/Stock/ModalManageStocks.vue'

const route = useRoute()
const router = useRouter()

const productId = Number(route.params.productId)
const variantId = Number(route.params.id)

const { product, isLoading: isLoadingProduct, execute: fetchProduct } = useGetShowProduct()
const { variant, isLoading: isLoadingVariant, error, execute: fetchVariant } = useGetShowProductVariant()
const { isLoading: isDeleting, execute: deleteVariant } = useDeleteProductVariant()
const { isLoading: isUploadingImage, execute: attachImage } = usePostAttachVariantGallery()
const { isLoading: isDeletingImage, execute: deleteImage } = useDeleteVariantGalleryImage()

const isEditModalOpen = ref(false)
const isAddImageModalOpen = ref(false)
const isManageStocksModalOpen = ref(false)
const selectedImages = ref<File[]>([])
const imagePreviewsToAdd = ref<string[]>([])

const getImageUrl = (url: string | undefined) => {
  return url || ''
}

const totalStock = computed(() => {
  if (!variant.value?.store_stocks) return 0
  return variant.value.store_stocks.reduce((total, stock) => {
    if (stock.is_unlimited) return Infinity
    return total + (stock.stock || 0)
  }, 0)
})

const hasUnlimitedStock = computed(() => {
  return variant.value?.store_stocks?.some(stock => stock.is_unlimited) || false
})

const stockBadgeVariant = computed(() => {
  if (hasUnlimitedStock.value) return 'primary'
  if (totalStock.value > 0) return 'success'
  return 'danger'
})

const stockText = computed(() => {
  if (hasUnlimitedStock.value) return 'Stock illimité'
  if (totalStock.value > 0) return `${totalStock.value} en stock`
  return 'Rupture de stock'
})

const loadData = async () => {
  await Promise.all([
    fetchProduct(productId),
    fetchVariant(productId, variantId)
  ])
}

const handleVariantUpdated = () => {
  isEditModalOpen.value = false
  loadData()
}

const handleDelete = async () => {
  if (!variant.value) return

  if (!confirm(`Êtes-vous sûr de vouloir supprimer la variante "${variant.value.sku}" ?`)) {
    return
  }

  const result = await deleteVariant(productId, variantId)

  if (result.success) {
    router.push({
      name: 'admin-product-variants-index',
      params: { productId }
    })
  }
}

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (!target.files) return

  const newFiles = Array.from(target.files)
  selectedImages.value.push(...newFiles)

  newFiles.forEach(file => {
    const reader = new FileReader()
    reader.onload = (e) => {
      if (e.target?.result) {
        imagePreviewsToAdd.value.push(e.target.result as string)
      }
    }
    reader.readAsDataURL(file)
  })
}

const removeImagePreview = (index: number) => {
  selectedImages.value.splice(index, 1)
  imagePreviewsToAdd.value.splice(index, 1)
}

const handleAddImage = async () => {
  if (selectedImages.value.length === 0) return

  // Upload each image one by one
  let allSuccess = true
  for (const image of selectedImages.value) {
    const result = await attachImage(productId, variantId, { image })
    if (!result.success) {
      allSuccess = false
      break
    }
  }

  if (allSuccess) {
    selectedImages.value = []
    imagePreviewsToAdd.value = []
    isAddImageModalOpen.value = false
    loadData()
  }
}

const closeAddImageModal = () => {
  selectedImages.value = []
  imagePreviewsToAdd.value = []
  isAddImageModalOpen.value = false
}

const handleDeleteImage = async (imageId: number) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) {
    return
  }

  const result = await deleteImage(productId, variantId, imageId)

  if (result.success) {
    loadData()
  }
}

const handleStocksUpdated = () => {
  loadData()
}

const goToProduct = () => {
  router.push({ name: 'admin-product-show', params: { id: productId } })
}

const goToVariants = () => {
  router.push({ name: 'admin-product-variants-index', params: { productId } })
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>
      <router-link
        :to="{ name: 'admin-products-index' }"
        class="hover:underline"
      >
        Produits
      </router-link>
      <span class="mx-2">/</span>
      <button
        v-if="product"
        @click="goToProduct"
        class="hover:underline"
      >
        {{ product.name }}
      </button>
      <span v-else class="animate-pulse bg-white/20 h-4 w-32 rounded inline-block"></span>
      <span class="mx-2">/</span>
      <button
        @click="goToVariants"
        class="hover:underline"
      >
        Variantes
      </button>
      <span class="mx-2">/</span>
      <span v-if="variant">{{ variant.sku }}</span>
      <span v-else class="animate-pulse bg-white/20 h-4 w-24 rounded inline-block"></span>
    </template>

    <div class="space-y-6">

    <!-- Loading State -->
    <div v-if="isLoadingVariant" class="flex items-center justify-center py-20">
        <div class="text-center">
          <i class="fas fa-spinner fa-spin text-4xl text-[var(--cafe-primary)] mb-4"></i>
        <p class="text-gray-600">Chargement de la variante...</p>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-white rounded-lg border border-red-200 p-8 text-center">
        <i class="fas fa-exclamation-circle text-4xl text-red-500 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Erreur de chargement</h3>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button
          @click="loadData"
          class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity"
        >
          <i class="fas fa-redo mr-2"></i>
        Réessayer
      </button>
    </div>

    <!-- Content -->
    <div v-else-if="variant" class="space-y-6">
      <!-- Section 1: Header -->
      <Card>
          <div class="flex items-start justify-between">
            <div class="flex-1">
              <div class="flex items-center gap-3 mb-4">
                <h1 class="text-3xl font-bold text-gray-900">{{ variant.sku }}</h1>
                <Badge v-if="variant.is_default" variant="primary">Par défaut</Badge>
                <Badge :variant="variant.is_deleted ? 'danger' : 'success'">
                  {{ variant.is_deleted ? 'Inactif' : 'Actif' }}
                </Badge>
                <Badge :variant="stockBadgeVariant">{{ stockText }}</Badge>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                  <span class="text-gray-600">Prix HT:</span>
                  <span class="ml-2 font-medium text-lg text-gray-900">
                    {{ variant.price_euros.toFixed(2) }} €
                  </span>
                </div>
                <div v-if="variant.cost_price_euros !== null">
                  <span class="text-gray-600">Prix de revient HT:</span>
                  <span class="ml-2 font-medium text-gray-900">
                    {{ variant.cost_price_euros.toFixed(2) }} €
                  </span>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button
                @click="isEditModalOpen = true"
                class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
              >
                <i class="fas fa-edit"></i>
                Modifier
              </button>
              <button
                @click="handleDelete"
                :disabled="isDeleting"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center gap-2"
              >
                <i v-if="isDeleting" class="fas fa-spinner fa-spin"></i>
                <i v-else class="fas fa-trash"></i>
                Supprimer
              </button>
          </div>
        </div>
      </Card>

      <!-- Section 2: Galerie -->
      <Card>
          <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Galerie d'images</h2>
            <button
              @click="isAddImageModalOpen = true"
              class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
            >
              <i class="fas fa-plus"></i>
              Ajouter une image
            </button>
          </div>

          <div v-if="variant.gallery && variant.gallery.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div
              v-for="(image, index) in variant.gallery"
              :key="image.id"
              class="group relative aspect-square bg-gray-100 rounded-lg overflow-hidden"
            >
              <img
                :src="getImageUrl(image.url)"
                :alt="`Image ${index + 1}`"
                class="w-full h-full object-cover"
              />
              <div v-if="index === 0" class="absolute top-2 left-2">
                <Badge variant="primary">Principal</Badge>
              </div>
              <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                <button
                  @click="handleDeleteImage(image.id)"
                  :disabled="isDeletingImage"
                  class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                >
                  <i v-if="isDeletingImage" class="fas fa-spinner fa-spin"></i>
                  <i v-else class="fas fa-trash"></i>
                </button>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-12">
            <i class="fas fa-images text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-600 mb-4">Aucune image dans la galerie</p>
            <button
              @click="isAddImageModalOpen = true"
              class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity inline-flex items-center gap-2"
            >
              <i class="fas fa-plus"></i>
              Ajouter une image
          </button>
        </div>
      </Card>

      <!-- Section 3: Stocks -->
      <Card>
          <div class="flex items-center justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold text-gray-900 mb-2">Stocks par magasin</h2>
              <p class="text-sm text-gray-600">
                {{ variant.store_stocks?.length || 0 }} magasin(s) configuré(s)
              </p>
            </div>
            <button
              @click="isManageStocksModalOpen = true"
              class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
            >
              <i class="fas fa-warehouse"></i>
              Gérer les stocks
            </button>
          </div>

          <div v-if="variant.store_stocks && variant.store_stocks.length > 0" class="space-y-3">
            <div
              v-for="stock in variant.store_stocks"
              :key="stock.id"
              class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
            >
              <div>
                <h3 class="font-medium text-gray-900">{{ stock.store?.name || 'Magasin inconnu' }}</h3>
                <p class="text-sm text-gray-600 mt-1">
                  Stock: <span class="font-medium">{{ stock.is_unlimited ? '∞' : stock.stock }}</span>
                </p>
              </div>
              <div class="flex items-center gap-2">
                <Badge :variant="stock.is_unlimited ? 'primary' : stock.is_in_stock ? 'success' : 'danger'">
                  {{ stock.is_unlimited ? 'Illimité' : stock.is_in_stock ? 'En stock' : 'Rupture' }}
                </Badge>
              </div>
            </div>
          </div>

          <div v-else class="text-center py-12">
            <i class="fas fa-warehouse text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-600 mb-4">Aucun stock configuré</p>
            <button
              @click="isManageStocksModalOpen = true"
              class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity inline-flex items-center gap-2"
            >
              <i class="fas fa-warehouse"></i>
              Gérer les stocks
          </button>
        </div>
      </Card>

      <!-- Section 4: Informations -->
      <Card>
          <h2 class="text-xl font-bold text-gray-900 mb-6">Informations</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Produit parent</label>
              <button
                v-if="variant.product"
                @click="goToProduct"
                class="text-[var(--cafe-primary)] hover:underline"
              >
                {{ variant.product.name }}
              </button>
              <button
                v-else-if="product"
                @click="goToProduct"
                class="text-[var(--cafe-primary)] hover:underline"
              >
                {{ product.name }}
              </button>
            </div>

            <div v-if="variant.option_values && variant.option_values.length > 0">
              <label class="block text-sm font-medium text-gray-700 mb-1">Options</label>
              <div class="flex flex-wrap gap-2">
                <Badge
                  v-for="option in variant.option_values"
                  :key="option.id"
                  variant="neutral"
                >
                  {{ option.value }}
                </Badge>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Créé le</label>
              <p class="text-gray-900">{{ new Date(variant.created_at).toLocaleString('fr-FR') }}</p>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Modifié le</label>
              <p class="text-gray-900">{{ new Date(variant.updated_at).toLocaleString('fr-FR') }}</p>
            </div>

            <div v-if="variant.deleted_at">
              <label class="block text-sm font-medium text-gray-700 mb-1">Supprimé le</label>
              <p class="text-red-600">{{ new Date(variant.deleted_at).toLocaleString('fr-FR') }}</p>
          </div>
        </div>
      </Card>
    </div>
    </div>

    <!-- Edit Modal -->
    <Modal
      :is-open="isEditModalOpen"
      title="Modifier la variante"
      size="large"
      @close="isEditModalOpen = false"
    >
      <div class="p-6">
        <FormUpdateProductVariant
          v-if="variant"
          :product-id="productId"
          :variant="variant"
          @variant-updated="handleVariantUpdated"
          @cancel="isEditModalOpen = false"
        />
      </div>
    </Modal>

    <!-- Add Image Modal -->
    <Modal
      :is-open="isAddImageModalOpen"
      title="Ajouter des images"
      size="large"
      @close="closeAddImageModal"
    >
      <div class="p-6">
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Sélectionner des images
            </label>
            <input
              type="file"
              accept="image/jpeg,image/png,image/gif,image/webp"
              multiple
              @change="handleImageChange"
              class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[var(--cafe-primary)] file:text-white hover:file:opacity-90"
            />
            <p class="mt-1 text-xs text-gray-500">
              Formats acceptés : JPEG, PNG, GIF, WebP. Taille maximale : 5 Mo par image.
            </p>
          </div>

          <div v-if="imagePreviewsToAdd.length > 0" class="grid grid-cols-3 gap-4">
            <div
              v-for="(preview, index) in imagePreviewsToAdd"
              :key="index"
              class="relative aspect-square bg-gray-100 rounded-lg overflow-hidden group"
            >
              <img :src="preview" class="w-full h-full object-cover" />
              <button
                @click="removeImagePreview(index)"
                class="absolute top-2 right-2 p-2 bg-red-600 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 relative z-10">
            <button
              type="button"
              @click.stop="closeAddImageModal"
              :disabled="isUploadingImage"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors disabled:opacity-50"
            >
              Annuler
            </button>
            <button
              type="button"
              @click.stop="handleAddImage"
              :disabled="isUploadingImage || selectedImages.length === 0"
              class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
            >
              <i v-if="isUploadingImage" class="fas fa-spinner fa-spin"></i>
              <i v-else class="fas fa-upload"></i>
              {{ isUploadingImage ? 'Envoi...' : 'Ajouter' }}
            </button>
          </div>
        </div>
      </div>
    </Modal>

    <!-- Manage Stocks Modal -->
    <ModalManageStocks
      :is-open="isManageStocksModalOpen"
      :product-id="productId"
      :variant-id="variantId"
      @close="isManageStocksModalOpen = false"
      @stocks-updated="handleStocksUpdated"
    />
  </DashboardLayout>
</template>
