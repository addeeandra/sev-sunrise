import apiClient from './client'
import type { TimelineEntry, PaginatedResponse } from '@/types'

export function getTimeline(cursor?: string | null) {
  return apiClient.get<PaginatedResponse<TimelineEntry>>('/timeline', {
    params: cursor ? { cursor } : {},
  })
}
