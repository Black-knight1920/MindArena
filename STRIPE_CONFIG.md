# Configuration Paiement Stripe

## Installation

### 1. Installer la bibliothèque Stripe PHP

```bash
composer require stripe/stripe-php
```

### 2. Obtenir vos clés Stripe

1. Créer un compte Stripe: https://stripe.com/fr
2. Accéder au tableau de bord: https://dashboard.stripe.com
3. Aller dans **Développeurs** > **Clés API**
4. Copier votre **clé publique** (pk_test_...)
5. Copier votre **clé secrète** (sk_test_...)

### 3. Configurer les clés

#### Dans `view/frontoffice/addDon.php` (ligne ~1619)
```javascript
const publishableKey = 'pk_test_YOUR_KEY_HERE'; // Votre clé publique
```

#### Dans `view/frontoffice/process-payment.php` (ligne 5)
```php
define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE'); // Votre clé secrète
```

### 4. Structure du flux de paiement

1. **Frontend** (`addDon.php`)
   - Validation JavaScript (FormValidator)
   - Initialisation Stripe.js avec élément de carte
   - Création du paiement avec `stripe.confirmCardPayment()`

2. **Backend** (`process-payment.php`)
   - Création d'un PaymentIntent Stripe
   - Retour du `clientSecret` au frontend
   - Validation du paiement

3. **Soumission du formulaire**
   - Après succès du paiement Stripe
   - Sauvegarde du don en base de données
   - Redirection/confirmation

### 5. Environnements

- **Test**: Utilisez les clés `pk_test_` et `sk_test_`
- **Production**: Remplacez par les clés `pk_live_` et `sk_live_`

### 6. Cartes de test Stripe

Pour tester en mode sandbox:

| Type | Numéro | CVC | Date |
|------|--------|-----|------|
| Succès | 4242 4242 4242 4242 | 123 | 12/25 |
| Échec | 4000 0000 0000 0002 | 123 | 12/25 |
| 3D Secure | 4000 0025 0000 3155 | 123 | 12/25 |

### 7. Webhooks (optionnel mais recommandé)

Pour confirmer les paiements en temps réel:

1. Tableau de bord Stripe > Webhooks
2. URL endpoint: `https://votre-domaine.com/webhook-stripe.php`
3. Événements: `payment_intent.succeeded`, `payment_intent.payment_failed`

### 8. Sécurité

⚠️ **IMPORTANT:**
- Ne jamais commiter les clés secrètes dans Git
- Utiliser des variables d'environnement ou .env
- En production, activez le mode HTTPS obligatoire
- Validez toujours côté serveur les montants

### 9. Dépannage

**Erreur: "Stripe is not defined"**
- Vérifier que le script Stripe.js est chargé (`<script src="https://js.stripe.com/v3/"></script>`)

**Erreur: "clientSecret not provided"**
- Vérifier que `process-payment.php` est accessible
- Vérifier les clés Stripe dans les deux fichiers

**Paiement échoue avec "Invalid API key"**
- Vérifier que les clés sont correctes (test vs production)
- Vérifier les permissions des clés dans Stripe

### 10. Tests E2E

1. Ouvrir `view/frontoffice/index.php`
2. Sélectionner une organisation
3. Remplir le formulaire de don
4. Tester avec une carte de test
5. Vérifier la sauvegarde en base de données

---

## Fichiers modifiés

- `view/frontoffice/addDon.php` - Formulaire Stripe + JavaScript
- `view/frontoffice/process-payment.php` - Backend Stripe (créé)
- `composer.json` - Dépendances (ajout Stripe)

## Besoin d'aide?

Documentation Stripe: https://stripe.com/docs
Support: contact@stripe.com
