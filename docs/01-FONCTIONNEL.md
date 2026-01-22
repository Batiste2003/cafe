# 01 - Documentation Fonctionnelle

> Spécifications fonctionnelles complètes du projet Smart Cafe

---

## Table des matières

1. [Présentation du Projet](#1-présentation-du-projet)
2. [Contexte et Objectifs](#2-contexte-et-objectifs)
3. [Acteurs et Rôles](#3-acteurs-et-rôles)
4. [User Stories](#4-user-stories)
5. [Parcours Utilisateurs](#5-parcours-utilisateurs)
6. [Fonctionnalités par Application](#6-fonctionnalités-par-application)

---

## 1. Présentation du Projet

### 1.1 Description Générale

**Smart Cafe** est une solution digitale complète pour la gestion moderne d'un établissement de type café/restaurant. Elle se compose de trois applications interconnectées :

| Application | Cible | Fonction |
|-------------|-------|----------|
| **Dashboard Web** | Administrateurs, Managers, Employés | Gestion back-office complète |
| **Application Mobile** | Clients | Consultation du menu et commandes |
| **API REST** | Toutes applications | Centralisation des données et logique métier |

### 1.2 Périmètre Fonctionnel

```
┌─────────────────────────────────────────────────────────────────┐
│                        SMART CAFE                               │
├─────────────────────────────────────────────────────────────────┤
│  ┌─────────────┐    ┌─────────────┐    ┌─────────────┐        │
│  │  Dashboard  │    │     API     │    │   Mobile    │        │
│  │    Web      │◄──►│   Laravel   │◄──►│    App      │        │
│  └─────────────┘    └─────────────┘    └─────────────┘        │
│                                                                 │
│  • Gestion utilisateurs    • Authentification   • Catalogue    │
│  • Gestion produits        • Autorisation       • Navigation   │
│  • Gestion magasins        • Data persistence   • Commandes    │
│  • Gestion stocks          • Validation         • Favoris      │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. Contexte et Objectifs

### 2.1 Contexte Métier

Dans un contexte où la digitalisation des points de vente devient essentielle, Smart Cafe répond aux besoins :
- **D'optimisation** de la gestion quotidienne d'un café
- **De modernisation** de l'expérience client
- **De centralisation** des données produits, stocks et commandes
- **De scalabilité** pour une chaîne de plusieurs établissements

### 2.2 Objectifs Principaux

| Objectif | Description | Mesure de succès |
|----------|-------------|------------------|
| **Centralisation** | Unifier la gestion de tous les établissements | 1 dashboard = N magasins |
| **Efficacité** | Réduire le temps de gestion administrative | Moins d'actions manuelles |
| **Expérience Client** | Offrir une expérience mobile moderne | Application intuitive |
| **Traçabilité** | Suivre les stocks et historiques | Logs et rapports |
| **Évolutivité** | Pouvoir ajouter des fonctionnalités | Architecture modulaire |

### 2.3 Contraintes Identifiées

- **Performance** : Temps de réponse API < 200ms
- **Disponibilité** : Service accessible 99.9% du temps
- **Sécurité** : Conformité RGPD, données chiffrées
- **Multi-plateforme** : iOS, Android, navigateurs modernes
- **Accessibilité** : Conformité WCAG 2.1 niveau AA

---

## 3. Acteurs et Rôles

### 3.1 Matrice des Acteurs

```
┌─────────────────────────────────────────────────────────────────┐
│                    HIÉRARCHIE DES RÔLES                        │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│                      ┌───────────┐                             │
│                      │   ADMIN   │                             │
│                      │  (Super)  │                             │
│                      └─────┬─────┘                             │
│                            │                                    │
│              ┌─────────────┼─────────────┐                     │
│              │             │             │                      │
│        ┌─────▼─────┐ ┌─────▼─────┐ ┌─────▼─────┐              │
│        │  MANAGER  │ │  MANAGER  │ │  MANAGER  │              │
│        │ (Store 1) │ │ (Store 2) │ │ (Store N) │              │
│        └─────┬─────┘ └─────┬─────┘ └─────┬─────┘              │
│              │             │             │                      │
│        ┌─────▼─────┐ ┌─────▼─────┐ ┌─────▼─────┐              │
│        │ EMPLOYEE  │ │ EMPLOYEE  │ │ EMPLOYEE  │              │
│        └───────────┘ └───────────┘ └───────────┘              │
│                                                                 │
│        ┌───────────────────────────────────────┐               │
│        │              CLIENTS                   │               │
│        │         (Application Mobile)           │               │
│        └───────────────────────────────────────┘               │
└─────────────────────────────────────────────────────────────────┘
```

### 3.2 Détail des Rôles

#### Admin (Administrateur)
| Attribut | Valeur |
|----------|--------|
| **Niveau d'accès** | Total |
| **Périmètre** | Toutes les données, tous les magasins |
| **Application** | Dashboard Web uniquement |

**Responsabilités :**
- Création et gestion de tous les utilisateurs
- Configuration globale du système
- Gestion de tous les magasins (Stores)
- Accès aux rapports et statistiques globales
- Gestion du catalogue produits complet

#### Manager (Responsable de Magasin)
| Attribut | Valeur |
|----------|--------|
| **Niveau d'accès** | Élevé |
| **Périmètre** | Magasin(s) assigné(s) uniquement |
| **Application** | Dashboard Web |

**Responsabilités :**
- Gestion des employés de son/ses magasin(s)
- Gestion des stocks de son périmètre
- Consultation des rapports de son magasin
- Gestion des commandes

#### Employee (Employé)
| Attribut | Valeur |
|----------|--------|
| **Niveau d'accès** | Limité |
| **Périmètre** | Magasin assigné, opérations quotidiennes |
| **Application** | Dashboard Web |

**Responsabilités :**
- Consultation du catalogue produits
- Traitement des commandes
- Mise à jour ponctuelle des stocks

#### Client
| Attribut | Valeur |
|----------|--------|
| **Niveau d'accès** | Public/Authentifié |
| **Périmètre** | Propres données, catalogue public |
| **Application** | Application Mobile |

**Responsabilités :**
- Navigation dans le catalogue
- Création de compte
- Passage de commandes
- Gestion de ses favoris

### 3.3 Matrice des Permissions

| Fonctionnalité | Admin | Manager | Employee | Client |
|----------------|:-----:|:-------:|:--------:|:------:|
| **Utilisateurs** |||||
| Créer utilisateur | ✅ | ⚠️ | ❌ | ❌ |
| Voir tous les utilisateurs | ✅ | ❌ | ❌ | ❌ |
| Modifier utilisateur | ✅ | ⚠️ | ❌ | ❌ |
| Supprimer utilisateur | ✅ | ❌ | ❌ | ❌ |
| **Magasins** |||||
| Créer magasin | ✅ | ❌ | ❌ | ❌ |
| Voir tous les magasins | ✅ | ❌ | ❌ | ❌ |
| Modifier magasin | ✅ | ⚠️ | ❌ | ❌ |
| Supprimer magasin | ✅ | ❌ | ❌ | ❌ |
| **Produits** |||||
| Créer produit | ✅ | ❌ | ❌ | ❌ |
| Voir catalogue | ✅ | ✅ | ✅ | ✅ |
| Modifier produit | ✅ | ❌ | ❌ | ❌ |
| Supprimer produit | ✅ | ❌ | ❌ | ❌ |
| **Stocks** |||||
| Voir stocks | ✅ | ⚠️ | ⚠️ | ❌ |
| Modifier stocks | ✅ | ⚠️ | ⚠️ | ❌ |
| **Commandes** |||||
| Créer commande | ❌ | ❌ | ❌ | ✅ |
| Traiter commande | ✅ | ✅ | ✅ | ❌ |
| Voir historique | ✅ | ⚠️ | ⚠️ | ⚠️ |

> ✅ Accès total | ⚠️ Accès limité à son périmètre | ❌ Pas d'accès

---

## 4. User Stories

### 4.1 User Stories Admin

```gherkin
US-A01: En tant qu'Admin, je veux créer un nouveau magasin
        afin d'étendre le réseau Smart Cafe.

  Critères d'acceptation:
  - Je peux saisir le nom, l'adresse et les informations du magasin
  - Le magasin apparaît dans la liste après création
  - Je peux y attacher des utilisateurs (managers, employés)
```

```gherkin
US-A02: En tant qu'Admin, je veux gérer les utilisateurs du système
        afin de contrôler les accès.

  Critères d'acceptation:
  - Je peux créer des utilisateurs avec différents rôles
  - Je peux modifier les informations d'un utilisateur
  - Je peux désactiver/supprimer un utilisateur
  - Je peux restaurer un utilisateur supprimé (soft delete)
```

```gherkin
US-A03: En tant qu'Admin, je veux gérer le catalogue de produits
        afin de maintenir l'offre à jour.

  Critères d'acceptation:
  - Je peux créer des catégories de produits
  - Je peux créer des produits avec images
  - Je peux définir des variantes (tailles, options)
  - Je peux attacher des produits à des magasins
```

```gherkin
US-A04: En tant qu'Admin, je veux gérer les options de personnalisation
        afin d'offrir de la flexibilité aux clients.

  Critères d'acceptation:
  - Je peux créer des options (ex: "Lait")
  - Je peux définir des valeurs d'options (ex: "Entier", "Écrémé", "Végétal")
  - Je peux associer des options aux produits
```

### 4.2 User Stories Manager

```gherkin
US-M01: En tant que Manager, je veux consulter les stocks de mon magasin
        afin d'anticiper les réapprovisionnements.

  Critères d'acceptation:
  - Je vois uniquement les stocks de mon/mes magasin(s)
  - Je peux filtrer par produit ou catégorie
  - Je reçois des alertes de stock bas
```

```gherkin
US-M02: En tant que Manager, je veux mettre à jour les stocks
        afin de refléter la réalité des inventaires.

  Critères d'acceptation:
  - Je peux modifier les quantités par variante
  - L'historique des modifications est conservé
  - Les changements sont immédiats
```

```gherkin
US-M03: En tant que Manager, je veux gérer mes employés
        afin d'organiser mon équipe.

  Critères d'acceptation:
  - Je peux voir les employés attachés à mon magasin
  - Je peux créer de nouveaux employés (rôle limité)
  - Je peux modifier leurs informations basiques
```

### 4.3 User Stories Employee

```gherkin
US-E01: En tant qu'Employee, je veux consulter le catalogue
        afin de renseigner les clients.

  Critères d'acceptation:
  - Je peux voir tous les produits disponibles
  - Je vois les prix et descriptions
  - Je vois les options de personnalisation
```

```gherkin
US-E02: En tant qu'Employee, je veux traiter les commandes
        afin de servir les clients.

  Critères d'acceptation:
  - Je vois les commandes en attente
  - Je peux marquer une commande comme en préparation
  - Je peux marquer une commande comme prête/livrée
```

### 4.4 User Stories Client

```gherkin
US-C01: En tant que Client, je veux consulter le menu
        afin de choisir mes consommations.

  Critères d'acceptation:
  - Je vois les produits par catégorie
  - Je vois les images, descriptions et prix
  - Je peux filtrer et rechercher
```

```gherkin
US-C02: En tant que Client, je veux créer un compte
        afin de passer des commandes.

  Critères d'acceptation:
  - Je peux m'inscrire avec email et mot de passe
  - Je reçois une confirmation
  - Je peux me connecter ensuite
```

```gherkin
US-C03: En tant que Client, je veux personnaliser ma commande
        afin de l'adapter à mes préférences.

  Critères d'acceptation:
  - Je peux sélectionner les options disponibles
  - Je vois l'impact sur le prix en temps réel
  - Ma sélection est sauvegardée dans ma commande
```

```gherkin
US-C04: En tant que Client, je veux consulter mes commandes passées
        afin de suivre mon historique.

  Critères d'acceptation:
  - Je vois la liste de mes commandes
  - Je vois le détail de chaque commande
  - Je vois le statut actuel
```

---

## 5. Parcours Utilisateurs

### 5.1 Parcours Admin - Création d'un produit

```
┌─────────────────────────────────────────────────────────────────┐
│                  PARCOURS: Création Produit                     │
└─────────────────────────────────────────────────────────────────┘

[Connexion]           [Dashboard]           [Produits]
    │                     │                     │
    ▼                     ▼                     ▼
┌───────┐           ┌───────────┐         ┌──────────┐
│ Login │──────────►│  Accueil  │────────►│  Liste   │
│  Form │           │ Dashboard │         │ Produits │
└───────┘           └───────────┘         └────┬─────┘
                                               │
                                               ▼
                    ┌──────────────────────────────────────┐
                    │         Création Produit              │
                    ├──────────────────────────────────────┤
                    │ 1. Informations de base              │
                    │    • Nom, description, prix          │
                    │    • Catégorie                       │
                    ├──────────────────────────────────────┤
                    │ 2. Médias                            │
                    │    • Upload images principales       │
                    │    • Galerie d'images                │
                    ├──────────────────────────────────────┤
                    │ 3. Variantes                         │
                    │    • Tailles (S, M, L)               │
                    │    • Prix par variante               │
                    ├──────────────────────────────────────┤
                    │ 4. Options                           │
                    │    • Personnalisations               │
                    │    • Prix supplémentaires            │
                    ├──────────────────────────────────────┤
                    │ 5. Distribution                      │
                    │    • Magasins assignés               │
                    │    • Stocks initiaux                 │
                    └──────────────────────────────────────┘
                                    │
                                    ▼
                            ┌──────────────┐
                            │   Produit    │
                            │    créé !    │
                            └──────────────┘
```

### 5.2 Parcours Client - Commande Mobile

```
┌─────────────────────────────────────────────────────────────────┐
│                  PARCOURS: Commande Client                      │
└─────────────────────────────────────────────────────────────────┘

[Ouverture App]      [Navigation]         [Commande]
      │                   │                    │
      ▼                   ▼                    ▼
┌───────────┐       ┌──────────┐        ┌───────────┐
│  Splash   │──────►│  Accueil │───────►│ Catégorie │
│  Screen   │       │   Home   │        │  (Carte)  │
└───────────┘       └──────────┘        └─────┬─────┘
                                              │
                    ┌─────────────────────────┘
                    ▼
             ┌─────────────┐
             │  Sélection  │
             │   Produit   │
             └──────┬──────┘
                    │
                    ▼
        ┌───────────────────────┐
        │  Options Produit       │
        │  ──────────────────    │
        │  □ Taille: S M [L]     │
        │  □ Lait: [Entier] Soja │
        │  □ Sucre: [Normal]     │
        │  ──────────────────    │
        │  Total: 4.50€          │
        │                        │
        │  [Ajouter au panier]   │
        └───────────┬───────────┘
                    │
                    ▼
             ┌─────────────┐
             │   Panier    │
             │  ─────────  │
             │  1x Latte L │
             │  ─────────  │
             │  [Valider]  │
             └──────┬──────┘
                    │
                    ▼
             ┌─────────────┐
             │ Confirmation│
             │  Commande   │
             │    #1234    │
             └─────────────┘
```

### 5.3 Parcours Manager - Gestion des Stocks

```
┌─────────────────────────────────────────────────────────────────┐
│                 PARCOURS: Gestion Stocks                        │
└─────────────────────────────────────────────────────────────────┘

[Connexion]          [Mon Magasin]         [Stocks]
    │                     │                    │
    ▼                     ▼                    ▼
┌───────┐           ┌───────────┐        ┌──────────┐
│ Login │──────────►│  Dashboard│───────►│  Stocks  │
│       │           │  Manager  │        │  Magasin │
└───────┘           └───────────┘        └────┬─────┘
                                              │
                                              ▼
                              ┌────────────────────────┐
                              │   Liste des Variantes  │
                              │   ──────────────────   │
                              │   Espresso S    [12]   │
                              │   Espresso M    [8]    │
                              │   Latte S       [5] ⚠️ │
                              │   Latte M       [15]   │
                              └───────────┬────────────┘
                                          │
                            ┌─────────────┴─────────────┐
                            │                           │
                            ▼                           ▼
                    ┌───────────────┐          ┌───────────────┐
                    │ Modification  │          │   Alerte      │
                    │    Stock      │          │  Stock Bas    │
                    │ Latte S: [20] │          │   → Action    │
                    │   [Sauver]    │          │   requise     │
                    └───────────────┘          └───────────────┘
```

---

## 6. Fonctionnalités par Application

### 6.1 Dashboard Web (Admin/Manager/Employee)

#### Module Authentification
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Connexion | Authentification par email/mot de passe | Tous |
| Déconnexion | Invalidation du token | Tous |
| Session persistante | Maintien de la session active | Tous |

#### Module Utilisateurs
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Liste utilisateurs | Tableau paginé avec filtres | Admin |
| Création utilisateur | Formulaire avec attribution de rôle | Admin, Manager |
| Détail utilisateur | Fiche complète avec historique | Admin |
| Modification utilisateur | Édition des informations | Admin |
| Suppression utilisateur | Soft delete réversible | Admin |
| Restauration utilisateur | Récupération d'un compte supprimé | Admin |

#### Module Magasins (Stores)
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Liste magasins | Grille de cartes des magasins | Admin |
| Création magasin | Formulaire avec adresse | Admin |
| Détail magasin | Informations + produits + équipe | Admin, Manager |
| Modification magasin | Édition des informations | Admin |
| Gestion équipe | Attacher/détacher des utilisateurs | Admin, Manager |
| Stocks par magasin | Voir/modifier les quantités | Admin, Manager |

#### Module Produits
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Liste produits | Grille avec filtres et recherche | Admin, Manager, Employee |
| Création produit | Formulaire multi-étapes | Admin |
| Détail produit | Fiche avec variantes et options | Admin, Manager, Employee |
| Modification produit | Édition complète | Admin |
| Gestion galerie | Upload/suppression d'images | Admin |
| Association magasins | Attacher produits à des stores | Admin |

#### Module Variantes
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Liste variantes | Par produit parent | Admin |
| Création variante | Nom, prix, SKU | Admin |
| Détail variante | Informations + stocks | Admin |
| Gestion stocks | Par magasin | Admin, Manager |

#### Module Catégories
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Liste catégories | Arborescence | Admin |
| CRUD catégories | Création, modification, suppression | Admin |

#### Module Options
| Fonctionnalité | Description | Rôles |
|----------------|-------------|-------|
| Liste options | Par produit | Admin |
| Gestion options | Création avec valeurs | Admin |
| Valeurs d'options | Prix additionnels | Admin |

### 6.2 Application Mobile (Client)

#### Module Navigation
| Fonctionnalité | Description |
|----------------|-------------|
| Accueil | Vue d'ensemble avec promotions |
| Catalogue (Carte) | Navigation par catégories |
| Explorer | Découverte et recherche |

#### Module Authentification
| Fonctionnalité | Description |
|----------------|-------------|
| Inscription | Création de compte |
| Connexion | Authentification |
| Déconnexion | Fin de session |
| Session persistante | AsyncStorage |

#### Module Produits
| Fonctionnalité | Description |
|----------------|-------------|
| Liste produits | Grille avec images |
| Détail produit | Fiche complète |
| Options produit | Personnalisation |
| Filtres | Par catégorie, prix |

#### Module Commandes (à venir)
| Fonctionnalité | Description |
|----------------|-------------|
| Panier | Gestion des items |
| Validation | Confirmation de commande |
| Historique | Commandes passées |
| Suivi | Statut en temps réel |

---

## Annexes

### A. Glossaire

| Terme | Définition |
|-------|------------|
| **Store** | Magasin/établissement physique |
| **Product** | Article vendable (café, pâtisserie...) |
| **Variant** | Déclinaison d'un produit (tailles) |
| **Option** | Personnalisation possible (type de lait) |
| **Option Value** | Valeur d'une option (lait de soja) |
| **Stock** | Quantité disponible par variante/magasin |
| **RBAC** | Contrôle d'accès basé sur les rôles |

### B. Règles Métier

1. **Produit sans variante** : Impossible, au moins une variante par défaut
2. **Stock** : Ne peut pas être négatif
3. **Prix** : Toujours positif, en centimes pour éviter les erreurs de calcul
4. **Utilisateur** : Un utilisateur peut être attaché à plusieurs magasins
5. **Soft delete** : Les utilisateurs supprimés peuvent être restaurés pendant 30 jours

---
