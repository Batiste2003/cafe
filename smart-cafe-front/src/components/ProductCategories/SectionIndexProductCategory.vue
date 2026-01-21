<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import Card from '@/components/Card.vue'
import Modal from '@/components/Modal.vue'
import CardProductCategoryTree from '@/components/ProductCategories/Cards/CardProductCategoryTree.vue'
import CardProductCategoryRecursive from '@/components/ProductCategories/Cards/CardProductCategoryRecursive.vue'
import CardSkeletonProductCategory from '@/components/ProductCategories/Skeleton/CardSkeletonProductCategory.vue'
import FormCreateProductCategory from '@/components/ProductCategories/Form/FormCreateProductCategory.vue'
import { useGetIndexProductCategory } from '@/composable/API/Admin/ProductCategories/useGetIndexProductCategory'
import type { ProductCategory } from '@/types/ProductCategory'

const { categories, paging, isLoading, execute } = useGetIndexProductCategory()

const isCreateModalOpen = ref(false)
const searchQuery = ref('')
const selectedFilter = ref<'all' | 'active' | 'inactive'>('all')
const viewMode = ref<'tree' | 'grid'>('tree')
const preselectedParent = ref<ProductCategory | null>(null)

onMounted(() => {
  execute()
})

// Build hierarchical tree structure
const buildCategoryTree = (cats: ProductCategory[]): ProductCategory[] => {
  // If categories already have children populated from API, just filter root categories
  const hasPopulatedChildren = cats.some(cat => cat.children && cat.children.length > 0)

  if (hasPopulatedChildren) {
    // Categories already have their children, just return root categories
    return cats.filter(cat => !cat.parent || !cat.parent.id)
  }

  // Otherwise, build the tree structure from flat list
  const categoryMap = new Map<number, ProductCategory>()
  const rootCategories: ProductCategory[] = []

  // First pass: create a map of all categories
  cats.forEach((cat) => {
    categoryMap.set(cat.id, { ...cat, children: [] })
  })

  // Second pass: build the tree structure
  cats.forEach((cat) => {
    const category = categoryMap.get(cat.id)!
    if (cat.parent?.id) {
      const parent = categoryMap.get(cat.parent.id)
      if (parent) {
        if (!parent.children) {
          parent.children = []
        }
        parent.children.push(category)
      } else {
        // Parent not found, treat as root
        rootCategories.push(category)
      }
    } else {
      // No parent, this is a root category
      rootCategories.push(category)
    }
  })

  return rootCategories
}

// Filter categories recursively
const filterCategoriesRecursive = (cats: ProductCategory[], query: string, statusFilter: string): ProductCategory[] => {
  return cats
    .map((cat) => {
      // Clone the category
      const filteredCat = { ...cat }

      // Apply status filter
      let matchesStatus = true
      if (statusFilter === 'active') {
        matchesStatus = cat.is_active
      } else if (statusFilter === 'inactive') {
        matchesStatus = !cat.is_active
      }

      // Apply search filter
      let matchesSearch = true
      if (query) {
        matchesSearch =
          cat.name.toLowerCase().includes(query) ||
          cat.description?.toLowerCase().includes(query) ||
          cat.slug.toLowerCase().includes(query)
      }

      // Filter children recursively
      if (cat.children && cat.children.length > 0) {
        filteredCat.children = filterCategoriesRecursive(cat.children, query, statusFilter)
      }

      // Include category if it matches OR if any of its children match
      const hasMatchingChildren = filteredCat.children && filteredCat.children.length > 0
      if ((matchesStatus && matchesSearch) || hasMatchingChildren) {
        return filteredCat
      }

      return null
    })
    .filter((cat): cat is ProductCategory => cat !== null)
}

const categoryTree = computed(() => {
  return buildCategoryTree(categories.value)
})

const filteredCategories = computed(() => {
  const query = searchQuery.value.toLowerCase().trim()

  if (viewMode.value === 'tree') {
    return filterCategoriesRecursive(categoryTree.value, query, selectedFilter.value)
  } else {
    // Grid view: show hierarchy with recursive filtering
    return filterCategoriesRecursive(categoryTree.value, query, selectedFilter.value)
  }
})

