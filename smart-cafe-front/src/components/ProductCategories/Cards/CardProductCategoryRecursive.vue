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

const isExpanded = ref(true)

const hasChildren = props.category.children && props.category.children.length > 0

const toggleExpand = () => {
  if (hasChildren) {
    isExpanded.value = !isExpanded.value
  }
}

const getIndentClass = () => {
  if (props.level === 0) return ''
  return `ml-${props.level * 4}`
}

const getBorderClass = () => {
  if (props.level === 0) return 'border-l-4 border-[var(--cafe-primary)]'
  if (props.level === 1) return 'border-l-4 border-blue-400'
  if (props.level === 2) return 'border-l-4 border-green-400'
  return 'border-l-4 border-gray-400'
}
</script>

<template>
  <div class="category-recursive-item">
    <!-- Category Card -->
    <div
      :class="[
        'bg-white rounded-xl p-4 border border-gray-100 transition-all duration-200',
        getBorderClass(),
        level > 0 ? 'ml-6' : '',
      ]"
    >
      <div class="flex items-start gap-3">
        <!-- Expand/Collapse Button -->
        <button
          v-if="hasChildren"
          @click="toggleExpand"
          class="mt-1 w-6 h-6 flex items-center justify-center text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded transition-all flex-shrink-0"
        >
          <i
            :class="isExpanded ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
            class="text-xs"
          ></i>
        </button>
        <div v-else class="w-6"></div>

        <!-- Category Content -->
        <div class="flex-1 min-w-0">
          <RouterLink
            :to="{ name: 'admin-product-category-show', params: { id: category.id } }"
            class="block hover:opacity-75 transition-opacity"
          >
            <!-- Header -->
            <div class="flex items-start justify-between mb-2">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <i
                    :class="[
                      'text-sm flex-shrink-0',
                      hasChildren
                        ? isExpanded
                          ? 'fas fa-folder-open text-[var(--cafe-primary)]'
                          : 'fas fa-folder text-[var(--cafe-primary)]'
                        : 'fas fa-tag text-gray-400',
                    ]"
                  ></i>
                  <h3 class="text-sm font-semibold text-gray-900 truncate">{{ category.name }}</h3>
                </div>
                <p v-if="category.slug" class="text-xs text-gray-400 font-mono mt-0.5 truncate ml-6">
                  {{ category.slug }}
                </p>
              </div>
              <Badge :variant="category.is_active ? 'success' : 'secondary'" class="flex-shrink-0 ml-2">
                {{ category.is_active ? 'Actif' : 'Inactif' }}
              </Badge>
            </div>

            <!-- Description -->
            <p v-if="category.description" class="text-xs text-gray-600 mb-2 line-clamp-2 ml-6">
              {{ category.description }}
            </p>

            <!-- Footer Info -->
            <div class="flex items-center gap-3 pt-2 border-t border-gray-100 ml-6">
              <div
                v-if="category.products_count !== undefined"
                class="flex items-center gap-1.5 text-xs text-gray-500"
              >
                <i class="fas fa-box text-gray-400"></i>
                <span>{{ category.products_count }}</span>
              </div>
              <div v-if="hasChildren" class="flex items-center gap-1.5 text-xs text-gray-500">
                <i class="fas fa-sitemap text-gray-400"></i>
                <span>{{ category.children!.length }} sous-catégorie{{ category.children!.length > 1 ? 's' : '' }}</span>
              </div>
              <div v-if="category.parent" class="flex items-center gap-1.5 text-xs text-gray-500">
                <i class="fas fa-level-up-alt text-gray-400"></i>
                <span>{{ category.parent.name }}</span>
              </div>
            </div>
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Children (Recursive) -->
    <transition
      enter-active-class="transition-all duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition-all duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div v-if="isExpanded && hasChildren" class="mt-3 space-y-3">
        <CardProductCategoryRecursive
          v-for="child in category.children"
          :key="child.id"
          :category="child"
          :level="level + 1"
        />
      </div>
    </transition>
  </div>
</template>

<style scoped>
.category-recursive-item {
  @apply relative;
}
</style>
