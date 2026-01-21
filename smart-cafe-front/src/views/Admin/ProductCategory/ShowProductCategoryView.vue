<script setup lang="ts">
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionShowProductCategory from '@/components/ProductCategories/SectionShowProductCategory.vue'

const route = useRoute()

const categoryId = computed(() => {
  return parseInt(route.params.id as string, 10)
})
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Détails de la catégorie</template>

    <SectionShowProductCategory :category-id="categoryId" />
  </DashboardLayout>
</template>
