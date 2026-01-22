# 07 - Référence API

> Documentation complète des endpoints REST de l'API Smart Cafe

---

## Table des matières

1. [Introduction](#1-introduction)
2. [Authentification](#2-authentification)
3. [Format des Réponses](#3-format-des-réponses)
4. [Endpoints Publics](#4-endpoints-publics)
5. [Endpoints Authentifiés](#5-endpoints-authentifiés)
6. [Endpoints Admin](#6-endpoints-admin)
7. [Codes d'Erreur](#7-codes-derreur)
8. [Pagination](#8-pagination)

---

## 1. Introduction

### 1.1 Base URL

| Environnement | URL |
|---------------|-----|
| **Développement** | `http://localhost:8000/api` |
| **Production** | `https://api.smartcafe.com/api` |

### 1.2 Versioning

L'API utilise actuellement la version 1 (v1). Le versioning est implicite dans la base URL.

### 1.3 Headers Requis

```http
Accept: application/json
Content-Type: application/json
```

Pour les endpoints authentifiés :
```http
Authorization: Bearer {token}
```

---

## 2. Authentification

### 2.1 POST /auth/login

Authentifie un utilisateur et retourne un token.

**Request:**
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Connexion réussie",
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "email_verified_at": "2026-01-20T10:00:00.000000Z",
      "roles": ["admin"]
    },
    "token": "1|abc123def456..."
  }
}
```

**Errors:**
- `401` - Identifiants invalides
- `422` - Validation échouée

---

### 2.2 POST /auth/register

Crée un nouveau compte utilisateur.

**Request:**
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Inscription réussie",
  "data": {
    "user": {
      "id": 2,
      "name": "John Doe",
      "email": "john@example.com"
    },
    "token": "2|xyz789..."
  }
}
```

---

### 2.3 POST /auth/logout

Déconnecte l'utilisateur et invalide le token.

**Request:**
```http
POST /api/auth/logout
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Déconnexion réussie"
}
```

---

### 2.4 GET /auth/me

Récupère l'utilisateur actuellement authentifié.

**Request:**
```http
GET /api/auth/me
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "email_verified_at": "2026-01-20T10:00:00.000000Z",
      "roles": ["admin"],
      "permissions": ["users.view", "users.create", ...]
    }
  }
}
```

---

## 3. Format des Réponses

### 3.1 Réponse de Succès

```json
{
  "success": true,
  "message": "Message optionnel",
  "data": {
    // Données de la ressource
  }
}
```

### 3.2 Réponse avec Pagination

```json
{
  "success": true,
  "data": [
    // Array de ressources
  ],
  "meta": {
    "current_page": 1,
    "last_page": 10,
    "per_page": 15,
    "total": 150,
    "from": 1,
    "to": 15
  },
  "links": {
    "first": "http://localhost:8000/api/products?page=1",
    "last": "http://localhost:8000/api/products?page=10",
    "prev": null,
    "next": "http://localhost:8000/api/products?page=2"
  }
}
```

### 3.3 Réponse d'Erreur

```json
{
  "success": false,
  "error": "Message d'erreur principal",
  "errors": {
    "field_name": [
      "Message d'erreur pour ce champ"
    ]
  }
}
```

---

## 4. Endpoints Publics

Ces endpoints sont accessibles sans authentification.

### 4.1 GET /products (Public)

Liste les produits actifs.

**Request:**
```http
GET /api/products?page=1&per_page=15&category_id=1&is_featured=1
```

**Query Parameters:**
| Paramètre | Type | Description |
|-----------|------|-------------|
| `page` | integer | Page courante (défaut: 1) |
| `per_page` | integer | Éléments par page (défaut: 15) |
| `category_id` | integer | Filtrer par catégorie |
| `search` | string | Recherche par nom |
| `is_featured` | boolean | Produits mis en avant |
| `is_active` | boolean | Produits actifs |

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Espresso",
      "slug": "espresso",
      "description": "Café italien traditionnel",
      "is_active": true,
      "is_featured": true,
      "category": {
        "id": 1,
        "name": "Cafés",
        "slug": "cafes"
      },
      "variants": [
        {
          "id": 1,
          "name": "Simple",
          "sku": "ESP-001",
          "price": "2.50"
        }
      ]
    }
  ],
  "meta": {
    "current_page": 1,
    "total": 50
  }
}
```

---

### 4.2 GET /products/{id} (Public)

Récupère un produit par son ID.

**Request:**
```http
GET /api/products/1
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Espresso",
    "slug": "espresso",
    "description": "Café italien traditionnel...",
    "is_active": true,
    "is_featured": true,
    "created_at": "2026-01-20T10:00:00.000000Z",
    "category": {
      "id": 1,
      "name": "Cafés"
    },
    "variants": [...],
    "options": [...],
    "gallery": [...]
  }
}
```

---

### 4.3 GET /product-categories

Liste les catégories de produits.

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Cafés",
      "slug": "cafes",
      "description": "Tous nos cafés",
      "parent_id": null,
      "children": [
        {
          "id": 2,
          "name": "Espressos",
          "slug": "espressos",
          "parent_id": 1
        }
      ]
    }
  ]
}
```

