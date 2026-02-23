<script setup lang="ts">
import AppShell from '@/components/AppShell.vue'
import { useAuthStore } from '@/stores/auth'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const auth = useAuthStore()
const route = useRoute()

const showShell = computed(() => auth.isAuthenticated && !route.meta.guest)
</script>

<template>
  <AppShell v-if="showShell">
    <RouterView />
  </AppShell>
  <RouterView v-else />
</template>
