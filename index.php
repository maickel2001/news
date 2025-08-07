<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Statistiques dynamiques
try {
    $db = Database::getInstance();
    
    $stats_queries = [
        'total_orders' => "SELECT COUNT(*) FROM orders WHERE status = 'completed'",
        'active_users' => "SELECT COUNT(*) FROM users WHERE status = 'active'",
        'total_services' => "SELECT COUNT(*) FROM services WHERE status = 'active'",
        'avg_rating' => "SELECT COALESCE(AVG(rating), 4.8) FROM reviews"
    ];
    
    $stats = [];
    foreach ($stats_queries as $key => $query) {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stats[$key] = $stmt->fetchColumn();
    }
    
    // Formater les statistiques
    $stats['total_orders'] = number_format($stats['total_orders']);
    $stats['active_users'] = number_format($stats['active_users']);
    $stats['total_services'] = number_format($stats['total_services']);
    $stats['avg_rating'] = number_format($stats['avg_rating'], 1);
    
} catch (Exception $e) {
    $stats = [
        'total_orders' => '25,000+',
        'active_users' => '5,000+',
        'total_services' => '50+',
        'avg_rating' => '4.8'
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TarantulaSMM Bénin - Boost tes Réseaux Sociaux 🔥</title>
    <meta name="description" content="La plateforme SMM #1 au Bénin. Boostez vos réseaux sociaux avec nos services ultra-rapides : Instagram, TikTok, Facebook, YouTube. Paiement Mobile Money.">
    
    <!-- Open Graph -->
    <meta property="og:title" content="TarantulaSMM Bénin - Boost tes Réseaux Sociaux">
    <meta property="og:description" content="Services SMM premium au Bénin. Livraison rapide, qualité garantie, paiement Mobile Money.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://tarantulasmm.bj">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-spider"></i>
                TarantulaSMM
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#accueil">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistiques">Statistiques</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex gap-2 ms-3">
                    <?php if (isLoggedIn()): ?>
                        <a href="dashboard.php" class="btn btn-outline">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a href="logout.php" class="btn btn-secondary">
                            <i class="fas fa-sign-out-alt"></i>
                            Déconnexion
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">
                            <i class="fas fa-sign-in-alt"></i>
                            Connexion
                        </a>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            S'inscrire
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="accueil">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <div class="hero-badge">
                            <i class="fas fa-star"></i>
                            Plateforme #1 au Bénin
                        </div>
                        
                        <h1 class="hero-title">
                            Boost tes <span class="highlight">Réseaux Sociaux</span> comme jamais !
                        </h1>
                        
                        <p class="hero-subtitle">
                            Transforme ta présence digitale avec TarantulaSMM. Services ultra-rapides, 
                            qualité premium, paiement Mobile Money. Rejoins plus de 5000+ créateurs satisfaits !
                        </p>
                        
                        <div class="hero-actions">
                            <a href="register.php" class="btn btn-primary btn-xl">
                                <i class="fas fa-rocket"></i>
                                Commencer Maintenant
                            </a>
                            <a href="#services" class="btn btn-outline btn-xl">
                                <i class="fas fa-play"></i>
                                Voir les Services
                            </a>
                        </div>
                        
                        <div class="hero-stats">
                            <div class="hero-stat">
                                <span class="hero-stat-number" data-count="25000">0</span>
                                <span class="hero-stat-label">Commandes livrées</span>
                            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-number" data-count="5000">0</span>
                                <span class="hero-stat-label">Clients satisfaits</span>
                            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-number" data-count="50">0</span>
                                <span class="hero-stat-label">Services disponibles</span>
                            </div>
                            <div class="hero-stat">
                                <span class="hero-stat-number" data-count="4.8">0</span>
                                <span class="hero-stat-label">Note moyenne</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-visual fade-in-right">
                        <div class="social-logos">
                            <div class="social-logo instagram">
                                <i class="fab fa-instagram"></i>
                            </div>
                            <div class="social-logo tiktok">
                                <i class="fab fa-tiktok"></i>
                            </div>
                            <div class="social-logo facebook">
                                <i class="fab fa-facebook"></i>
                            </div>
                            <div class="social-logo youtube">
                                <i class="fab fa-youtube"></i>
                            </div>
                            <div class="social-logo twitter">
                                <i class="fab fa-twitter"></i>
                            </div>
                            <div class="social-logo linkedin">
                                <i class="fab fa-linkedin"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section class="services" id="services">
        <div class="container">
            <div class="services-header">
                <h2 class="fade-in">Nos Services Premium</h2>
                <p class="fade-in">Découvre notre gamme complète de services SMM pour booster ta présence sur tous les réseaux sociaux</p>
            </div>
            
            <div class="services-grid">
                <div class="service-card fade-in">
                    <div class="service-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3 class="service-title">Instagram Boost</h3>
                    <p class="service-description">
                        Followers, likes, vues, commentaires de qualité premium pour faire exploser ton Instagram
                    </p>
                    <ul class="service-features">
                        <li>Followers français & internationaux</li>
                        <li>Likes & commentaires réels</li>
                        <li>Vues stories & reels</li>
                        <li>Livraison en moins de 30 min</li>
                    </ul>
                    <div class="service-price">À partir de 500 FCFA</div>
                    <a href="register.php" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i>
                        Commander
                    </a>
                </div>
                
                <div class="service-card fade-in">
                    <div class="service-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3 class="service-title">TikTok Explosion</h3>
                    <p class="service-description">
                        Devient viral sur TikTok avec nos services de followers, likes et vues ultra-rapides
                    </p>
                    <ul class="service-features">
                        <li>Followers actifs TikTok</li>
                        <li>Likes & partages massifs</li>
                        <li>Vues vidéos garanties</li>
                        <li>Boost algorithme TikTok</li>
                    </ul>
                    <div class="service-price">À partir de 300 FCFA</div>
                    <a href="register.php" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i>
                        Commander
                    </a>
                </div>
                
                <div class="service-card fade-in">
                    <div class="service-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3 class="service-title">Facebook Pro</h3>
                    <p class="service-description">
                        Développe ta page Facebook avec des likes, partages et commentaires authentiques
                    </p>
                    <ul class="service-features">
                        <li>Likes page Facebook</li>
                        <li>Partages & commentaires</li>
                        <li>Vues vidéos FB</li>
                        <li>Engagement ciblé</li>
                    </ul>
                    <div class="service-price">À partir de 400 FCFA</div>
                    <a href="register.php" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i>
                        Commander
                    </a>
                </div>
                
                <div class="service-card fade-in">
                    <div class="service-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3 class="service-title">YouTube Growth</h3>
                    <p class="service-description">
                        Fais exploser ta chaîne YouTube avec des abonnés, vues et likes de qualité
                    </p>
                    <ul class="service-features">
                        <li>Abonnés YouTube réels</li>
                        <li>Vues vidéos ciblées</li>
                        <li>Likes & commentaires</li>
                        <li>Amélioration SEO</li>
                    </ul>
                    <div class="service-price">À partir de 600 FCFA</div>
                    <a href="register.php" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i>
                        Commander
                    </a>
                </div>
                
                <div class="service-card fade-in">
                    <div class="service-icon">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <h3 class="service-title">Twitter Power</h3>
                    <p class="service-description">
                        Booste ton influence Twitter avec des followers, retweets et likes ciblés
                    </p>
                    <ul class="service-features">
                        <li>Followers Twitter actifs</li>
                        <li>Retweets & mentions</li>
                        <li>Likes tweets</li>
                        <li>Visibilité maximale</li>
                    </ul>
                    <div class="service-price">À partir de 350 FCFA</div>
                    <a href="register.php" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i>
                        Commander
                    </a>
                </div>
                
                <div class="service-card fade-in">
                    <div class="service-icon">
                        <i class="fab fa-linkedin"></i>
                    </div>
                    <h3 class="service-title">LinkedIn Business</h3>
                    <p class="service-description">
                        Développe ton réseau professionnel avec des connexions et likes LinkedIn
                    </p>
                    <ul class="service-features">
                        <li>Connexions professionnelles</li>
                        <li>Likes publications</li>
                        <li>Partages LinkedIn</li>
                        <li>Visibilité B2B</li>
                    </ul>
                    <div class="service-price">À partir de 800 FCFA</div>
                    <a href="register.php" class="btn btn-primary w-100">
                        <i class="fas fa-shopping-cart"></i>
                        Commander
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats" id="statistiques">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="<?= str_replace([',' , '+'], '', $stats['total_orders']) ?>">0</span>
                    <span class="stat-label">Commandes Livrées</span>
                </div>
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="<?= str_replace([',' , '+'], '', $stats['active_users']) ?>">0</span>
                    <span class="stat-label">Clients Satisfaits</span>
                </div>
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="<?= str_replace([',' , '+'], '', $stats['total_services']) ?>">0</span>
                    <span class="stat-label">Services Disponibles</span>
                </div>
                <div class="stat-item fade-in">
                    <span class="stat-number" data-count="<?= $stats['avg_rating'] ?>">0</span>
                    <span class="stat-label">Note Moyenne</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <div class="cta-content fade-in">
                <h2 class="cta-title">Prêt à Exploser sur les Réseaux ?</h2>
                <p class="cta-subtitle">
                    Rejoins plus de 5000+ créateurs béninois qui font confiance à TarantulaSMM pour booster leur présence digitale
                </p>
                <div class="d-flex flex-wrap gap-3 justify-content-center">
                    <a href="register.php" class="btn btn-dark btn-xl">
                        <i class="fas fa-rocket"></i>
                        Créer mon Compte Gratuitement
                    </a>
                    <a href="#contact" class="btn btn-outline btn-xl">
                        <i class="fab fa-whatsapp"></i>
                        Contacter sur WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <div class="footer-brand">
                        <i class="fas fa-spider"></i>
                        TarantulaSMM
                    </div>
                    <p class="footer-description">
                        La plateforme SMM #1 au Bénin. Nous aidons les créateurs et entreprises à développer 
                        leur présence sur les réseaux sociaux avec des services de qualité premium.
                    </p>
                    <div class="footer-social">
                        <a href="#" aria-label="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" aria-label="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" aria-label="TikTok">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://wa.me/22997000000" aria-label="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Services</h4>
                    <ul class="footer-links">
                        <li><a href="#services">Instagram Boost</a></li>
                        <li><a href="#services">TikTok Explosion</a></li>
                        <li><a href="#services">Facebook Pro</a></li>
                        <li><a href="#services">YouTube Growth</a></li>
                        <li><a href="#services">Twitter Power</a></li>
                        <li><a href="#services">LinkedIn Business</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul class="footer-links">
                        <li><a href="#contact">Nous Contacter</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="#aide">Centre d'Aide</a></li>
                        <li><a href="#garanties">Nos Garanties</a></li>
                        <li><a href="#temoignages">Témoignages</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact</h4>
                    <ul class="footer-links">
                        <li>
                            <i class="fas fa-phone text-orange me-2"></i>
                            +229 97 00 00 00
                        </li>
                        <li>
                            <i class="fas fa-envelope text-orange me-2"></i>
                            contact@tarantulasmm.bj
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt text-orange me-2"></i>
                            Cotonou, Bénin
                        </li>
                        <li>
                            <i class="fas fa-clock text-orange me-2"></i>
                            24h/7j - Support disponible
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 TarantulaSMM Bénin. Tous droits réservés. | 
                   <a href="#privacy" class="text-orange">Politique de confidentialité</a> | 
                   <a href="#terms" class="text-orange">Conditions d'utilisation</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('[data-count]');
            
            counters.forEach(counter => {
                const target = parseFloat(counter.getAttribute('data-count'));
                const increment = target / 100;
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    
                    // Format number based on target value
                    if (target >= 1000) {
                        counter.textContent = Math.floor(current).toLocaleString() + '+';
                    } else if (target % 1 !== 0) {
                        counter.textContent = current.toFixed(1);
                    } else {
                        counter.textContent = Math.floor(current);
                    }
                }, 20);
            });
        }
        
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    
                    // Start counter animation when stats section is visible
                    if (entry.target.closest('.stats')) {
                        animateCounters();
                    }
                }
            });
        }, observerOptions);
        
        // Observe fade-in elements
        document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(30px)';
            observer.observe(el);
        });
        
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80;
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Social logos interaction
        document.querySelectorAll('.social-logo').forEach(logo => {
            logo.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.1) rotate(5deg)';
            });
            
            logo.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
            
            logo.addEventListener('click', function() {
                // Add click animation
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });
        
        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            if (hero) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
        
        // Loading animation
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });
        
        // Service card hover effects
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.querySelector('.service-icon').style.transform = 'rotate(10deg) scale(1.1)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.querySelector('.service-icon').style.transform = '';
            });
        });
        
        // CTA button pulse animation
        setInterval(() => {
            const ctaBtn = document.querySelector('.cta .btn-dark');
            if (ctaBtn) {
                ctaBtn.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    ctaBtn.style.transform = '';
                }, 200);
            }
        }, 3000);
    </script>
</body>
</html>