# Services StoredFile

## CreateStoredFileService

### Responsabilité

Gère l'upload et le stockage des fichiers avec déduplication automatique basée sur le hash SHA-256.

### Emplacement

`app/Domain/StoredFile/Services/CreateStoredFileService.php`

### Namespace

`App\Domain\StoredFile\Services`

### Signature

```php
public function execute(CreateStoredFileInputDTO $input): StoredFile
```

### Logique de déduplication

1. Calcul du hash SHA-256 du fichier uploadé
2. Recherche d'un fichier existant avec le même hash (non soft-deleted)
3. Si trouvé : création d'un nouvel enregistrement pointant vers le fichier existant
4. Si non trouvé : stockage du fichier sur le disque + création de l'enregistrement

### Exemple d'utilisation

```php
<?php

namespace App\Http\Controllers;

use App\Domain\StoredFile\Services\CreateStoredFileService;
use App\Domain\StoredFile\DTOs\CreateStoredFileInputDTO;
use App\Domain\StoredFile\Enumeration\DiskEnum;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Http\Resources\StoredFileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function __construct(
        private readonly CreateStoredFileService $createService,
        private readonly ApiResponseSuccessService $successResponse
    ) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:51200'], // 50MB
        ]);

        $input = new CreateStoredFileInputDTO(
            file: $request->file('file'),
            userId: auth()->id(),
            disk: DiskEnum::PUBLIC,
            directory: 'uploads/documents'
        );

        $storedFile = $this->createService->execute($input);

        return $this->successResponse->execute(
            message: 'Fichier uploadé avec succès',
            data: new StoredFileResource($storedFile),
            statusCode: 201
        );
    }
}
```

### Utilisation avec le DTO fromArray

```php
$input = CreateStoredFileInputDTO::fromArray([
    'file' => $request->file('file'),
    'user_id' => auth()->id(),
    'disk' => 'public',
    'directory' => 'avatars',
]);

$storedFile = $createService->execute($input);
```

---

## DeleteStoredFileService

### Responsabilité

Gère la suppression des fichiers avec logique conditionnelle basée sur le partage de hash.

### Emplacement

`app/Domain/StoredFile/Services/DeleteStoredFileService.php`

### Namespace

`App\Domain\StoredFile\Services`

### Signatures

```php
// Suppression standard (soft delete + suppression physique si dernier)
public function execute(StoredFile $storedFile): bool

// Suppression forcée (hard delete + suppression physique si dernier)
public function forceExecute(StoredFile $storedFile): bool
```

### Logique de suppression

#### Méthode `execute()` (soft delete)

1. Compte les autres enregistrements actifs avec le même hash
2. Si count > 0 : soft delete uniquement de l'enregistrement
3. Si count == 0 : soft delete de l'enregistrement + suppression physique du fichier

#### Méthode `forceExecute()` (hard delete)

1. Hard delete de l'enregistrement
2. Compte les enregistrements restants avec le même hash (incluant soft deleted)
3. Si count == 0 : suppression physique du fichier

### Exemple d'utilisation

```php
<?php

namespace App\Http\Controllers;

use App\Domain\StoredFile\Services\DeleteStoredFileService;
use App\Domain\ApiResponse\Services\ApiResponseSuccessService;
use App\Models\StoredFile;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    public function __construct(
        private readonly DeleteStoredFileService $deleteService,
        private readonly ApiResponseSuccessService $successResponse
    ) {}

    public function destroy(StoredFile $storedFile): JsonResponse
    {
        $this->deleteService->execute($storedFile);

        return $this->successResponse->execute(
            message: 'Fichier supprimé'
        );
    }

    public function forceDestroy(StoredFile $storedFile): JsonResponse
    {
        $this->deleteService->forceExecute($storedFile);

        return $this->successResponse->execute(
            message: 'Fichier définitivement supprimé'
        );
    }
}
```

---

## Scénarios de déduplication

### Scénario 1 : Upload d'un nouveau fichier

```
User A upload "photo.jpg" (hash: abc123)
→ Fichier stocké sur disque
→ Enregistrement créé (id=1, hash=abc123)
```

### Scénario 2 : Upload d'un fichier dupliqué

```
User B upload "image.jpg" (même contenu, hash: abc123)
→ Fichier NON stocké (déjà présent)
→ Enregistrement créé (id=2, hash=abc123, path=même que id=1)
```

### Scénario 3 : Suppression avec autres références

```
Suppression de l'enregistrement id=2
→ count(hash=abc123) = 1 (id=1 existe encore)
→ Soft delete de id=2 uniquement
→ Fichier physique conservé
```

### Scénario 4 : Suppression du dernier

```
Suppression de l'enregistrement id=1
→ count(hash=abc123) = 0 (plus aucun actif)
→ Soft delete de id=1
→ Fichier physique supprimé
```
