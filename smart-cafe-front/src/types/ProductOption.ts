import type { Product } from './Product'

export interface ProductOptionValue {
  id: number
  product_option_id: number
  value: string
  created_at: string
  updated_at: string
}

export interface ProductOption {
  id: number
  product_id: number
  name: string
  is_required: boolean
  values?: ProductOptionValue[]
  product?: Product
  created_at: string
  updated_at: string
}
