<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TarantulaSMM Bénin - Boost tes Réseaux Sociaux au Bénin</title>
    <meta name="description" content="Le meilleur SMM Panel au Bénin. Services de boost pour Instagram, TikTok, Facebook, YouTube. Paiement Mobile Money MTN/Moov accepté.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --primary-light: #818cf8;
            --secondary-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --dark-color: #0f172a;
            --light-color: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-soft: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-medium: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --shadow-large: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            overflow-x: hidden;
        }

        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }

        .navbar.scrolled {
            background: rgba(255, 255, 255, 0.98);
            box-shadow: var(--shadow-soft);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            font-weight: 500;
            color: var(--dark-color) !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
            transform: translateY(-1px);
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: var(--gradient-primary);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .btn-primary-custom {
            background: var(--gradient-primary);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-soft);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        .hero-section {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="150" fill="url(%23a)"/><circle cx="800" cy="300" r="100" fill="url(%23a)"/><circle cx="300" cy="700" r="120" fill="url(%23a)"/><circle cx="700" cy="800" r="80" fill="url(%23a)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
            font-weight: 400;
        }

        .stats-section {
            background: white;
            padding: 5rem 0;
            position: relative;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: white;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            color: #64748b;
            font-weight: 500;
        }

        .features-section {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 5rem 0;
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-large);
        }

        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 15px;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: white;
            font-size: 1.8rem;
        }

        .feature-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .feature-description {
            color: #64748b;
            line-height: 1.6;
        }

        .cta-section {
            background: var(--gradient-primary);
            padding: 5rem 0;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2rem;
        }

        .btn-cta {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 1rem 3rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cta:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            color: white;
            box-shadow: var(--shadow-medium);
        }

        .footer {
            background: var(--dark-color);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-bottom: 0.5rem;
            display: block;
        }

        .footer-links a:hover {
            color: var(--primary-light);
            transform: translateX(5px);
        }

        .social-links a {
            display: inline-block;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            text-align: center;
            line-height: 45px;
            color: white;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            background: var(--primary-color);
            transform: translateY(-3px);
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .stat-number {
                font-size: 2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
        }

        .floating-animation {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-spider"></i> TarantulaSMM</a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#accueil">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#avantages">Avantages</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex gap-2">
                    <a href="login.php" class="btn btn-outline-primary">Connexion</a>
                    <a href="register.php" class="btn btn-primary-custom">S'inscrire</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="accueil" class="hero-section">
        <!-- Particules d'arrière-plan -->
        <div class="particles-bg">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
        
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content fade-in-up">
                        <div class="hero-badge">
                            <i class="fas fa-crown me-2"></i>#1 SMM Panel au Bénin
                        </div>
                        <h1 class="hero-title">
                            Booste tes <span class="gradient-text">Réseaux Sociaux</span> 
                            au <span class="benin-flag">Bénin</span>
                        </h1>
                        <p class="hero-subtitle">
                            Le SMM Panel de référence pour tous les créateurs béninois. Followers, likes, vues et plus encore pour 
                            Instagram, TikTok, Facebook, YouTube. Paiement sécurisé via Mobile Money MTN/Moov.
                        </p>
                        
                        <!-- Statistiques rapides -->
                        <div class="hero-stats">
                            <div class="stat-item">
                                <span class="stat-number">10K+</span>
                                <span class="stat-label">Clients satisfaits</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">24h</span>
                                <span class="stat-label">Support béninois</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">50+</span>
                                <span class="stat-label">Services disponibles</span>
                            </div>
                        </div>
                        
                        <div class="hero-actions">
                            <a href="register.php" class="btn btn-cta pulse-animation">
                                <i class="fas fa-rocket me-2"></i>Commencer Maintenant
                                <span class="btn-shine"></span>
                            </a>
                            <a href="#services" class="btn btn-outline-light glass-btn">
                                <i class="fas fa-play me-2"></i>Voir la Démo
                            </a>
                        </div>
                        
                        <!-- Badges de confiance -->
                        <div class="trust-badges">
                            <div class="badge-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>100% Sécurisé</span>
                            </div>
                            <div class="badge-item">
                                <i class="fas fa-bolt"></i>
                                <span>Livraison Rapide</span>
                            </div>
                            <div class="badge-item">
                                <i class="fas fa-heart"></i>
                                <span>Support 24/7</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-visual">
                        <!-- Téléphone 3D mockup -->
                        <div class="phone-mockup">
                            <div class="phone-screen">
                                <div class="social-icons-float">
                                    <div class="social-icon instagram">
                                        <i class="fab fa-instagram"></i>
                                    </div>
                                    <div class="social-icon tiktok">
                                        <i class="fab fa-tiktok"></i>
                                    </div>
                                    <div class="social-icon facebook">
                                        <i class="fab fa-facebook"></i>
                                    </div>
                                    <div class="social-icon youtube">
                                        <i class="fab fa-youtube"></i>
                                    </div>
                                </div>
                                
                                <!-- Notifications de boost -->
                                <div class="boost-notifications">
                                    <div class="notification n1">
                                        <i class="fas fa-heart text-danger"></i>
                                        +100 Likes reçus
                                    </div>
                                    <div class="notification n2">
                                        <i class="fas fa-users text-primary"></i>
                                        +50 Followers
                                    </div>
                                    <div class="notification n3">
                                        <i class="fas fa-eye text-success"></i>
                                        +1000 Vues
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Éléments décoratifs -->
                        <div class="floating-elements">
                            <div class="float-icon f1"><i class="fas fa-hashtag"></i></div>
                            <div class="float-icon f2"><i class="fas fa-at"></i></div>
                            <div class="float-icon f3"><i class="fas fa-thumbs-up"></i></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Indicateur de scroll -->
        <div class="scroll-indicator">
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
            <span>Scroll pour découvrir</span>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card fade-in-up">
                        <div class="stat-number" data-count="15000">0</div>
                        <div class="stat-label">Commandes Traitées</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card fade-in-up">
                        <div class="stat-number" data-count="2500">0</div>
                        <div class="stat-label">Clients Satisfaits</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card fade-in-up">
                        <div class="stat-number" data-count="98">0</div>
                        <div class="stat-label">% de Réussite</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stat-card fade-in-up">
                        <div class="stat-number" data-count="24">0</div>
                        <div class="stat-label">Heures Support</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="avantages" class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-4 fw-bold mb-3">Pourquoi Choisir TarantulaSMM ?</h2>
                    <p class="lead text-muted">Des services de qualité premium adaptés au marché béninois</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3 class="feature-title">Paiement Mobile Money</h3>
                        <p class="feature-description">
                            Paiement facile et sécurisé via MTN Money et Moov Money. 
                            Adapté aux habitudes de paiement béninoises.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 class="feature-title">Livraison Rapide</h3>
                        <p class="feature-description">
                            Commandes traitées en moins de 24h. 
                            Suivi en temps réel et notifications automatiques.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3 class="feature-title">100% Sécurisé</h3>
                        <p class="feature-description">
                            Vos données sont protégées. Pas de mot de passe requis 
                            pour vos comptes sociaux.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3 class="feature-title">Support 24/7</h3>
                        <p class="feature-description">
                            Notre équipe béninoise vous accompagne 24h/24 
                            via WhatsApp, email et système de tickets.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Qualité Premium</h3>
                        <p class="feature-description">
                            Followers et interactions de haute qualité. 
                            Comptes réels, engagement authentique.
                        </p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="feature-card fade-in-up">
                        <div class="feature-icon">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="feature-title">Prix Compétitifs</h3>
                        <p class="feature-description">
                            Les meilleurs tarifs du marché béninois. 
                            Promotions régulières et packs avantageux.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="cta-title">Prêt à Booster ton Influence ?</h2>
                    <p class="cta-subtitle">
                        Rejoins des milliers de créateurs béninois qui nous font confiance
                    </p>
                    <a href="register.php" class="btn-cta">
                        <i class="fas fa-user-plus me-2"></i>Créer mon Compte Gratuitement
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-spider me-2"></i>TarantulaSMM Bénin
                    </h5>
                    <p class="text-muted">
                        Le meilleur SMM Panel du Bénin. Boost professionnel 
                        pour tous tes réseaux sociaux avec paiement Mobile Money.
                    </p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Services</h6>
                    <div class="footer-links">
                        <a href="#">Instagram</a>
                        <a href="#">TikTok</a>
                        <a href="#">Facebook</a>
                        <a href="#">YouTube</a>
                        <a href="#">Spotify</a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="fw-bold mb-3">Liens Utiles</h6>
                    <div class="footer-links">
                        <a href="faq.php">FAQ</a>
                        <a href="how-to-pay.php">Comment Payer</a>
                        <a href="terms.php">Conditions</a>
                        <a href="privacy.php">Confidentialité</a>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3">Contact</h6>
                    <div class="footer-links">
                        <a href="mailto:support@tarantulasmm.bj">
                            <i class="fas fa-envelope me-2"></i>support@tarantulasmm.bj
                        </a>
                        <a href="https://wa.me/22997000000">
                            <i class="fab fa-whatsapp me-2"></i>+229 97 00 00 00
                        </a>
                        <a href="#">
                            <i class="fas fa-map-marker-alt me-2"></i>Cotonou, Bénin
                        </a>
                    </div>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: #334155;">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        &copy; 2024 TarantulaSMM Bénin. Tous droits réservés.
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">
                        <i class="fas fa-heart text-danger"></i> Fait avec amour au Bénin
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('.stat-number[data-count]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const increment = target / (duration / 16);
                let current = 0;
                
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString();
                    }
                }, 16);
            });
        }

        // Fade in animation
        function fadeInOnScroll() {
            const elements = document.querySelectorAll('.fade-in-up');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Initialize animations
        window.addEventListener('scroll', fadeInOnScroll);
        window.addEventListener('load', () => {
            fadeInOnScroll();
            setTimeout(animateCounters, 500);
        });

        // Mobile menu auto-close
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(navbarCollapse).hide();
                }
            });
        });
    </script>
</body>
</html>