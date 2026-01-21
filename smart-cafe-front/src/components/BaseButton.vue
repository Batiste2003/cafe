<script setup>
const props = defineProps({
  variant: {
    type: String,
    default: 'primary',
  },
})

const variants = {
  primary:
    'bg-(--cafe-secondary) text-(--cafe-primary) hover:bg-(--cafe-primary) hover:text-(--cafe-secondary) hover:border-(--cafe-secondary)',
  secondary:
    'bg-(--cafe-primary) text-(--cafe-secondary) border-(--cafe-primary) hover:bg-(--cafe-secondary) hover:text-(--cafe-primary) hover:border-(--cafe-primary)',
}
</script>

<template>
  <button
    @click="$emit('click')"
    class="px-8 py-3 rounded-lg font-medium transition-colors duration-200 ease-in-out border-2 cursor-pointer"
    :class="variants[variant]"
  >
    <slot />
  </button>
</template>
