<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import Card from '@/components/Card.vue'
import Modal from '@/components/Modal.vue'
import ToolbarFilter from '@/components/Toolbar/ToolbarFilter.vue'
import CardStore from '@/components/Stores/Cards/CardStore.vue'
import CardSkeletonStore from '@/components/Stores/Skeleton/CardSkeletonStore.vue'
import FormCreateStore from '@/components/Stores/Form/FormCreateStore.vue'
import { useGetIndexStore } from '@/composable/API/Admin/Stores/useGetIndexStore'
import type { Store } from '@/types/Store'

const { stores, isLoading, execute } = useGetIndexStore()
const isModalOpen = ref(false)

const activeFilter = ref('all')
const searchQuery = ref('')

const storeFilters = [
  { value: 'all', label: 'Tous' },
  { value: 'active', label: 'Actifs' },
  { value: 'draft', label: 'Brouillons' },
  { value: 'unpublish', label: 'Non publiés' },
  { value: 'deleted', label: 'Supprimés' },
]

const filteredStores = computed<Store[]>(() => {
  let result = stores.value

  // Apply active filter
  if (activeFilter.value !== 'all') {
    result = result.filter((store) => {
      switch (activeFilter.value) {
        case 'active':
          return store.status === 'active' && !store.is_deleted
        case 'draft':
          return store.status === 'draft' && !store.is_deleted
        case 'unpublish':
          return store.status === 'unpublish' && !store.is_deleted
        case 'deleted':
          return store.is_deleted
        default:
          return true
      }
    })
  }

  // Apply search query
  if (searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    result = result.filter(
      (store) =>
        store.id.toString().includes(query) ||
        (store.name && store.name.toLowerCase().includes(query)) ||
        (store.address?.city && store.address.city.toLowerCase().includes(query)),
    )
  }

  return result
})

const openModal = () => {
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
}

const handleStoreCreated = () => {
  closeModal()
  execute()
}

onMounted(() => {
  execute()
})
</script>

<template>
  <Card>
    <div
      class="flex flex-col lg:flex-row items-center justify-between gap-3 pb-4 border-b border-solid border-gray-200"
    >
      <div class="block">
        <h3 class="text-base font-semibold text-black mb-1">Magasins</h3>
        <p class="text-xs font-normal text-gray-500">Gérez et suivez tous les magasins</p>
      </div>
      <div class="flex flex-col min-[470px]:flex-row items-center gap-3">
        <button
          @click="openModal"
          class="px-4 py-2 bg-[var(--cafe-primary)] text-white text-xs font-medium rounded-lg hover:bg-[#4a0000] transition-colors duration-200 flex items-center gap-2"
        >
          <i class="fas fa-store" />
          Ajouter un magasin
        </button>
        <div class="relative text-gray-500 focus-within:text-gray-900">
          <div class="absolute inset-y-0 left-0 flex items-center pl-2.5 pointer-events-none">
            <i class="fas fa-search text-gray-400 text-xs" />
          </div>
          <input
            v-model="searchQuery"
            type="search"
            class="block w-full max-w-sm pr-3 pl-7 py-1.5 text-xs font-normal shadow-xs text-gray-900 bg-transparent border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none"
            placeholder="Rechercher"
          />
        </div>
      </div>
    </div>

    <div class="flex flex-col gap-3 sm:flex-row items-center justify-between my-4">
      <ToolbarFilter v-model="activeFilter" :filters="storeFilters" />
    </div>

    <!-- Loading State with Skeleton -->
    <div v-if="isLoading" class="space-y-3">
      <CardSkeletonStore v-for="i in 5" :key="i" />
    </div>

    <!-- Empty State -->
    <div
      v-else-if="filteredStores.length === 0"
      class="flex flex-col items-center justify-center py-12"
    >
      <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
        <i class="fas fa-store text-gray-400 text-3xl" />
      </div>
      <h3 class="text-base font-semibold text-gray-900 mb-1">Aucun magasin</h3>
      <p class="text-sm text-gray-500">Les magasins apparaîtront ici</p>
    </div>

    <!-- Stores List - Cards View -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
      <CardStore v-for="store in filteredStores" :key="store.id" :store="store" />
    </div>
  </Card>

  <!-- Modal for creating store -->
  <Modal :is-open="isModalOpen" title="Ajouter un magasin" @close="closeModal">
    <FormCreateStore @store-created="handleStoreCreated" @cancel="closeModal" />
  </Modal>
</template>
