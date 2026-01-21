import type { ProductCategory } from './ProductCategory'
import type { Store } from './Store'
import type { User } from './User'
import type { ProductVariant } from './ProductVariant'

export interface StoredFile {
  id: number
  filename?: string
  original_name?: string
  path: string
  mime_type: string
  size: number
  size_human?: string
  disk: string
  extension?: string
  url: string
  created_at: string
  updated_at: string
}

export interface ProductOption {
  id: number
  name: string
  values: string[]
  created_at: string
  updated_at: string
}

export interface Product {
  id: number
  name: string
  slug: string
  description: string | null
  is_active: boolean
  is_featured: boolean
  category?: ProductCategory
  stores?: Store[]
  creator?: User
  variants?: ProductVariant[]
  options?: ProductOption[]
  gallery?: StoredFile[]
  default_variant?: ProductVariant
  variants_count?: number
  is_deleted: boolean
  created_at: string
  updated_at: string
  deleted_at?: string
}
