<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import Card from '@/components/Card.vue'
import Modal from '@/components/Modal.vue'
import CardProduct from '@/components/Products/Cards/CardProduct.vue'
import CardSkeletonProduct from '@/components/Products/Skeleton/CardSkeletonProduct.vue'
import FormCreateProduct from '@/components/Products/Form/FormCreateProduct.vue'
import { useGetIndexProduct } from '@/composable/API/Admin/Products/useGetIndexProduct'

const { products, paging, isLoading, execute } = useGetIndexProduct()

const isCreateModalOpen = ref(false)
const searchQuery = ref('')
const selectedFilter = ref<'all' | 'active' | 'inactive' | 'featured'>('all')

onMounted(() => {
  execute()
})

const filteredProducts = computed(() => {
  let filtered = products.value

  if (selectedFilter.value === 'active') {
    filtered = filtered.filter((product) => product.is_active && !product.is_deleted)
  } else if (selectedFilter.value === 'inactive') {
    filtered = filtered.filter((product) => !product.is_active && !product.is_deleted)
  } else if (selectedFilter.value === 'featured') {
    filtered = filtered.filter((product) => product.is_featured && !product.is_deleted)
  } else if (selectedFilter.value === 'all') {
    filtered = filtered.filter((product) => !product.is_deleted)
  }

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (product) =>
        product.name.toLowerCase().includes(query) ||
        product.description?.toLowerCase().includes(query) ||
        product.slug.toLowerCase().includes(query),
    )
  }

  return filtered
})

const openCreateModal = () => {
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
}

const handleProductCreated = () => {
  closeCreateModal()
  execute()
}
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Produits</template>

    <Card>
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-bold text-gray-900">Produits</h2>
          <p class="text-sm text-gray-500 mt-1">
            {{ filteredProducts.length }} produit{{ filteredProducts.length > 1 ? 's' : '' }}
            <span v-if="paging" class="text-gray-400">
              · {{ paging.total }} au total (page {{ paging.current_page }}/{{ paging.total_pages }})
            </span>
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
        >
          <i class="fas fa-plus" />
          Ajouter un produit
        </button>
      </div>

      <!-- Filters -->
      <div class="flex items-center gap-3 mb-6">
        <!-- Search -->
        <div class="flex-1 relative text-gray-500 focus-within:text-gray-900">
          <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <i class="fas fa-search text-gray-400 text-sm" />
          </div>
          <input
            v-model="searchQuery"
            type="search"
            class="block w-full pr-3 pl-10 py-2.5 text-sm font-normal shadow-xs text-gray-900 bg-transparent border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[var(--cafe-primary)]/20 focus:border-[var(--cafe-primary)]"
            placeholder="Rechercher un produit..."
          />
        </div>

        <!-- Status Filter -->
        <div class="flex items-center gap-2">
          <button
            @click="selectedFilter = 'all'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
              selectedFilter === 'all'
                ? 'bg-[var(--cafe-primary)] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            Tout
          </button>
          <button
            @click="selectedFilter = 'active'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
              selectedFilter === 'active'
                ? 'bg-[var(--cafe-primary)] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            Actif
          </button>
          <button
            @click="selectedFilter = 'inactive'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
              selectedFilter === 'inactive'
                ? 'bg-[var(--cafe-primary)] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            Inactif
          </button>
          <button
            @click="selectedFilter = 'featured'"
            :class="[
              'px-4 py-2 text-sm font-medium rounded-lg transition-colors',
              selectedFilter === 'featured'
                ? 'bg-[var(--cafe-primary)] text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
            ]"
          >
            <i class="fas fa-star mr-1"></i>
            Featured
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <CardSkeletonProduct v-for="i in 6" :key="i" />
      </div>

      <!-- Empty State -->
      <div
        v-else-if="filteredProducts.length === 0"
        class="flex flex-col items-center justify-center py-12"
      >
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
          <i class="fas fa-box text-gray-400 text-2xl" />
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun produit trouvé</h3>
        <p class="text-sm text-gray-500 text-center mb-4">
          {{
            searchQuery
              ? 'Essayez de modifier votre recherche'
              : 'Commencez par créer un produit'
          }}
        </p>
      </div>

      <!-- Products Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <CardProduct v-for="product in filteredProducts" :key="product.id" :product="product" />
      </div>

      <!-- Modal for creating product -->
      <Modal :is-open="isCreateModalOpen" title="Nouveau produit" @close="closeCreateModal">
        <FormCreateProduct @product-created="handleProductCreated" @cancel="closeCreateModal" />
      </Modal>
    </Card>
  </DashboardLayout>
</template>
