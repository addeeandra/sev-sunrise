<script setup lang="ts">
import { onMounted } from 'vue'
import type { Comment } from '@/types'
import { getComments } from '@/api/comments'
import { useCursorPagination } from '@/composables/useCursorPagination'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const props = defineProps<{
  postId: number
}>()

const { items: comments, isLoading, hasMore, loadInitial, loadMore, prepend } = useCursorPagination<Comment>(
  async (cursor) => {
    const { data } = await getComments(props.postId, cursor)
    return data
  },
)

defineExpose({ prepend })

onMounted(() => {
  loadInitial()
})

function timeAgo(dateStr: string): string {
  const seconds = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000)
  if (seconds < 60) return `${seconds}s`
  const minutes = Math.floor(seconds / 60)
  if (minutes < 60) return `${minutes}m`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours}h`
  const days = Math.floor(hours / 24)
  return `${days}d`
}
</script>

<template>
  <div>
    <div v-for="comment in comments" :key="comment.id" class="flex gap-2 px-3 py-2">
      <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center text-[10px] font-bold text-gray-600 shrink-0 mt-0.5">
        {{ comment.user.name.charAt(0).toUpperCase() }}
      </div>
      <div class="flex-1 min-w-0">
        <p class="text-sm text-gray-900 m-0">
          <span class="font-semibold">{{ comment.user.name }}</span>
          {{ ' ' }}
          <span>{{ comment.body }}</span>
        </p>
        <span class="text-xs text-gray-400">{{ timeAgo(comment.created_at) }}</span>
      </div>
    </div>

    <LoadingSpinner v-if="isLoading" />

    <button
      v-if="hasMore && !isLoading"
      @click="loadMore"
      class="w-full text-center text-sm text-gray-400 py-2 bg-transparent border-none cursor-pointer hover:text-gray-600"
    >
      Load more comments
    </button>

    <p v-if="!isLoading && comments.length === 0" class="text-center text-sm text-gray-400 py-4 m-0">
      No comments yet.
    </p>
  </div>
</template>
