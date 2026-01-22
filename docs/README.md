# Documentation Smart Cafe

> Documentation complète du projet Smart Cafe - Application de gestion de café connecté

## Vue d'ensemble

Smart Cafe est une solution complète de gestion de café comprenant :
- **Backend API** : Laravel 12 avec architecture DDD
- **Frontend Web** : Vue 3 + TypeScript (Dashboard administrateur)
- **Application Mobile** : React Native + Expo (Interface client)

---

## Table des matières

### Documentation Fonctionnelle
| Document | Description |
|----------|-------------|
| [01 - Fonctionnel](./01-FONCTIONNEL.md) | Spécifications fonctionnelles, user stories, parcours utilisateurs |

### Documentation Technique
| Document | Description |
|----------|-------------|
| [02 - Architecture Globale](./02-ARCHITECTURE-GLOBALE.md) | Vue d'ensemble technique, stack, communication inter-applications |
| [03 - Frontend Web](./03-FRONTEND-WEB.md) | Architecture Vue.js, composants, state management |
| [04 - Backend API](./04-BACKEND-API.md) | Architecture Laravel, DDD, services, DTOs |
| [05 - Mobile](./05-MOBILE.md) | Architecture React Native, navigation, contextes |
| [06 - Base de Données](./06-BASE-DE-DONNEES.md) | Modélisation MCD/MLD, relations, optimisations |
| [07 - Référence API](./07-API-REFERENCE.md) | Documentation complète des endpoints REST |
| [08 - Sécurité](./08-SECURITE.md) | Authentification, autorisation, protection des données |

### Documentation Organisationnelle
| Document | Description |
|----------|-------------|
| [09 - Bonnes Pratiques](./09-BONNES-PRATIQUES.md) | SOLID, DRY, KISS, patterns de conception |
| [10 - Gestion de Projet](./10-GESTION-PROJET.md) | Git workflow, organisation, collaboration |

---

## Structure du Projet

```
smart-cafe/
├── smart-cafe-back/          # API Laravel 12
│   ├── app/
│   │   ├── Domain/           # Couche métier (Services, DTOs)
│   │   ├── Http/             # Controllers, Resources, Middleware
│   │   ├── Models/           # Modèles Eloquent
│   │   └── Policies/         # Politiques d'autorisation
│   ├── database/
│   │   ├── migrations/       # Schéma de base de données
│   │   └── seeders/          # Données de test
│   └── routes/               # Définition des routes API
│
├── smart-cafe-front/         # Dashboard Vue.js
│   ├── src/
│   │   ├── components/       # Composants Vue réutilisables
│   │   ├── composable/       # Composables (logique réutilisable)
│   │   ├── stores/           # State management Pinia
│   │   ├── views/            # Pages de l'application
│   │   └── router/           # Configuration du routage
│   └── public/               # Assets statiques
│
├── smart-cafe-app/           # Application mobile React Native
│   ├── app/                  # Pages (Expo Router file-based)
│   ├── components/           # Composants React Native
│   ├── contexts/             # Context API (state global)
│   ├── hooks/                # Custom hooks
│   └── services/             # Services API
│
└── docs/                     # Cette documentation
```

---

## Technologies Principales

| Couche | Technologie | Version |
|--------|-------------|---------|
| Backend | Laravel | 12.0 |
| Base de données | MySQL/SQLite | - |
| Authentification | Laravel Sanctum | 4.0 |
| RBAC | Spatie Permission | 6.24 |
| Frontend Web | Vue.js | 3.5 |
| State Management Web | Pinia | 3.0 |
| Mobile | React Native | 0.81 |
| Mobile Framework | Expo | 54.0 |
| Navigation Mobile | Expo Router | 6.0 |

---

## Démarrage Rapide

### Backend
```bash
cd smart-cafe-back
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend Web
```bash
cd smart-cafe-front
npm install
npm run dev
```

### Application Mobile
```bash
cd smart-cafe-app
npm install
npx expo start
```

---

## Critères de Validation Couverts

Cette documentation répond aux critères suivants :

- [x] **Justifications des choix technologiques** → [Architecture Globale](./02-ARCHITECTURE-GLOBALE.md)
- [x] **Architecture modulaire et maintenable** → [Backend](./04-BACKEND-API.md), [Frontend](./03-FRONTEND-WEB.md)
- [x] **Normes d'accessibilité** → [Frontend Web](./03-FRONTEND-WEB.md#accessibilité)
- [x] **Optimisations performance** → Chaque section technique
- [x] **Feedbacks utilisateur** → [Frontend](./03-FRONTEND-WEB.md), [Mobile](./05-MOBILE.md)
- [x] **Sécurité et authentification** → [Sécurité](./08-SECURITE.md)
- [x] **Bonnes pratiques (SOLID, DRY, KISS)** → [Bonnes Pratiques](./09-BONNES-PRATIQUES.md)
- [x] **Modélisation BDD avec contraintes** → [Base de Données](./06-BASE-DE-DONNEES.md)
- [x] **Documentation API complète** → [Référence API](./07-API-REFERENCE.md)
- [x] **Gestion Git professionnelle** → [Gestion de Projet](./10-GESTION-PROJET.md)

---

## Contributeurs

Projet réalisé dans le cadre de la formation Ynov.

---