---

## 5. Endpoints Authentifiés

Ces endpoints nécessitent un token d'authentification.

### 5.1 GET /stores (Authenticated)

Liste les magasins accessibles à l'utilisateur.

**Request:**
```http
GET /api/stores
Authorization: Bearer {token}
```

**Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Smart Cafe - Centre Ville",
      "status": "active",
      "address": {
        "street": "123 Rue Principale",
        "city": "Paris",
        "zip_code": "75001"
      },
      "banner_url": "https://...",
      "logo_url": "https://..."
    }
  ]
}
```

---

## 6. Endpoints Admin

Ces endpoints sont réservés aux utilisateurs avec le rôle `admin`.

### 6.1 Users

#### GET /admin/users
Liste tous les utilisateurs.

```http
GET /api/admin/users?page=1&per_page=15
Authorization: Bearer {token}
```

#### POST /admin/users
Crée un nouvel utilisateur.

```http
POST /api/admin/users
Authorization: Bearer {token}

{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "manager"
}
```

#### GET /admin/users/{id}
Récupère un utilisateur.

```http
GET /api/admin/users/1
Authorization: Bearer {token}
```

#### PUT /admin/users/{id}
Met à jour un utilisateur.

```http
PUT /api/admin/users/1
Authorization: Bearer {token}

{
  "name": "Jane Doe Updated",
  "email": "jane.updated@example.com"
}
```

#### DELETE /admin/users/{id}
Supprime un utilisateur (soft delete).

```http
DELETE /api/admin/users/1
Authorization: Bearer {token}
```

#### POST /admin/users/{id}/restore
Restaure un utilisateur supprimé.

```http
POST /api/admin/users/1/restore
Authorization: Bearer {token}
```

---

### 6.2 Stores

#### GET /admin/stores
Liste tous les magasins.

#### POST /admin/stores
Crée un nouveau magasin.

```http
POST /api/admin/stores
Authorization: Bearer {token}

{
  "name": "Smart Cafe - Nouveau",
  "status": "draft",
  "address": {
    "street": "456 Rue Nouvelle",
    "city": "Lyon",
    "zip_code": "69001",
    "country": "France"
  }
}
```

#### GET /admin/stores/{id}
Récupère un magasin.

#### PUT /admin/stores/{id}
Met à jour un magasin.

#### DELETE /admin/stores/{id}
Supprime un magasin.

#### POST /admin/stores/{id}/users
Attache des utilisateurs à un magasin.

```http
POST /api/admin/stores/1/users
Authorization: Bearer {token}

{
  "user_ids": [2, 3, 4]
}
```

#### DELETE /admin/stores/{id}/users/{userId}
Détache un utilisateur d'un magasin.

#### GET /admin/stores/{id}/products
Liste les produits d'un magasin.

#### GET /admin/stores/{id}/variant-stocks
Liste les stocks de variantes d'un magasin.

---

### 6.3 Products

#### GET /admin/products
Liste tous les produits (y compris inactifs).

#### POST /admin/products
Crée un nouveau produit.

```http
POST /api/admin/products
Authorization: Bearer {token}

