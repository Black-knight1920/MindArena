# Module Gestion des Dons – MindArena

Projet CRUD avec architecture MVC pour gérer les organisations partenaires et les dons (frontoffice + backoffice).

## Structure du Projet

```
projet-dons - Copie/
├── config.php                     # Configuration de la connexion à la base de données
├── backoffice.php                 # Dashboard administrateur
├── index.php                      # Entrée racine
├── migrate.php                    # (optionnel) script d'initialisation BD si utilisé
├── Controller/
│   ├── DonController.php
│   └── OrganisationController.php
├── Model/
│   ├── Don.php
│   └── Organisation.php
├── view/
│   ├── backoffice/
│   │   ├── don/
│   │   │   └── donList.php        # Liste et gestion des dons
│   │   └── organisation/
│   │       ├── organisationList.php
│   │       ├── addOrganisation.php
│   │       ├── modifyOrganisation.php
│   │       └── deleteOrganisation.php
│   └── frontoffice/
│       ├── index.php              # Page d'accueil publique
│       ├── addDon.php             # Formulaire de don
│       ├── stats-live.php         # Visualisation temps réel (optionnelle)
│       ├── LanguageHelper.php     # Gestion i18n et devise
│       ├── style.css / script.js  # Assets front
│       └── lang/                  # Traductions (fr, en, es, de, it, pt)
└── uploads/
	└── organisations/             # Médias des organisations
```

## Installation

### 1. Base de données

1. Ouvrir phpMyAdmin dans XAMPP : `http://localhost/phpmyadmin`
2. Créer une base (exemple) :

```sql
CREATE DATABASE mindarena_dons;
USE mindarena_dons;
-- Créez ensuite les tables attendues par les modèles Don et Organisation
```

### 2. Configuration

Le fichier `config.php` est prêt pour l'environnement local type XAMPP :
- Serveur : localhost
- Utilisateur : root
- Mot de passe : (vide)
- Base de données : à ajuster (ex: mindarena_dons)

Adaptez ces valeurs selon votre setup.

### 3. Accès à l'application

Placez le dossier dans `C:/xampp/htdocs/` puis accédez à :

**FrontOffice (Public) :**
- Accueil : `http://localhost/projet-dons - Copie/view/frontoffice/index.php`
- Formulaire de don : `http://localhost/projet-dons - Copie/view/frontoffice/addDon.php`

**BackOffice (Admin) :**
- Dashboard : `http://localhost/projet-dons - Copie/backoffice.php`
- Dons : `http://localhost/projet-dons - Copie/view/backoffice/don/donList.php`
- Organisations : `http://localhost/projet-dons - Copie/view/backoffice/organisation/organisationList.php`

## Fonctionnalités

### Gestion des Organisations
- ✅ Lister les organisations
- ✅ Ajouter / modifier / supprimer une organisation
- ✅ Objectifs et montants collectés par organisation

### Gestion des Dons
- ✅ Lister les dons
- ✅ Enregistrer un don (formulaire front)
- ✅ Validation côté client
- ✅ Notifications backoffice pour nouveaux dons

### Internationalisation & Devise
- ✅ Langues supportées : fr, en, es, de, it, pt
- ✅ Sélection par paramètre `?lang=xx`, session ou langue navigateur
- ✅ Traductions chargées via `LanguageHelper` et les fichiers `view/frontoffice/lang/`
- ✅ Affichage en USD quand la langue est EN, stockage en EUR (conversion entrée USD→EUR à 0.86 ; affichage EUR→USD à 1.16)

## Tables de la Base de Données (exemple)

### Table `organisation`
- id (INT, AI, PK)
- nom (VARCHAR)
- description (TEXT)
- site_web (VARCHAR)
- image (VARCHAR)
- objectif (DECIMAL) – montant cible en EUR
- montant_total (DECIMAL) – montant collecté en EUR

### Table `don`
- id (INT, AI, PK)
- organisationId (INT, FK vers organisation.id)
- nom (VARCHAR)
- prenom (VARCHAR)
- email (VARCHAR)
- montant (DECIMAL, stocké en EUR)
- type (ENUM/VARCHAR : monétaire, matériel)
- date_don (DATETIME/DATE)

Adaptez ces champs à votre schéma réel si différent.

## Technologies Utilisées
- PHP (POO) + PDO MySQL
- Apache (XAMPP/LAMP)
- HTML/CSS/JS + icônes Bootstrap/Remix
- Architecture MVC

## Notes
- Démarrer Apache et MySQL dans XAMPP avant de tester.
- Les chemins relatifs sont configurés pour un dossier nommé `projet-dons - Copie` sous `htdocs`.
- La logique de devise applique l'affichage USD uniquement quand la langue sélectionnée est EN, avec stockage toujours en EUR.
