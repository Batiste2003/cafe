<script setup lang="ts">
import { useRoute } from 'vue-router'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionShowStore from '@/components/Stores/SectionShowStore.vue'

const route = useRoute()
const storeId = Number(route.params.id)
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>
      <RouterLink :to="{ name: 'admin-stores-index' }" class="hover:underline">
        Magasins
      </RouterLink>
      <span class="mx-2">/</span>
      <span>Détails</span>
    </template>

    <SectionShowStore :store-id="storeId" />
  </DashboardLayout>
</template>
