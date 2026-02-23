// ─── Domain Models ───────────────────────────────────────

export interface User {
  id: number
  name: string
  email: string
  created_at: string
}

export interface PostImage {
  id: number
  image_url: string
  order: number
}

export interface Post {
  id: number
  text: string | null
  user: User
  images: PostImage[]
  likes_count: number
  comments_count: number
  liked_by_me: boolean
  created_at: string
  updated_at: string
}

export interface Comment {
  id: number
  body: string
  user: User
  post_id: number
  created_at: string
}

export interface TimelineEntry {
  id: number
  post: Post
  created_at: string
}

// ─── API Responses ───────────────────────────────────────

export interface AuthResponse {
  token: string
  user: User
}

export interface LikeResponse {
  liked: boolean
  likes_count: number
}

export interface CursorPaginationMeta {
  path: string
  per_page: number
  next_cursor: string | null
  next_page_url: string | null
  prev_cursor: string | null
  prev_page_url: string | null
}

export interface PaginatedResponse<T> {
  data: T[]
  meta: CursorPaginationMeta
}

export interface DataWrapper<T> {
  data: T
}

// ─── Request Bodies ──────────────────────────────────────

export interface LoginRequest {
  email: string
  password: string
}

export interface RegisterRequest {
  name: string
  email: string
  password: string
  password_confirmation: string
}

export interface CreateCommentRequest {
  body: string
}

// ─── Error ───────────────────────────────────────────────

export interface ValidationError {
  message: string
  errors: Record<string, string[]>
}
