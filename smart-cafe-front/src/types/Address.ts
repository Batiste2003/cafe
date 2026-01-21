export interface Address {
  id: number
  label: string | null
  address_line1: string
  address_line2: string | null
  city: string
  postal_code: string
  country: string
  created_at: string
  updated_at: string
}
