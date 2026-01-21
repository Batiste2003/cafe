export enum UserRoleEnum {
  ADMIN = 'admin',
  MANAGER = 'manager',
  EMPLOYER = 'employer',
}

export const UserRoleLabels: Record<UserRoleEnum, string> = {
  [UserRoleEnum.ADMIN]: 'Administrateur',
  [UserRoleEnum.MANAGER]: 'Manager',
  [UserRoleEnum.EMPLOYER]: 'Employé',
}

export const getUserRoleLabel = (roleName: string): string => {
  const role = Object.values(UserRoleEnum).find((r) => r === roleName)
  return role ? UserRoleLabels[role] : roleName
}
