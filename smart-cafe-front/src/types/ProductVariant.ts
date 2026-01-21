import type { Product, StoredFile } from './Product'
import type { Store } from './Store'

export interface ProductOptionValue {
  id: number
  product_option_id: number
  value: string
  created_at: string
  updated_at: string
}

export interface StoreProductVariant {
  id: number
  store_id: number
  product_variant_id: number
  stock: number | null
  is_unlimited: boolean
  is_in_stock: boolean
  store?: Store
  variant?: ProductVariant
  created_at: string
  updated_at: string
}

export interface ProductVariant {
  id: number
  sku: string
  price_cent_ht: number
  price_euros: number
  cost_price_cent_ht: number | null
  cost_price_euros: number | null
  is_default: boolean
  store_stocks?: StoreProductVariant[]
  product?: Product
  gallery?: StoredFile[]
  option_values?: ProductOptionValue[]
  is_deleted: boolean
  created_at: string
  updated_at: string
  deleted_at?: string
}
