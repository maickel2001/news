<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TarantulaSMM Bénin - SMM Panel #1 au Bénin</title>
    <meta name="description" content="Le SMM Panel de référence au Bénin. Followers, likes, vues pour Instagram, TikTok, Facebook, YouTube. Paiement Mobile Money MTN/Moov.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #f093fb;
            --dark: #1a1a1a;
            --light: #f8f9fa;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary) !important;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark) !important;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 8rem 0 4rem;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
            line-height: 1.1;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .btn-hero {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            border-radius: 30px;
            font-weight: 600;
            margin: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-outline-light:hover {
            background: white;
            color: var(--primary) !important;
        }
        
        /* Stats */
        .stats {
            padding: 4rem 0;
            background: white;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 2rem;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        /* Services */
        .services {
            padding: 5rem 0;
            background: var(--light);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
            border-top: 3px solid var(--primary);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        
        .service-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 1.5rem;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }
        
        .service-card h4 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .service-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .service-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        /* CTA */
        .cta {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 5rem 0;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .cta p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer h5 {
            color: var(--warning);
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .footer a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--warning);
        }
        
        .footer-bottom {
            border-top: 1px solid #333;
            margin-top: 2rem;
            padding-top: 2rem;
            text-align: center;
            color: #ccc;
        }
        
        .social-links a {
            display: inline-block;
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            margin: 0 0.25rem;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .btn-hero {
                display: block;
                width: 100%;
                margin: 0.5rem 0;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
        }
        
        /* Animations */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-spider me-2"></i>TarantulaSMM
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                        <a class="nav-link" href="#prix">Prix</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
                
                <div class="d-flex">
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="accueil" class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="fade-in">
                        <h1 class="hero-title">
                            Booste tes <span style="color: #ffc107;">Réseaux Sociaux</span> au Bénin
                        </h1>
                        <p class="hero-subtitle">
                            Le SMM Panel de référence pour tous les créateurs béninois. Followers, likes, vues et plus encore pour Instagram, TikTok, Facebook, YouTube.
                        </p>
                        <div class="mt-4">
                            <a href="register.php" class="btn btn-warning btn-hero">
                                <i class="fas fa-rocket me-2"></i>Commencer Maintenant
                            </a>
                            <a href="#services" class="btn btn-outline-light btn-hero">
                                <i class="fas fa-play me-2"></i>Voir les Services
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center fade-in">
                        <i class="fas fa-mobile-alt" style="font-size: 15rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-6">
                    <div class="stat-card fade-in">
                        <span class="stat-number">15K+</span>
                        <div class="stat-label">Clients Satisfaits</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card fade-in">
                        <span class="stat-number">24h</span>
                        <div class="stat-label">Support Béninois</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card fade-in">
                        <span class="stat-number">99%</span>
                        <div class="stat-label">Taux de Réussite</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="stat-card fade-in">
                        <span class="stat-number">50+</span>
                        <div class="stat-label">Services Disponibles</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Nos Services</h2>
                <p class="lead">Des services de qualité pour tous les réseaux sociaux</p>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <h4>Instagram</h4>
                        <p>Followers, likes, vues stories, commentaires de qualité</p>
                        <div class="service-price">À partir de 500 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: linear-gradient(45deg, #ff0050, #00f2ea);">
                            <i class="fab fa-tiktok"></i>
                        </div>
                        <h4>TikTok</h4>
                        <p>Followers, likes, vues, partages pour exploser</p>
                        <div class="service-price">À partir de 400 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: #1877f2;">
                            <i class="fab fa-facebook"></i>
                        </div>
                        <h4>Facebook</h4>
                        <p>Likes de pages, followers, réactions authentiques</p>
                        <div class="service-price">À partir de 600 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: #ff0000;">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h4>YouTube</h4>
                        <p>Abonnés, vues, likes pour faire décoller ta chaîne</p>
                        <div class="service-price">À partir de 800 FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="prix" class="cta">
        <div class="container">
            <div class="fade-in">
                <h2>Prêt à Booster ton Influence ?</h2>
                <p>Rejoins des milliers de créateurs béninois qui nous font confiance. Paiement facile via Mobile Money !</p>
                <a href="register.php" class="btn btn-warning btn-hero">
                    <i class="fas fa-star me-2"></i>Créer mon Compte Gratuit
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-spider me-2"></i>TarantulaSMM Bénin</h5>
                    <p>Le SMM Panel de référence au Bénin. Boost tes réseaux sociaux avec notre support béninois 24h/7j.</p>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Instagram</a></li>
                        <li><a href="#">TikTok</a></li>
                        <li><a href="#">Facebook</a></li>
                        <li><a href="#">YouTube</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Support</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Centre d'aide</a></li>
                        <li><a href="#">Chat en direct</a></li>
                        <li><a href="#">WhatsApp: +229 97 00 00 00</a></li>
                        <li><a href="#">Email: support@tarantulasmm.bj</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Paiement</h5>
                    <p>Nous acceptons :</p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge bg-warning">MTN Mobile Money</span>
                        <span class="badge bg-primary">Moov Money</span>
                    </div>
                    <p><small>Paiements 100% sécurisés</small></p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 TarantulaSMM Bénin. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation simple
        function animateOnScroll() {
            const elements = document.querySelectorAll('.fade-in');
            
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }
        
        // Smooth scroll
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
        
        // Events
        window.addEventListener('scroll', animateOnScroll);
        window.addEventListener('load', animateOnScroll);
        
        // Mobile menu
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