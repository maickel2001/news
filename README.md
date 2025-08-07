# 🕷️ TarantulaSMM Bénin

## 🎯 Description

**TarantulaSMM Bénin** est un SMM Panel professionnel et responsive conçu spécifiquement pour le marché béninois et international. La plateforme permet aux utilisateurs d'acheter des services de boost pour réseaux sociaux avec un système de paiement adapté aux habitudes locales (Mobile Money MTN/Moov).

## ✨ Fonctionnalités

### 👤 Côté Utilisateur
- **Page d'accueil moderne** avec design premium et responsive
- **Système d'inscription/connexion** sécurisé
- **Catalogue de services** organisé par catégories avec icônes Font Awesome
- **Commandes intuitives** avec upload de preuve de paiement obligatoire
- **Tableau de bord complet** avec suivi des commandes
- **Paiement Mobile Money** (MTN Money, Moov Money)
- **Support client** intégré avec système de tickets

### 🔐 Côté Admin
- **Dashboard professionnel** avec statistiques
- **Gestion complète des commandes** avec validation des preuves
- **Gestion des services et catégories**
- **Gestion des utilisateurs**
- **Système de support** avec réponses administrateur
- **Configuration dynamique** (numéros Mobile Money, SMTP)

## 🛠️ Technologies Utilisées

- **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript ES6+
- **Backend:** PHP 7.4+ (compatible Hostinger)
- **Base de données:** MySQL
- **Fonts:** Google Fonts (Inter), Font Awesome 6
- **Emails:** SMTP / Mailgun / SendGrid
- **Sécurité:** Protection XSS, validation côté serveur
- **Performance:** Compression GZIP, cache optimisé

## 📁 Structure du Projet

```
tarantulasmm-benin/
├── index.php                 # Page d'accueil
├── .htaccess                 # Configuration Apache
├── assets/
│   ├── css/
│   │   └── style.css         # Styles principaux
│   ├── js/
│   │   └── main.js           # JavaScript principal
│   └── images/               # Images et logos
├── config/
│   └── database.php          # Configuration BDD
├── includes/
│   ├── header.php            # En-tête commune
│   ├── footer.php            # Pied de page
│   └── functions.php         # Fonctions utilitaires
├── pages/
│   ├── login.php             # Connexion
│   ├── register.php          # Inscription
│   ├── dashboard.php         # Tableau de bord
│   ├── services.php          # Catalogue services
│   └── order.php             # Commande
├── admin/                    # Interface administration
└── uploads/                  # Preuves de paiement
```

## 🚀 Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Apache avec mod_rewrite activé
- Extensions PHP : mysqli, gd, curl

### Étapes d'installation

1. **Cloner le projet**
```bash
git clone https://github.com/votre-repo/tarantulasmm-benin.git
cd tarantulasmm-benin
```

2. **Configuration de la base de données**
```bash
# Créer la base de données
mysql -u root -p
CREATE DATABASE tarantulasmm_benin;
```

3. **Configuration Apache**
```bash
# Vérifier que mod_rewrite est activé
sudo a2enmod rewrite
sudo systemctl restart apache2
```

4. **Permissions des dossiers**
```bash
chmod 755 uploads/
chmod 644 .htaccess
```

5. **Configuration SMTP** (optionnel)
- Éditer `config/email.php`
- Configurer vos paramètres SMTP

## 🎨 Design & UX

### Palette de couleurs
- **Primaire:** `#6366f1` (Indigo moderne)
- **Secondaire:** `#f59e0b` (Orange Bénin)
- **Succès:** `#10b981` (Vert validation)
- **Danger:** `#ef4444` (Rouge erreur)

### Typography
- **Police principale:** Inter (Google Fonts)
- **Hiérarchie:** 8 niveaux de titres
- **Responsive:** Adaptation automatique mobile/desktop

### Animations
- **Fade-in** au scroll
- **Compteurs animés** pour les statistiques
- **Hover effects** sur les cartes et boutons
- **Smooth scrolling** pour la navigation

## 📱 Responsive Design

La plateforme est entièrement responsive avec des breakpoints optimisés :
- **Mobile:** < 576px
- **Tablet:** 576px - 768px
- **Desktop:** > 768px

## 🔒 Sécurité

### Mesures implémentées
- **Protection XSS** avec headers sécurisés
- **Validation côté serveur** pour tous les formulaires
- **Hash des mots de passe** avec password_hash()
- **Sessions sécurisées** avec httpOnly
- **Protection des fichiers** sensibles via .htaccess

### Headers de sécurité
```apache
X-Content-Type-Options: nosniff
X-Frame-Options: SAMEORIGIN
X-XSS-Protection: 1; mode=block
```

## ⚡ Performance

### Optimisations
- **Compression GZIP** pour tous les assets
- **Cache navigateur** configuré via .htaccess
- **CSS/JS minifiés** en production
- **Images optimisées** format WebP supporté
- **Lazy loading** pour les images non critiques

## 🌍 Internationalisation

- **Langue principale:** Français (marché béninois)
- **Format dates:** DD/MM/YYYY
- **Devise:** CFA (XOF)
- **Numérotation:** Format français

## 📞 Support & Contact

- **Email:** support@tarantulasmm.bj
- **WhatsApp:** +229 97 00 00 00
- **Adresse:** Cotonou, Bénin

## 🔄 Versions

### v1.0.0 (Actuelle)
- ✅ Page d'accueil responsive
- ✅ Design premium moderne
- ✅ Configuration Apache optimisée
- ✅ Animations et interactions
- ✅ Structure MVC propre

### v1.1.0 (Prochaine)
- 🔲 Système d'authentification
- 🔲 Catalogue de services
- 🔲 Système de commandes
- 🔲 Interface administration

## 📄 Licence

© 2024 TarantulaSMM Bénin. Tous droits réservés.

## 🤝 Contribution

Pour contribuer au projet :
1. Fork le repository
2. Créer une branche feature (`git checkout -b feature/nouvelle-fonctionnalite`)
3. Commit les changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/nouvelle-fonctionnalite`)
5. Créer une Pull Request

---

**Fait avec ❤️ au Bénin** 🇧🇯