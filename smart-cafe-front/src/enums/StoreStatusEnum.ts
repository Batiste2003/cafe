export enum StoreStatusEnum {
  ACTIVE = 'active',
  DRAFT = 'draft',
  UNPUBLISH = 'unpublish',
}

export const StoreStatusLabels: Record<StoreStatusEnum, string> = {
  [StoreStatusEnum.ACTIVE]: 'Actif',
  [StoreStatusEnum.DRAFT]: 'Brouillon',
  [StoreStatusEnum.UNPUBLISH]: 'Non publié',
}
