<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { createPost } from '@/api/posts'
import { extractValidationErrors } from '@/api/client'
import { useTimelineStore } from '@/stores/timeline'
import type { ValidationError } from '@/types'
import ValidationErrors from '@/components/ValidationErrors.vue'

const router = useRouter()
const timeline = useTimelineStore()

const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const text = ref('')
const isSubmitting = ref(false)
const errors = ref<ValidationError | null>(null)

const canSubmit = computed(() => imageFile.value !== null && !isSubmitting.value)

function handleFileChange(event: Event) {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (!file) return

  imageFile.value = file

  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
  }
  imagePreview.value = URL.createObjectURL(file)
}

function removeImage() {
  imageFile.value = null
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value)
    imagePreview.value = null
  }
}

async function handleSubmit() {
  if (!imageFile.value) return

  errors.value = null
  isSubmitting.value = true

  const formData = new FormData()
  formData.append('images[]', imageFile.value)
  if (text.value.trim()) {
    formData.append('text', text.value.trim())
  }

  try {
    const { data } = await createPost(formData)
    timeline.prependEntry(data.data)
    router.push({ name: 'timeline' })
  } catch (e) {
    errors.value = extractValidationErrors(e)
    if (!errors.value) {
      errors.value = { message: 'Failed to create post. Please try again.', errors: {} }
    }
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex items-center justify-between px-3 py-2 border-b border-gray-100">
      <button
        @click="router.back()"
        class="flex items-center gap-1 bg-transparent border-none p-0 cursor-pointer text-gray-700"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        <span class="text-sm font-medium">Back</span>
      </button>
      <h2 class="text-base font-semibold text-gray-900 m-0">New Post</h2>
      <button
        @click="handleSubmit"
        :disabled="!canSubmit"
        class="text-sm font-semibold text-primary bg-transparent border-none cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed p-0"
      >
        {{ isSubmitting ? 'Sharing...' : 'Share' }}
      </button>
    </div>

    <!-- Content -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4">
      <ValidationErrors :errors="errors" />

      <!-- Image picker -->
      <div v-if="!imagePreview" class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
        <label class="cursor-pointer">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0022.5 18.75V5.25A2.25 2.25 0 0020.25 3H3.75A2.25 2.25 0 001.5 5.25v13.5A2.25 2.25 0 003.75 21z" />
          </svg>
          <p class="text-sm text-gray-500 m-0">Tap to select an image</p>
          <input
            type="file"
            accept="image/*"
            class="hidden"
            @change="handleFileChange"
          />
        </label>
      </div>

      <!-- Image preview -->
      <div v-else class="relative">
        <img :src="imagePreview" alt="Preview" class="w-full aspect-square object-cover rounded-lg" />
        <button
          @click="removeImage"
          class="absolute top-2 right-2 w-8 h-8 rounded-full bg-black/50 text-white flex items-center justify-center border-none cursor-pointer"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <!-- Caption -->
      <textarea
        v-model="text"
        placeholder="Write a caption..."
        maxlength="2200"
        rows="4"
        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary resize-none"
      />
    </div>
  </div>
</template>
