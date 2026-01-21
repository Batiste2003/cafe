<script setup lang="ts">
import type { User } from '@/types/User'
import Badge from '@/components/Badge.vue'

defineProps<{
  users: User[]
  expandedUserId: number | null
}>()

const emit = defineEmits<{
  'toggle-user': [userId: number]
}>()

const formatDate = (date: string | null) => {
  if (!date) return 'N/A'
  return new Date(date).toLocaleDateString('fr-FR')
}
</script>

<template>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead class="text-xs text-gray-500 uppercase bg-gray-50">
        <tr>
          <th class="px-4 py-3 rounded-tl-lg">Utilisateur</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Rôles</th>
          <th class="px-4 py-3">Statut</th>
          <th class="px-4 py-3 rounded-tr-lg">Créé le</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="user in users" :key="user.id">
          <tr
            class="border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors"
            @click="emit('toggle-user', user.id)"
          >
            <td class="px-4 py-3">
              <div class="flex items-center gap-3">
                <div
                  class="w-8 h-8 bg-(--cafe-primary) rounded-full flex items-center justify-center text-white text-xs font-semibold"
                >
                  {{ user.name?.charAt(0).toUpperCase() ?? 'U' }}
                </div>
                <router-link
                  :to="{ name: 'admin-user-show', params: { id: user.id } }"
                  class="font-medium text-gray-900"
                  >{{ user.name }}</router-link
                >
              </div>
            </td>
            <td class="px-4 py-3 text-gray-500">{{ user.email }}</td>
            <td class="px-4 py-3">
              <div class="flex gap-1 flex-wrap">
                <Badge v-for="role in user.roles" :key="role.id" variant="primary">
                  {{ role.label }}
                </Badge>
                <span v-if="!user.roles?.length" class="text-gray-400">-</span>
              </div>
            </td>
            <td class="px-4 py-3">
              <Badge v-if="user.is_deleted" variant="danger">Supprimé</Badge>
              <Badge v-else-if="user.email_verified_at" variant="success">Actif</Badge>
              <Badge v-else variant="warning">Non vérifié</Badge>
            </td>
            <td class="px-4 py-3 text-gray-500">{{ formatDate(user.created_at) }}</td>
          </tr>

          <!-- Expanded details row -->
          <tr v-if="expandedUserId === user.id" class="bg-gray-50">
            <td colspan="5" class="px-4 py-4">
              <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                  <span class="text-gray-500">ID:</span>
                  <span class="ml-2 font-medium">{{ user.id }}</span>
                </div>
                <div>
                  <span class="text-gray-500">Email vérifié:</span>
                  <span class="ml-2 font-medium">{{
                    user.email_verified_at ? formatDate(user.email_verified_at) : 'Non'
                  }}</span>
                </div>
                <div>
                  <span class="text-gray-500">Mis à jour:</span>
                  <span class="ml-2 font-medium">{{ formatDate(user.updated_at) }}</span>
                </div>
                <div v-if="user.deleted_at">
                  <span class="text-gray-500">Supprimé le:</span>
                  <span class="ml-2 font-medium">{{ formatDate(user.deleted_at) }}</span>
                </div>
              </div>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>
