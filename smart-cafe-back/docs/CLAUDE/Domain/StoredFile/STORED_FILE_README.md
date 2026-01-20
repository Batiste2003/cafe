# StoredFile Domain

## Vue d'ensemble

Le domaine StoredFile gère le stockage et la gestion des fichiers uploadés par les utilisateurs avec un système de déduplication basé sur le hash des fichiers.

## Structure

```
app/Domain/StoredFile/
├── Services/
│   ├── CreateStoredFileService.php    # Création avec déduplication
│   └── DeleteStoredFileService.php    # Suppression conditionnelle
├── Enumeration/
│   └── DiskEnum.php                   # Disques de stockage
├── Constant/
│   └── StoredFileConstant.php         # Constantes
└── DTOs/
    └── CreateStoredFileInputDTO.php   # DTO d'entrée
```

## Table stored_files

| Colonne       | Type                | Description                          |
|---------------|---------------------|--------------------------------------|
| id            | bigint              | Clé primaire                         |
| disk          | string              | Disque de stockage (local/public/s3) |
| path          | string              | Chemin relatif dans le disque        |
| full_path     | string              | Chemin complet du fichier            |
| hash          | string(64)          | Hash SHA-256 pour déduplication      |
| original_name | string              | Nom original du fichier              |
| mime_type     | string              | Type MIME                            |
| size          | bigint              | Taille en bytes                      |
| extension     | string(20)          | Extension du fichier                 |
| user_id       | foreignId           | Référence à l'utilisateur            |
| created_at    | timestamp           | Date de création                     |
| updated_at    | timestamp           | Date de modification                 |
| deleted_at    | timestamp (null)    | Soft delete                          |

## Fonctionnalités clés

### Déduplication par hash

Le système calcule un hash SHA-256 de chaque fichier uploadé. Si un fichier avec le même hash existe déjà :
- Aucun nouveau fichier n'est stocké sur le disque
- Un nouvel enregistrement est créé pointant vers le fichier existant
- Le nom original est conservé pour chaque enregistrement

### Suppression intelligente

Lors de la suppression d'un fichier :
- **Si d'autres enregistrements partagent le même hash** : soft delete uniquement de l'enregistrement
- **Si c'est le seul enregistrement avec ce hash** : soft delete de l'enregistrement + suppression physique du fichier

## Model

```php
use App\Models\StoredFile;

// Créer un fichier (via service)
$storedFile = $createService->execute($dto);

// Récupérer par hash
$files = StoredFile::activeByHash($hash)->get();

// Compter les fichiers avec un hash
$count = StoredFile::countByHash($hash);

// Vérifier l'existence physique
$exists = $storedFile->existsOnDisk();

// Obtenir l'URL (fichiers publics uniquement)
$url = $storedFile->url;
```

## Resource

```php
use App\Http\Resources\StoredFileResource;

// Sérialisation JSON
return new StoredFileResource($storedFile);

// Avec relation user
return new StoredFileResource($storedFile->load('user'));
```

### Format de sortie

```json
{
    "id": 1,
    "disk": "public",
    "path": "2026/01/20/uuid.jpg",
    "url": "http://example.com/storage/2026/01/20/uuid.jpg",
    "original_name": "photo.jpg",
    "mime_type": "image/jpeg",
    "size": 1024000,
    "size_human": "1000.00 KB",
    "extension": "jpg",
    "user": { ... },
    "created_at": "2026-01-20 10:30:00",
    "updated_at": "2026-01-20 10:30:00"
}
```

## Voir aussi

- [SERVICES.md](SERVICES.md) - Documentation des services
- [ENUMERATION.md](ENUMERATION.md) - Documentation du DiskEnum
