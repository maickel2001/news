# 🚀 SMM Pro Services - Site Web SMM Complet

Un site web professionnel pour services de Social Media Marketing (SMM) avec interface client et administrateur, système de paiement Mobile Money et gestion complète des commandes.

## ✨ Fonctionnalités

### 🎨 Interface Utilisateur
- **Design moderne et sombre** avec thème responsive
- **Mobile-first** optimisé pour tous les appareils
- **Animations CSS3** et transitions fluides
- **Google Fonts** (Poppins) pour une typographie moderne

### 👥 Système d'Authentification
- **Inscription/Connexion** sécurisée avec validation
- **Sessions PHP** sécurisées avec protection CSRF
- **Gestion des permissions** (Client/Admin)
- **Mots de passe hashés** avec salt personnalisé

### 🛒 Système de Commandes
- **Sélection dynamique** des services par catégorie
- **Calcul automatique** des montants en FCFA avec séparateurs
- **Upload de preuves** de paiement (JPG/PNG)
- **Suivi en temps réel** des commandes
- **Validation manuelle** par l'administrateur

### 💰 Paiement Mobile Money
- **MTN Money** et **Moov Money** supportés
- **Instructions de paiement** détaillées
- **Validation manuelle** des reçus uploadés
- **Historique complet** des transactions

### 🔧 Dashboard Client
- **Statistiques personnelles** (commandes, dépenses)
- **Historique des commandes** avec filtres
- **Suivi en temps réel** des statuts
- **Interface intuitive** et responsive

### ⚙️ Interface Administrateur
- **Dashboard complet** avec statistiques
- **Gestion des commandes** avec filtres par statut
- **Gestion des services** et catégories
- **Gestion des clients** et utilisateurs
- **Paramètres du site** configurables
- **Graphiques interactifs** (Chart.js)

### 🛡️ Sécurité
- **Protection SQL Injection** avec PDO préparé
- **Validation côté serveur** et client
- **Upload sécurisé** avec vérification de type
- **Sessions sécurisées** avec timeout
- **Échappement XSS** des données

## 📋 Prérequis

- **PHP 7.4+** (recommandé: PHP 8.0+)
- **MySQL 5.7+** ou **MariaDB 10.2+**
- **Apache** avec mod_rewrite activé
- **Extensions PHP** : PDO, PDO_MySQL, GD, FileInfo
- **HTTPS** recommandé pour la production

## 🚀 Installation sur Hostinger

### 1. Préparation des fichiers

1. **Téléchargez** tous les fichiers du projet
2. **Compressez** le dossier en `.zip`
3. **Connectez-vous** à votre cPanel Hostinger

### 2. Upload des fichiers

1. Allez dans **Gestionnaire de fichiers**
2. Naviguez vers le dossier `public_html`
3. **Uploadez** et **extrayez** le fichier zip
4. Assurez-vous que tous les fichiers sont dans `public_html`

### 3. Configuration de la base de données

1. Dans cPanel, allez dans **Bases de données MySQL**
2. **Créez une nouvelle base de données** : `smm_site`
3. **Créez un utilisateur** avec tous les privilèges
4. **Notez** les informations de connexion

### 4. Import de la base de données

1. Allez dans **phpMyAdmin**
2. Sélectionnez votre base de données
3. Cliquez sur **Importer**
4. **Uploadez** le fichier `database.sql`
5. Cliquez sur **Exécuter**

### 5. Configuration des paramètres

Éditez le fichier `config/database.php` avec vos informations :

```php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_nom_de_base');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

### 6. Permissions des dossiers

Assurez-vous que les dossiers suivants ont les bonnes permissions :

```bash
chmod 755 uploads/
chmod 755 uploads/payment_proofs/
chmod 644 config/database.php
```

### 7. Vérification de l'installation

1. Visitez votre site : `https://votre-domaine.com`
2. Vérifiez que la page d'accueil s'affiche correctement
3. Testez la connexion admin (voir ci-dessous)

## 👤 Comptes par défaut

### Administrateur
- **Email** : `admin@smmsite.com`
- **Mot de passe** : `admin123`

⚠️ **IMPORTANT** : Changez immédiatement ces identifiants après installation !

## 🔧 Configuration post-installation

### 1. Modifier les paramètres du site

Connectez-vous en tant qu'admin et allez dans **Paramètres** pour configurer :

- **Nom du site**
- **Description**
- **Informations de contact**
- **Numéros Mobile Money**
- **Instructions de paiement**

