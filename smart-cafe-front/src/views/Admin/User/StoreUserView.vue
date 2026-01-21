<script setup lang="ts">
import { useRouter } from 'vue-router'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import FormCreateUser from '@/components/Users/Form/FormCreateUser.vue'

const router = useRouter()

const handleUserCreated = () => {
  router.push('/dashboard')
}

const handleCancel = () => {
  router.push('/dashboard')
}
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>
      <span class="text-white/50">Dashboard</span>
      <span class="mx-2">/</span>
      <span>Créer un utilisateur</span>
    </template>

    <div class="max-w-2xl mx-auto">
      <div class="mb-6">
        <button
          class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors"
          @click="handleCancel"
        >
          <i class="fas fa-arrow-left"></i>
          Retour au dashboard
        </button>
      </div>

      <FormCreateUser @user-created="handleUserCreated" @cancel="handleCancel" />
    </div>
  </DashboardLayout>
</template>
