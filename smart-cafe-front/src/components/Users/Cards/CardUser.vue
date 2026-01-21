<script setup lang="ts">
import type { User } from '@/types/User'
import Badge from '@/components/Badge.vue'

defineProps<{
  user: User
}>()
</script>

<template>
  <div
    class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300"
  >
    <div class="flex items-center gap-3">
      <div
        class="w-10 h-10 bg-(--cafe-primary) rounded-full flex items-center justify-center text-white font-semibold"
      >
        {{ user.name?.charAt(0).toUpperCase() ?? 'U' }}
      </div>
      <div>
        <h4 class="text-sm font-semibold text-gray-900">{{ user.name }}</h4>
        <p class="text-xs text-gray-500">{{ user.email }}</p>
      </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
      <Badge v-if="user.email_verified_at" variant="success">
        <i class="fas fa-check-circle mr-1"></i>
        Vérifié
      </Badge>
      <Badge v-else variant="warning">
        <i class="fas fa-clock mr-1"></i>
        Non vérifié
      </Badge>

      <Badge v-for="role in user.roles" :key="role.id" variant="primary">
        {{ role.label }}
      </Badge>

      <Badge v-if="user.is_deleted" variant="danger">
        <i class="fas fa-trash mr-1"></i>
        Supprimé
      </Badge>
    </div>

    <div class="text-xs text-gray-400">
      Créé le {{ user.created_at ? new Date(user.created_at).toLocaleDateString('fr-FR') : 'N/A' }}
    </div>
  </div>
</template>