### 2. Ajouter vos services

1. Allez dans **Services** dans l'interface admin
2. **Modifiez** les catégories existantes ou **créez** de nouvelles
3. **Ajoutez** vos services avec prix et descriptions
4. **Activez/Désactivez** selon vos besoins

### 3. Personnaliser les prix

Les prix sont en **FCFA** par défaut. Modifiez dans la base de données ou l'interface admin.

### 4. Configurer Mobile Money

Mettez à jour les numéros Mobile Money dans **Paramètres > Site** :
- **MTN Money** : Votre numéro professionnel
- **Moov Money** : Votre numéro professionnel

## 📱 Utilisation

### Pour les clients

1. **Inscription** sur le site
2. **Sélection** du service souhaité
3. **Saisie** du lien à promouvoir
4. **Paiement** via Mobile Money
5. **Upload** de la preuve de paiement
6. **Suivi** de la commande dans le dashboard

### Pour l'administrateur

1. **Connexion** avec les identifiants admin
2. **Vérification** des nouvelles commandes
3. **Validation** des paiements
4. **Mise à jour** des statuts
5. **Gestion** des services et clients

## 🔧 Maintenance

### Sauvegardes

- **Base de données** : Exportez régulièrement via phpMyAdmin
- **Fichiers** : Sauvegardez le dossier `uploads/`
- **Configuration** : Sauvegardez `config/database.php`

### Mises à jour

- **PHP** : Maintenez PHP à jour
- **SSL** : Assurez-vous que le certificat SSL est valide
- **Sécurité** : Changez régulièrement les mots de passe admin

### Monitoring

- **Espace disque** : Surveillez l'espace dans `uploads/`
- **Performances** : Vérifiez les logs d'erreur
- **Sécurité** : Surveillez les tentatives de connexion

## 🐛 Dépannage

### Erreurs courantes

#### "Erreur de connexion à la base de données"
- Vérifiez les paramètres dans `config/database.php`
- Assurez-vous que la base de données existe
- Vérifiez les privilèges utilisateur

#### "Upload failed"
- Vérifiez les permissions du dossier `uploads/`
- Augmentez `upload_max_filesize` dans PHP
- Vérifiez `post_max_size` dans PHP

#### "Session expired"
- Vérifiez la configuration des sessions PHP
- Assurez-vous que les cookies sont activés
- Vérifiez `session.gc_maxlifetime`

### Logs d'erreur

Les erreurs PHP sont loggées dans :
- **cPanel** : Logs d'erreur
- **Fichier** : `error_log` dans le dossier principal

## 🔒 Sécurité

### Recommandations

1. **Changez** tous les mots de passe par défaut
2. **Activez** HTTPS/SSL
3. **Limitez** les tentatives de connexion
4. **Sauvegardez** régulièrement
5. **Mettez à jour** PHP et MySQL

### Fichiers sensibles

Protégez ces fichiers/dossiers :
- `config/database.php`
- `uploads/payment_proofs/`
- Interface admin (`/admin/`)

## 📞 Support

### Documentation technique

- **PHP** : [php.net](https://php.net)
- **MySQL** : [mysql.com](https://mysql.com)
- **Chart.js** : [chartjs.org](https://chartjs.org)

### Structure des fichiers

```
/
├── assets/
│   ├── css/style.css          # Styles principaux
│   └── js/main.js             # JavaScript principal
├── config/
│   └── database.php           # Configuration DB
├── includes/
│   └── functions.php          # Fonctions utilitaires
├── admin/                     # Interface administrateur
├── client/                    # Interface client
├── uploads/                   # Fichiers uploadés
├── database.sql               # Structure de la DB
├── index.php                  # Page d'accueil
├── login.php                  # Connexion
├── register.php               # Inscription
└── README.md                  # Ce fichier
```

## 📄 Licence

Ce projet est sous licence MIT. Vous êtes libre de l'utiliser, le modifier et le distribuer.

## 🤝 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :
- Signaler des bugs
- Proposer des améliorations
- Contribuer au code

---

**Développé avec ❤️ pour les professionnels du SMM**

**Version** : 1.0.0  
**Dernière mise à jour** : 2024

---

### 📞 Besoin d'aide ?

Si vous rencontrez des difficultés lors de l'installation ou de l'utilisation, n'hésitez pas à consulter la documentation ou à demander de l'aide.

**Bon succès avec votre plateforme SMM ! 🚀**