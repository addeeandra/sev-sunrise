import { ref, computed, type Ref } from 'vue'
import type { CursorPaginationMeta } from '@/types'

export function useCursorPagination<T>(
  fetchFn: (cursor?: string | null) => Promise<{ data: T[]; meta: CursorPaginationMeta }>,
) {
  const items = ref<T[]>([]) as Ref<T[]>
  const nextCursor = ref<string | null>(null)
  const isLoading = ref(false)
  const hasMore = computed(() => nextCursor.value !== null)

  async function loadInitial() {
    isLoading.value = true
    try {
      const result = await fetchFn()
      items.value = result.data
      nextCursor.value = result.meta.next_cursor
    } finally {
      isLoading.value = false
    }
  }

  async function loadMore() {
    if (isLoading.value || !hasMore.value) return
    isLoading.value = true
    try {
      const result = await fetchFn(nextCursor.value)
      items.value.push(...result.data)
      nextCursor.value = result.meta.next_cursor
    } finally {
      isLoading.value = false
    }
  }

  function prepend(item: T) {
    items.value.unshift(item)
  }

  return { items, isLoading, hasMore, loadInitial, loadMore, prepend }
}
