<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import BaseButton from '@/components/BaseButton.vue'
import { useLoginPost } from '@/composable/API/Auth/useLoginPost'

const router = useRouter()
const { execute, isLoading, error } = useLoginPost()

const email = ref('')
const password = ref('')

const login = async () => {
  const response = await execute({ email: email.value, password: password.value })

  if (response.success) {
    router.push('/takeorder')
  }
}
</script>

<template>
  <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
    <h1
      class="text-[40px] md:text-[55px] xl:text-[70px] xxl:text-[80px] w-full absolute font-bold font-asset text-center top-20 text-white px-2"
    >
      Smart Café
    </h1>
    <!-- formulaire -->
    <div
      class="flex items-start md:items-end justify-center bg-(--cafe-primary) px-8 py-10 md:py-36"
    >
      <div class="w-full md:max-w-md space-y-6">
        <p class="text-white text-sm lg:text-lg font-raleway italic">
          Connectez-vous pour accéder à la gestion des commandes
        </p>

        <div class="flex flex-col gap-8 md:gap-20 mb-0">
          <!-- Email -->
          <div class="space-y-1">
            <label class="text-md xl:text-xl font-raleway text-white font-semibold"
              >Identifiant</label
            >
            <input
              v-model="email"
              type="email"
              placeholder="serveur@cafe.com"
              class="w-full px-4 py-3 bg-(--cafe-secondary) transition-normal rounded-full mt-2"
            />
          </div>

          <!-- Password -->
          <div class="space-y-1">
            <label class="text-md xl:text-xl text-white font-semibold font-raleway"
              >Mot de passe</label
            >
            <input
              v-model="password"
              type="password"
              placeholder="••••••••"
              class="w-full px-4 py-3 bg-(--cafe-secondary) transition-normal rounded-full mt-2"
            />
          </div>
        </div>

        <!-- Erreur -->
        <div v-if="error" class="text-red-400 text-sm">
          {{ error }}
        </div>

        <!-- Mot de passe oublié -->
        <div class="text-right">
          <a href="#" class="text-sm text-(--cafe-secondary) italic hover:underline">
            Mot de passe oublié ?
          </a>
        </div>

        <div class="flex justify-end w-full">
          <!-- Bouton -->
          <BaseButton
            text="Se connecter"
            variant="primary"
            :disabled="isLoading"
            @click="login"
          >
            {{ isLoading ? 'Connexion...' : 'Se connecter' }}
          </BaseButton>
        </div>
      </div>
    </div>

    <!-- Image -->
    <div class="order-[-1] md:order-2 h-[10vh] md:h-autorelative">
      <img
        src="../assets/img/grain_cafe.jpg"
        alt="Café"
        class="absolute inset-0 w-full h-full object-cover z-[-1]"
      />
    </div>
  </div>
</template>
