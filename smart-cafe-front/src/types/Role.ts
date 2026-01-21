import type { Permission } from './Permission';

export interface Role {
  id: number;
  name: string;
  guard_name: string;
  permissions?: Permission[];
  created_at: string | null;
  updated_at: string | null;
}