{
  "name": "Latte",
  "description": "Café au lait onctueux",
  "product_category_id": 1,
  "is_active": true,
  "is_featured": false,
  "store_ids": [1, 2]
}
```

#### GET /admin/products/{id}
Récupère un produit avec toutes ses relations.

#### PUT /admin/products/{id}
Met à jour un produit.

#### DELETE /admin/products/{id}
Supprime un produit.

#### POST /admin/products/{id}/gallery
Ajoute des images à la galerie.

```http
POST /api/admin/products/1/gallery
Authorization: Bearer {token}
Content-Type: multipart/form-data

images[]: (file)
images[]: (file)
```

#### DELETE /admin/products/{id}/gallery/{fileId}
Supprime une image de la galerie.

---

### 6.4 Product Variants

#### GET /admin/products/{productId}/variants
Liste les variantes d'un produit.

#### POST /admin/products/{productId}/variants
Crée une variante.

```http
POST /api/admin/products/1/variants
Authorization: Bearer {token}

{
  "name": "Large",
  "sku": "LATTE-L",
  "price": 5.50,
  "is_active": true
}
```

#### GET /admin/products/{productId}/variants/{id}
Récupère une variante.

#### PUT /admin/products/{productId}/variants/{id}
Met à jour une variante.

#### DELETE /admin/products/{productId}/variants/{id}
Supprime une variante.

#### POST /admin/products/{productId}/variants/{id}/gallery
Ajoute des images à la variante.

#### DELETE /admin/products/{productId}/variants/{id}/gallery/{fileId}
Supprime une image de la variante.

---

### 6.5 Product Options

#### GET /admin/products/{productId}/options
Liste les options d'un produit.

#### POST /admin/products/{productId}/options
Crée une option.

```http
POST /api/admin/products/1/options
Authorization: Bearer {token}

{
  "name": "Type de lait",
  "is_required": false
}
```

#### PUT /admin/products/{productId}/options/{id}
Met à jour une option.

#### DELETE /admin/products/{productId}/options/{id}
Supprime une option.

---

### 6.6 Product Option Values

#### GET /admin/product-options/{optionId}/values
Liste les valeurs d'une option.

#### POST /admin/product-options/{optionId}/values
Crée une valeur d'option.

```http
POST /api/admin/product-options/1/values
Authorization: Bearer {token}

{
  "value": "Lait de soja",
  "extra_price": 0.50
}
```

#### PUT /admin/product-options/{optionId}/values/{id}
Met à jour une valeur.

#### DELETE /admin/product-options/{optionId}/values/{id}
Supprime une valeur.

---

### 6.7 Product-Store Associations

#### GET /admin/products/{productId}/stores
Liste les magasins associés à un produit.

#### POST /admin/products/{productId}/stores
Associe des magasins à un produit.

```http
POST /api/admin/products/1/stores
Authorization: Bearer {token}

{
  "store_ids": [1, 2, 3]
}
```

#### DELETE /admin/products/{productId}/stores/{storeId}
Dissocie un magasin d'un produit.

---

### 6.8 Variant Stocks

#### GET /admin/products/{productId}/variants/{variantId}/stocks
Liste les stocks d'une variante par magasin.

#### PUT /admin/products/{productId}/variants/{variantId}/stocks/{storeId}
Met à jour le stock d'une variante dans un magasin.

```http
PUT /api/admin/products/1/variants/1/stocks/1
Authorization: Bearer {token}

{
  "stock": 100,
  "price": 5.00,
  "is_available": true
}
```

#### DELETE /admin/products/{productId}/variants/{variantId}/stocks/{storeId}
Supprime le stock d'une variante dans un magasin.

---

### 6.9 Product Categories

#### GET /admin/product-categories
Liste toutes les catégories.

#### POST /admin/product-categories
Crée une catégorie.

```http
POST /api/admin/product-categories
Authorization: Bearer {token}

{
  "name": "Boissons Chaudes",
  "description": "Toutes nos boissons chaudes",
  "parent_id": null
}
```

#### GET /admin/product-categories/{id}
Récupère une catégorie.

#### PUT /admin/product-categories/{id}
Met à jour une catégorie.

#### DELETE /admin/product-categories/{id}
Supprime une catégorie.

---

### 6.10 Addresses

#### GET /admin/addresses
Liste toutes les adresses.

#### POST /admin/addresses
Crée une adresse.

```http
POST /api/admin/addresses
Authorization: Bearer {token}

