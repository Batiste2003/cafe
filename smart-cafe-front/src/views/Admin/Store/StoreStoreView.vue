<script setup lang="ts">
import { useRouter } from 'vue-router'
import DashboardLayout from '@/layout/DashboardLayout.vue'
import Card from '@/components/Card.vue'
import FormCreateStore from '@/components/Stores/Form/FormCreateStore.vue'

const router = useRouter()

const handleStoreCreated = () => {
  router.push({ name: 'admin-stores-index' })
}

const handleCancel = () => {
  router.push({ name: 'admin-stores-index' })
}
</script>

<template>
  <DashboardLayout>
    <template #breadcrumb>Créer un magasin</template>

    <Card>
      <FormCreateStore @store-created="handleStoreCreated" @cancel="handleCancel" />
    </Card>
  </DashboardLayout>
</template>
