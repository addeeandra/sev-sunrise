<script setup lang="ts">
import type { ValidationError } from '@/types'

defineProps<{
  errors: ValidationError | null
}>()
</script>

<template>
  <div v-if="errors" class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700">
    <p class="font-medium">{{ errors.message }}</p>
    <ul v-if="Object.keys(errors.errors).length" class="mt-1 list-disc list-inside">
      <li v-for="(messages, field) in errors.errors" :key="field" v-text="messages[0]" />
    </ul>
  </div>
</template>
