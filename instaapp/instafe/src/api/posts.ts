import apiClient from './client'
import type { Post, DataWrapper, LikeResponse } from '@/types'

export function getPost(postId: number) {
  return apiClient.get<DataWrapper<Post>>(`/posts/${postId}`)
}

export function createPost(data: FormData) {
  return apiClient.post<DataWrapper<Post>>('/posts', data, {
    headers: { 'Content-Type': 'multipart/form-data' },
  })
}

export function deletePost(postId: number) {
  return apiClient.delete(`/posts/${postId}`)
}

export function toggleLike(postId: number) {
  return apiClient.post<LikeResponse>(`/posts/${postId}/like`)
}
