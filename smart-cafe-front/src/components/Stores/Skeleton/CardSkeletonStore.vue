<template>
  <div class="flex flex-col gap-3 p-4 bg-gray-50 rounded-xl animate-pulse">
    <!-- Banner skeleton -->
    <div class="w-full h-32 bg-gray-300 rounded-lg"></div>

    <!-- Logo and info skeleton -->
    <div class="flex items-start gap-3">
      <div class="w-12 h-12 bg-gray-300 rounded-full flex-shrink-0"></div>
      <div class="flex-1 space-y-2">
        <div class="h-4 w-32 bg-gray-300 rounded"></div>
        <div class="h-3 w-48 bg-gray-200 rounded"></div>
      </div>
    </div>

    <!-- Status badges skeleton -->
    <div class="flex items-center justify-between gap-2">
      <div class="flex items-center gap-2">
        <div class="h-6 w-16 bg-gray-300 rounded-full"></div>
      </div>
      <div class="h-3 w-12 bg-gray-200 rounded"></div>
    </div>

    <!-- Date skeleton -->
    <div class="h-3 w-24 bg-gray-200 rounded"></div>
  </div>
</template>
