import type { StoreStatusEnum } from '@/enums/StoreStatusEnum'
import type { StoredFile } from './StoredFile'
import type { Address } from './Address'
import type { User } from './User'

export interface Store {
  id: number
  name: string
  status: StoreStatusEnum | null
  status_label: string | null
  banner?: StoredFile
  logo?: StoredFile
  address?: Address
  creator?: User
  users?: User[]
  users_count?: number
  is_deleted: boolean
  created_at: string
  updated_at: string
  deleted_at?: string
}
