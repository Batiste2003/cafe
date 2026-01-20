# Services Store

## CreateStoreService

### Responsabilité

Crée un nouveau magasin avec ses données initiales.

### Emplacement

`app/Domain/Store/Services/CreateStoreService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(CreateStoreInputDTO $dto, User $creator): Store
```

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\DTOs\CreateStoreInputDTO;
use App\Domain\Store\Services\CreateStoreService;
use App\Domain\Store\Enumeration\StoreStatusEnum;

$dto = CreateStoreInputDTO::fromArray([
    'name' => 'Mon Nouveau Café',
    'banner_stored_file_id' => 1,
    'logo_stored_file_id' => 2,
    'address_id' => 1,
    'status' => StoreStatusEnum::DRAFT->value,
]);

$service = new CreateStoreService();
$store = $service->execute($dto, $admin);
```

---

## UpdateStoreService

### Responsabilité

Met à jour un magasin existant avec possibilité de supprimer les fichiers/adresse.

### Emplacement

`app/Domain/Store/Services/UpdateStoreService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(Store $store, UpdateStoreInputDTO $dto): Store
```

### Fonctionnalités

- Mise à jour partielle (seuls les champs fournis sont modifiés)
- Support des flags `remove_banner`, `remove_logo`, `remove_address` pour supprimer les relations

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\DTOs\UpdateStoreInputDTO;
use App\Domain\Store\Services\UpdateStoreService;

// Mise à jour du nom et du statut
$dto = UpdateStoreInputDTO::fromArray([
    'name' => 'Nouveau Nom',
    'status' => 'active',
]);

$service = new UpdateStoreService();
$updatedStore = $service->execute($store, $dto);

// Suppression de la bannière
$dto = UpdateStoreInputDTO::fromArray([
    'remove_banner' => true,
]);

$updatedStore = $service->execute($store, $dto);
```

---

## DeleteStoreService

### Responsabilité

Supprime un magasin (soft delete).

### Emplacement

`app/Domain/Store/Services/DeleteStoreService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(Store $store): bool
```

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\Services\DeleteStoreService;

$service = new DeleteStoreService();
$success = $service->execute($store);
```

---

## ListStoresService

### Responsabilité

Liste les magasins avec filtres, pagination et contrôle d'accès.

### Emplacement

`app/Domain/Store/Services/ListStoresService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(ListStoresFilterDTO $filters, ?User $user = null): LengthAwarePaginator
```

### Paramètres

| Paramètre | Description |
|-----------|-------------|
| `$filters` | Critères de filtrage (search, status, withTrashed, perPage) |
| `$user` | Si fourni, limite aux stores accessibles par cet utilisateur |

### Logique d'accès

- Si `$user` est `null` : retourne tous les stores (mode admin)
- Si `$user` est Admin : retourne tous les stores
- Si `$user` est Manager/Employer : retourne uniquement les stores actifs associés

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\DTOs\ListStoresFilterDTO;
use App\Domain\Store\Services\ListStoresService;

// Liste admin (tous les stores)
$filters = ListStoresFilterDTO::fromArray([
    'search' => 'café',
    'status' => 'active',
    'per_page' => 20,
]);

$service = new ListStoresService();
$stores = $service->execute($filters);

// Liste pour un utilisateur spécifique
$stores = $service->execute($filters, $currentUser);
```

---

## GetStoreService

### Responsabilité

Récupère un magasin avec toutes ses relations chargées.

### Emplacement

`app/Domain/Store/Services/GetStoreService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(Store $store): Store
```

### Relations chargées

- `banner` (StoredFile)
- `logo` (StoredFile)
- `address` (Address)
- `creator` (User)
- `users.roles` (Users avec leurs rôles)

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\Services\GetStoreService;

$service = new GetStoreService();
$storeWithRelations = $service->execute($store);
```

---

## AttachUsersToStoreService

### Responsabilité

Associe des utilisateurs à un magasin en respectant les règles métier.

### Emplacement

`app/Domain/Store/Services/AttachUsersToStoreService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(Store $store, AttachUsersToStoreDTO $dto): Store
```

### Règles métier

1. **Admin interdit** : Un administrateur ne peut pas être associé à un magasin
2. **Employer unique** : Un employé ne peut être associé qu'à un seul magasin
3. **Manager multiple** : Un manager peut être associé à plusieurs magasins
4. **Pas de doublons** : Utilise `syncWithoutDetaching` pour éviter les duplications

### Exceptions

| Exception | Condition |
|-----------|-----------|
| `ValidationException` | Tentative d'associer un admin |
| `ValidationException` | Employer déjà associé à un autre store |

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\DTOs\AttachUsersToStoreDTO;
use App\Domain\Store\Services\AttachUsersToStoreService;

$dto = AttachUsersToStoreDTO::fromArray([
    'user_ids' => [2, 3, 4],
]);

$service = new AttachUsersToStoreService();

try {
    $store = $service->execute($store, $dto);
} catch (ValidationException $e) {
    // Gérer l'erreur (admin ou employer déjà associé)
}
```

---

## DetachUserFromStoreService

### Responsabilité

Dissocie un utilisateur d'un magasin.

### Emplacement

`app/Domain/Store/Services/DetachUserFromStoreService.php`

### Namespace

`App\Domain\Store\Services`

### Signature

```php
public function execute(Store $store, User $user): Store
```

### Exceptions

| Exception | Condition |
|-----------|-----------|
| `ValidationException` | L'utilisateur n'est pas associé au magasin |

### Exemple d'utilisation

```php
<?php

use App\Domain\Store\Services\DetachUserFromStoreService;

$service = new DetachUserFromStoreService();

try {
    $store = $service->execute($store, $user);
} catch (ValidationException $e) {
    // L'utilisateur n'était pas associé
}
```

---

## Récapitulatif des Services

| Service | Action | Retour |
|---------|--------|--------|
| CreateStoreService | Création | Store |
| UpdateStoreService | Mise à jour | Store |
| DeleteStoreService | Suppression | bool |
| ListStoresService | Liste paginée | LengthAwarePaginator |
| GetStoreService | Récupération | Store |
| AttachUsersToStoreService | Association users | Store |
| DetachUserFromStoreService | Dissociation user | Store |
