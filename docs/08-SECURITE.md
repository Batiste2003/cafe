# 08 - Sécurité

> Stratégies et implémentation de la sécurité dans Smart Cafe

---

## Table des matières

1. [Vue d'Ensemble](#1-vue-densemble)
2. [Authentification](#2-authentification)
3. [Autorisation (RBAC)](#3-autorisation-rbac)
4. [Protection des Données](#4-protection-des-données)
5. [Validation des Entrées](#5-validation-des-entrées)
6. [Protection contre les Attaques](#6-protection-contre-les-attaques)
7. [Sécurité des API](#7-sécurité-des-api)
8. [Audit et Logging](#8-audit-et-logging)
9. [Bonnes Pratiques](#9-bonnes-pratiques)

---

## 1. Vue d'Ensemble

### 1.1 Architecture de Sécurité

```
┌─────────────────────────────────────────────────────────────────┐
│                    ARCHITECTURE SÉCURITÉ                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ┌─────────────────────────────────────────────────────────┐  │
│   │                      CLIENT                              │  │
│   │  Frontend (Vue.js) │ Mobile (React Native)              │  │
│   └───────────────────────────┬─────────────────────────────┘  │
│                               │                                 │
│                          HTTPS │ TLS 1.3                        │
│                               │                                 │
│   ┌───────────────────────────▼─────────────────────────────┐  │
│   │                    API GATEWAY                           │  │
│   │  ─────────────────────────────────────────────────────  │  │
│   │  • Rate Limiting                                        │  │
│   │  • CORS Policy                                          │  │
│   │  • Request Validation                                   │  │
│   └───────────────────────────┬─────────────────────────────┘  │
│                               │                                 │
│   ┌───────────────────────────▼─────────────────────────────┐  │
│   │                   AUTHENTIFICATION                       │  │
│   │  ─────────────────────────────────────────────────────  │  │
│   │  • Laravel Sanctum                                      │  │
│   │  • Token-based auth                                     │  │
│   │  • Session management                                   │  │
│   └───────────────────────────┬─────────────────────────────┘  │
│                               │                                 │
│   ┌───────────────────────────▼─────────────────────────────┐  │
│   │                    AUTORISATION                          │  │
│   │  ─────────────────────────────────────────────────────  │  │
│   │  • Spatie Permission (RBAC)                             │  │
│   │  • Policies Laravel                                     │  │
│   │  • Middleware par rôle                                  │  │
│   └───────────────────────────┬─────────────────────────────┘  │
│                               │                                 │
│   ┌───────────────────────────▼─────────────────────────────┐  │
│   │                APPLICATION LAYER                         │  │
│   │  ─────────────────────────────────────────────────────  │  │
│   │  • Input Validation                                     │  │
│   │  • SQL Injection Prevention (Eloquent)                  │  │
│   │  • XSS Prevention                                       │  │
│   │  • CSRF Protection                                      │  │
│   └───────────────────────────┬─────────────────────────────┘  │
│                               │                                 │
│   ┌───────────────────────────▼─────────────────────────────┐  │
│   │                    BASE DE DONNÉES                       │  │
│   │  ─────────────────────────────────────────────────────  │  │
│   │  • Encrypted at rest                                    │  │
│   │  • Hashed passwords (bcrypt)                            │  │
│   │  • Principle of least privilege                         │  │
│   └─────────────────────────────────────────────────────────┘  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.2 Principes de Sécurité

| Principe | Application |
|----------|-------------|
| **Defense in Depth** | Multiples couches de sécurité |
| **Least Privilege** | Permissions minimales nécessaires |
| **Secure by Default** | Configuration sécurisée par défaut |
| **Fail Securely** | Échec sécurisé en cas d'erreur |
| **Input Validation** | Validation de toutes les entrées |

---

## 2. Authentification

### 2.1 Laravel Sanctum

Smart Cafe utilise **Laravel Sanctum** pour l'authentification API basée sur des tokens.

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUX D'AUTHENTIFICATION                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   1. LOGIN REQUEST                                              │
│   ─────────────────────────────────────────────────────────    │
│   Client ──► POST /api/auth/login                              │
│              { email, password }                                │
│                                                                 │
│   2. VALIDATION                                                 │
│   ─────────────────────────────────────────────────────────    │
│   Server ──► Validate credentials                              │
│          ──► Check password hash (bcrypt)                      │
│          ──► Verify email if required                          │
│                                                                 │
│   3. TOKEN GENERATION                                           │
│   ─────────────────────────────────────────────────────────    │
│   Server ──► Generate Sanctum token                            │
│          ──► Store in personal_access_tokens table             │
│          ──► Return token to client                            │
│                                                                 │
│   4. AUTHENTICATED REQUESTS                                     │
│   ─────────────────────────────────────────────────────────    │
│   Client ──► GET /api/resource                                 │
│              Authorization: Bearer {token}                      │
│                                                                 │
│   5. TOKEN VALIDATION                                           │
│   ─────────────────────────────────────────────────────────    │
│   Server ──► Verify token exists and not expired               │
│          ──► Retrieve associated user                          │
│          ──► Allow/Deny request                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Configuration Sanctum

```php
// config/sanctum.php
return [
    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', 'localhost')),
    'guard' => ['web'],
    'expiration' => 60 * 24 * 7, // 7 jours
    'token_prefix' => '',
    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],
];
```

### 2.3 Hachage des Mots de Passe

```php
// Laravel utilise bcrypt par défaut
// config/hashing.php
return [
    'driver' => 'bcrypt',
    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
    ],
];

// Utilisation automatique dans Eloquent
User::create([
    'password' => Hash::make($request->password),
]);
```

### 2.4 Stockage Sécurisé des Tokens

| Application | Stockage | Sécurité |
|-------------|----------|----------|
| **Frontend Web** | localStorage | httpOnly non disponible pour SPA |
| **Mobile** | AsyncStorage | Chiffré sur iOS (Keychain), Android (EncryptedSharedPreferences) |

```typescript
// Frontend - authStore.ts
function setToken(authToken: string | null) {
  token.value = authToken
  if (authToken) {
    localStorage.setItem('auth_token', authToken)
  } else {
    localStorage.removeItem('auth_token')
  }
}

// Mobile - api.ts
await AsyncStorage.setItem('auth_token', token);
```

---

## 3. Autorisation (RBAC)

### 3.1 Spatie Laravel Permission

Smart Cafe utilise **Spatie Laravel Permission** pour le contrôle d'accès basé sur les rôles.

```
┌─────────────────────────────────────────────────────────────────┐
│                    MODÈLE RBAC                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   RÔLES                        PERMISSIONS                      │
│   ─────────────────────────────────────────────────────────    │
│                                                                 │
│   admin ────────────► users.view                               │
│         │             users.create                              │
│         │             users.update                              │
│         │             users.delete                              │
│         │             stores.view                               │
│         │             stores.create                             │
│         │             stores.update                             │
│         │             stores.delete                             │
│         │             products.* (toutes)                       │
│         └──────────►  ... (accès total)                        │
│                                                                 │
│   manager ──────────► users.view (limité à son store)          │
│           │          stores.view (ses stores)                  │
│           │          products.view                              │
│           └────────► stocks.update (ses stores)                │
│                                                                 │
│   employee ─────────► products.view                            │
│            │          stores.view (son store)                  │
│            └───────►  orders.process                           │
│                                                                 │
│   client ───────────► products.view (public)                   │
│          └─────────► orders.create (ses commandes)             │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Définition des Rôles et Permissions

```php
// database/seeders/RoleAndPermissionSeeder.php
public function run(): void
{
    // Créer les permissions
    $permissions = [
        // Users
        'users.view', 'users.create', 'users.update', 'users.delete', 'users.restore',
        // Stores
        'stores.view', 'stores.create', 'stores.update', 'stores.delete', 'stores.attach-users',
        // Products
        'products.view', 'products.create', 'products.update', 'products.delete',
        // Product Variants
        'product-variants.view', 'product-variants.create', 'product-variants.update', 'product-variants.delete',
        // Product Categories
        'product-categories.view', 'product-categories.create', 'product-categories.update', 'product-categories.delete',
        // Product Options
        'product-options.view', 'product-options.create', 'product-options.update', 'product-options.delete',
        // Stocks
        'stocks.view', 'stocks.update',
    ];

    foreach ($permissions as $permission) {
        Permission::create(['name' => $permission, 'guard_name' => 'sanctum']);
    }

    // Créer les rôles
    $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'sanctum']);
    $managerRole = Role::create(['name' => 'manager', 'guard_name' => 'sanctum']);
    $employeeRole = Role::create(['name' => 'employee', 'guard_name' => 'sanctum']);

    // Assigner toutes les permissions à admin
    $adminRole->givePermissionTo(Permission::all());

    // Permissions manager
    $managerRole->givePermissionTo([
        'users.view',
        'stores.view',
        'products.view',
        'product-variants.view',
        'stocks.view', 'stocks.update',
    ]);

    // Permissions employee
    $employeeRole->givePermissionTo([
        'products.view',
        'stores.view',
    ]);
}
```

### 3.3 Middleware de Rôle

```php
// routes/api/admin.php
Route::prefix('admin')
    ->middleware(['role:admin'])
    ->group(function () {
        // Routes admin uniquement
    });

// routes/api/manager.php
Route::prefix('manager')
    ->middleware(['role:admin,manager'])
    ->group(function () {
        // Routes manager et admin
    });
```

### 3.4 Policies Laravel

```php
// app/Policies/ProductPolicy.php
class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('products.view');
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('products.create');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.update');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasPermissionTo('products.delete');
    }
}

// Utilisation dans le controller
public function update(UpdateProductRequest $request, Product $product)
{
    $this->authorize('update', $product);
    // ...
}
```

---

## 4. Protection des Données

### 4.1 Données Sensibles

| Donnée | Protection |
|--------|------------|
| **Mots de passe** | Hachage bcrypt (12 rounds) |
| **Tokens** | SHA-256 hachés en BDD |
| **Emails** | Validation format strict |
| **Données personnelles** | Accès restreint par rôle |

### 4.2 Chiffrement

```php
// Chiffrement automatique Laravel
// config/app.php
'key' => env('APP_KEY'),
'cipher' => 'AES-256-CBC',

// Utilisation
use Illuminate\Support\Facades\Crypt;

$encrypted = Crypt::encryptString('donnée sensible');
$decrypted = Crypt::decryptString($encrypted);
```

### 4.3 Protection des Variables d'Environnement

```env
# .env (JAMAIS versionné)
APP_KEY=base64:xxxxx...
DB_PASSWORD=secret
MAIL_PASSWORD=secret

# .env.example (versionné, sans valeurs sensibles)
APP_KEY=
DB_PASSWORD=
MAIL_PASSWORD=
```

---

## 5. Validation des Entrées

### 5.1 Form Requests

```php
// app/Http/Requests/StoreProductRequest.php
class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'is_active' => ['boolean'],
            'store_ids' => ['array'],
            'store_ids.*' => ['exists:stores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du produit est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'price.min' => 'Le prix ne peut pas être négatif.',
        ];
    }
}
```

### 5.2 Règles de Validation Communes

| Champ | Règles |
|-------|--------|
| **email** | `required\|email\|max:255\|unique:users` |
| **password** | `required\|string\|min:8\|confirmed` |
| **name** | `required\|string\|max:255` |
| **price** | `required\|numeric\|min:0` |
| **id (foreign)** | `required\|exists:table,id` |
| **file** | `file\|mimes:jpg,png\|max:2048` |

### 5.3 Sanitization

```php
// Échappement automatique dans Blade
{{ $userInput }}  // XSS safe

// Pour HTML autorisé (rare)
{!! clean($htmlContent) !!}  // Avec package de sanitization
```

---

## 6. Protection contre les Attaques

### 6.1 SQL Injection

**Protection native via Eloquent ORM :**

```php
// ✅ SAFE - Eloquent query builder
$users = User::where('email', $request->email)->get();

// ✅ SAFE - Paramètres préparés
$users = DB::select('SELECT * FROM users WHERE email = ?', [$request->email]);

// ❌ DANGER - Interpolation directe
$users = DB::select("SELECT * FROM users WHERE email = '{$request->email}'");
```

### 6.2 Cross-Site Scripting (XSS)

```php
// Blade échappe automatiquement
{{ $variable }}  // htmlspecialchars() appliqué

// Pour du HTML intentionnel (rare et dangereux)
{!! $trustedHtml !!}

// Vue.js - v-html seulement pour contenu de confiance
<div v-text="userInput"></div>  // ✅ Safe
<div v-html="trustedContent"></div>  // ⚠️ Attention
```

### 6.3 Cross-Site Request Forgery (CSRF)

```php
// Protection CSRF automatique pour les formulaires web
// Pour API avec Sanctum, utilisation des tokens Bearer
// config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [env('FRONTEND_URL')],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

### 6.4 Mass Assignment

```php
// app/Models/User.php
class User extends Authenticatable
{
    // Seuls ces champs peuvent être assignés en masse
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    // Ces champs sont TOUJOURS protégés
    protected $guarded = [
        'id',
        'is_admin',
        'email_verified_at',
    ];
}
```

---

## 7. Sécurité des API

### 7.1 Rate Limiting

```php
// app/Providers/RouteServiceProvider.php
RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

// Limites spécifiques
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip());
});

// routes/api.php
Route::post('/auth/login', [LoginController::class, 'login'])
    ->middleware('throttle:login');
```

### 7.2 CORS Configuration

```php
// config/cors.php
return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:5173'),
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept'],
    'exposed_headers' => [],
    'max_age' => 86400,
    'supports_credentials' => true,
];
```

### 7.3 Headers de Sécurité

```php
// app/Http/Middleware/SecurityHeaders.php
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Content-Security-Policy', "default-src 'self'");

        return $response;
    }
}
```

### 7.4 Validation des Fichiers Upload

```php
// Validation stricte des uploads
$request->validate([
    'image' => [
        'required',
        'file',
        'mimes:jpg,jpeg,png,gif,webp',
        'max:2048', // 2MB max
        'dimensions:min_width=100,min_height=100,max_width=4000,max_height=4000',
    ],
]);

// Stockage sécurisé
$path = $request->file('image')->store('products', 'public');
// Génère un nom de fichier unique, empêche le path traversal
```

---

## 8. Audit et Logging

### 8.1 Logging des Actions Sensibles

```php
// app/Domain/Logger/Services/LoggerService.php
class LoggerService
{
    public function logSecurityEvent(string $event, array $context = []): void
    {
        Log::channel('security')->info($event, array_merge($context, [
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toISOString(),
        ]));
    }
}

// Utilisation
$logger->logSecurityEvent('user.login.success', ['email' => $user->email]);
$logger->logSecurityEvent('user.login.failed', ['email' => $request->email]);
$logger->logSecurityEvent('user.password.reset', ['user_id' => $user->id]);
```

### 8.2 Événements à Logger

| Événement | Niveau | Données |
|-----------|--------|---------|
| Login réussi | INFO | user_id, ip, timestamp |
| Login échoué | WARNING | email, ip, timestamp |
| Logout | INFO | user_id, timestamp |
| Création user | INFO | created_by, new_user_id |
| Suppression | WARNING | deleted_by, resource |
| Changement permission | WARNING | changed_by, details |
| Accès refusé | WARNING | user_id, resource, action |

### 8.3 Configuration des Channels

```php
// config/logging.php
'channels' => [
    'security' => [
        'driver' => 'daily',
        'path' => storage_path('logs/security.log'),
        'level' => 'debug',
        'days' => 90,
    ],
],
```

---

## 9. Bonnes Pratiques

### 9.1 Checklist Sécurité

```
┌─────────────────────────────────────────────────────────────────┐
│                    CHECKLIST SÉCURITÉ                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   AUTHENTIFICATION                                              │
│   ─────────────────────────────────────────────────────────    │
│   [x] Mots de passe hachés avec bcrypt                         │
│   [x] Tokens sécurisés (Sanctum)                               │
│   [x] Expiration des tokens configurée                         │
│   [x] Rate limiting sur login                                  │
│                                                                 │
│   AUTORISATION                                                  │
│   ─────────────────────────────────────────────────────────    │
│   [x] RBAC avec Spatie Permission                              │
│   [x] Policies sur toutes les ressources                       │
│   [x] Middleware de rôle sur les routes                        │
│   [x] Principle of least privilege                             │
│                                                                 │
│   VALIDATION                                                    │
│   ─────────────────────────────────────────────────────────    │
│   [x] Form Requests sur tous les endpoints                     │
│   [x] Validation des types et formats                          │
│   [x] Taille max des champs                                    │
│   [x] Validation des fichiers upload                           │
│                                                                 │
│   PROTECTION                                                    │
│   ─────────────────────────────────────────────────────────    │
│   [x] SQL Injection (Eloquent ORM)                             │
│   [x] XSS (échappement Blade/Vue)                              │
│   [x] CSRF (tokens Sanctum)                                    │
│   [x] Mass Assignment ($fillable)                              │
│   [x] CORS configuré                                           │
│   [x] Headers de sécurité                                      │
│                                                                 │
│   DONNÉES                                                       │
│   ─────────────────────────────────────────────────────────    │
│   [x] .env non versionné                                       │
│   [x] APP_KEY générée et sécurisée                             │
│   [x] Données sensibles chiffrées                              │
│   [x] Soft deletes pour traçabilité                            │
│                                                                 │
│   AUDIT                                                         │
│   ─────────────────────────────────────────────────────────    │
│   [x] Logging des événements sécurité                          │
│   [x] Rotation des logs                                        │
│   [x] Timestamps sur toutes les tables                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 9.2 Variables d'Environnement Sensibles

```env
# CRITIQUE - Ne jamais versionner
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
DB_PASSWORD=xxxxxx
MAIL_PASSWORD=xxxxxx
AWS_SECRET_ACCESS_KEY=xxxxxx

# PRODUCTION
APP_DEBUG=false
APP_ENV=production
```

### 9.3 Mise à Jour des Dépendances

```bash
# Vérifier les vulnérabilités
composer audit
npm audit

# Mettre à jour
composer update
npm update

# Fix automatique
npm audit fix
```

### 9.4 Ressources OWASP

| Vulnérabilité | Protection |
|---------------|------------|
| **Injection** | ORM, prepared statements |
| **Broken Auth** | Sanctum, bcrypt, rate limiting |
| **Sensitive Data** | Chiffrement, HTTPS |
| **XXE** | Désactivation des entités externes |
| **Broken Access** | RBAC, Policies |
| **Security Misconfig** | Hardening, headers |
| **XSS** | Échappement, CSP |
| **Insecure Deserial** | Validation des inputs |
| **Components** | Audit régulier |
| **Logging** | Journalisation complète |

---

## Ressources

- [Laravel Security Documentation](https://laravel.com/docs/security)
- [Laravel Sanctum](https://laravel.com/docs/sanctum)
- [Spatie Permission](https://spatie.be/docs/laravel-permission)
- [OWASP Top 10](https://owasp.org/Top10/)
- [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org/)

---
