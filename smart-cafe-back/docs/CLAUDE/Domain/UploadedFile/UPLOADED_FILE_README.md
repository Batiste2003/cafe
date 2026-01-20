# Domain UploadedFile

## Vue d'ensemble

Le domaine UploadedFile gère la transformation des fichiers uploadés via les requêtes HTTP en enregistrements StoredFile. Il sert de couche intermédiaire entre les FormRequests et le domain StoredFile.

## Structure

```
app/Domain/UploadedFile/
├── Constant/
│   └── UploadedFileConstant.php    # Constantes (tailles, types, répertoires)
├── DTOs/
│   └── ProcessUploadedFileInputDTO.php  # DTO pour le traitement
└── Services/
    └── ProcessUploadedFileService.php   # Service de traitement
```

## Constantes

### UploadedFileConstant

| Constante | Valeur | Description |
|-----------|--------|-------------|
| MAX_IMAGE_SIZE | 5242880 | Taille max en bytes (5 Mo) |
| MAX_IMAGE_SIZE_KB | 5120 | Taille max en Ko pour validation |
| ALLOWED_IMAGE_EXTENSIONS | jpg, jpeg, png, gif, webp | Extensions autorisées |
| ALLOWED_IMAGE_MIME_TYPES | image/jpeg, image/png, etc. | Types MIME autorisés |
| STORE_BANNER_DIRECTORY | stores/banners | Répertoire des bannières |
| STORE_LOGO_DIRECTORY | stores/logos | Répertoire des logos |

## Flux de traitement

```
UploadedFile (HTTP)
       │
       ▼
ProcessUploadedFileInputDTO
       │
       ▼
ProcessUploadedFileService
       │
       ▼
CreateStoredFileService (Domain StoredFile)
       │
       ▼
StoredFile (Model)
```

## Utilisation

### Dans un Controller

```php
use App\Domain\UploadedFile\Constant\UploadedFileConstant;
use App\Domain\UploadedFile\DTOs\ProcessUploadedFileInputDTO;
use App\Domain\UploadedFile\Services\ProcessUploadedFileService;

class StoreController extends Controller
{
    public function __construct(
        private readonly ProcessUploadedFileService $processUploadedFileService,
    ) {}

    public function store(StoreStoreRequest $request): JsonResponse
    {
        $bannerStoredFileId = null;

        if ($request->hasFile('banner')) {
            $dto = ProcessUploadedFileInputDTO::fromArray([
                'file' => $request->file('banner'),
                'user_id' => $request->user()->id,
                'directory' => UploadedFileConstant::STORE_BANNER_DIRECTORY,
            ]);

            $storedFile = $this->processUploadedFileService->execute($dto);
            $bannerStoredFileId = $storedFile->id;
        }

        // Utiliser $bannerStoredFileId pour créer l'entité
    }
}
```

### Dans un FormRequest

```php
use App\Domain\UploadedFile\Constant\UploadedFileConstant;

class StoreStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'banner' => [
                'nullable',
                'file',
                'mimes:'.implode(',', UploadedFileConstant::ALLOWED_IMAGE_EXTENSIONS),
                'max:'.UploadedFileConstant::MAX_IMAGE_SIZE_KB,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'banner.mimes' => 'La bannière doit être une image (jpeg, png, gif, webp).',
            'banner.max' => 'La bannière ne doit pas dépasser 5 Mo.',
        ];
    }
}
```

## Relation avec StoredFile

Ce domain utilise le service `CreateStoredFileService` du domain StoredFile pour :
- Calculer le hash du fichier (déduplication)
- Stocker le fichier sur le disque configuré
- Créer l'enregistrement en base de données

## Répertoires de stockage par entité

| Entité | Type | Répertoire |
|--------|------|------------|
| Store | Banner | stores/banners |
| Store | Logo | stores/logos |

## Voir aussi

- [UPLOADED_FILE_SERVICES.md](UPLOADED_FILE_SERVICES.md) - Documentation des services
- [../StoredFile/STORED_FILE_README.md](../StoredFile/STORED_FILE_README.md) - Domain StoredFile
