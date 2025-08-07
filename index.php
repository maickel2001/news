<?php
require_once 'includes/functions.php';

// Récupérer les paramètres du site
$pdo = getDBConnection();
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// Récupérer les catégories et services
$categories = getServicesByCategory();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $settings['site_name'] ?? 'SMM Pro Services'; ?></title>
    <meta name="description" content="<?php echo $settings['site_description'] ?? 'Votre partenaire pour booster votre présence sur les réseaux sociaux'; ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjMyIiBoZWlnaHQ9IjMyIiByeD0iOCIgZmlsbD0iIzAwZmY4OCIvPgo8cGF0aCBkPSJNOCAxNkwxNCAxNkwxNiA4TDE4IDE2TDI0IDE2IiBzdHJva2U9IiMwZjBmMjMiIHN0cm9rZS13aWR0aD0iMiIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIiBzdHJva2UtbGluZWpvaW49InJvdW5kIi8+Cjwvc3ZnPgo=">
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="index.php" class="logo">
                <i class="fas fa-rocket"></i>
                <?php echo $settings['site_name'] ?? 'SMM Pro'; ?>
            </a>
            
            <ul class="nav-menu">
                <li><a href="#accueil">Accueil</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#a-propos">À propos</a></li>
                <li><a href="#contact">Contact</a></li>
                <?php if (isLoggedIn()): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/dashboard.php">Admin</a></li>
                    <?php else: ?>
                        <li><a href="client/dashboard.php">Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="login.php">Connexion</a></li>
                    <li><a href="register.php" class="btn btn-primary btn-sm">S'inscrire</a></li>
                <?php endif; ?>
            </ul>
            
            <button class="nav-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main>
        <!-- Hero Section -->
        <section id="accueil" class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Boostez votre présence sur les réseaux sociaux</h1>
                    <p><?php echo $settings['site_description'] ?? 'Des services de qualité pour développer votre audience et augmenter votre engagement sur toutes les plateformes sociales.'; ?></p>
                    <div class="hero-buttons">
                        <?php if (isLoggedIn()): ?>
                            <a href="client/order.php" class="btn btn-primary">Passer une commande</a>
                            <a href="client/dashboard.php" class="btn btn-secondary">Mon dashboard</a>
                        <?php else: ?>
                            <a href="register.php" class="btn btn-primary">Commencer maintenant</a>
                            <a href="#services" class="btn btn-secondary">Découvrir nos services</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="container">
            <div class="text-center mb-4">
                <h2 class="text-primary" style="font-size: 2.5rem; margin-bottom: 1rem;">Nos Services</h2>
                <p class="text-muted" style="font-size: 1.1rem; max-width: 600px; margin: 0 auto;">
                    Découvrez notre gamme complète de services pour développer votre présence sur tous les réseaux sociaux
                </p>
            </div>

            <div class="services-grid">
                <?php foreach ($categories as $category): ?>
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="<?php echo $category['icon'] ?? 'fas fa-share-alt'; ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        <p><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                        
                        <?php if (!empty($category['services'])): ?>
                            <div class="services-list" style="text-align: left; margin-top: 1rem;">
                                <?php foreach (array_slice($category['services'], 0, 3) as $service): ?>
                                    <div class="service-item" style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; padding: 0.5rem; background: var(--darker-bg); border-radius: 8px;">
                                        <span style="color: var(--text-secondary); font-size: 0.9rem;"><?php echo htmlspecialchars($service['name']); ?></span>
                                        <span style="color: var(--primary-color); font-weight: 600; font-size: 0.9rem;"><?php echo formatPrice($service['price']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                                <?php if (count($category['services']) > 3): ?>
                                    <div class="text-center mt-2">
                                        <small class="text-muted">+<?php echo count($category['services']) - 3; ?> autres services</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <?php if (isLoggedIn()): ?>
                                <a href="client/order.php" class="btn btn-primary btn-sm">Commander</a>
                            <?php else: ?>
                                <a href="register.php" class="btn btn-primary btn-sm">S'inscrire pour commander</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Features Section -->
        <section class="container mt-4">
            <div class="text-center mb-4">
                <h2 class="text-primary" style="font-size: 2.5rem; margin-bottom: 1rem;">Pourquoi nous choisir ?</h2>
            </div>

            <div class="row">
                <div class="col-4">
                    <div class="card text-center">
                        <div class="service-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Livraison rapide</h3>
                        <p>Vos commandes sont traitées rapidement avec des résultats visibles sous 24-48h</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card text-center">
                        <div class="service-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>100% Sécurisé</h3>
                        <p>Toutes nos méthodes sont sûres et respectent les conditions d'utilisation des plateformes</p>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card text-center">
                        <div class="service-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Support 24/7</h3>
                        <p>Notre équipe support est disponible pour vous accompagner à tout moment</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How it works Section -->
        <section id="a-propos" class="container mt-4">
            <div class="text-center mb-4">
                <h2 class="text-primary" style="font-size: 2.5rem; margin-bottom: 1rem;">Comment ça marche ?</h2>
            </div>

            <div class="row">
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--primary-color); font-size: 2rem;">
                            <span style="background: var(--primary-color); color: var(--dark-bg); width: 50px; height: 50px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">1</span>
                        </div>
                        <h4>Inscrivez-vous</h4>
                        <p>Créez votre compte gratuitement en quelques clics</p>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--primary-color); font-size: 2rem;">
                            <span style="background: var(--primary-color); color: var(--dark-bg); width: 50px; height: 50px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">2</span>
                        </div>
                        <h4>Choisissez votre service</h4>
                        <p>Sélectionnez le service qui correspond à vos besoins</p>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--primary-color); font-size: 2rem;">
                            <span style="background: var(--primary-color); color: var(--dark-bg); width: 50px; height: 50px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">3</span>
                        </div>
                        <h4>Payez via Mobile Money</h4>
                        <p>Effectuez votre paiement sécurisé par MTN ou Moov Money</p>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--primary-color); font-size: 2rem;">
                            <span style="background: var(--primary-color); color: var(--dark-bg); width: 50px; height: 50px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700;">4</span>
                        </div>
                        <h4>Recevez vos résultats</h4>
                        <p>Profitez de l'augmentation de votre audience</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="container mt-4">
            <div class="text-center mb-4">
                <h2 class="text-primary" style="font-size: 2.5rem; margin-bottom: 1rem;">Contactez-nous</h2>
                <p class="text-muted" style="font-size: 1.1rem;">
                    Une question ? Notre équipe est là pour vous aider
                </p>
            </div>

            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <h3 class="card-title">Informations de contact</h3>
                        <div class="contact-info">
                            <div class="contact-item" style="display: flex; align-items: center; margin-bottom: 1rem;">
                                <i class="fas fa-envelope" style="color: var(--primary-color); margin-right: 1rem; width: 20px;"></i>
                                <span><?php echo $settings['contact_email'] ?? 'contact@smmsite.com'; ?></span>
                            </div>
                            <div class="contact-item" style="display: flex; align-items: center; margin-bottom: 1rem;">
                                <i class="fas fa-phone" style="color: var(--primary-color); margin-right: 1rem; width: 20px;"></i>
                                <span><?php echo $settings['contact_phone'] ?? '+226 70 00 00 00'; ?></span>
                            </div>
                            <div class="contact-item" style="display: flex; align-items: center; margin-bottom: 1rem;">
                                <i class="fab fa-whatsapp" style="color: var(--primary-color); margin-right: 1rem; width: 20px;"></i>
                                <span>WhatsApp : <?php echo $settings['contact_phone'] ?? '+226 70 00 00 00'; ?></span>
                            </div>
                        </div>

                        <div class="payment-info mt-3" style="background: var(--darker-bg); padding: 1.5rem; border-radius: var(--border-radius);">
                            <h4 class="text-primary mb-2">Paiement Mobile Money</h4>
                            <div class="payment-method" style="margin-bottom: 1rem;">
                                <i class="fas fa-mobile-alt" style="color: #FFD700; margin-right: 0.5rem;"></i>
                                <strong>MTN Money :</strong> <?php echo $settings['mtn_money_number'] ?? '70 00 00 00'; ?>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-mobile-alt" style="color: #FF6B35; margin-right: 0.5rem;"></i>
                                <strong>Moov Money :</strong> <?php echo $settings['moov_money_number'] ?? '60 00 00 00'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card">
                        <h3 class="card-title">Envoyez-nous un message</h3>
                        <form action="contact.php" method="POST">
                            <div class="form-group">
                                <label for="name" class="form-label">Nom complet</label>
                                <input type="text" id="name" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="subject" class="form-label">Sujet</label>
                                <input type="text" id="subject" name="subject" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="message" class="form-label">Message</label>
                                <textarea id="message" name="message" class="form-control" rows="4" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                                Envoyer le message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?php echo $settings['site_name'] ?? 'SMM Pro Services'; ?></h3>
                    <p><?php echo $settings['site_description'] ?? 'Votre partenaire pour booster votre présence sur les réseaux sociaux'; ?></p>
                    <div class="social-links" style="margin-top: 1rem;">
                        <a href="#" style="margin-right: 1rem; color: var(--primary-color);"><i class="fab fa-facebook"></i></a>
                        <a href="#" style="margin-right: 1rem; color: var(--primary-color);"><i class="fab fa-twitter"></i></a>
                        <a href="#" style="margin-right: 1rem; color: var(--primary-color);"><i class="fab fa-instagram"></i></a>
                        <a href="#" style="margin-right: 1rem; color: var(--primary-color);"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Services</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach (array_slice($categories, 0, 5) as $category): ?>
                            <li style="margin-bottom: 0.5rem;">
                                <a href="#services"><?php echo htmlspecialchars($category['name']); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Liens utiles</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 0.5rem;"><a href="#accueil">Accueil</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="#services">Services</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="#a-propos">À propos</a></li>
                        <li style="margin-bottom: 0.5rem;"><a href="#contact">Contact</a></li>
                        <?php if (!isLoggedIn()): ?>
                            <li style="margin-bottom: 0.5rem;"><a href="register.php">S'inscrire</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p><i class="fas fa-envelope"></i> <?php echo $settings['contact_email'] ?? 'contact@smmsite.com'; ?></p>
                    <p><i class="fas fa-phone"></i> <?php echo $settings['contact_phone'] ?? '+226 70 00 00 00'; ?></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo $settings['site_name'] ?? 'SMM Pro Services'; ?>. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
</body>
</html>