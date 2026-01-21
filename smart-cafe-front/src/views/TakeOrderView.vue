<script setup lang="ts">
import { useLogoutPost } from '@/composable/API/Auth/useLogoutPost'
import { useRouter } from 'vue-router'

const router = useRouter()
const handleLogout = async () => {
  const { execute } = useLogoutPost()
  const response = await execute()
  if (response.success) {
    router.push({ name: 'login' })
  }
}
</script>

<template>
  <div>
    <button @click="handleLogout">Se Déconnecter</button>
  </div>
</template>
