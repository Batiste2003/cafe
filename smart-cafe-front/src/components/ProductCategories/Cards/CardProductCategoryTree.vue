<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import Badge from '@/components/Badge.vue'
import type { ProductCategory } from '@/types/ProductCategory'

interface Props {
  category: ProductCategory
  level?: number
}

const props = withDefaults(defineProps<Props>(), {
  level: 0,
})

const emit = defineEmits<{
  'add-subcategory': [parentCategory: ProductCategory]
}>()

const isExpanded = ref(true)

const hasChildren = props.category.children && props.category.children.length > 0

const toggleExpand = (event: Event) => {
  event.preventDefault()
  event.stopPropagation()
  if (hasChildren) {
    isExpanded.value = !isExpanded.value
  }
}

const handleAddSubcategory = (event: Event) => {
  event.preventDefault()
  event.stopPropagation()
  emit('add-subcategory', props.category)
}

const getIndentStyle = () => {
  return {
    paddingLeft: `${props.level * 1.5}rem`,
  }
}
</script>

<template>
  <div class="category-tree-item">
    <!-- Category Row -->
    <div
      class="flex items-center gap-2 py-2.5 px-3 hover:bg-gray-50 rounded-lg transition-colors group"
      :style="getIndentStyle()"
    >
      <!-- Expand/Collapse Button -->
      <button
        v-if="hasChildren"
        @click="toggleExpand"
        class="w-5 h-5 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-200 rounded transition-all flex-shrink-0"
      >
        <i :class="isExpanded ? 'fas fa-chevron-down' : 'fas fa-chevron-right'" class="text-xs"></i>
      </button>
      <div v-else class="w-5"></div>

      <!-- Folder/Category Icon -->
      <div class="flex-shrink-0">
        <i
          :class="[
            'text-sm',
            hasChildren
              ? isExpanded
                ? 'fas fa-folder-open text-[var(--cafe-primary)]'
                : 'fas fa-folder text-[var(--cafe-primary)]'
              : 'fas fa-tag text-gray-400',
          ]"
        ></i>
      </div>

      <!-- Category Info -->
      <RouterLink
        :to="{ name: 'admin-product-category-show', params: { id: category.id } }"
        class="flex-1 flex items-center gap-2 min-w-0"
      >
        <span class="text-sm font-medium text-gray-900 truncate">{{ category.name }}</span>
        <Badge
          v-if="!category.is_active"
          variant="secondary"
          class="flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity"
        >
          Inactif
        </Badge>
      </RouterLink>

      <!-- Stats -->
      <div class="flex items-center gap-3 text-xs text-gray-500 flex-shrink-0">
        <span v-if="category.products_count !== undefined" class="flex items-center gap-1">
          <i class="fas fa-box text-gray-400"></i>
          {{ category.products_count }}
        </span>
        <span v-if="hasChildren" class="flex items-center gap-1">
          <i class="fas fa-sitemap text-gray-400"></i>
          {{ category.children!.length }}
        </span>
      </div>

      <!-- Add Subcategory Button -->
      <button
        @click="handleAddSubcategory"
        class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 text-[var(--cafe-primary)] hover:bg-[var(--cafe-primary)]/10 rounded-lg flex-shrink-0"
        title="Ajouter une sous-catégorie"
      >
        <i class="fas fa-plus text-xs"></i>
      </button>
    </div>

    <!-- Children (Recursive) -->
    <transition
      enter-active-class="transition-all duration-200 ease-out"
      enter-from-class="opacity-0 max-h-0"
      enter-to-class="opacity-100 max-h-screen"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 max-h-screen"
      leave-to-class="opacity-0 max-h-0"
    >
      <div v-if="isExpanded && hasChildren" class="overflow-hidden">
        <CardProductCategoryTree
          v-for="child in category.children"
          :key="child.id"
          :category="child"
          :level="level + 1"
          @add-subcategory="(cat) => $emit('add-subcategory', cat)"
        />
      </div>
    </transition>
  </div>
</template>

<style scoped>
.category-tree-item {
  @apply relative;
}

/* Subtle vertical line for tree structure */
.category-tree-item::before {
  content: '';
  position: absolute;
  left: calc(v-bind('level') * 1.5rem + 0.625rem);
  top: 0;
  bottom: 0;
  width: 1px;
  background: linear-gradient(to bottom, transparent 0%, #e5e7eb 20%, #e5e7eb 80%, transparent 100%);
  opacity: 0.5;
}

.category-tree-item:first-child::before {
  top: 50%;
}

.category-tree-item:last-child::before {
  bottom: 50%;
}
</style>
