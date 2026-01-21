<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionShowUser from '@/components/Users/SectionShowUser.vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const userId = Number(route.params.id)
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>
      <span class="text-white/50">Dashboard</span>
      <span class="mx-2">/</span>
      <span>Informations utilisateur</span>
    </template>

    <SectionShowUser :user-id="userId" />
  </DashboardLayout>
</template>
