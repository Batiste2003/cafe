<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import { useGetShowUser } from '@/composable/API/Admin/Users/useGetShowUser.ts'
import { onMounted } from 'vue'
import { useRoute } from 'vue-router'
import type { User } from '@/types/User'
import { ref } from 'vue'

const user = ref<User | null>(null)
const route = useRoute()
const userId = Number(route.params.id)
const { execute } = useGetShowUser(userId)

onMounted(async () => {
  const result = await execute()
  if (result.success) {
    user.value = result.data
  } else {
    console.error('Failed to fetch user data:', result.message)
  }
})
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>
      <span class="text-white/50">Dashboard</span>
      <span class="mx-2">/</span>
      <span>Informations utilisateur</span>
    </template>
  </DashboardLayout>
</template>
