<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import { useGetShowProduct } from '@/composable/API/Admin/Products/useGetShowProduct'
import { useGetIndexProductVariant } from '@/composable/API/Admin/ProductVariants/useGetIndexProductVariant'
import CardProductVariant from '@/components/ProductVariants/Cards/CardProductVariant.vue'
import CardSkeletonProductVariant from '@/components/ProductVariants/Skeleton/CardSkeletonProductVariant.vue'
import Modal from '@/components/Modal.vue'
import FormCreateProductVariant from '@/components/ProductVariants/Form/FormCreateProductVariant.vue'

const route = useRoute()
const router = useRouter()

const productId = Number(route.params.productId)

const { product, isLoading: isLoadingProduct, execute: fetchProduct } = useGetShowProduct()
const { variants, isLoading: isLoadingVariants, error, execute: fetchVariants } = useGetIndexProductVariant()

const searchQuery = ref('')
const filterDefault = ref<'all' | 'default'>('all')
const isCreateModalOpen = ref(false)

const filteredVariants = computed(() => {
  let result = variants.value

  // Filter by default
  if (filterDefault.value === 'default') {
    result = result.filter(v => v.is_default)
  }

  // Filter by search query (SKU)
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(v => v.sku.toLowerCase().includes(query))
  }

  return result
})

const loadData = async () => {
  await Promise.all([
    fetchProduct(productId),
    fetchVariants(productId)
  ])
}

const handleVariantCreated = () => {
  isCreateModalOpen.value = false
  loadData()
}

const goToProduct = () => {
  router.push({ name: 'admin-product-show', params: { id: productId } })
}

const goToVariant = (variantId: number) => {
  router.push({
    name: 'admin-product-variant-show',
    params: { productId, id: variantId }
  })
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
      <span>Variantes</span>
    </template>

    <div class="space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
          Variantes
        </h1>
        <p v-if="product" class="text-gray-600">
          Gérez les variantes du produit "{{ product.name }}"
        </p>
      </div>
      <button
        @click="isCreateModalOpen = true"
        class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
      >
        <i class="fas fa-plus"></i>
        Ajouter une variante
      </button>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-lg border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1">
            <div class="relative">
              <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input
                v-model="searchQuery"
                type="text"
                placeholder="Rechercher par SKU..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
              />
            </div>
          </div>

          <!-- Filter -->
          <div class="flex items-center gap-2">
            <button
              @click="filterDefault = 'all'"
              :class="[
                'px-4 py-2 rounded-lg font-medium text-sm transition-colors',
                filterDefault === 'all'
                  ? 'bg-[var(--cafe-primary)] text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Toutes
            </button>
            <button
              @click="filterDefault = 'default'"
              :class="[
                'px-4 py-2 rounded-lg font-medium text-sm transition-colors',
                filterDefault === 'default'
                  ? 'bg-[var(--cafe-primary)] text-white'
                  : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
              ]"
            >
              Par défaut
            </button>
          </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoadingVariants" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <CardSkeletonProductVariant v-for="i in 8" :key="i" />
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

    <!-- Empty State -->
    <div v-else-if="filteredVariants.length === 0" class="bg-white rounded-lg border border-gray-200 p-12 text-center">
        <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">
          {{ searchQuery || filterDefault === 'default' ? 'Aucune variante trouvée' : 'Aucune variante' }}
        </h3>
        <p class="text-gray-600 mb-6">
          {{ searchQuery || filterDefault === 'default'
            ? 'Essayez de modifier vos filtres de recherche.'
            : 'Commencez par ajouter votre première variante pour ce produit.'
          }}
        </p>
        <button
          v-if="!searchQuery && filterDefault === 'all'"
          @click="isCreateModalOpen = true"
          class="px-4 py-2 bg-[var(--cafe-primary)] text-white rounded-lg hover:opacity-90 transition-opacity inline-flex items-center gap-2"
        >
          <i class="fas fa-plus"></i>
        Ajouter une variante
      </button>
    </div>

    <!-- Variants Grid -->
    <div v-else>
      <div class="mb-4 text-sm text-gray-600">
        {{ filteredVariants.length }} variante{{ filteredVariants.length > 1 ? 's' : '' }}
      </div>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div
          v-for="variant in filteredVariants"
          :key="variant.id"
          @click="goToVariant(variant.id)"
          class="cursor-pointer"
        >
          <CardProductVariant :product-variant="variant" :product-id="productId" />
        </div>
      </div>
    </div>
    </div>

    <!-- Create Modal -->
    <Modal
      :is-open="isCreateModalOpen"
      title="Ajouter une variante"
      size="large"
      @close="isCreateModalOpen = false"
    >
      <div class="p-6">
        <FormCreateProductVariant
          :product-id="productId"
          @variant-created="handleVariantCreated"
          @cancel="isCreateModalOpen = false"
        />
      </div>
    </Modal>
  </DashboardLayout>
</template>
