export interface ProductCategory {
  id: number
  name: string
  slug: string
  description: string | null
  is_active: boolean
  parent?: ProductCategory
  children?: ProductCategory[]
  products_count?: number
  is_deleted: boolean
  created_at: string
  updated_at: string
  deleted_at?: string
}
