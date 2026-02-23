<script setup lang="ts">
import TopBar from '@/components/TopBar.vue'
import BottomNav from '@/components/BottomNav.vue'
</script>

<template>
  <div class="flex flex-col h-dvh bg-white max-w-lg mx-auto">
    <TopBar />
    <main class="flex-1 overflow-y-auto">
      <slot />
    </main>
    <BottomNav />
  </div>
</template>