{
  "street": "789 Boulevard Central",
  "street_additional": "Bâtiment A",
  "city": "Marseille",
  "state": "Bouches-du-Rhône",
  "zip_code": "13001",
  "country": "France"
}
```

#### GET /admin/addresses/{id}
Récupère une adresse.

#### PUT /admin/addresses/{id}
Met à jour une adresse.

#### DELETE /admin/addresses/{id}
Supprime une adresse.

---

## 7. Codes d'Erreur

### 7.1 Codes HTTP

| Code | Signification | Utilisation |
|------|---------------|-------------|
| `200` | OK | Requête réussie |
| `201` | Created | Ressource créée |
| `204` | No Content | Suppression réussie |
| `400` | Bad Request | Requête malformée |
| `401` | Unauthorized | Token manquant ou invalide |
| `403` | Forbidden | Accès refusé (permissions) |
| `404` | Not Found | Ressource non trouvée |
| `422` | Unprocessable Entity | Erreur de validation |
| `429` | Too Many Requests | Rate limit dépassé |
| `500` | Internal Server Error | Erreur serveur |

### 7.2 Exemples d'Erreurs

**Erreur de validation (422):**
```json
{
  "success": false,
  "error": "Les données fournies sont invalides.",
  "errors": {
    "email": [
      "Le champ email est obligatoire.",
      "Le champ email doit être une adresse email valide."
    ],
    "password": [
      "Le mot de passe doit contenir au moins 8 caractères."
    ]
  }
}
```

**Erreur d'authentification (401):**
```json
{
  "success": false,
  "error": "Token invalide ou expiré."
}
```

**Erreur d'autorisation (403):**
```json
{
  "success": false,
  "error": "Vous n'avez pas les permissions nécessaires pour effectuer cette action."
}
```

**Ressource non trouvée (404):**
```json
{
  "success": false,
  "error": "Ressource non trouvée."
}
```

---

## 8. Pagination

### 8.1 Paramètres de Pagination

| Paramètre | Type | Défaut | Description |
|-----------|------|--------|-------------|
| `page` | integer | 1 | Numéro de page |
| `per_page` | integer | 15 | Éléments par page (max: 100) |

### 8.2 Exemple de Requête Paginée

```http
GET /api/admin/products?page=2&per_page=20
```

### 8.3 Structure de la Réponse

```json
{
  "success": true,
  "data": [...],
  "meta": {
    "current_page": 2,
    "from": 21,
    "last_page": 5,
    "per_page": 20,
    "to": 40,
    "total": 100
  },
  "links": {
    "first": "http://localhost:8000/api/admin/products?page=1",
    "last": "http://localhost:8000/api/admin/products?page=5",
    "prev": "http://localhost:8000/api/admin/products?page=1",
    "next": "http://localhost:8000/api/admin/products?page=3"
  }
}
```

---

## Annexe: Tableau Récapitulatif des Endpoints

| Méthode | Endpoint | Auth | Rôle | Description |
|---------|----------|------|------|-------------|
| POST | `/auth/login` | Non | - | Connexion |
| POST | `/auth/register` | Non | - | Inscription |
| POST | `/auth/logout` | Oui | - | Déconnexion |
| GET | `/auth/me` | Oui | - | User courant |
| GET | `/products` | Oui | - | Liste produits |
| GET | `/products/{id}` | Oui | - | Détail produit |
| GET | `/product-categories` | Oui | - | Liste catégories |
| GET | `/stores` | Oui | - | Magasins accessibles |
| GET | `/admin/users` | Oui | Admin | Liste users |
| POST | `/admin/users` | Oui | Admin | Créer user |
| GET | `/admin/users/{id}` | Oui | Admin | Détail user |
| PUT | `/admin/users/{id}` | Oui | Admin | MAJ user |
| DELETE | `/admin/users/{id}` | Oui | Admin | Suppr user |
| POST | `/admin/users/{id}/restore` | Oui | Admin | Restaurer user |
| GET | `/admin/stores` | Oui | Admin | Liste stores |
| POST | `/admin/stores` | Oui | Admin | Créer store |
| ... | ... | ... | ... | ... |

---
