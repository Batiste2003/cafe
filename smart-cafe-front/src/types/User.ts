import type { Permission } from './Permission';
import type { Role } from './Role';

export interface User {
  id: number;
  name: string;
  email: string;
  email_verified_at: string | null;
  roles?: Role[];
  permissions?: Permission[];
  is_deleted: boolean;
  created_at: string | null;
  updated_at: string | null;
  deleted_at?: string | null;
}
