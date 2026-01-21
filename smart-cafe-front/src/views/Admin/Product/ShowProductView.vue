<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { ProductOption } from '@/types/ProductOption'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import Card from '@/components/Card.vue'
import Badge from '@/components/Badge.vue'
import Modal from '@/components/Modal.vue'
import { useGetShowProduct } from '@/composable/API/Admin/Products/useGetShowProduct'
import { useDeleteProduct } from '@/composable/API/Admin/Products/useDeleteProduct'
import { usePostAttachGallery } from '@/composable/API/Admin/Products/usePostAttachGallery'
import { useDeleteGalleryImage } from '@/composable/API/Admin/Products/useDeleteGalleryImage'
import { useGetIndexProductOption } from '@/composable/API/Admin/ProductOptions/useGetIndexProductOption'
import { useDeleteProductOption } from '@/composable/API/Admin/ProductOptions/useDeleteProductOption'
import { useGetProductStores } from '@/composable/API/Admin/Products/useGetProductStores'
import { useDeleteDetachProductFromStore } from '@/composable/API/Admin/Products/useDeleteDetachProductFromStore'
import CardProductOption from '@/components/ProductOptions/CardProductOption.vue'
import FormProductOption from '@/components/ProductOptions/FormProductOption.vue'
import CardProductStore from '@/components/ProductStores/CardProductStore.vue'
import FormAttachStores from '@/components/ProductStores/FormAttachStores.vue'

const route = useRoute()
const router = useRouter()

const { product, isLoading, execute } = useGetShowProduct()
const { execute: deleteProduct } = useDeleteProduct()
const { execute: attachGallery } = usePostAttachGallery()
const { execute: deleteGalleryImage } = useDeleteGalleryImage()
const { options, execute: fetchOptions } = useGetIndexProductOption()
const { execute: deleteOption } = useDeleteProductOption()
const { stores, execute: fetchStores } = useGetProductStores()
const { execute: detachStore } = useDeleteDetachProductFromStore()

const isDeleteModalOpen = ref(false)
const isAddImageModalOpen = ref(false)
const selectedImage = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const isOptionModalOpen = ref(false)
const selectedOption = ref<ProductOption | null>(null)
const isAttachStoresModalOpen = ref(false)

const productId = computed(() => Number(route.params.id))

onMounted(async () => {
  await execute(productId.value)
  await fetchOptions(productId.value)
  await fetchStores(productId.value)
})

const getImageUrl = (url: string | undefined) => {
  return url || ''
}

const openDeleteModal = () => {
  isDeleteModalOpen.value = true
}

const closeDeleteModal = () => {
  isDeleteModalOpen.value = false
}

const handleDelete = async () => {
  const result = await deleteProduct(productId.value)

  if (result.success) {
    closeDeleteModal()
    router.push({ name: 'admin-products-index' })
  }
}

const openAddImageModal = () => {
  isAddImageModalOpen.value = true
}

const closeAddImageModal = () => {
  isAddImageModalOpen.value = false
  selectedImage.value = null
  imagePreview.value = null
}

const handleImageSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  console.log('Image selected:', target.files)
  if (target.files && target.files[0]) {
    selectedImage.value = target.files[0]
    console.log('Selected image set:', selectedImage.value)

    // Generate preview
    const reader = new FileReader()
    reader.onload = (e) => {
      if (e.target?.result) {
        imagePreview.value = e.target.result as string
        console.log('Preview generated')
      }
    }
    reader.readAsDataURL(target.files[0])
  }
}

const handleAddImage = async () => {
  console.log('Adding image...', selectedImage.value)
  if (!selectedImage.value) {
    console.log('No image selected, returning')
    return
  }

  console.log('Calling attachGallery...')
  const result = await attachGallery(productId.value, {
    image: selectedImage.value,
  })

  console.log('Result:', result)
  if (result.success && result.data) {
    product.value = result.data
    closeAddImageModal()
  }
}

const handleDeleteImage = async (imageId: number) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette image ?')) return

  const result = await deleteGalleryImage(productId.value, imageId)

  if (result.success && result.data) {
    product.value = result.data
  }
}

// Product Options handlers
const openOptionModal = () => {
  selectedOption.value = null
  isOptionModalOpen.value = true
}

const openEditOptionModal = (option: ProductOption) => {
  selectedOption.value = option
  isOptionModalOpen.value = true
}

const closeOptionModal = () => {
  selectedOption.value = null
  isOptionModalOpen.value = false
}

const handleOptionSaved = async () => {
  closeOptionModal()
  await fetchOptions(productId.value)
}

