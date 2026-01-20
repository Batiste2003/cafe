# Services UploadedFile

## ProcessUploadedFileService

### Responsabilité

Transforme un fichier uploadé (UploadedFile) en enregistrement StoredFile en utilisant le service CreateStoredFileService du domain StoredFile.

### Emplacement

`app/Domain/UploadedFile/Services/ProcessUploadedFileService.php`

### Namespace

`App\Domain\UploadedFile\Services`

### Signature

```php
public function execute(ProcessUploadedFileInputDTO $dto): StoredFile
```

### Paramètres d'entrée (ProcessUploadedFileInputDTO)

| Propriété | Type | Requis | Description |
|-----------|------|--------|-------------|
| file | UploadedFile | Oui | Le fichier uploadé via HTTP |
| userId | int | Oui | ID de l'utilisateur propriétaire |
| disk | DiskEnum | Non | Disque de stockage (défaut: PUBLIC) |
| directory | string | Non | Répertoire de destination |

### Retour

Retourne une instance de `StoredFile` avec toutes ses propriétés renseignées :
- `id` : Identifiant unique
- `disk` : Disque de stockage utilisé
- `path` : Chemin relatif du fichier
- `hash` : Hash SHA-256 du fichier
- `original_name` : Nom original du fichier
- `mime_type` : Type MIME
- `size` : Taille en bytes
- `extension` : Extension du fichier
- `user_id` : ID du propriétaire

### Exemple d'utilisation

```php
<?php

use App\Domain\StoredFile\Enumeration\DiskEnum;
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Domain\UploadedFile\Services\ProcessUploadedFileService;

// Injection de dépendance
$service = app(ProcessUploadedFileService::class);

// Création du DTO
$dto = ProcessUploadedFileInputDTO::fromArray([
    'file' => $request->file('banner'),
    'user_id' => auth()->id(),
    'disk' => DiskEnum::PUBLIC->value,
    'directory' => UploadedFileConstant::STORE_BANNER_DIRECTORY,
]);

// Exécution
$storedFile = $service->execute($dto);

// Utilisation du résultat
echo $storedFile->id;           // 1
echo $storedFile->path;         // stores/banners/uuid.jpg
echo $storedFile->url;          // http://localhost/storage/stores/banners/uuid.jpg
```

### Déduplication

Le service hérite de la logique de déduplication de `CreateStoredFileService` :
- Si un fichier avec le même hash existe déjà, un nouvel enregistrement est créé pointant vers le même fichier physique
- Aucun fichier dupliqué n'est stocké sur le disque

### Gestion des erreurs

Le service peut lever des exceptions dans les cas suivants :
- Échec d'écriture sur le disque
- Erreur de transaction base de données

```php
use Illuminate\Support\Facades\DB;

try {
    $storedFile = $service->execute($dto);
} catch (\Exception $e) {
    // Gérer l'erreur
    Log::error('Upload failed: ' . $e->getMessage());
}
```

## Implémentation complète

```php
<?php

namespace App\Domain\UploadedFile\Services;

use App\Domain\StoredFile\DTOs\CreateStoredFileInputDTO;
use App\Domain\StoredFile\Services\CreateStoredFileService;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Models\StoredFile;

class ProcessUploadedFileService
{
    public function __construct(
        private readonly CreateStoredFileService $createStoredFileService
    ) {}

    /**
     * Process an uploaded file and create a StoredFile record.
     */
    public function execute(ProcessUploadedFileInputDTO $dto): StoredFile
    {
        $createDto = CreateStoredFileInputDTO::fromArray([
            'file' => $dto->file,
            'user_id' => $dto->userId,
            'disk' => $dto->disk->value,
            'directory' => $dto->directory,
        ]);

        return $this->createStoredFileService->execute($createDto);
    }
}
```
