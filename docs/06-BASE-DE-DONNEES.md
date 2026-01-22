# 06 - Base de Données

> Modélisation et architecture de la base de données Smart Cafe

---

## Table des matières

1. [Choix Technologiques](#1-choix-technologiques)
2. [Modèle Conceptuel (MCD)](#2-modèle-conceptuel-mcd)
3. [Modèle Logique (MLD)](#3-modèle-logique-mld)
4. [Schéma des Tables](#4-schéma-des-tables)
5. [Relations et Cardinalités](#5-relations-et-cardinalités)
6. [Contraintes d'Intégrité](#6-contraintes-dintégrité)
7. [Index et Optimisations](#7-index-et-optimisations)
8. [Volumétrie et Performance](#8-volumétrie-et-performance)

---

## 1. Choix Technologiques

### 1.1 SGBD Utilisé

| Caractéristique | Valeur |
|-----------------|--------|
| **SGBD** | MySQL / SQLite |
| **ORM** | Laravel Eloquent |
| **Migrations** | Laravel Migrations |
| **Seeds** | Laravel Seeders & Factories |

### 1.2 Justification du Choix MySQL

```
┌─────────────────────────────────────────────────────────────────┐
│                    POURQUOI MYSQL ?                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ✅ Fiabilité                                                  │
│      • SGBD mature et éprouvé                                  │
│      • Transactions ACID                                       │
│      • Réplication native                                      │
│                                                                 │
│   ✅ Performance                                                │
│      • Optimiseur de requêtes performant                       │
│      • Index efficaces (B-Tree, Full-Text)                     │
│      • Query cache                                             │
│                                                                 │
│   ✅ Intégration Laravel                                        │
│      • Driver natif                                            │
│      • Eloquent ORM optimisé                                   │
│      • Migrations fluides                                      │
│                                                                 │
│   ✅ Écosystème                                                 │
│      • Outils d'administration (phpMyAdmin, MySQL Workbench)   │
│      • Documentation abondante                                 │
│      • Hébergement facile                                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.3 Environnements

| Environnement | SGBD | Justification |
|---------------|------|---------------|
| **Développement** | SQLite | Simplicité, pas de serveur |
| **Test** | SQLite (in-memory) | Rapidité des tests |
| **Production** | MySQL 8.0+ | Performance, scalabilité |

---

## 2. Modèle Conceptuel (MCD)

### 2.1 Diagramme Entité-Relation

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              MODÈLE CONCEPTUEL                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│                              ┌─────────────┐                                │
│                              │    USER     │                                │
│                              │─────────────│                                │
│                              │ id          │                                │
│                              │ name        │                                │
│                              │ email       │                                │
│                              │ password    │                                │
│                              └──────┬──────┘                                │
│                                     │                                       │
│            ┌────────────────────────┼────────────────────────┐             │
│            │                        │                        │              │
│            ▼                        ▼                        ▼              │
│     ┌─────────────┐          ┌─────────────┐          ┌─────────────┐      │
│     │   ADDRESS   │          │    STORE    │          │   PRODUCT   │      │
│     │─────────────│          │─────────────│          │─────────────│      │
│     │ id          │◄─────────│ address_id  │          │ id          │      │
│     │ street      │          │ name        │          │ name        │      │
│     │ city        │          │ status      │          │ description │      │
│     │ zip_code    │          │ banner      │          │ is_active   │      │
│     │ country     │          │ logo        │          │ category_id │◄──┐  │
│     └─────────────┘          └──────┬──────┘          └──────┬──────┘   │  │
│                                     │                        │          │  │
│                                     │ many-to-many           │          │  │
│                                     │ (store_user)           │          │  │
│                                     ▼                        │          │  │
│                              ┌─────────────┐                 │          │  │
│                              │ STORE_USER  │                 │          │  │
│                              │─────────────│                 │          │  │
│                              │ store_id    │                 │          │  │
│                              │ user_id     │                 │          │  │
│                              └─────────────┘                 │          │  │
│                                                              │          │  │
│     ┌────────────────────────────────────────────────────────┤          │  │
│     │                                                        │          │  │
│     │ many-to-many (product_store)                           │          │  │
│     ▼                                                        ▼          │  │
│ ┌─────────────┐                                       ┌─────────────┐   │  │
│ │PRODUCT_STORE│                                       │  VARIANT    │   │  │
│ │─────────────│                                       │─────────────│   │  │
│ │ product_id  │                                       │ product_id  │   │  │
│ │ store_id    │                                       │ name        │   │  │
│ └─────────────┘                                       │ sku         │   │  │
│                                                       │ price       │   │  │
│                                                       └──────┬──────┘   │  │
│                                                              │          │  │
│                                                              │          │  │
│     ┌─────────────┐                                   ┌──────┴──────┐   │  │
│     │   OPTION    │                                   │STORE_VARIANT│   │  │
│     │─────────────│                                   │─────────────│   │  │
│     │ product_id  │                                   │ store_id    │   │  │
│     │ name        │                                   │ variant_id  │   │  │
│     └──────┬──────┘                                   │ stock       │   │  │
│            │                                          │ price       │   │  │
│            ▼                                          └─────────────┘   │  │
│     ┌─────────────┐                                                     │  │
│     │OPTION_VALUE │                                                     │  │
│     │─────────────│                         ┌─────────────────────┐     │  │
│     │ option_id   │                         │ PRODUCT_CATEGORY    │─────┘  │
│     │ value       │                         │─────────────────────│        │
│     │ extra_price │                         │ id                  │        │
│     └─────────────┘                         │ name                │        │
│                                             │ description         │        │
│                                             │ parent_id (self)    │        │
│                                             └─────────────────────┘        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Entités Principales

| Entité | Description | Cardinalité estimée |
|--------|-------------|---------------------|
| **User** | Utilisateurs du système | ~100-1000 |
| **Store** | Magasins/établissements | ~1-50 |
| **Product** | Produits du catalogue | ~50-500 |
| **ProductVariant** | Variantes de produits | ~100-2000 |
| **ProductCategory** | Catégories de produits | ~10-50 |
| **ProductOption** | Options de personnalisation | ~20-100 |
| **ProductOptionValue** | Valeurs des options | ~50-300 |
| **Address** | Adresses des magasins | ~1-50 |
| **StoredFile** | Fichiers uploadés (images) | ~100-5000 |

---

## 3. Modèle Logique (MLD)

### 3.1 Schéma Relationnel

```
users (
    id PK,
    name,
    email UNIQUE,
    password,
    email_verified_at NULL,
    remember_token NULL,
    created_at,
    updated_at,
    deleted_at NULL
)

stores (
    id PK,
    name,
    banner_stored_file_id FK NULL -> stored_files,
    logo_stored_file_id FK NULL -> stored_files,
    created_by FK -> users,
    address_id FK NULL -> addresses,
    status DEFAULT 'draft',
    created_at,
    updated_at,
    deleted_at NULL
)

store_user (
    id PK,
    store_id FK -> stores,
    user_id FK -> users,
    created_at,
    updated_at,
    UNIQUE(store_id, user_id)
)

product_categories (
    id PK,
    name,
    slug UNIQUE,
    description NULL,
    parent_id FK NULL -> product_categories,
    created_by FK -> users,
    created_at,
    updated_at,
    deleted_at NULL
)

products (
    id PK,
    name,
    slug UNIQUE,
    description NULL,
    product_category_id FK -> product_categories,
    created_by FK -> users,
    is_active DEFAULT true,
    is_featured DEFAULT false,
    created_at,
    updated_at,
    deleted_at NULL
)

product_variants (
    id PK,
    product_id FK -> products,
    name,
    sku UNIQUE,
    price DECIMAL(10,2),
    is_active DEFAULT true,
    created_at,
    updated_at,
    deleted_at NULL
)

product_options (
    id PK,
    product_id FK -> products,
    name,
    is_required DEFAULT false,
    created_at,
    updated_at
)

product_option_values (
    id PK,
    product_option_id FK -> product_options,
    value,
    extra_price DECIMAL(10,2) DEFAULT 0,
    created_at,
    updated_at
)

product_store (
    id PK,
    product_id FK -> products,
    store_id FK -> stores,
    created_at,
    updated_at,
    UNIQUE(product_id, store_id)
)

store_product_variant (
    id PK,
    store_id FK -> stores,
    product_variant_id FK -> product_variants,
    stock INT DEFAULT 0,
    price DECIMAL(10,2) NULL,
    is_available DEFAULT true,
    created_at,
    updated_at,
    UNIQUE(store_id, product_variant_id)
)

addresses (
    id PK,
    street,
    street_additional NULL,
    city,
    state NULL,
    zip_code,
    country DEFAULT 'France',
    created_at,
    updated_at
)

stored_files (
    id PK,
    filename,
    original_filename,
    path,
    mime_type,
    size INT,
    created_at,
    updated_at
)

product_gallery (
    id PK,
    product_id FK -> products,
    stored_file_id FK -> stored_files,
    position INT DEFAULT 0,
    created_at,
    updated_at
)

product_variant_gallery (
    id PK,
    product_variant_id FK -> product_variants,
    stored_file_id FK -> stored_files,
    position INT DEFAULT 0,
    created_at,
    updated_at
)

-- Tables Spatie Permission
roles (
    id PK,
    name,
    guard_name,
    created_at,
    updated_at
)

permissions (
    id PK,
    name,
    guard_name,
    created_at,
    updated_at
)

model_has_roles (
    role_id FK -> roles,
    model_type,
    model_id,
    PRIMARY KEY (role_id, model_id, model_type)
)

model_has_permissions (
    permission_id FK -> permissions,
    model_type,
    model_id,
    PRIMARY KEY (permission_id, model_id, model_type)
)

role_has_permissions (
    permission_id FK -> permissions,
    role_id FK -> roles,
    PRIMARY KEY (permission_id, role_id)
)
```

---

## 4. Schéma des Tables

### 4.1 Table `users`

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    INDEX idx_email (email),
    INDEX idx_deleted_at (deleted_at)
);
```

### 4.2 Table `stores`

```sql
CREATE TABLE stores (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    banner_stored_file_id BIGINT UNSIGNED NULL,
    logo_stored_file_id BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    address_id BIGINT UNSIGNED NULL,
    status VARCHAR(255) DEFAULT 'draft',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (banner_stored_file_id) REFERENCES stored_files(id) ON DELETE SET NULL,
    FOREIGN KEY (logo_stored_file_id) REFERENCES stored_files(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE SET NULL,

    INDEX idx_status (status),
    INDEX idx_status_deleted (status, deleted_at)
);
```

### 4.3 Table `products`

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    product_category_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (product_category_id) REFERENCES product_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_is_active (is_active),
    INDEX idx_is_featured (is_featured),
    INDEX idx_category_active (product_category_id, is_active),
    INDEX idx_active_deleted (is_active, deleted_at)
);
```

### 4.4 Table `product_variants`

```sql
CREATE TABLE product_variants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,

    INDEX idx_product_id (product_id),
    INDEX idx_sku (sku),
    INDEX idx_is_active (is_active)
);
```

### 4.5 Table `store_product_variant` (Stock)

```sql
CREATE TABLE store_product_variant (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    store_id BIGINT UNSIGNED NOT NULL,
    product_variant_id BIGINT UNSIGNED NOT NULL,
    stock INT DEFAULT 0,
    price DECIMAL(10, 2) NULL,  -- Prix spécifique au magasin (optionnel)
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (product_variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,

    UNIQUE KEY unique_store_variant (store_id, product_variant_id),
    INDEX idx_stock (stock),
    INDEX idx_available (is_available)
);
```

---

## 5. Relations et Cardinalités

### 5.1 Diagramme des Relations

```
┌─────────────────────────────────────────────────────────────────┐
│                    CARDINALITÉS                                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   User ──────────────── 1:N ────────────────► Store             │
│         (created_by)                                            │
│                                                                 │
│   User ◄─────────────── N:M ────────────────► Store             │
│         (store_user)                                            │
│                                                                 │
│   User ──────────────── 1:N ────────────────► Product           │
│         (created_by)                                            │
│                                                                 │
│   ProductCategory ───── 1:N ────────────────► Product           │
│         (product_category_id)                                   │
│                                                                 │
│   ProductCategory ───── 1:N ────────────────► ProductCategory   │
│         (parent_id - auto-référence)                            │
│                                                                 │
│   Product ◄──────────── N:M ────────────────► Store             │
│         (product_store)                                         │
│                                                                 │
│   Product ──────────── 1:N ─────────────────► ProductVariant    │
│         (product_id)                                            │
│                                                                 │
│   Product ──────────── 1:N ─────────────────► ProductOption     │
│         (product_id)                                            │
│                                                                 │
│   ProductOption ─────── 1:N ────────────────► ProductOptionValue│
│         (product_option_id)                                     │
│                                                                 │
│   ProductVariant ◄───── N:M ────────────────► Store             │
│         (store_product_variant - avec stock)                    │
│                                                                 │
│   Product ──────────── 1:N ─────────────────► StoredFile        │
│         (product_gallery)                                       │
│                                                                 │
│   ProductVariant ────── 1:N ────────────────► StoredFile        │
│         (product_variant_gallery)                               │
│                                                                 │
│   Store ◄──────────────── 1:1 ──────────────► Address           │
│         (address_id)                                            │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Tableau des Relations

| Relation | Table 1 | Table 2 | Type | Pivot |
|----------|---------|---------|------|-------|
| **Créateur Store** | users | stores | 1:N | - |
| **Équipe Store** | users | stores | N:M | store_user |
| **Créateur Product** | users | products | 1:N | - |
| **Catégorie** | product_categories | products | 1:N | - |
| **Hiérarchie Catégories** | product_categories | product_categories | 1:N | - |
| **Produit-Store** | products | stores | N:M | product_store |
| **Variantes** | products | product_variants | 1:N | - |
| **Options** | products | product_options | 1:N | - |
| **Valeurs Options** | product_options | product_option_values | 1:N | - |
| **Stock par Store** | product_variants | stores | N:M | store_product_variant |
| **Galerie Produit** | products | stored_files | N:M | product_gallery |
| **Galerie Variante** | product_variants | stored_files | N:M | product_variant_gallery |
| **Adresse Store** | stores | addresses | 1:1 | - |

---

## 6. Contraintes d'Intégrité

### 6.1 Contraintes de Clé Étrangère

| Contrainte | Action DELETE | Action UPDATE | Justification |
|------------|---------------|---------------|---------------|
| `products.created_by` | CASCADE | CASCADE | Supprimer les produits si l'utilisateur est supprimé |
| `products.product_category_id` | CASCADE | CASCADE | Supprimer les produits si la catégorie est supprimée |
| `stores.created_by` | CASCADE | CASCADE | Supprimer les stores si le créateur est supprimé |
| `stores.address_id` | SET NULL | CASCADE | Conserver le store même sans adresse |
| `stores.banner/logo` | SET NULL | CASCADE | Conserver le store même sans images |
| `product_variants.product_id` | CASCADE | CASCADE | Supprimer les variantes avec le produit |
| `store_product_variant.*` | CASCADE | CASCADE | Supprimer les stocks si store ou variante supprimé |

### 6.2 Contraintes d'Unicité

```sql
-- Email unique par utilisateur
ALTER TABLE users ADD UNIQUE INDEX (email);

-- Slug unique par produit
ALTER TABLE products ADD UNIQUE INDEX (slug);

-- SKU unique par variante
ALTER TABLE product_variants ADD UNIQUE INDEX (sku);

-- Un utilisateur ne peut être attaché qu'une fois à un store
ALTER TABLE store_user ADD UNIQUE INDEX (store_id, user_id);

-- Un produit ne peut être dans un store qu'une fois
ALTER TABLE product_store ADD UNIQUE INDEX (product_id, store_id);

-- Stock unique par variante et store
ALTER TABLE store_product_variant ADD UNIQUE INDEX (store_id, product_variant_id);
```

### 6.3 Contraintes de Validation

```php
// Laravel Eloquent - Règles de validation
[
    'email' => 'required|email|unique:users,email',
    'price' => 'required|numeric|min:0',
    'stock' => 'required|integer|min:0',
    'status' => 'required|in:draft,active,inactive',
]
```

### 6.4 Soft Deletes

Les tables suivantes utilisent le **soft delete** (suppression logique) :

| Table | Colonne | Comportement |
|-------|---------|--------------|
| `users` | `deleted_at` | Utilisateurs désactivés mais récupérables |
| `stores` | `deleted_at` | Magasins archivés |
| `products` | `deleted_at` | Produits retirés du catalogue |
| `product_variants` | `deleted_at` | Variantes retirées |
| `product_categories` | `deleted_at` | Catégories archivées |

---

## 7. Index et Optimisations

### 7.1 Index Créés

```sql
-- Table products
CREATE INDEX idx_is_active ON products(is_active);
CREATE INDEX idx_is_featured ON products(is_featured);
CREATE INDEX idx_category_active ON products(product_category_id, is_active);
CREATE INDEX idx_active_deleted ON products(is_active, deleted_at);

-- Table stores
CREATE INDEX idx_status ON stores(status);
CREATE INDEX idx_status_deleted ON stores(status, deleted_at);

-- Table product_variants
CREATE INDEX idx_product_id ON product_variants(product_id);
CREATE INDEX idx_sku ON product_variants(sku);

-- Table store_product_variant
CREATE INDEX idx_stock ON store_product_variant(stock);
CREATE INDEX idx_available ON store_product_variant(is_available);
```

### 7.2 Justification des Index

| Index | Requête Optimisée |
|-------|-------------------|
| `idx_is_active` | `WHERE is_active = true` |
| `idx_is_featured` | `WHERE is_featured = true` |
| `idx_category_active` | `WHERE category_id = ? AND is_active = true` |
| `idx_status` | `WHERE status = 'active'` |
| `idx_stock` | `WHERE stock > 0` ou `ORDER BY stock` |

### 7.3 Optimisation Eloquent

```php
// ❌ N+1 Problem
$products = Product::all();
foreach ($products as $product) {
    echo $product->category->name;  // Requête par produit
}

// ✅ Eager Loading
$products = Product::with('category')->get();

// ✅ Eager Loading multiple
$products = Product::with([
    'category',
    'variants',
    'variants.stocks',
    'options.values',
])->get();

// ✅ Chargement conditionnel
$products = Product::with([
    'variants' => function ($query) {
        $query->where('is_active', true);
    }
])->get();
```

---

## 8. Volumétrie et Performance

### 8.1 Estimation de Volumétrie

| Table | Records estimés | Croissance |
|-------|-----------------|------------|
| `users` | ~1 000 | +50/mois |
| `stores` | ~10-50 | +2/an |
| `products` | ~500 | +20/mois |
| `product_variants` | ~2 000 | +80/mois |
| `product_options` | ~100 | +5/mois |
| `product_option_values` | ~300 | +15/mois |
| `store_product_variant` | ~10 000 | +500/mois |
| `stored_files` | ~5 000 | +200/mois |

### 8.2 Requêtes Critiques

```sql
-- Liste des produits actifs avec pagination
SELECT * FROM products
WHERE is_active = 1 AND deleted_at IS NULL
ORDER BY created_at DESC
LIMIT 15 OFFSET 0;

-- Produits d'une catégorie
SELECT * FROM products
WHERE product_category_id = ?
AND is_active = 1
AND deleted_at IS NULL;

-- Stock d'une variante par store
SELECT * FROM store_product_variant
WHERE store_id = ?
AND is_available = 1
AND stock > 0;

-- Produits d'un store avec variantes
SELECT p.*, pv.*, spv.stock
FROM products p
JOIN product_store ps ON p.id = ps.product_id
JOIN product_variants pv ON p.id = pv.product_id
LEFT JOIN store_product_variant spv ON pv.id = spv.product_variant_id
WHERE ps.store_id = ?
AND p.is_active = 1;
```

### 8.3 Plan de Maintenance

| Tâche | Fréquence | Commande |
|-------|-----------|----------|
| **Backup complet** | Quotidien | `mysqldump` |
| **Optimisation tables** | Hebdomadaire | `OPTIMIZE TABLE` |
| **Analyse des index** | Mensuel | `ANALYZE TABLE` |
| **Purge soft deletes** | Trimestriel | Script Laravel |
| **Archivage logs** | Mensuel | Rotation |

### 8.4 Configuration Recommandée

```ini
# my.cnf - MySQL Configuration

[mysqld]
# Buffer Pool (70-80% de la RAM disponible)
innodb_buffer_pool_size = 1G

# Taille des logs
innodb_log_file_size = 256M

# Connexions
max_connections = 100

# Cache de requêtes
query_cache_type = 1
query_cache_size = 64M

# Timeouts
wait_timeout = 600
interactive_timeout = 600
```

---

## Migrations Laravel

### Liste des Migrations

| Migration | Description |
|-----------|-------------|
| `create_users_table` | Table des utilisateurs |
| `create_cache_table` | Cache Laravel |
| `create_jobs_table` | Queue jobs |
| `create_stored_files_table` | Fichiers uploadés |
| `create_personal_access_tokens_table` | Tokens Sanctum |
| `create_permission_tables` | Tables Spatie Permission |
| `add_soft_deletes_to_users_table` | Soft delete users |
| `create_addresses_table` | Adresses |
| `create_stores_table` | Magasins |
| `create_store_user_table` | Pivot store-user |
| `create_product_categories_table` | Catégories |
| `create_products_table` | Produits |
| `create_product_gallery_table` | Galerie produits |
| `create_product_variants_table` | Variantes |
| `create_product_variant_gallery_table` | Galerie variantes |
| `create_product_options_table` | Options |
| `create_product_option_values_table` | Valeurs options |
| `create_variant_option_values_table` | Pivot variante-options |
| `create_product_store_table` | Pivot produit-store |
| `create_store_product_variant_table` | Stock par store |

### Commandes

```bash
# Exécuter les migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh (drop all + migrate)
php artisan migrate:fresh

# Avec seeds
php artisan migrate:fresh --seed
```

---
