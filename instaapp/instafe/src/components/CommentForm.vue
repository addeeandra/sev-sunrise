<script setup lang="ts">
import { ref } from 'vue'
import type { Comment, ValidationError } from '@/types'
import { createComment } from '@/api/comments'
import { extractValidationErrors } from '@/api/client'

const props = defineProps<{
  postId: number
}>()

const emit = defineEmits<{
  commented: [comment: Comment]
}>()

const body = ref('')
const isSubmitting = ref(false)
const error = ref<string | null>(null)

async function handleSubmit() {
  if (!body.value.trim()) return
  error.value = null
  isSubmitting.value = true
  try {
    const { data } = await createComment(props.postId, { body: body.value.trim() })
    emit('commented', data.data)
    body.value = ''
  } catch (e) {
    const validationError = extractValidationErrors(e) as ValidationError | null
    error.value = validationError?.message ?? 'Failed to post comment.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="border-t border-gray-200">
    <p v-if="error" class="text-xs text-red-500 px-3 pt-2 m-0">{{ error }}</p>
    <form @submit.prevent="handleSubmit" class="flex items-center gap-2 px-3 py-2">
      <input
        v-model="body"
        type="text"
        placeholder="Add a comment..."
        :disabled="isSubmitting"
        class="flex-1 text-sm bg-transparent border-none outline-none placeholder-gray-400 text-gray-900 p-0"
      />
      <button
        type="submit"
        :disabled="isSubmitting || !body.trim()"
        class="text-sm font-semibold text-primary bg-transparent border-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed p-0"
      >
        Post
      </button>
    </form>
  </div>
</template>
