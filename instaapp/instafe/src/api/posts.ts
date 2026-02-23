import apiClient from './client'
import type { Post, DataWrapper, LikeResponse } from '@/types'

export function getPost(postId: number) {
  return apiClient.get<DataWrapper<Post>>(`/posts/${postId}`)
}

export function toggleLike(postId: number) {
  return apiClient.post<LikeResponse>(`/posts/${postId}/like`)
}
