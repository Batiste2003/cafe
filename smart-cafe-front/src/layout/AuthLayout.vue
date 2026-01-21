<script setup lang="ts">
defineProps<{
  title?: string
  subtitle?: string
}>()
</script>

<template>
  <section class="min-h-screen lg:pt-0 pt-10">
    <div class="grid lg:grid-cols-2 grid-cols-1 min-h-screen">
      <!-- Formulaire -->
      <div class="flex items-center justify-center lg:mb-0 mb-14 bg-(--cafe-primary) px-8 py-10">
        <div class="w-full max-w-md">
          <h1 class="text-white text-center text-4xl font-bold font-asset leading-tight mb-3">
            {{ title ?? 'Smart Café' }}
          </h1>
          <p
            v-if="subtitle"
            class="text-(--cafe-secondary) text-center text-base font-medium font-raleway leading-6 mb-8"
          >
            {{ subtitle }}
          </p>

          <div class="space-y-6">
            <slot />
          </div>
        </div>
      </div>

      <!-- Image -->
      <div class="order-[-1] lg:order-2 h-[20vh] lg:h-auto relative">
        <img
          src="../assets/img/grain_cafe.jpg"
          alt="Café"
          class="w-full h-full object-cover"
        />
      </div>
    </div>
  </section>
</template>
