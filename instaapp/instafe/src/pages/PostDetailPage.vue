<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import type { Post, Comment } from '@/types'
import { getPost, toggleLike as apiToggleLike, deletePost } from '@/api/posts'
import { useAuthStore } from '@/stores/auth'
import { useTimelineStore } from '@/stores/timeline'
import ImageCarousel from '@/components/ImageCarousel.vue'
import LikeButton from '@/components/LikeButton.vue'
import CommentList from '@/components/CommentList.vue'
import CommentForm from '@/components/CommentForm.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()
const timeline = useTimelineStore()

const post = ref<Post | null>(null)
const isLoading = ref(true)
const commentList = ref<InstanceType<typeof CommentList> | null>(null)

onMounted(async () => {
  try {
    const postId = Number(route.params.id)
    const { data } = await getPost(postId)
    post.value = data.data
  } catch {
    router.replace({ name: 'timeline' })
  } finally {
    isLoading.value = false
  }
})

async function handleToggleLike() {
  if (!post.value) return
  const prev = post.value.liked_by_me
  const prevCount = post.value.likes_count
  post.value.liked_by_me = !prev
  post.value.likes_count += prev ? -1 : 1

  try {
    const { data } = await apiToggleLike(post.value.id)
    post.value.liked_by_me = data.liked
    post.value.likes_count = data.likes_count
  } catch {
    post.value.liked_by_me = prev
    post.value.likes_count = prevCount
  }
}

function handleCommented(comment: Comment) {
  if (post.value) {
    post.value.comments_count++
  }
  commentList.value?.prepend(comment)
}

async function handleDelete() {
  if (!post.value) return
  if (!confirm('Delete this post?')) return
  try {
    await deletePost(post.value.id)
    timeline.removeEntry(post.value.id)
    router.replace({ name: 'timeline' })
  } catch {
    // silently fail
  }
}

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
  <div class="flex flex-col h-full">
    <!-- Back button -->
    <div class="flex items-center gap-2 px-3 py-2 border-b border-gray-100">
      <button
        @click="router.back()"
        class="flex items-center gap-1 bg-transparent border-none p-0 cursor-pointer text-gray-700"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="text-sm font-medium">Back</span>
      </button>
    </div>

    <LoadingSpinner v-if="isLoading" />

    <template v-if="post">
      <div class="flex-1 overflow-y-auto">
        <!-- Post header -->
        <div class="flex items-center p-3 gap-3">
          <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
            {{ post.user.name.charAt(0).toUpperCase() }}
          </div>
          <span class="font-semibold text-sm text-gray-900">{{ post.user.name }}</span>
          <span class="ml-auto text-xs text-gray-400">{{ timeAgo(post.created_at) }}</span>
          <button
            v-if="auth.user?.id === post.user.id"
            @click="handleDelete"
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
            @toggle="handleToggleLike"
          />
        </div>

        <!-- Caption -->
        <div v-if="post.text" class="px-3 pb-2">
          <p class="text-sm text-gray-900 m-0">
            <span class="font-semibold">{{ post.user.name }}</span>
            {{ ' ' }}
            <span>{{ post.text }}</span>
          </p>
        </div>

        <!-- Comments -->
        <div class="border-t border-gray-100 mt-2">
          <h3 class="text-sm font-semibold text-gray-900 px-3 py-2 m-0">Comments</h3>
          <CommentList ref="commentList" :post-id="post.id" />
        </div>
      </div>

      <!-- Comment form pinned at bottom -->
      <CommentForm :post-id="post.id" @commented="handleCommented" />
    </template>
  </div>
</template>
