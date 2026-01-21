<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionIndexStore from '@/components/Stores/SectionIndexStore.vue'
import SectionIndexUser from '@/components/Users/SectionIndexUser.vue'
import SectionIndexProductCategory from '@/components/ProductCategories/SectionIndexProductCategory.vue'
import SectionIndexProduct from '@/components/Products/SectionIndexProduct.vue'
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Dashboard</template>

    <div class="space-y-6">
      <SectionIndexUser />
      <SectionIndexStore />
      <SectionIndexProductCategory />
      <SectionIndexProduct />
    </div>
  </DashboardLayout>
</template>
