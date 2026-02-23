<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { extractValidationErrors } from '@/api/client'
import type { ValidationError } from '@/types'
import ValidationErrors from '@/components/ValidationErrors.vue'

const router = useRouter()
const auth = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const isSubmitting = ref(false)
const errors = ref<ValidationError | null>(null)

async function handleSubmit() {
  errors.value = null
  isSubmitting.value = true
  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })
    router.push({ name: 'timeline' })
  } catch (e) {
    errors.value = extractValidationErrors(e)
    if (!errors.value) {
      errors.value = { message: 'Registration failed. Please try again.', errors: {} }
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="flex items-center justify-center min-h-dvh bg-gray-50 px-4">
    <div class="w-full max-w-sm">
      <div class="p-8">
        <h1 class="text-2xl font-bold text-center text-primary mb-2">InstaApp</h1>
        <p class="text-center text-gray-500 text-sm mb-6">Sign up to see photos from your friends.</p>

        <ValidationErrors :errors="errors" class="mb-4" />

        <form @submit.prevent="handleSubmit" class="space-y-4">
          <div>
            <input
              v-model="name"
              type="text"
              placeholder="Full Name"
              required
              class="w-full px-3 py-2 border border-gray-300 text-sm bg-gray-50 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
            />
          </div>
          <div>
            <input
              v-model="email"
              type="email"
              placeholder="Email"
              required
              class="w-full px-3 py-2 border border-gray-300 text-sm bg-gray-50 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
            />
          </div>
          <div>
            <input
              v-model="password"
              type="password"
              placeholder="Password"
              required
              class="w-full px-3 py-2 border border-gray-300 text-sm bg-gray-50 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
            />
          </div>
          <div>
            <input
              v-model="passwordConfirmation"
              type="password"
              placeholder="Confirm Password"
              required
              class="w-full px-3 py-2 border border-gray-300 text-sm bg-gray-50 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"
            />
          </div>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="w-full py-2 bg-primary text-white font-semibold text-sm hover:bg-primary-dark disabled:opacity-50 disabled:cursor-not-allowed border-none cursor-pointer"
          >
            {{ isSubmitting ? 'Signing up...' : 'Sign Up' }}
          </button>
        </form>
      </div>

      <div class="p-4 mt-3 text-center text-sm">
        Have an account?
        <RouterLink to="/login" class="text-primary font-semibold no-underline">Log in</RouterLink>
      </div>
    </div>
  </div>
</template>
