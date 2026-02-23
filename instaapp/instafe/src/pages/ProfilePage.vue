<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()

function formatDate(dateStr: string): string {
  return new Date(dateStr).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}
</script>

<template>
  <div class="p-6">
    <div v-if="auth.user" class="flex flex-col items-center gap-4">
      <!-- Avatar -->
      <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-2xl font-bold text-gray-600">
        {{ auth.user.name.charAt(0).toUpperCase() }}
      </div>

      <!-- Name & email -->
      <div class="text-center">
        <h2 class="text-xl font-bold text-gray-900 m-0">{{ auth.user.name }}</h2>
        <p class="text-sm text-gray-500 mt-1 m-0">{{ auth.user.email }}</p>
      </div>

      <!-- Joined date -->
      <div class="text-sm text-gray-400">
        Joined {{ formatDate(auth.user.created_at) }}
      </div>
    </div>
  </div>
</template>
