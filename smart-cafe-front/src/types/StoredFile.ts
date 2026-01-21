import type { User } from './User'

export interface StoredFile {
  id: number
  disk: string | null
  path: string
  url: string
  original_name: string
  mime_type: string
  size: number
  size_human: string
  extension: string
  user?: User
  created_at: string
  updated_at: string
}
