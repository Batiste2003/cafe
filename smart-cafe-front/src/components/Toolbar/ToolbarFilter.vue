<script setup lang="ts">
interface Filter {
  value: string
  label: string
}

const props = defineProps<{
  filters: Filter[]
  modelValue: string
}>()

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const selectFilter = (value: string) => {
  emit('update:modelValue', value)
}
</script>

<template>
  <ul class="flex flex-wrap items-center gap-2">
    <li v-for="filter in props.filters" :key="filter.value">
      <button
        :class="[
          'py-1.5 px-3 rounded-lg text-xs font-medium transition-all duration-300',
          modelValue === filter.value
            ? 'bg-(--cafe-primary) text-white'
            : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
        ]"
        @click="selectFilter(filter.value)"
      >
        {{ filter.label }}
      </button>
    </li>
  </ul>
</template>
