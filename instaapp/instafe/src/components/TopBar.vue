<script setup lang="ts">
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

function handleLogout() {
  auth.logout()
  router.push({ name: 'login' })
}
</script>

<template>
  <header class="sticky top-0 z-50 flex items-center justify-between px-4 h-12 bg-white border-b border-gray-200">
    <h1 class="text-lg font-bold text-primary">InstaApp</h1>
    <button
      @click="handleLogout"
      class="text-sm text-gray-500 hover:text-gray-700 bg-transparent border-none p-0 cursor-pointer"
    >
      Logout
    </button>
  </header>
</template>
