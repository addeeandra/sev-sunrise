import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { TimelineEntry } from '@/types'
import { getTimeline } from '@/api/timeline'
import { toggleLike as apiToggleLike } from '@/api/posts'

export const useTimelineStore = defineStore('timeline', () => {
  const entries = ref<TimelineEntry[]>([])
  const nextCursor = ref<string | null>(null)
  const isLoading = ref(false)
  const isInitialized = ref(false)

  const hasMore = computed(() => nextCursor.value !== null)

  async function fetchInitial() {
    isLoading.value = true
    try {
      const { data } = await getTimeline()
      entries.value = data.data
      nextCursor.value = data.meta.next_cursor
      isInitialized.value = true
    } finally {
      isLoading.value = false
    }
  }

  async function fetchMore() {
    if (isLoading.value || !hasMore.value) return
    isLoading.value = true
    try {
      const { data } = await getTimeline(nextCursor.value)
      entries.value.push(...data.data)
      nextCursor.value = data.meta.next_cursor
    } finally {
      isLoading.value = false
    }
  }

  async function toggleLike(postId: number) {
    const entry = entries.value.find((e) => e.post.id === postId)
    if (!entry) return

    const previousLiked = entry.post.liked_by_me
    const previousCount = entry.post.likes_count
    entry.post.liked_by_me = !previousLiked
    entry.post.likes_count += previousLiked ? -1 : 1

    try {
      const { data } = await apiToggleLike(postId)
      entry.post.liked_by_me = data.liked
      entry.post.likes_count = data.likes_count
    } catch {
      entry.post.liked_by_me = previousLiked
      entry.post.likes_count = previousCount
    }
  }

  function reset() {
    entries.value = []
    nextCursor.value = null
    isInitialized.value = false
  }

  return { entries, nextCursor, isLoading, isInitialized, hasMore, fetchInitial, fetchMore, toggleLike, reset }
})
