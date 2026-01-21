# Domain User

## Structure

```
app/Domain/User/
├── Constant/
│   └── UserConstant.php          # Constantes (pagination, messages)
├── DTOs/
│   ├── CreateUserInputDTO.php    # DTO création utilisateur
│   ├── UpdateUserInputDTO.php    # DTO mise à jour utilisateur
│   └── ListUsersFilterDTO.php    # DTO filtres liste utilisateurs
├── Enumeration/
│   ├── UserRoleEnum.php          # Rôles (Admin, Manager, Employer)
│   └── UserPermissionEnum.php    # Permissions
└── Services/
    ├── CreateUserService.php     # Création utilisateur
    ├── UpdateUserService.php     # Mise à jour utilisateur
    ├── DeleteUserService.php     # Suppression (soft delete)
    ├── RestoreUserService.php    # Restauration utilisateur
    ├── ListUsersService.php      # Liste paginée avec filtres
    └── GetUserService.php        # Récupération par ID
```

## Rôles et Permissions

### Rôles

| Rôle | Valeur | Description |
|------|--------|-------------|
| Admin | `admin` | Administrateur - Toutes les permissions |
| Manager | `manager` | Manager - Gestion des commandes |
| Employer | `employer` | Employé - Consultation des commandes |

### Permissions par Rôle

#### Admin
Toutes les permissions du système.

#### Manager
- `order.view` - Voir les commandes
- `order.cancel` - Annuler des commandes
- `order.refund` - Rembourser des commandes

#### Employer
- `order.view` - Voir les commandes

### Liste complète des permissions

| Domaine | Permission | Description |
|---------|------------|-------------|
| Product | `product.view` | Voir les produits |
| Product | `product.create` | Créer des produits |
| Product | `product.update` | Modifier des produits |
| Product | `product.delete` | Supprimer des produits |
| Variant | `variant.view` | Voir les variantes |
| Variant | `variant.create` | Créer des variantes |
| Variant | `variant.update` | Modifier des variantes |
| Variant | `variant.delete` | Supprimer des variantes |
| Variant Option | `variant_option.view` | Voir les options de variantes |
| Variant Option | `variant_option.create` | Créer des options de variantes |
| Variant Option | `variant_option.update` | Modifier des options de variantes |
| Variant Option | `variant_option.delete` | Supprimer des options de variantes |
| Store | `store.view` | Voir les magasins |
| Store | `store.create` | Créer des magasins |
| Store | `store.update` | Modifier des magasins |
| Store | `store.delete` | Supprimer des magasins |
| Store | `store.product.attach` | Ajouter des produits aux magasins |
| Store | `store.product.detach` | Retirer des produits des magasins |
| Order | `order.view` | Voir les commandes |
| Order | `order.create` | Créer des commandes |
| Order | `order.update` | Modifier des commandes |
| Order | `order.delete` | Supprimer des commandes |
| Order | `order.cancel` | Annuler des commandes |
| Order | `order.refund` | Rembourser des commandes |
| User | `user.view` | Voir les utilisateurs |
| User | `user.create` | Créer des utilisateurs |
| User | `user.update` | Modifier des utilisateurs |
| User | `user.delete` | Supprimer des utilisateurs |

## Règles Métier

### Création d'utilisateur
- Un Admin peut créer des utilisateurs avec le rôle **Manager** ou **Employer**
- Il ne peut pas créer de nouveaux Admin

### Suppression d'utilisateur
- Un utilisateur ne peut pas se supprimer lui-même
- Un utilisateur ne peut pas supprimer un Admin
- La suppression est en **soft delete** (restauration possible)
- Supprimer un utilisateur inexistant retourne une erreur 404 : `Ressource User non trouvée.`

### Restauration d'utilisateur
- Nécessite la permission `user.update`

## Endpoints API

> **Note:** Les routes utilisateurs sont sous le préfixe `/api/admin/users/` et nécessitent le rôle Admin.

### Liste des utilisateurs
```
GET /api/admin/users
```

**Paramètres de requête:**
| Paramètre | Type | Description |
|-----------|------|-------------|
| `search` | string | Recherche par nom ou email |
| `role` | string | Filtrer par rôle (admin, manager, employer) |
| `with_trashed` | boolean | Inclure les utilisateurs supprimés |
| `per_page` | integer | Nombre d'éléments par page (max: 100) |

