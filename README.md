# SMM Boost - Site Web de Services SMM

SMM Boost est un site web complet en PHP pour vendre des services de médias sociaux (followers, likes, vues) pour Instagram, TikTok, YouTube et Facebook avec paiement via Mobile Money.

## 🌟 Fonctionnalités

### Frontend
- **Page d'accueil moderne** avec design sombre et responsive
- **Système de commande dynamique** avec calcul automatique des prix
- **Paiement Mobile Money** (MTN Money & Moov Money)
- **Upload de preuve de paiement** (images JPG/PNG)
- **Interface responsive** optimisée mobile-first
- **Animations CSS** avec design moderne

### Backend Admin
- **Dashboard complet** avec statistiques en temps réel
- **Gestion des commandes** avec filtres et actions
- **Système de statuts** (En attente, En cours, Terminée, Annulée)
- **Gestion des services et catégories** sans coder
- **Authentification sécurisée** avec sessions
- **Historique des changements** de statut

### Sécurité
- **Mots de passe hashés** avec password_hash()
- **Protection CSRF** sur tous les formulaires
- **Validation et filtrage** des entrées utilisateur
- **Protection SQL injection** avec requêtes préparées
- **Upload sécurisé** avec validation des types de fichiers

## 📋 Prérequis

- **PHP 7.4+** avec extensions : PDO, MySQL, GD, fileinfo
- **MySQL 5.7+** ou MariaDB 10.2+
- **Serveur web** Apache ou Nginx
- **Modules PHP** : session, json, mbstring

## 🚀 Installation

### 1. Télécharger les fichiers
```bash
# Cloner ou télécharger les fichiers du projet
git clone [URL_DU_REPO] smm-boost
cd smm-boost
```

### 2. Configuration de la base de données