const totalFilteredCount = computed(() => {
  // Count all categories including nested ones
  const countCategories = (cats: ProductCategory[]): number => {
    return cats.reduce((count, cat) => {
      return count + 1 + (cat.children ? countCategories(cat.children) : 0)
    }, 0)
  }

  return countCategories(filteredCategories.value)
})

const openCreateModal = () => {
  preselectedParent.value = null
  isCreateModalOpen.value = true
}

const closeCreateModal = () => {
  isCreateModalOpen.value = false
  preselectedParent.value = null
}

const handleCategoryCreated = () => {
  closeCreateModal()
  execute()
}

const handleAddSubcategory = (parentCategory: ProductCategory) => {
  preselectedParent.value = parentCategory
  isCreateModalOpen.value = true
}
</script>

<template>
  <Card>
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div>
        <h2 class="text-xl font-bold text-gray-900">Catégories de produits</h2>
        <p class="text-sm text-gray-500 mt-1">
          {{ totalFilteredCount }} catégorie{{ totalFilteredCount > 1 ? 's' : '' }}
          <span v-if="paging" class="text-gray-400">
            · {{ paging.total }} au total
          </span>
        </p>
      </div>
      <div class="flex items-center gap-3">
        <!-- View Mode Toggle -->
        <div class="flex items-center bg-gray-100 rounded-lg p-1">
          <button
            @click="viewMode = 'tree'"
            :class="[
              'px-3 py-1.5 text-sm font-medium rounded-md transition-colors',
              viewMode === 'tree'
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-600 hover:text-gray-900',
            ]"
            title="Vue arborescence"
          >
            <i class="fas fa-sitemap"></i>
          </button>
          <button
            @click="viewMode = 'grid'"
            :class="[
              'px-3 py-1.5 text-sm font-medium rounded-md transition-colors',
              viewMode === 'grid'
                ? 'bg-white text-gray-900 shadow-sm'
                : 'text-gray-600 hover:text-gray-900',
            ]"
            title="Vue grille"
          >
            <i class="fas fa-th"></i>
          </button>
        </div>

        <RouterLink
          :to="{ name: 'admin-product-categories-index' }"
          class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
        >
          Voir tout
        </RouterLink>
        <button
          @click="openCreateModal"
          class="px-4 py-2 text-sm font-medium text-white bg-[var(--cafe-primary)] rounded-lg hover:opacity-90 transition-opacity flex items-center gap-2"
        >
          <i class="fas fa-plus" />
          Ajouter une catégorie
        </button>
      </div>
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
          placeholder="Rechercher une catégorie..."
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
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <CardSkeletonProductCategory v-for="i in 6" :key="i" />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredCategories.length === 0"
      class="flex flex-col items-center justify-center py-12"
    >
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
        <i class="fas fa-tags text-gray-400 text-2xl" />
      </div>
      <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune catégorie trouvée</h3>
      <p class="text-sm text-gray-500 text-center mb-4">
        {{ searchQuery ? 'Essayez de modifier votre recherche' : 'Commencez par créer une catégorie' }}
      </p>
    </div>

    <!-- Categories Tree View -->
    <div v-else-if="viewMode === 'tree'" class="bg-white border border-gray-200 rounded-lg">
      <CardProductCategoryTree
        v-for="category in filteredCategories"
        :key="category.id"
        :category="category"
        :level="0"
        @add-subcategory="handleAddSubcategory"
      />
    </div>

    <!-- Categories Grid View (Recursive) -->
    <div v-else class="space-y-4">
      <CardProductCategoryRecursive
        v-for="category in filteredCategories"
        :key="category.id"
        :category="category"
        :level="0"
      />
    </div>

    <!-- Modal for creating category -->
    <Modal
      :is-open="isCreateModalOpen"
      :title="preselectedParent ? `Nouvelle sous-catégorie de ${preselectedParent.name}` : 'Nouvelle catégorie'"
      @close="closeCreateModal"
    >
      <FormCreateProductCategory
        :categories="categories"
        :preselected-parent="preselectedParent"
        @category-created="handleCategoryCreated"
        @cancel="closeCreateModal"
      />
    </Modal>
  </Card>
</template>
