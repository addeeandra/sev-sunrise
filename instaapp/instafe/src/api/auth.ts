import apiClient from './client'
import type { AuthResponse, LoginRequest, RegisterRequest, User, DataWrapper } from '@/types'

export function login(data: LoginRequest) {
  return apiClient.post<AuthResponse>('/auth/login', data)
}

export function register(data: RegisterRequest) {
  return apiClient.post<AuthResponse>('/auth/register', data)
}

export function getMe() {
  return apiClient.get<DataWrapper<User>>('/auth/me')
}
