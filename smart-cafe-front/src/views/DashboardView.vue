<script setup lang="ts">
import DashboardLayout from '@/layout/DashboardLayout.vue'
import SectionIndexUser from '@/components/Users/SectionIndexUser.vue'
import { useGetShowUser } from '@/composable/API/Admin/Users/useGetShowUser.ts'
import { onMounted } from 'vue'

const { execute } = useGetShowUser(2)

onMounted(async () => {
  const result = await execute()
  console.log(result)
})
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Dashboard</template>

    <SectionIndexUser />
  </DashboardLayout>
</template>
