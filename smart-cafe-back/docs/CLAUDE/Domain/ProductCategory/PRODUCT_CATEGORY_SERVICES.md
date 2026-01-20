# Services ProductCategory

## CreateProductCategoryService

Crée une nouvelle catégorie de produits.

```php
public function execute(CreateProductCategoryInputDTO $dto): ProductCategory
```

**Entrée** : `CreateProductCategoryInputDTO`
- `name` (string) : Nom de la catégorie
- `description` (?string) : Description
- `parentId` (?int) : ID de la catégorie parente
- `isActive` (bool) : Catégorie active

**Comportement** :
1. Génère un slug unique depuis le nom
2. Si le slug existe déjà, ajoute un suffixe numérique
3. Vérifie l'existence du parent si spécifié
4. Retourne la catégorie avec ses relations parent/children

**Sortie** : `ProductCategory` avec relations chargées

## UpdateProductCategoryService

Met à jour une catégorie existante.

```php
public function execute(ProductCategory $category, UpdateProductCategoryInputDTO $dto): ProductCategory
```

**Entrée** : `UpdateProductCategoryInputDTO`
- `name` (?string) : Nouveau nom
- `description` (?string) : Nouvelle description
- `parentId` (?int) : Nouveau parent (null pour catégorie racine)
- `isActive` (?bool) : Nouveau statut

**Validation** :
- Le parent ne peut pas être la catégorie elle-même
- Le parent ne peut pas être un descendant de la catégorie

## DeleteProductCategoryService

Supprime une catégorie (soft delete).

```php
public function execute(ProductCategory $category): bool
```

**Comportement** :
- Les sous-catégories deviennent des catégories racines (parent_id = null)
- Les produits de la catégorie ne sont pas affectés

## ListProductCategoriesService

Liste les catégories avec filtres et pagination.

```php
public function execute(ListProductCategoriesFilterDTO $dto): LengthAwarePaginator
```

**Filtres** :
- `search` : Recherche par nom
- `isActive` : Filtrer par statut
- `parentId` : Filtrer par parent
- `rootOnly` : Uniquement les catégories racines

**Pagination** :
- `perPage` : Nombre par page (défaut: 15, max: 100)
- `page` : Numéro de page

## GetProductCategoryService

Récupère une catégorie avec ses relations.

```php
public function execute(ProductCategory $category): ProductCategory
```

**Sortie** : `ProductCategory` avec `parent` et `children` chargés