**Réponse:**
```json
{
  "success": true,
  "message": "Liste des utilisateurs récupérée avec succès.",
  "data": [
    {
      "id": 1,
      "name": "Admin",
      "email": "admin@cafe.local",
      "email_verified_at": "2026-01-20 10:00:00",
      "roles": ["admin"],
      "is_deleted": false,
      "created_at": "2026-01-20 10:00:00",
      "updated_at": "2026-01-20 10:00:00"
    }
  ],
  "paging": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  }
}
```

### Créer un utilisateur
```
POST /api/admin/users
```

**Corps de la requête:**
```json
{
  "name": "John Doe",
  "email": "john@cafe.local",
  "password": "Password123!",
  "role": "employer"  // ou "manager"
}
```

**Réponse (201):**
```json
{
  "success": true,
  "message": "Utilisateur créé avec succès.",
  "data": {
    "id": 2,
    "name": "John Doe",
    "email": "john@cafe.local",
    "email_verified_at": null,
    "roles": ["employer"],
    "is_deleted": false,
    "created_at": "2026-01-20 10:00:00",
    "updated_at": "2026-01-20 10:00:00"
  }
}
```

### Récupérer un utilisateur
```
GET /api/admin/users/{id}
```

### Mettre à jour un utilisateur
```
PUT /api/admin/users/{id}
```

**Corps de la requête:**
```json
{
  "name": "John Updated",
  "email": "john.updated@cafe.local",
  "password": "NewPassword123!"
}
```

### Supprimer un utilisateur
```
DELETE /api/admin/users/{id}
```

**Erreurs possibles:**
- `403` - Impossible de supprimer votre propre compte
- `403` - Impossible de supprimer un administrateur
- `404` - Ressource User non trouvée

### Restaurer un utilisateur
```
POST /api/admin/users/{id}/restore
```

## Seeders

### RoleAndPermissionSeeder
Crée tous les rôles et permissions dans la base de données.

### UserSeeder
Crée un utilisateur par rôle:

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Admin | `admin@cafe.local` | `AdminPassword123!` |
| Manager | `manager@cafe.local` | `ManagerPassword123!` |
| Employer | `employer@cafe.local` | `EmployerPassword123!` |

### Exécution
```bash
php artisan db:seed
```

Ou pour exécuter uniquement les seeders User:
```bash
php artisan db:seed --class=RoleAndPermissionSeeder
php artisan db:seed --class=UserSeeder
```

## Utilisation dans le Code

### Vérifier le rôle d'un utilisateur
```php
$user = User::find(1);

if ($user->isAdmin()) {
    // L'utilisateur est admin
}

if ($user->hasRole('manager')) {
    // L'utilisateur est manager
}
```

### Vérifier une permission
```php
if ($user->hasPermissionTo('order.view')) {
    // L'utilisateur peut voir les commandes
}

// Ou via Gate
Gate::authorize('viewAny', User::class);
```

### Créer un utilisateur via Service
```php
use App\Domain\User\DTOs\CreateUserInputDTO;
use App\Domain\User\Services\CreateUserService;

$dto = CreateUserInputDTO::fromArray([
    'name' => 'New Employee',
    'email' => 'employee@cafe.local',
    'password' => 'Password123!',
    'role' => 'employer',
]);

$service = new CreateUserService();
$user = $service->execute($dto);
```

### Lister les utilisateurs avec filtres
```php
use App\Domain\User\DTOs\ListUsersFilterDTO;
use App\Domain\User\Services\ListUsersService;

$filters = ListUsersFilterDTO::fromArray([
    'search' => 'john',
    'role' => 'employer',
    'with_trashed' => false,
    'per_page' => 20,
]);

$service = new ListUsersService();
$users = $service->execute($filters);
```

## Middleware Spatie Permission

Les middlewares suivants sont disponibles:

```php
// Vérifier un rôle
Route::middleware(['role:admin'])->group(function () {
    // Routes accessibles uniquement aux admins
});

// Vérifier une permission
Route::middleware(['permission:user.create'])->group(function () {
    // Routes nécessitant la permission user.create
});

// Vérifier un rôle OU une permission
Route::middleware(['role_or_permission:admin|user.view'])->group(function () {
    // Routes accessibles aux admins OU ayant la permission user.view
});
```
