<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionIndexStore from '@/components/Stores/SectionIndexStore.vue'
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Magasins</template>

    <SectionIndexStore />
  </DashboardLayout>
</template>
