import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { User, LoginRequest, RegisterRequest } from '@/types'
import * as authApi from '@/api/auth'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(localStorage.getItem('auth_token'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(credentials: LoginRequest) {
    const { data } = await authApi.login(credentials)
    token.value = data.token
    user.value = data.user
    localStorage.setItem('auth_token', data.token)
  }

  async function register(payload: RegisterRequest) {
    const { data } = await authApi.register(payload)
    token.value = data.token
    user.value = data.user
    localStorage.setItem('auth_token', data.token)
  }

  async function fetchUser() {
    const { data } = await authApi.getMe()
    user.value = data.data
  }

  function logout() {
    token.value = null
    user.value = null
    localStorage.removeItem('auth_token')
  }

  async function initialize() {
    if (token.value && !user.value) {
      try {
        await fetchUser()
      } catch {
        logout()
      }
    }
  }

  return { user, token, isAuthenticated, login, register, fetchUser, logout, initialize }
})
