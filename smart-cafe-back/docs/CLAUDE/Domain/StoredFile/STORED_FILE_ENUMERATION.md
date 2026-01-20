# Enumerations StoredFile

## DiskEnum

Définit les disques de stockage disponibles pour les fichiers.

### Emplacement

`app/Domain/StoredFile/Enumeration/DiskEnum.php`

### Namespace

`App\Domain\StoredFile\Enumeration`

### Valeurs

| Case   | Valeur   | Description                    |
|--------|----------|--------------------------------|
| LOCAL  | 'local'  | Stockage local privé           |
| PUBLIC | 'public' | Stockage public local          |
| S3     | 's3'     | Amazon S3 (cloud)              |

### Correspondance avec filesystems.php

```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app/private'),
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'visibility' => 'public',
    ],
    's3' => [
        'driver' => 's3',
        // AWS configuration...
    ],
],
```

### Méthodes utilitaires

#### label()

Retourne un libellé lisible pour l'utilisateur.

```php
DiskEnum::LOCAL->label();  // 'Stockage local privé'
DiskEnum::PUBLIC->label(); // 'Stockage public'
DiskEnum::S3->label();     // 'Amazon S3'
```

#### isPublic()

Vérifie si le disque est accessible publiquement.

```php
DiskEnum::LOCAL->isPublic();  // false
DiskEnum::PUBLIC->isPublic(); // true
DiskEnum::S3->isPublic();     // true
```

#### isLocal()

Vérifie si le disque est un stockage local (non cloud).

```php
DiskEnum::LOCAL->isLocal();  // true
DiskEnum::PUBLIC->isLocal(); // true
DiskEnum::S3->isLocal();     // false
```

#### basePath()

Retourne le chemin de base du disque (vide pour S3).

```php
DiskEnum::LOCAL->basePath();  // '/path/to/storage/app/private'
DiskEnum::PUBLIC->basePath(); // '/path/to/storage/app/public'
DiskEnum::S3->basePath();     // ''
```

### Utilisation dans le Model

```php
// Dans StoredFile.php
protected function casts(): array
{
    return [
        'disk' => DiskEnum::class,
    ];
}

// Utilisation
$file = StoredFile::find(1);
$file->disk;                    // DiskEnum::PUBLIC
$file->disk->value;             // 'public'
$file->disk === DiskEnum::PUBLIC; // true
$file->disk->label();           // 'Stockage public'
$file->disk->isPublic();        // true
```

### Utilisation dans les Services

```php
use App\Domain\StoredFile\Enumeration\DiskEnum;
use App\Domain\StoredFile\DTOs\CreateStoredFileInputDTO;

// Avec l'enum directement
$input = new CreateStoredFileInputDTO(
    file: $uploadedFile,
    userId: $userId,
    disk: DiskEnum::PUBLIC
);

// Depuis une string (formulaire)
$disk = DiskEnum::from($request->input('disk')); // 'public' → DiskEnum::PUBLIC

// Avec tryFrom (null si invalide)
$disk = DiskEnum::tryFrom($value); // null si valeur invalide
```

### Validation dans les Form Requests

```php
use App\Domain\StoredFile\Enumeration\DiskEnum;
use Illuminate\Validation\Rule;

public function rules(): array
{
    return [
        'disk' => [
            'required',
            Rule::enum(DiskEnum::class),
        ],
    ];
}
```

### Choix du disque selon le contexte

```php
// Fichiers privés (documents sensibles)
$disk = DiskEnum::LOCAL;

// Fichiers publics (avatars, images produits)
$disk = DiskEnum::PUBLIC;

// Fichiers volumineux ou distribués
$disk = DiskEnum::S3;
```
