<template>
  <div class="block bg-white rounded-xl overflow-hidden border border-gray-100 animate-pulse">
    <!-- Image Skeleton -->
    <div class="aspect-square bg-gray-200"></div>

    <!-- Content Skeleton -->
    <div class="p-4">
      <!-- SKU -->
      <div class="h-5 bg-gray-200 rounded w-3/4 mb-3"></div>

      <!-- Price -->
      <div class="flex items-center gap-4 mb-3">
        <div class="h-4 bg-gray-200 rounded w-24"></div>
        <div class="h-3 bg-gray-200 rounded w-16"></div>
      </div>

      <!-- Stock Info -->
      <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
        <div class="h-3 bg-gray-200 rounded w-20"></div>
        <div class="h-3 bg-gray-200 rounded w-16"></div>
      </div>
    </div>
  </div>
</template>
