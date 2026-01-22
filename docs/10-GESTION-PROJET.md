# 10 - Gestion de Projet

> Organisation Git, workflow de collaboration et outils pour Smart Cafe

---

## Table des matières

1. [Organisation Git](#1-organisation-git)
2. [Stratégie de Branches](#2-stratégie-de-branches)
3. [Conventions de Commits](#3-conventions-de-commits)
4. [Workflow de Collaboration](#4-workflow-de-collaboration)
5. [Code Review](#5-code-review)
6. [Intégration Continue](#6-intégration-continue)
7. [Déploiement](#7-déploiement)
8. [Outils et Configuration](#8-outils-et-configuration)

---

## 1. Organisation Git

### 1.1 Structure du Repository

```
┌─────────────────────────────────────────────────────────────────┐
│                    STRUCTURE MONOREPO                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   smart-cafe/                    ← Repository racine            │
│   │                                                             │
│   ├── smart-cafe-back/           ← Laravel API                  │
│   │   ├── app/                                                  │
│   │   ├── database/                                             │
│   │   └── ...                                                   │
│   │                                                             │
│   ├── smart-cafe-front/          ← Vue.js Dashboard             │
│   │   ├── src/                                                  │
│   │   └── ...                                                   │
│   │                                                             │
│   ├── smart-cafe-app/            ← React Native App             │
│   │   ├── app/                                                  │
│   │   └── ...                                                   │
│   │                                                             │
│   ├── docs/                      ← Documentation                │
│   │                                                             │
│   ├── .gitignore                                                │
│   └── README.md                                                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 1.2 Fichiers Git Essentiels

#### .gitignore (Racine)

```gitignore
# Dependencies
node_modules/
vendor/

# Environment files
.env
.env.local
.env.*.local

# Build outputs
dist/
build/
.output/

# IDE
.idea/
.vscode/
*.swp

# OS
.DS_Store
Thumbs.db

# Logs
*.log
npm-debug.log*

# Laravel specific
smart-cafe-back/storage/*.key
smart-cafe-back/storage/app/*
smart-cafe-back/storage/logs/*
smart-cafe-back/bootstrap/cache/*

# Frontend specific
smart-cafe-front/dist/

# Mobile specific
smart-cafe-app/.expo/
smart-cafe-app/ios/
smart-cafe-app/android/
```

---

## 2. Stratégie de Branches

### 2.1 Modèle Git Flow Simplifié

```
┌─────────────────────────────────────────────────────────────────┐
│                    STRATÉGIE DE BRANCHES                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   main ─────●─────●─────●─────●─────●─────●─────►               │
│             │     ▲     │     ▲     │     ▲                     │
│             │     │     │     │     │     │                     │
│   develop ──●──●──●──●──●──●──●──●──●──●──●─────►               │
│                │     │     │     │                               │
│                │     │     │     │                               │
│   feat/xxx ────●─────●     │     │                               │
│                      ▲     │     │                               │
│                      │     │     │                               │
│   feat/yyy ──────────●─────●     │                               │
│                            ▲     │                               │
│                            │     │                               │
│   fix/zzz ─────────────────●─────●                               │
│                                                                 │
│   ──────────────────────────────────────────────────────────    │
│   main     : Production (stable)                                │
│   develop  : Intégration des features                           │
│   feat/*   : Nouvelles fonctionnalités                          │
│   fix/*    : Corrections de bugs                                │
│   refactor/*: Refactoring de code                               │
│   docs/*   : Documentation                                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Branches Principales

| Branche | Description | Protection |
|---------|-------------|------------|
| `main` | Production, toujours stable | Protégée, PR obligatoire |
| `develop` | Intégration des features | Protégée, PR obligatoire |

### 2.3 Branches de Travail

| Préfixe | Usage | Exemple |
|---------|-------|---------|
| `feat/` | Nouvelle fonctionnalité | `feat/user-authentication` |
| `fix/` | Correction de bug | `fix/login-error` |
| `refactor/` | Refactoring | `refactor/product-service` |
| `docs/` | Documentation | `docs/api-reference` |
| `chore/` | Maintenance | `chore/update-dependencies` |
| `test/` | Tests | `test/product-service` |

### 2.4 Nommage des Branches

```
# Format
<type>/<description-courte>

# Exemples valides
feat/add-product-gallery
fix/store-creation-error
refactor/split-product-controller
docs/update-readme
chore/upgrade-laravel-12

# À éviter
feature-123
bugfix
my-branch
test
```

---

## 3. Conventions de Commits

### 3.1 Format Conventional Commits

```
<type>(<scope>): <description>

[body optionnel]

[footer optionnel]
```

### 3.2 Types de Commits

| Type | Description | Exemple |
|------|-------------|---------|
| `feat` | Nouvelle fonctionnalité | `feat(products): add image gallery` |
| `fix` | Correction de bug | `fix(auth): resolve token expiration` |
| `refactor` | Refactoring | `refactor(api): split controller methods` |
| `docs` | Documentation | `docs: update API reference` |
| `style` | Formatage | `style: fix indentation` |
| `test` | Tests | `test(products): add unit tests` |
| `chore` | Maintenance | `chore: update dependencies` |
| `perf` | Performance | `perf(db): add indexes` |
| `ci` | CI/CD | `ci: add GitHub Actions workflow` |

### 3.3 Scopes Courants

| Scope | Description |
|-------|-------------|
| `api` | Backend Laravel |
| `web` | Frontend Vue.js |
| `mobile` | Application React Native |
| `auth` | Authentification |
| `products` | Module produits |
| `stores` | Module magasins |
| `users` | Module utilisateurs |
| `db` | Base de données |
| `deps` | Dépendances |

### 3.4 Exemples de Commits

```bash
# Nouvelle fonctionnalité
git commit -m "feat(products): add product variant management"

# Correction de bug
git commit -m "fix(auth): resolve session persistence issue"

# Refactoring
git commit -m "refactor(api): extract product service from controller"

# Documentation
git commit -m "docs(api): add endpoint documentation for products"

# Avec body détaillé
git commit -m "feat(stores): implement multi-store user assignment

- Add store_user pivot table
- Create AttachUsersToStoreService
- Add API endpoints for user management
- Update StorePolicy for authorization

Closes #42"
```

### 3.5 Règles de Commits

```
┌─────────────────────────────────────────────────────────────────┐
│                    RÈGLES DE COMMITS                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   ✅ DO                                                         │
│   ─────────────────────────────────────────────────────────    │
│   • Commits atomiques (une seule modification logique)         │
│   • Messages en anglais ou français (cohérent)                 │
│   • Présent impératif ("add" pas "added")                      │
│   • Première lettre minuscule après le type                    │
│   • Pas de point final                                         │
│   • Maximum 72 caractères pour le titre                        │
│                                                                 │
│   ❌ DON'T                                                      │
│   ─────────────────────────────────────────────────────────    │
│   • "WIP", "fix", "update" sans contexte                       │
│   • Commits massifs mélangeant plusieurs fonctionnalités       │
│   • Messages vagues comme "diverses corrections"               │
│   • Commits de fichiers sensibles (.env, credentials)          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. Workflow de Collaboration

### 4.1 Processus de Développement

```
┌─────────────────────────────────────────────────────────────────┐
│                    WORKFLOW DÉVELOPPEMENT                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   1. CRÉATION DE BRANCHE                                        │
│   ─────────────────────────────────────────────────────────    │
│   $ git checkout develop                                       │
│   $ git pull origin develop                                    │
│   $ git checkout -b feat/my-feature                            │
│                                                                 │
│   2. DÉVELOPPEMENT                                              │
│   ─────────────────────────────────────────────────────────    │
│   $ # Écrire le code                                           │
│   $ git add -p                      # Staging interactif       │
│   $ git commit -m "feat: ..."       # Commits atomiques        │
│   $ # Répéter jusqu'à feature complète                         │
│                                                                 │
│   3. MISE À JOUR                                                │
│   ─────────────────────────────────────────────────────────    │
│   $ git fetch origin                                           │
│   $ git rebase origin/develop       # Garder historique propre │
│                                                                 │
│   4. PUSH & PR                                                  │
│   ─────────────────────────────────────────────────────────    │
│   $ git push origin feat/my-feature                            │
│   $ # Créer Pull Request sur GitHub                            │
│                                                                 │
│   5. REVIEW & MERGE                                             │
│   ─────────────────────────────────────────────────────────    │
│   $ # Adresser les commentaires                                │
│   $ # Squash merge dans develop                                │
│   $ # Supprimer la branche                                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Création d'une Pull Request

```markdown
## Description
[Description claire de ce que fait cette PR]

## Type de changement
- [ ] Bug fix
- [ ] Nouvelle fonctionnalité
- [ ] Breaking change
- [ ] Documentation

## Changements effectués
- [x] Implémentation de X
- [x] Tests unitaires
- [x] Documentation mise à jour

## Tests
- [ ] Tests unitaires passent
- [ ] Tests d'intégration passent
- [ ] Testé manuellement

## Screenshots (si applicable)
[Captures d'écran]

## Checklist
- [ ] Mon code suit les conventions du projet
- [ ] J'ai commenté le code si nécessaire
- [ ] J'ai mis à jour la documentation
- [ ] Mes changements ne génèrent pas de warnings
```

### 4.3 Template de PR (GitHub)

```yaml
# .github/PULL_REQUEST_TEMPLATE.md
## Description
<!-- Décrivez vos changements -->

## Type de changement
<!-- Cochez les cases pertinentes -->
- [ ] 🐛 Bug fix
- [ ] ✨ Nouvelle fonctionnalité
- [ ] 💥 Breaking change
- [ ] 📝 Documentation

## Comment tester ?
<!-- Instructions pour tester les changements -->

## Checklist
- [ ] J'ai suivi les conventions de code
- [ ] J'ai ajouté des tests
- [ ] La documentation est à jour
- [ ] Tous les tests passent
```

---

## 5. Code Review

### 5.1 Processus de Review

```
┌─────────────────────────────────────────────────────────────────┐
│                    PROCESSUS CODE REVIEW                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   PR Créée ──► Review Assignée ──► Commentaires ──►            │
│                                         │                       │
│                                         ▼                       │
│                              ┌──────────────────┐               │
│                              │ Modifications    │               │
│                              │ demandées ?      │               │
│                              └────────┬─────────┘               │
│                                       │                         │
│                          Oui          │          Non            │
│                           │           │           │             │
│                           ▼           │           ▼             │
│                    ┌──────────┐       │    ┌──────────┐        │
│                    │  Push    │       │    │ Approved │        │
│                    │  fixes   │───────┘    └────┬─────┘        │
│                    └──────────┘                 │               │
│                                                 ▼               │
│                                          ┌──────────┐           │
│                                          │  Merge   │           │
│                                          └──────────┘           │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 5.2 Critères de Review

| Catégorie | Points à vérifier |
|-----------|-------------------|
| **Fonctionnel** | La feature fonctionne-t-elle correctement ? |
| **Code Quality** | SOLID, DRY, KISS respectés ? |
| **Tests** | Tests suffisants et pertinents ? |
| **Sécurité** | Pas de vulnérabilités introduites ? |
| **Performance** | Pas de régression de performance ? |
| **Documentation** | Code et docs à jour ? |

### 5.3 Bonnes Pratiques de Review

```
┌─────────────────────────────────────────────────────────────────┐
│                    BONNES PRATIQUES REVIEW                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   EN TANT QUE REVIEWER                                          │
│   ─────────────────────────────────────────────────────────    │
│   ✅ Être constructif et bienveillant                          │
│   ✅ Expliquer le "pourquoi" des suggestions                   │
│   ✅ Distinguer bloquant vs suggestion                         │
│   ✅ Proposer des alternatives                                 │
│   ✅ Approuver quand c'est bon                                 │
│                                                                 │
│   EN TANT QU'AUTEUR                                             │
│   ─────────────────────────────────────────────────────────    │
│   ✅ PR de taille raisonnable (< 400 lignes)                   │
│   ✅ Description claire                                        │
│   ✅ Auto-review avant soumission                              │
│   ✅ Répondre à tous les commentaires                          │
│   ✅ Remercier les reviewers                                   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. Intégration Continue

### 6.1 GitHub Actions Workflow

```yaml
# .github/workflows/ci.yml
name: CI

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  # Backend Tests
  backend:
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: smart-cafe-back
    steps:
      - uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
          extensions: mbstring, pdo_sqlite

      - name: Install Dependencies
        run: composer install --prefer-dist --no-progress

      - name: Run Tests
        run: php artisan test

      - name: Run PHPStan
        run: vendor/bin/phpstan analyse

  # Frontend Tests
  frontend:
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: smart-cafe-front
    steps:
      - uses: actions/checkout@v4

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
          cache-dependency-path: smart-cafe-front/package-lock.json

      - name: Install Dependencies
        run: npm ci

      - name: Type Check
        run: npm run type-check

      - name: Build
        run: npm run build

  # Mobile Tests
  mobile:
    runs-on: ubuntu-latest
    defaults:
      run:
        working-directory: smart-cafe-app
    steps:
      - uses: actions/checkout@v4

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
          cache-dependency-path: smart-cafe-app/package-lock.json

      - name: Install Dependencies
        run: npm ci

      - name: Type Check
        run: npx tsc --noEmit
```

### 6.2 Checks Obligatoires

| Check | Description | Bloquant |
|-------|-------------|----------|
| **Backend Tests** | Tests PestPHP | Oui |
| **Frontend Build** | Build Vite | Oui |
| **Type Check** | TypeScript | Oui |
| **PHPStan** | Analyse statique PHP | Oui |
| **Code Review** | Au moins 1 approbation | Oui |

---

## 7. Déploiement

### 7.1 Environnements

| Environnement | Branche | URL | Déploiement |
|---------------|---------|-----|-------------|
| **Development** | `develop` | dev.smartcafe.com | Automatique |
| **Staging** | `release/*` | staging.smartcafe.com | Automatique |
| **Production** | `main` | smartcafe.com | Manuel |

### 7.2 Processus de Release

```
┌─────────────────────────────────────────────────────────────────┐
│                    PROCESSUS DE RELEASE                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   1. Créer branche release depuis develop                       │
│      $ git checkout -b release/1.2.0 develop                   │
│                                                                 │
│   2. Bump version & changelog                                   │
│      $ # Mettre à jour version dans package.json, etc.         │
│      $ git commit -m "chore: bump version to 1.2.0"            │
│                                                                 │
│   3. Tests finaux sur staging                                   │
│      $ # Vérifications manuelles                               │
│                                                                 │
│   4. Merge dans main                                            │
│      $ git checkout main                                       │
│      $ git merge --no-ff release/1.2.0                         │
│      $ git tag -a v1.2.0 -m "Release 1.2.0"                    │
│      $ git push origin main --tags                             │
│                                                                 │
│   5. Merge dans develop                                         │
│      $ git checkout develop                                    │
│      $ git merge --no-ff release/1.2.0                         │
│      $ git push origin develop                                 │
│                                                                 │
│   6. Déploiement production                                     │
│      $ # Déclenché par le tag                                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 7.3 Versioning Sémantique

```
MAJOR.MINOR.PATCH

Exemples:
- 1.0.0 → 1.0.1  (PATCH: bug fix)
- 1.0.1 → 1.1.0  (MINOR: nouvelle feature)
- 1.1.0 → 2.0.0  (MAJOR: breaking change)
```

---

## 8. Outils et Configuration

### 8.1 Outils Utilisés

| Outil | Usage |
|-------|-------|
| **GitHub** | Repository, Issues, PRs |
| **GitHub Actions** | CI/CD |
| **Git** | Version control |

### 8.2 Configuration Git Locale

```bash
# Configuration globale recommandée
git config --global user.name "Votre Nom"
git config --global user.email "votre@email.com"
git config --global init.defaultBranch main
git config --global pull.rebase true
git config --global core.autocrlf input  # macOS/Linux
git config --global core.autocrlf true   # Windows
```

### 8.3 Aliases Git Utiles

```bash
# Dans ~/.gitconfig
[alias]
    st = status
    co = checkout
    br = branch
    ci = commit
    lg = log --oneline --graph --decorate --all
    undo = reset --soft HEAD~1
    amend = commit --amend --no-edit
    sync = !git fetch origin && git rebase origin/develop
```

### 8.4 Protection des Branches (GitHub)

```yaml
# Settings recommandés pour main et develop

Branch protection rules:
  - Require pull request reviews before merging: Yes
  - Required approving reviews: 1
  - Dismiss stale PR approvals: Yes
  - Require status checks to pass: Yes
  - Required checks:
    - backend
    - frontend
    - mobile
  - Include administrators: No
  - Restrict who can push: Yes
```

---

## Résumé

```
┌─────────────────────────────────────────────────────────────────┐
│                    CHECKLIST GESTION PROJET                     │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│   GIT                                                           │
│   [x] Monorepo organisé                                        │
│   [x] .gitignore complet                                       │
│   [x] Branches protégées                                       │
│                                                                 │
│   BRANCHES                                                      │
│   [x] main = production                                        │
│   [x] develop = intégration                                    │
│   [x] feat/*, fix/*, etc. pour le travail                      │
│                                                                 │
│   COMMITS                                                       │
│   [x] Conventional Commits                                     │
│   [x] Messages clairs et atomiques                             │
│   [x] Scopes définis                                           │
│                                                                 │
│   COLLABORATION                                                 │
│   [x] Pull Requests obligatoires                               │
│   [x] Code Review systématique                                 │
│   [x] Templates de PR                                          │
│                                                                 │
│   CI/CD                                                         │
│   [x] GitHub Actions configuré                                 │
│   [x] Tests automatisés                                        │
│   [x] Checks bloquants                                         │
│                                                                 │
│   RELEASE                                                       │
│   [x] Versioning sémantique                                    │
│   [x] Tags pour les releases                                   │
│   [x] Processus documenté                                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---
