<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionFullIndexProductCategory from '@/components/ProductCategories/SectionFullIndexProductCategory.vue'
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Catégories de produits</template>

    <SectionFullIndexProductCategory />
  </DashboardLayout>
</template>