const handleDeleteOption = async (optionId: number) => {
  if (!confirm('Êtes-vous sûr de vouloir supprimer cette option ?')) return

  const result = await deleteOption(productId.value, optionId)

  if (result.success) {
    await fetchOptions(productId.value)
  }
}

// Product Stores handlers
const openAttachStoresModal = () => {
  isAttachStoresModalOpen.value = true
}

const closeAttachStoresModal = () => {
  isAttachStoresModalOpen.value = false
}

const handleStoresAttached = async () => {
  closeAttachStoresModal()
  await fetchStores(productId.value)
}

const handleDetachStore = async (storeId: number) => {
  if (!confirm('Êtes-vous sûr de vouloir dissocier ce magasin ?')) return

  const result = await detachStore(productId.value, storeId)

  if (result.success) {
    await fetchStores(productId.value)
  }
}
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>
      <router-link :to="{ name: 'admin-products-index' }" class="hover:underline">
        Produits
      </router-link>
      <span class="mx-2">/</span>
      <span>{{ product?.name || 'Chargement...' }}</span>
    </template>

    <div v-if="isLoading" class="flex justify-center items-center py-12">
      <i class="fas fa-spinner fa-spin text-4xl text-gray-400"></i>
    </div>

    <div v-else-if="product" class="space-y-6">
      <!-- Header -->
      <Card>
        <div class="flex items-start justify-between">
          <div class="flex-1">
            <div class="flex items-center gap-3 mb-2">
              <h1 class="text-2xl font-bold text-gray-900">{{ product.name }}</h1>
              <Badge :variant="product.is_active ? 'success' : 'neutral'">
                {{ product.is_active ? 'Actif' : 'Inactif' }}
              </Badge>
              <Badge v-if="product.is_featured" variant="warning">
                <i class="fas fa-star mr-1"></i>
                Featured
              </Badge>
            </div>
            <p v-if="product.slug" class="text-sm text-gray-500 font-mono">{{ product.slug }}</p>
          </div>

          <div class="flex items-center gap-2">
            <button
              @click="openDeleteModal"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2"
            >
              <i class="fas fa-trash"></i>
              Supprimer
            </button>
          </div>
        </div>
      </Card>

      <!-- Gallery -->
      <Card>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold text-gray-900">
            <i class="fas fa-images mr-2 text-gray-400"></i>
            Galerie d'images
          </h2>
          <button
            @click="openAddImageModal"
            class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
          >
            <i class="fas fa-plus"></i>
            Ajouter une image
          </button>
        </div>

        <div v-if="product.gallery && product.gallery.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div
            v-for="(image, index) in product.gallery"
            :key="image.id"
            class="relative aspect-square rounded-lg overflow-hidden bg-gray-100 group"
          >
            <img
              :src="getImageUrl(image.url)"
              :alt="`Image ${index + 1}`"
              class="w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
              <button
                @click="handleDeleteImage(image.id)"
                class="bg-red-500 text-white p-2 rounded-lg hover:bg-red-600 transition-colors"
              >
                <i class="fas fa-trash text-sm"></i>
              </button>
            </div>
            <div v-if="index === 0" class="absolute top-2 left-2">
              <Badge variant="primary">
                <i class="fas fa-star mr-1"></i>
                Principal
              </Badge>
            </div>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <i class="fas fa-images text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500">Aucune image dans la galerie</p>
        </div>
      </Card>

      <!-- Product Variants -->
      <Card>
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">
              <i class="fas fa-layer-group mr-2 text-gray-400"></i>
              Variantes
            </h2>
            <p v-if="product.variants_count !== undefined" class="text-sm text-gray-500 mt-1">
              {{ product.variants_count }} variante{{ product.variants_count > 1 ? 's' : '' }}
            </p>
          </div>
          <div class="flex items-center gap-2">
            <router-link
              :to="{ name: 'admin-product-variants-index', params: { productId: product.id } }"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2"
            >
              <i class="fas fa-list"></i>
              Voir toutes
            </router-link>
            <router-link
              :to="{ name: 'admin-product-variants-index', params: { productId: product.id } }"
              class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
            >
              <i class="fas fa-plus"></i>
              Ajouter une variante
            </router-link>
          </div>
        </div>

        <div v-if="product.variants && product.variants.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link
              v-for="variant in product.variants.slice(0, 4)"
              :key="variant.id"
              :to="{ name: 'admin-product-variant-show', params: { productId: product.id, id: variant.id } }"
              class="block p-4 border border-gray-200 rounded-lg hover:border-[var(--cafe-primary)] hover:shadow-sm transition-all"
            >
              <div class="flex items-start gap-3">
                <div
                  v-if="variant.gallery && variant.gallery.length > 0"
                  class="w-16 h-16 rounded-lg overflow-hidden bg-gray-100 flex-shrink-0"
                >
                  <img
                    :src="getImageUrl(variant.gallery[0].url)"
                    :alt="variant.sku"
                    class="w-full h-full object-cover"
                  />
                </div>
                <div
                  v-else
                  class="w-16 h-16 rounded-lg bg-gray-100 flex-shrink-0 flex items-center justify-center"
                >
                  <i class="fas fa-image text-gray-400"></i>
                </div>

                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 mb-1">
                    <p class="font-medium text-gray-900 truncate">{{ variant.sku }}</p>
                    <Badge v-if="variant.is_default" variant="primary" class="text-xs">Par défaut</Badge>
                  </div>
                  <p class="text-sm font-medium text-[var(--cafe-primary)]">
                    {{ variant.price_euros.toFixed(2) }} €
                  </p>
                  <p v-if="variant.gallery" class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-images mr-1"></i>
                    {{ variant.gallery.length }} image{{ variant.gallery.length > 1 ? 's' : '' }}
                  </p>
                </div>
              </div>
            </router-link>
          </div>

          <div v-if="product.variants_count && product.variants_count > 4" class="mt-4 text-center">
            <router-link
              :to="{ name: 'admin-product-variants-index', params: { productId: product.id } }"
              class="text-sm text-[var(--cafe-primary)] hover:underline"
            >
              Voir les {{ product.variants_count - 4 }} autres variante{{ (product.variants_count - 4) > 1 ? 's' : '' }}
            </router-link>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <i class="fas fa-layer-group text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500 mb-4">Aucune variante pour ce produit</p>
          <router-link
            :to="{ name: 'admin-product-variants-index', params: { productId: product.id } }"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity"
          >
            <i class="fas fa-plus"></i>
            Ajouter votre première variante
          </router-link>
        </div>
      </Card>

      <!-- Product Options -->
      <Card>
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">
              <i class="fas fa-sliders-h mr-2 text-gray-400"></i>
              Options du produit
            </h2>
            <p class="text-sm text-gray-500 mt-1">
              {{ options.length }} option{{ options.length > 1 ? 's' : '' }}
            </p>
          </div>
          <button
            @click="openOptionModal"
            class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
          >
            <i class="fas fa-plus"></i>
            Ajouter une option
          </button>
        </div>

        <div v-if="options.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <CardProductOption
              v-for="option in options"
              :key="option.id"
              :product-option="option"
              @edit="openEditOptionModal(option)"
              @delete="handleDeleteOption(option.id)"
            />
          </div>
        </div>

        <div v-else class="text-center py-8">
          <i class="fas fa-sliders-h text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500 mb-4">Aucune option configurée pour ce produit</p>
          <button
            @click="openOptionModal"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity"
          >
            <i class="fas fa-plus"></i>
            Ajouter votre première option
          </button>
        </div>
      </Card>

      <!-- Product Stores -->
      <Card>
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-lg font-semibold text-gray-900">
              <i class="fas fa-store mr-2 text-gray-400"></i>
              Magasins associés
            </h2>
            <p class="text-sm text-gray-500 mt-1">
              {{ stores.length }} magasin{{ stores.length > 1 ? 's' : '' }} associé{{ stores.length > 1 ? 's' : '' }}
            </p>
          </div>
          <button
            @click="openAttachStoresModal"
            class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
          >
            <i class="fas fa-link"></i>
            Associer des magasins
          </button>
        </div>

        <div v-if="stores.length > 0">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <CardProductStore
              v-for="store in stores"
              :key="store.id"
              :store="store"
              @detach="handleDetachStore(store.id)"
            />
          </div>
        </div>

        <div v-else class="text-center py-8">
          <i class="fas fa-store-slash text-gray-300 text-4xl mb-3"></i>
          <p class="text-gray-500 mb-4">Aucun magasin associé à ce produit</p>
          <button
            @click="openAttachStoresModal"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity"
          >
            <i class="fas fa-link"></i>
            Associer des magasins
          </button>
        </div>
      </Card>

      <!-- Product Details -->
      <Card>
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
          <i class="fas fa-info-circle mr-2 text-gray-400"></i>
          Informations
        </h2>

        <div class="space-y-4">
          <div v-if="product.description">
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <p class="text-gray-900">{{ product.description }}</p>
          </div>

          <div v-if="product.category">
            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
            <router-link
              :to="{ name: 'admin-product-category-show', params: { id: product.category.id } }"
              class="inline-flex items-center gap-2 text-[var(--cafe-primary)] hover:underline"
            >
              <i class="fas fa-tag"></i>
              {{ product.category.name }}
            </router-link>
          </div>

          <div v-if="product.stores && product.stores.length > 0">
            <label class="block text-sm font-medium text-gray-700 mb-2">Magasins</label>
            <div class="flex flex-wrap gap-2">
              <router-link
                v-for="store in product.stores"
                :key="store.id"
                :to="{ name: 'admin-store-show', params: { id: store.id } }"
                class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm"
              >
                <i class="fas fa-store"></i>
                {{ store.name }}
              </router-link>
            </div>
          </div>

          <div v-if="product.default_variant">
            <label class="block text-sm font-medium text-gray-700 mb-1">Prix</label>
            <p class="text-2xl font-bold text-gray-900">
              {{ product.default_variant.price_euros.toFixed(2) }} €
            </p>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Créé le</label>
              <p class="text-gray-900">{{ new Date(product.created_at).toLocaleString('fr-FR') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Modifié le</label>
              <p class="text-gray-900">{{ new Date(product.updated_at).toLocaleString('fr-FR') }}</p>
            </div>
          </div>
        </div>
      </Card>

      <!-- Delete Modal -->
      <Modal :is-open="isDeleteModalOpen" title="Supprimer le produit" @close="closeDeleteModal">
        <div class="p-6">
          <p class="text-gray-700 mb-4">
            Êtes-vous sûr de vouloir supprimer le produit
            <strong>{{ product.name }}</strong> ?
          </p>
          <p class="text-sm text-gray-500 mb-6">Cette action peut être annulée.</p>

          <div class="flex items-center justify-end gap-3">
            <button
              @click="closeDeleteModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Annuler
            </button>
            <button
              @click="handleDelete"
              class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
            >
              <i class="fas fa-trash mr-2"></i>
              Supprimer
            </button>
          </div>
        </div>
      </Modal>

      <!-- Add Image Modal -->
      <Modal :is-open="isAddImageModalOpen" title="Ajouter une image" @close="closeAddImageModal">
        <div class="p-6">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Image</label>

            <div v-if="imagePreview" class="mb-4">
              <img
                :src="imagePreview"
                alt="Preview"
                class="w-full max-h-64 object-contain rounded-lg border border-gray-200"
              />
            </div>

            <label
              class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors"
            >
              <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <i class="fas fa-cloud-upload-alt text-gray-400 text-2xl mb-2"></i>
                <p class="text-sm text-gray-500">
                  <span class="font-semibold">Cliquez pour sélectionner</span> ou glissez-déposez
                </p>
                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF ou WEBP (MAX. 5 Mo)</p>
              </div>
              <input
                type="file"
                class="hidden"
                accept="image/jpeg,image/png,image/gif,image/webp"
                @change="handleImageSelect"
              />
            </label>
          </div>

          <div class="flex items-center justify-end gap-3 relative z-10">
            <button
              type="button"
              @click.stop="closeAddImageModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors"
            >
              Annuler
            </button>
            <button
              type="button"
              @click.stop="handleAddImage"
              :disabled="!selectedImage"
              class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i class="fas fa-plus mr-2"></i>
              Ajouter
            </button>
          </div>
        </div>
      </Modal>

      <!-- Product Option Modal -->
      <Modal
        :is-open="isOptionModalOpen"
        :title="selectedOption ? 'Modifier une option' : 'Ajouter une option'"
        @close="closeOptionModal"
      >
        <div class="p-6">
          <FormProductOption
            :product-id="productId"
            :product-option="selectedOption || undefined"
            @saved="handleOptionSaved"
            @cancel="closeOptionModal"
          />
        </div>
      </Modal>

      <!-- Attach Stores Modal -->
      <Modal
        :is-open="isAttachStoresModalOpen"
        title="Associer des magasins"
        size="large"
        @close="closeAttachStoresModal"
      >
        <div class="p-6">
          <FormAttachStores
            :product-id="productId"
            :exclude-store-ids="stores.map(s => s.id)"
            @saved="handleStoresAttached"
            @cancel="closeAttachStoresModal"
          />
        </div>
      </Modal>
    </div>

    <div v-else class="text-center py-12">
      <p class="text-gray-500">Produit non trouvé</p>
    </div>
  </DashboardLayout>
</template>
