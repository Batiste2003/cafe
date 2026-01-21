<script setup lang="ts">
interface Props {
  variant?: 'primary';
  disabled?: boolean;
}

withDefaults(defineProps<Props>(), {
  variant: 'primary',
  disabled: false,
});

const variants: Record<string, string> = {
  primary:
    'bg-(--cafe-secondary) text-(--cafe-primary) hover:bg-(--cafe-primary) hover:text-(--cafe-secondary) hover:border-(--cafe-secondary)',
};
</script>

<template>
  <button
    :disabled="disabled"
    class="px-8 py-3 rounded-lg font-medium transition-colors duration-200 ease-in-out border-2 border-transparent cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
    :class="variants[variant]"
  >
    <slot />
  </button>
</template>
