# Enumerations Store

## StoreStatusEnum

Définit les statuts possibles pour un magasin.

### Emplacement

`app/Domain/Store/Enumeration/StoreStatusEnum.php`

### Namespace

`App\Domain\Store\Enumeration`

### Valeurs

| Case | Valeur | Description |
|------|--------|-------------|
| ACTIVE | `'active'` | Magasin actif, visible aux utilisateurs associés |
| DRAFT | `'draft'` | Magasin en brouillon, non visible |
| UNPUBLISH | `'unpublish'` | Magasin dépublié, accès restreint |

### Méthodes utilitaires

#### label()

Retourne un libellé lisible pour l'utilisateur.

```php
StoreStatusEnum::ACTIVE->label();    // 'Actif'
StoreStatusEnum::DRAFT->label();     // 'Brouillon'
StoreStatusEnum::UNPUBLISH->label(); // 'Non publié'
```

#### isVisible()

Vérifie si le magasin est visible aux utilisateurs non-admin.

```php
StoreStatusEnum::ACTIVE->isVisible();    // true
StoreStatusEnum::DRAFT->isVisible();     // false
StoreStatusEnum::UNPUBLISH->isVisible(); // false
```

#### isDraft()

Vérifie si le statut est brouillon.

```php
StoreStatusEnum::DRAFT->isDraft(); // true
```

#### isUnpublished()

Vérifie si le statut est non publié.

```php
StoreStatusEnum::UNPUBLISH->isUnpublished(); // true
```

#### values()

Retourne toutes les valeurs sous forme de tableau.

```php
StoreStatusEnum::values(); // ['active', 'draft', 'unpublish']
```

### Utilisation dans le Model

```php
// Dans Store.php
protected function casts(): array
{
    return [
        'status' => StoreStatusEnum::class,
    ];
}

// Utilisation
$store = Store::find(1);
$store->status;                         // StoreStatusEnum::ACTIVE
$store->status->value;                  // 'active'
$store->status === StoreStatusEnum::ACTIVE; // true
$store->status->label();                // 'Actif'
$store->status->isVisible();            // true
```

### Utilisation dans les Form Requests

```php
use App\Domain\Store\Enumeration\StoreStatusEnum;
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'status' => [
            'sometimes',
            'string',
            Rule::enum(StoreStatusEnum::class),
        ],
    ];
}
```

### Utilisation dans les DTOs

```php
use App\Domain\Store\Enumeration\StoreStatusEnum;

// Création depuis une string
$status = StoreStatusEnum::from('active'); // StoreStatusEnum::ACTIVE

// Création sécurisée (retourne null si invalide)
$status = StoreStatusEnum::tryFrom('invalid'); // null

// Dans le DTO
$dto = CreateStoreInputDTO::fromArray([
    'name' => 'Mon Café',
    'status' => 'active', // Converti en StoreStatusEnum::ACTIVE
]);
```

### Règles de visibilité par statut

| Statut | Admin | Manager/Employer associé | Manager/Employer non associé |
|--------|-------|--------------------------|------------------------------|
| ACTIVE | ✅ | ✅ | ❌ |
| DRAFT | ✅ | ❌ | ❌ |
| UNPUBLISH | ✅ | ❌ | ❌ |

### Cycle de vie d'un magasin

```
DRAFT (création)
   │
   ▼
ACTIVE (publication)
   │
   ▼
UNPUBLISH (dépublication)
   │
   ▼
ACTIVE (republication possible)
```

### Implémentation complète

```php
<?php

namespace App\Domain\Store\Enumeration;

enum StoreStatusEnum: string
{
    case ACTIVE = 'active';
    case DRAFT = 'draft';
    case UNPUBLISH = 'unpublish';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::DRAFT => 'Brouillon',
            self::UNPUBLISH => 'Non publié',
        };
    }

    public function isVisible(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this === self::DRAFT;
    }

    public function isUnpublished(): bool
    {
        return $this === self::UNPUBLISH;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
```
