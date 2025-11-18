# Comment accéder à votre projet

## ⚠️ IMPORTANT : Ne pas utiliser file://

Le protocole `file://` ne peut **PAS** exécuter du code PHP. Vous devez utiliser un serveur web.

## ✅ Solution : Utiliser XAMPP avec http://localhost

### Étapes pour accéder à votre projet :

1. **Démarrer XAMPP**
   - Ouvrez le panneau de contrôle XAMPP
   - Démarrez **Apache** (et **MySQL** si nécessaire)

2. **Accéder à votre projet**
   - Ouvrez votre navigateur
   - Accédez à : `http://localhost/projet webb/View/FrontOffice/index.html`
   - Pour la liste des cours : `http://localhost/projet webb/View/FrontOffice/coursList.php`
   - Pour le dashboard : `http://localhost/projet webb/View/BackOffice/index.html`

### URLs complètes :

- **Page d'accueil** : `http://localhost/projet webb/View/FrontOffice/index.html`
- **Liste des cours** : `http://localhost/projet webb/View/FrontOffice/coursList.php`
- **Dashboard** : `http://localhost/projet webb/View/BackOffice/index.html`

### Si vous avez des problèmes :

1. Vérifiez que Apache est démarré dans XAMPP
2. Vérifiez que le dossier `projet webb` est bien dans `C:\xampp\htdocs\`
3. Vérifiez que la base de données est créée (importez `database.sql` via phpMyAdmin)

### Note sur les espaces dans les URLs :

Si votre dossier s'appelle "projet webb" (avec un espace), vous devrez peut-être utiliser `%20` dans l'URL :
- `http://localhost/projet%20webb/View/FrontOffice/index.html`

Ou mieux encore, renommez le dossier sans espace :
- `C:\xampp\htdocs\projet-webb\` ou `C:\xampp\htdocs\projetwebb\`


