<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionIndexUser from '@/components/Users/SectionIndexUser.vue'
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Dashboard</template>

    <SectionIndexUser />
  </DashboardLayout>
</template>
