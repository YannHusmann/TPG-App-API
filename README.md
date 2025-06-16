# Backend – API Laravel pour TPG Signal

## Description
Ce backend alimente l’application mobile **TPG Signal** réalisé dans le cadre du module 66-62 **Travail de Bachelor** à la **HEG Genève**. Construit avec Laravel, il gère l’authentification des utilisateurs, les arrêts, les lignes et les signalements (avec photos). L’API est sécurisée avec Laravel Sanctum et fonctionne en REST.

## Prérequis

- PHP ≥ 8.2
- Composer ≥ 2.5
- Laravel ≥ 10
- MySQL / MariaDB
- (Optionnel) SQLite pour les tests

## Installation

```bash
git clone <repo_url>
cd TPGSignal-API
composer install
cp .env.example .env
php artisan key:generate
# Configurer la base de données dans .env
php artisan migrate --seed
php artisan serve
```

## Authentification

- Authentification basée sur **Laravel Sanctum**
- Token stocké via AsyncStorage sur l’app mobile

## Fonctionnalités principales

| Contrôleur              | Méthodes         | Description                                    |
|------------------------|------------------|------------------------------------------------|
| AuthController         | login, logout    | Connexion / déconnexion                        |
| UserController         | CRUD             | Utilisateurs                                   |
| StopController         | index            | Liste des arrêts                               |
| RouteController        | index            | Liste des lignes                               |
| ReportController       | CRUD, filter     | Signalements                                   |
| PasswordResetController| sendLink, reset  | Réinitialisation par email                     |

## Uploads

- Photos stockées dans `storage/app/public/images`
- Accès via `http://<host>/storage/images/...`

## Exemple `.env`

```env
APP_NAME=TPGSignal
APP_ENV=local
APP_URL=http://[adresse]:8000

DB_DATABASE=tpg_signal
DB_USERNAME=root
DB_PASSWORD=root

FILESYSTEM_DISK=public
MAIL_MAILER=log
```

## Tests

```bash
php artisan test
```

Tests couvrant : authentification, validation, signalement, rôles, etc.

## Auteurs

Travail de Bachelor 2025  
**Yann Husmann**
