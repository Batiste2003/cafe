<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import AuthLayout from '@/layout/AuthLayout.vue'
import BaseButton from '@/components/BaseButton.vue'
import { useLoginPost } from '@/composable/API/Auth/useLoginPost'

const router = useRouter()
const { execute, isLoading, error } = useLoginPost()

const email = ref('')
const password = ref('')

const login = async () => {
  const response = await execute({ email: email.value, password: password.value })

  if (response.success) {
    router.push('/dashboard')
  }
}
</script>

<template>
  <AuthLayout title="Smart Café" subtitle="Connectez-vous pour accéder à la gestion des commandes">
    <!-- Email -->
    <div class="space-y-2">
      <label class="text-white font-semibold font-raleway flex items-center gap-2">
        <i class="fa-solid fa-envelope"></i>
        Identifiant
      </label>
      <input
        v-model="email"
        type="email"
        placeholder="serveur@cafe.com"
        class="w-full h-12 px-4 bg-(--cafe-secondary) rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-300"
      />
    </div>

    <!-- Password -->
    <div class="space-y-2">
      <label class="text-white font-semibold font-raleway flex items-center gap-2">
        <i class="fa-solid fa-lock"></i>
        Mot de passe
      </label>
      <input
        v-model="password"
        type="password"
        placeholder="••••••••"
        class="w-full h-12 px-4 bg-(--cafe-secondary) rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-white/50 transition-all duration-300"
      />
    </div>

    <!-- Erreur -->
    <div v-if="error" class="text-red-400 text-sm text-center">
      <i class="fa-solid fa-circle-exclamation mr-2"></i>
      {{ error }}
    </div>

    <!-- Mot de passe oublié -->
    <div class="text-right">
      <a href="#" class="text-sm text-(--cafe-secondary) italic hover:underline">
        Mot de passe oublié ?
      </a>
    </div>

    <!-- Bouton -->
    <BaseButton
      text="Se connecter"
      variant="primary"
      :disabled="isLoading"
      class="w-full"
      @click="login"
    >
      <i v-if="!isLoading" class="fa-solid fa-right-to-bracket mr-2"></i>
      <i v-else class="fa-solid fa-spinner fa-spin mr-2"></i>
      {{ isLoading ? 'Connexion...' : 'Se connecter' }}
    </BaseButton>
  </AuthLayout>
</template>
