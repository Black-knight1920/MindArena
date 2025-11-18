# Module Formation et Apprentissage

Projet CRUD simple avec architecture MVC pour la gestion des cours et des chapitres.

## Structure du Projet

```
projet webb/
├── config.php                 # Configuration de la connexion à la base de données
├── database.sql               # Script SQL pour créer les tables
├── Model/
│   ├── Cours.php             # Modèle Cours
│   └── Chapitre.php          # Modèle Chapitre
├── Controller/
│   ├── CoursController.php
│   └── ChapitreController.php
└── View/
    ├── BackOffice/
    │   ├── index.html              # Page d'accueil du dashboard
    │   ├── coursList.php
    │   ├── addCours.php
    │   ├── updateCours.php
    │   ├── deleteCours.php
    │   ├── chapitreList.php
    │   ├── addChapitre.php
    │   ├── updateChapitre.php
    │   └── deleteChapitre.php
    └── FrontOffice/
        ├── index.html              # Page d'accueil publique
        └── coursList.php           # Liste publique des cours
```

## Installation

### 1. Base de données

1. Ouvrez phpMyAdmin dans XAMPP (http://localhost/phpmyadmin)
2. Importez le fichier `database.sql` ou exécutez les commandes SQL suivantes :

```sql
CREATE DATABASE formation_apprentissage;
USE formation_apprentissage;
-- Puis exécutez le contenu de database.sql
```

### 2. Configuration

Le fichier `config.php` est déjà configuré avec :
- Serveur: localhost
- Utilisateur: root
- Mot de passe: (vide)
- Base de données: formation_apprentissage

Si vous avez des paramètres différents, modifiez `config.php`.

### 3. Assets (CSS/JS)

**Important**: Vous devez copier les dossiers `assets` depuis le template EspritBook.

**Pour le BackOffice**, copiez :
```
EspritBook-CRUD-AvecTemplate/View/BackOffice/assets/
```
vers
```
projet webb/View/BackOffice/assets/
```

**Pour le FrontOffice**, copiez :
```
EspritBook-CRUD-AvecTemplate/View/FrontOffice/assets/
```
vers
```
projet webb/View/FrontOffice/assets/
```

Et copiez aussi :
```
EspritBook-CRUD-AvecTemplate/View/FrontOffice/style.css
```
vers
```
projet webb/View/FrontOffice/style.css
```

### 4. Accès à l'application

Une fois les assets copiés, accédez à :

**FrontOffice (Public) :**
- Page d'accueil: `http://localhost/projet webb/View/FrontOffice/index.html`
- Liste des cours: `http://localhost/projet webb/View/FrontOffice/coursList.php`

**BackOffice (Admin) :**
- Dashboard: `http://localhost/projet webb/View/BackOffice/index.html`
- Liste des cours: `http://localhost/projet webb/View/BackOffice/coursList.php`
- Liste des chapitres: `http://localhost/projet webb/View/BackOffice/chapitreList.php`

## Fonctionnalités

### Gestion des Cours
- ✅ Liste des cours
- ✅ Ajouter un cours
- ✅ Modifier un cours
- ✅ Supprimer un cours

### Gestion des Chapitres
- ✅ Liste des chapitres
- ✅ Ajouter un chapitre
- ✅ Modifier un chapitre
- ✅ Supprimer un chapitre
- ✅ Association avec un cours (Relation 1-N)

## Tables de la Base de Données

### Table `cours`
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- titre (VARCHAR)
- description (TEXT)
- duree (INT) - Durée en heures
- niveau (VARCHAR) - Débutant, Intermédiaire, Avancé, Expert
- formateur (VARCHAR)

### Table `chapitre`
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- titre (VARCHAR)
- description (TEXT)
- ordre (INT) - Ordre du chapitre dans le cours
- cours_id (INT, FOREIGN KEY vers cours.id) - Relation 1-N : un cours contient plusieurs chapitres

## Technologies Utilisées

- PHP (POO)
- MySQL
- Bootstrap (via le template)
- Architecture MVC

## Notes

- Assurez-vous que XAMPP est démarré (Apache et MySQL)
- Le projet utilise PDO pour la connexion à la base de données
- Les chemins relatifs sont configurés pour fonctionner depuis le dossier View/BackOffice
- Relation 1-N : Un cours peut contenir plusieurs chapitres
