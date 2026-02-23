<script setup lang="ts">
import type { Post } from '@/types'
import ImageCarousel from '@/components/ImageCarousel.vue'
import LikeButton from '@/components/LikeButton.vue'
import { useAuthStore } from '@/stores/auth'
import { useRouter } from 'vue-router'

const props = defineProps<{
  post: Post
}>()

const emit = defineEmits<{
  like: [postId: number]
  delete: [postId: number]
}>()

const auth = useAuthStore()
const router = useRouter()

function goToDetail() {
  router.push({ name: 'post-detail', params: { id: props.post.id } })
}

function timeAgo(dateStr: string): string {
  const seconds = Math.floor((Date.now() - new Date(dateStr).getTime()) / 1000)
  if (seconds < 60) return `${seconds}s`
  const minutes = Math.floor(seconds / 60)
  if (minutes < 60) return `${minutes}m`
  const hours = Math.floor(minutes / 60)
  if (hours < 24) return `${hours}h`
  const days = Math.floor(hours / 24)
  if (days < 7) return `${days}d`
  const weeks = Math.floor(days / 7)
  return `${weeks}w`
}
</script>

<template>
  <article class="border-b border-gray-100">
    <!-- Header -->
    <div class="flex items-center p-3 gap-3">
      <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
        {{ post.user.name.charAt(0).toUpperCase() }}
      </div>
      <span class="font-semibold text-sm text-gray-900">{{ post.user.name }}</span>
      <span class="ml-auto text-xs text-gray-400">{{ timeAgo(post.created_at) }}</span>
      <button
        v-if="auth.user?.id === post.user.id"
        @click.stop="emit('delete', post.id)"
        class="bg-transparent border-none p-0 cursor-pointer text-gray-400 hover:text-red-500"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>
      </button>
    </div>

    <!-- Images -->
    <ImageCarousel :images="post.images" />

    <!-- Actions -->
    <div class="flex items-center gap-4 px-3 py-2">
      <LikeButton
        :liked="post.liked_by_me ?? false"
        :count="post.likes_count"
        @toggle="emit('like', post.id)"
      />
      <button
        @click="goToDetail"
        class="flex items-center gap-1 bg-transparent border-none p-0 cursor-pointer"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
        <span class="text-sm font-semibold text-gray-900">{{ post.comments_count }}</span>
      </button>
    </div>

    <!-- Caption -->
    <div v-if="post.text" class="px-3 pb-2">
      <p class="text-sm text-gray-900 m-0">
        <span class="font-semibold">{{ post.user.name }}</span>
        {{ ' ' }}
        <span>{{ post.text }}</span>
      </p>
    </div>

    <!-- View comments link -->
    <button
      v-if="post.comments_count > 0"
      @click="goToDetail"
      class="px-3 pb-3 text-sm text-gray-400 bg-transparent border-none p-0 cursor-pointer"
    >
      View all {{ post.comments_count }} comments
    </button>
  </article>
</template>
