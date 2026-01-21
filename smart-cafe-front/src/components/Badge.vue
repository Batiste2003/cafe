<script setup lang="ts">
type BadgeVariant = 'primary' | 'success' | 'warning' | 'danger' | 'neutral'

withDefaults(
  defineProps<{
    variant?: BadgeVariant
  }>(),
  {
    variant: 'neutral',
  }
)

const variantClasses: Record<BadgeVariant, string> = {
  primary: 'bg-(--cafe-primary) text-white',
  success: 'bg-green-100 text-green-700',
  warning: 'bg-yellow-100 text-yellow-700',
  danger: 'bg-red-100 text-red-700',
  neutral: 'bg-gray-100 text-gray-700',
}
</script>

<template>
  <span
    :class="[
      'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium',
      variantClasses[variant],
    ]"
  >
    <slot />
  </span>
</template>
