<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useTimelineStore } from '@/stores/timeline'
import PostCard from '@/components/PostCard.vue'
import LoadingSpinner from '@/components/LoadingSpinner.vue'

const timeline = useTimelineStore()
const sentinel = ref<HTMLDivElement | null>(null)

onMounted(async () => {
  if (!timeline.isInitialized) {
    await timeline.fetchInitial()
  }

  if (sentinel.value) {
    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0]?.isIntersecting && timeline.hasMore && !timeline.isLoading) {
          timeline.fetchMore()
        }
      },
      { rootMargin: '200px' },
    )
    observer.observe(sentinel.value)
  }
})
</script>

<template>
  <div>
    <div v-if="timeline.isLoading && !timeline.isInitialized" class="py-12">
      <LoadingSpinner />
    </div>

    <template v-else>
      <p v-if="timeline.entries.length === 0" class="text-center text-gray-400 text-sm py-12 m-0">
        No posts in your timeline yet.
      </p>

      <PostCard
        v-for="entry in timeline.entries"
        :key="entry.id"
        :post="entry.post"
        @like="timeline.toggleLike"
      />

      <div ref="sentinel" class="h-1" />

      <LoadingSpinner v-if="timeline.isLoading && timeline.isInitialized" />

      <p v-if="!timeline.hasMore && timeline.entries.length > 0" class="text-center text-gray-400 text-xs py-4 m-0">
        You've reached the end.
      </p>
    </template>
  </div>
</template>
