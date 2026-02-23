import apiClient from './client'
import type { Comment, PaginatedResponse, DataWrapper, CreateCommentRequest } from '@/types'

export function getComments(postId: number, cursor?: string | null) {
  return apiClient.get<PaginatedResponse<Comment>>(`/posts/${postId}/comments`, {
    params: cursor ? { cursor } : {},
  })
}

export function createComment(postId: number, data: CreateCommentRequest) {
  return apiClient.post<DataWrapper<Comment>>(`/posts/${postId}/comments`, data)
}
