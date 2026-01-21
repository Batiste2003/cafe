<script setup lang="ts">
import type { Store } from '@/types/Store'
import Badge from '@/components/Badge.vue'

defineProps<{
  store: Store
}>()
</script>

<template>
  <RouterLink
    :to="{ name: 'admin-store-show', params: { id: store.id } }"
    class="flex flex-col gap-3 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300 cursor-pointer"
  >
    <!-- Store Image/Banner -->
    <div class="w-full h-32 bg-gray-200 rounded-lg overflow-hidden">
      <img
        v-if="store.banner?.url"
        :src="store.banner.url"
        :alt="store.name"
        class="w-full h-full object-cover"
      />
      <div v-else class="w-full h-full flex items-center justify-center">
        <i class="fas fa-store text-gray-400 text-3xl" />
      </div>
    </div>

    <!-- Store Info -->
    <div class="flex items-start gap-3">
      <div
        v-if="store.logo?.url"
        class="w-12 h-12 rounded-full overflow-hidden flex-shrink-0 border-2 border-white shadow-sm"
      >
        <img :src="store.logo.url" :alt="store.name" class="w-full h-full object-cover" />
      </div>
      <div
        v-else
        class="w-12 h-12 bg-[var(--cafe-primary)] rounded-full flex items-center justify-center text-white font-semibold flex-shrink-0"
      >
        {{ store.name?.charAt(0).toUpperCase() ?? 'S' }}
      </div>
      <div class="flex-1 min-w-0">
        <h4 class="text-sm font-semibold text-gray-900 truncate">{{ store.name }}</h4>
        <p v-if="store.address" class="text-xs text-gray-500 truncate">
          <i class="fas fa-map-marker-alt mr-1"></i>
          {{ store.address.city }}, {{ store.address.postal_code }}
        </p>
      </div>
    </div>

    <!-- Store Status & Info -->
    <div class="flex items-center justify-between gap-2">
      <div class="flex items-center gap-2 flex-wrap">
        <Badge v-if="store.status === 'active'" variant="success">
          {{ store.status_label }}
        </Badge>
        <Badge v-else-if="store.status === 'draft'" variant="warning">
          {{ store.status_label }}
        </Badge>
        <Badge v-else-if="store.status === 'unpublish'" variant="secondary">
          {{ store.status_label }}
        </Badge>

        <Badge v-if="store.is_deleted" variant="danger">
          <i class="fas fa-trash mr-1"></i>
          Supprimé
        </Badge>
      </div>

      <span v-if="store.users_count !== undefined" class="text-xs text-gray-400">
        <i class="fas fa-users mr-1"></i>
        {{ store.users_count }}
      </span>
    </div>

    <div class="text-xs text-gray-400">
      Créé le
      {{ store.created_at ? new Date(store.created_at).toLocaleDateString('fr-FR') : 'N/A' }}
    </div>
  </RouterLink>
</template>