1. **Créer la base de données** :
   ```sql
   CREATE DATABASE smm_boost CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. **Importer la structure** :
   ```bash
   mysql -u root -p smm_boost < smm_boost.sql
   ```

3. **Configurer la connexion** dans `config/database.php` :
   ```php
   private $host = 'localhost';
   private $dbname = 'smm_boost';
   private $username = 'votre_utilisateur';
   private $password = 'votre_mot_de_passe';
   ```

### 3. Configuration du serveur web

#### Apache (.htaccess déjà inclus)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

### 4. Permissions des dossiers
```bash
chmod 755 uploads/
chmod 755 logs/
chmod 644 config/database.php
```

### 5. Configuration des paiements

Modifier les numéros Mobile Money dans l'admin ou directement en base :
```sql
UPDATE settings SET setting_value = '+226 XX XX XX XX' WHERE setting_key = 'mtn_money_number';
UPDATE settings SET setting_value = '+226 XX XX XX XX' WHERE setting_key = 'moov_money_number';
```

## 👤 Compte Administrateur

### Connexion par défaut
- **URL** : `votre-site.com/admin/login.php`
- **Email** : `admin@smmboost.com`  
- **Mot de passe** : `admin123`

⚠️ **IMPORTANT** : Changez ces identifiants immédiatement après l'installation !

### Changer le mot de passe admin
```sql
UPDATE admin_users 
SET password = '$2y$10$[NOUVEAU_HASH_DU_MOT_DE_PASSE]' 
WHERE email = 'admin@smmboost.com';
```

## 📁 Structure du projet

```
smm-boost/
├── admin/                 # Interface d'administration
│   ├── includes/
│   │   └── auth.php      # Protection des pages admin
│   ├── dashboard.php     # Tableau de bord
│   ├── orders.php        # Gestion des commandes
│   ├── login.php         # Connexion admin
│   └── logout.php        # Déconnexion
├── assets/
│   ├── css/
│   │   └── style.css     # Styles principaux
│   └── js/
│       └── script.js     # JavaScript
├── config/
│   └── database.php      # Configuration DB + fonctions
├── uploads/               # Preuves de paiement
├── logs/                  # Logs d'erreurs
├── index.php             # Page d'accueil
├── order.php             # Page de commande
├── payment.php           # Page de paiement
├── order-confirmation.php # Confirmation de commande
├── smm_boost.sql         # Structure de la base de données
└── README.md             # Ce fichier
```

## 🎨 Personnalisation

### 1. Modifier les couleurs
Dans `assets/css/style.css`, modifier les variables CSS :
```css
:root {
    --primary-color: #00ff88;    /* Couleur principale */
    --secondary-color: #1a1a1a;  /* Couleur secondaire */
    --dark-bg: #0a0a0a;          /* Arrière-plan sombre */
    --card-bg: #151515;          /* Arrière-plan des cartes */
}
```

### 2. Ajouter des services
Depuis l'admin ou directement en base :
```sql
INSERT INTO services (category_id, name, description, price, min_quantity, max_quantity) 
VALUES (1, 'Nouveau Service', 'Description du service', 15.00, 100, 50000);
```

### 3. Modifier les informations du site
```sql
UPDATE settings SET setting_value = 'Nouveau Nom' WHERE setting_key = 'site_name';
UPDATE settings SET setting_value = 'contact@nouveausite.com' WHERE setting_key = 'site_email';
```

## 🔧 Configuration avancée

### Variables d'environnement recommandées
```php
// config/database.php - Pour la production
private $host = $_ENV['DB_HOST'] ?? 'localhost';
private $dbname = $_ENV['DB_NAME'] ?? 'smm_boost';
private $username = $_ENV['DB_USER'] ?? 'root';
private $password = $_ENV['DB_PASS'] ?? '';
```

### Optimisations de sécurité
1. **Désactiver l'affichage des erreurs** en production
2. **Configurer HTTPS** obligatoire
3. **Limiter les tentatives de connexion** admin
4. **Sauvegarder régulièrement** la base de données
5. **Mettre à jour** PHP et MySQL régulièrement

## 📊 Base de données

### Tables principales
- **`admin_users`** : Comptes administrateurs
- **`categories`** : Plateformes (Instagram, TikTok, etc.)
- **`services`** : Services disponibles avec prix
- **`orders`** : Commandes clients
- **`order_status_history`** : Historique des changements
- **`settings`** : Configuration du site

### Vues automatiques
- **`order_stats`** : Statistiques des commandes
- **`popular_services`** : Services les plus commandés

## 🔍 Dépannage

### Erreurs courantes

#### "Erreur de connexion à la base de données"
- Vérifiez les identifiants dans `config/database.php`
- Assurez-vous que MySQL est démarré
- Vérifiez que la base de données existe

#### "Page non trouvée" 
- Vérifiez la configuration du serveur web
- Assurez-vous que mod_rewrite est activé (Apache)

#### "Permission denied" pour les uploads
```bash
chmod 755 uploads/
chown www-data:www-data uploads/
```

#### Erreurs PHP
- Vérifiez les logs dans le dossier `logs/`
- Activez l'affichage des erreurs en développement

### Logs et debugging
```php
// Activer les logs d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/error.log');
```

## 🚀 Mise en production

### Checklist de déploiement
- [ ] Changer les identifiants admin par défaut
- [ ] Configurer HTTPS
- [ ] Désactiver l'affichage des erreurs
- [ ] Sauvegarder la base de données
- [ ] Tester les paiements Mobile Money
- [ ] Vérifier les permissions des dossiers
- [ ] Configurer les sauvegardes automatiques

### Optimisations recommandées
- **Cache** : Implémenter un système de cache pour les services
- **CDN** : Utiliser un CDN pour les assets statiques
- **Compression** : Activer gzip sur le serveur
- **Monitoring** : Surveiller les performances et erreurs

## 📱 Mobile Money

### Configuration MTN Money
1. Obtenir un numéro marchand MTN
2. Mettre à jour le numéro dans les paramètres
3. Tester les transactions

### Configuration Moov Money  
1. Obtenir un numéro marchand Moov
2. Mettre à jour le numéro dans les paramètres
3. Tester les transactions

## 🤝 Support

### Contact
- **Email** : contact@smmboost.com
- **Documentation** : Voir ce fichier README

### Problèmes connus
- Les uploads nécessitent PHP 7.4+ pour la validation des types MIME
- La fonction `password_hash()` nécessite PHP 5.5+

## 📄 Licence

Ce projet est sous licence MIT. Vous êtes libre de l'utiliser, le modifier et le distribuer.

## 🔄 Mises à jour

Pour mettre à jour le site :
1. Sauvegarder la base de données
2. Sauvegarder les fichiers de configuration
3. Remplacer les fichiers par la nouvelle version
4. Exécuter les migrations SQL si nécessaire
5. Tester toutes les fonctionnalités

---

**Développé avec ❤️ pour simplifier la vente de services SMM**