<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import type { PostImage } from '@/types'

const props = defineProps<{
  images: PostImage[]
}>()

const scrollContainer = ref<HTMLDivElement | null>(null)
const activeIndex = ref(0)

onMounted(() => {
  if (!scrollContainer.value || props.images.length <= 1) return

  const observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) {
          const index = Number((entry.target as HTMLElement).dataset.index)
          if (!isNaN(index)) activeIndex.value = index
        }
      }
    },
    { root: scrollContainer.value, threshold: 0.5 },
  )

  for (const img of scrollContainer.value.querySelectorAll('img')) {
    observer.observe(img)
  }

  watch(
    () => props.images,
    () => {
      observer.disconnect()
      if (!scrollContainer.value) return
      for (const img of scrollContainer.value.querySelectorAll('img')) {
        observer.observe(img)
      }
    },
  )
})
</script>

<template>
  <div class="relative bg-black">
    <div
      ref="scrollContainer"
      class="flex overflow-x-auto snap-x snap-mandatory"
      style="scrollbar-width: none; -ms-overflow-style: none;"
    >
      <img
        v-for="(img, i) in images"
        :key="img.id"
        :src="img.image_url"
        :alt="`Image ${img.order + 1}`"
        :data-index="i"
        class="w-full flex-none aspect-square object-cover snap-start"
      />
    </div>
    <div
      v-if="images.length > 1"
      class="absolute bottom-2 left-1/2 -translate-x-1/2 flex gap-1.5"
    >
      <span
        v-for="(_, i) in images"
        :key="i"
        class="w-1.5 h-1.5 rounded-full transition-colors"
        :class="i === activeIndex ? 'bg-primary' : 'bg-white/50'"
      />
    </div>
  </div>
</template>

<style scoped>
div::-webkit-scrollbar {
  display: none;
}
</style>
