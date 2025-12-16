# MindArena Forum

Portail communautaire (forums, dons, équipes) avec mini‑apps jeux & catégories. Architecture PHP MVC, header/footer unifiés via le kit ENDGAME.

## Fonctionnalités principales
- Forums publics + création de sujets
- Dons et statistiques de dons
- Classement des contributeurs
- Pages équipes/jeux/catégories
- Back‑office dons / admin

## Stack
- PHP 8+, PDO, Apache/Nginx (XAMPP OK)
- MySQL/MariaDB
- UI kit ENDGAME (Bootstrap, FontAwesome, Owl, Slicknav)

## Arborescence
```
mindarena_forum/
├── config/                      # bootstrap, constants, Core/Database
├── Controllers/ Models/ Services/
├── Views/
│   ├── front/
│   │   ├── layout/header.php    # en-tête commun
│   │   └── public/ENDGAME/      # assets UI
│   ├── admin/                   # vues back-office (dons, etc.)
│   └── ...
├── projet webb/                 # module legacy (team/front pages)
└── crud-gestion des jeux/       # CRUD jeux & catégories (front/back)
```

## Prérequis
- PHP 8+ avec PDO
- MySQL
- Apache

## Installation rapide
1. Copier/cloner dans le webroot (ex. `C:\xampp\htdocs\mindarena_forum`).
2. Régler l’URL de base dans `config/constants.php` (`/mindarena_forum` par défaut).
3. Configurer la base : `Models/database.php` (legacy) ou `config/Core/Database.php` (nouveau).
4. Démarrer Apache/MySQL puis visiter :
   - Front principal : `http://localhost/mindarena_forum/index.php`
   - Admin : `http://localhost/mindarena_forum/admin.php`
   - Jeux : `http://localhost/mindarena_forum/crud-gestion des jeux/View/FrontOffice/jeuxliste.php`
   - Catégories : `http://localhost/mindarena_forum/crud-gestion des jeux/View/FrontOffice/categorieliste.php`

## Header commun
```php
$base       = BASE_URL;
$action     = 'home';  // clé de page pour l'état actif
$isLoggedIn = isset($_SESSION['user']);
$username   = $isLoggedIn ? $_SESSION['user']['username'] : '';
$role       = $isLoggedIn ? $_SESSION['user']['role'] : '';
include __DIR__ . '/Views/front/layout/header.php';
```
Charge automatiquement les assets ENDGAME et aligne le menu sur toutes les pages.

## Conseils & dépannage
- Assets : rester dans `Views/front/public/ENDGAME` pour éviter les liens cassés.
- Sessions : appeler `session_start()` avant tout output pour éviter “Session cannot be started after headers”.
- Nettoyer les styles/scripts inline vers les bundles ENDGAME pour une UI cohérente.
- CSP : éviter `eval`/`new Function`; autoriser `unsafe-eval` seulement si nécessaire.
