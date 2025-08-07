<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TarantulaSMM Bénin - SMM Panel #1 au Bénin 🇧🇯</title>
    <meta name="description" content="Le SMM Panel de référence au Bénin. Followers, likes, vues pour Instagram, TikTok, Facebook, YouTube. Paiement Mobile Money MTN/Moov.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        /* === VARIABLES GLOBALES === */
        :root {
            --primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gold: linear-gradient(135deg, #ffd700 0%, #ffb347 100%);
            --dark: #0f172a;
            --light: #f8fafc;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
        
        /* === BASE === */
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
        
        /* === NAVIGATION === */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }
        
        .navbar.transparent {
            background: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .navbar-brand {
            font-weight: 900;
            font-size: 1.8rem;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .nav-link {
            font-weight: 600;
            color: var(--dark) !important;
            margin: 0 0.5rem;
            padding: 0.5rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: var(--primary);
            color: white !important;
            transform: translateY(-2px);
        }
        
        .btn-login {
            background: var(--primary);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
            color: white;
        }
        
        /* === HERO SECTION === */
        .hero {
            min-height: 100vh;
            background: var(--primary);
            position: relative;
            display: flex;
            align-items: center;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
            animation: float 20s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }
        
        .hero-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            margin-bottom: 2rem;
            animation: pulse 3s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.3); }
            50% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }
        
        .hero-title .highlight {
            background: var(--gold);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 600px;
        }
        
        .hero-stats {
            display: flex;
            gap: 3rem;
            margin: 2rem 0;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            display: block;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--warning);
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn-hero {
            padding: 1rem 2rem;
            border-radius: 30px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary-hero {
            background: var(--secondary);
            color: white;
            border: none;
        }
        
        .btn-primary-hero:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            color: white;
        }
        
        .btn-outline-hero {
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .btn-outline-hero:hover {
            background: white;
            color: var(--dark);
            border-color: white;
        }
        
        .hero-visual {
            position: relative;
            z-index: 2;
        }
        
        .phone-mockup {
            position: relative;
            width: 300px;
            height: 600px;
            margin: 0 auto;
            background: linear-gradient(145deg, #1e293b, #334155);
            border-radius: 40px;
            padding: 20px;
            box-shadow: var(--shadow-lg);
            animation: floatPhone 6s ease-in-out infinite;
        }
        
        @keyframes floatPhone {
            0%, 100% { transform: translateY(0px) rotateY(0deg); }
            50% { transform: translateY(-30px) rotateY(5deg); }
        }
        
        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #0f172a, #1e293b);
            border-radius: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .social-icons {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .social-icon {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            animation: socialFloat 4s ease-in-out infinite;
        }
        
        .social-icon.instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            top: 15%;
            left: 15%;
            animation-delay: 0s;
        }
        
        .social-icon.tiktok {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
            top: 20%;
            right: 20%;
            animation-delay: 1s;
        }
        
        .social-icon.facebook {
            background: #1877f2;
            bottom: 25%;
            left: 20%;
            animation-delay: 2s;
        }
        
        .social-icon.youtube {
            background: #ff0000;
            bottom: 20%;
            right: 15%;
            animation-delay: 3s;
        }
        
        @keyframes socialFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-15px) scale(1.1); }
        }
        
        /* === SERVICES SECTION === */
        .services {
            padding: 5rem 0;
            background: var(--light);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title h2 {
            font-size: 3rem;
            font-weight: 900;
            background: var(--primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            font-size: 1.2rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .service-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-lg);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--primary);
            border-radius: 50%;
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
            color: #64748b;
            margin-bottom: 1.5rem;
        }
        
        .service-price {
            font-size: 1.5rem;
            font-weight: 900;
            background: var(--secondary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* === STATS SECTION === */
        .stats {
            padding: 5rem 0;
            background: var(--dark);
            color: white;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
        }
        
        .stat-item {
            padding: 2rem;
        }
        
        .stat-item .number {
            font-size: 3.5rem;
            font-weight: 900;
            background: var(--accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-item .label {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        
        /* === CTA SECTION === */
        .cta {
            padding: 5rem 0;
            background: var(--primary);
            color: white;
            text-align: center;
        }
        
        .cta h2 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }
        
        .cta p {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        /* === FOOTER === */
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
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .footer a:hover {
            color: var(--warning);
        }
        
        .footer-bottom {
            border-top: 1px solid #334155;
            margin-top: 2rem;
            padding-top: 2rem;
            text-align: center;
            color: #94a3b8;
        }
        
        .social-links a {
            display: inline-block;
            width: 50px;
            height: 50px;
            background: var(--primary);
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }
        
        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .phone-mockup {
                width: 250px;
                height: 500px;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .nav-link {
                margin: 0.2rem 0;
            }
        }
        
        /* === ANIMATIONS === */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 1s ease forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg transparent" id="navbar">
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
                
                <div class="d-flex gap-2">
                    <a href="login.php" class="btn btn-login">
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
                    <div class="hero-content">
                        <div class="hero-badge fade-in-up">
                            <i class="fas fa-crown me-2"></i>#1 SMM Panel au Bénin 🇧🇯
                        </div>
                        
                        <h1 class="hero-title fade-in-up delay-1">
                            Booste tes <span class="highlight">Réseaux Sociaux</span> 
                            comme jamais !
                        </h1>
                        
                        <p class="hero-subtitle fade-in-up delay-2">
                            Le SMM Panel de référence au Bénin. Des milliers de créateurs nous font confiance pour booster 
                            leur présence sur Instagram, TikTok, Facebook et YouTube.
                        </p>
                        
                        <div class="hero-stats fade-in-up delay-3">
                            <div class="stat">
                                <span class="stat-number" data-count="15000">0</span>
                                <span class="stat-label">Clients satisfaits</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number" data-count="24">0</span>
                                <span class="stat-label">Support 24h/7j</span>
                            </div>
                            <div class="stat">
                                <span class="stat-number" data-count="99">0</span>
                                <span class="stat-label">% de réussite</span>
                            </div>
                        </div>
                        
                        <div class="hero-buttons fade-in-up delay-4">
                            <a href="register.php" class="btn btn-hero btn-primary-hero">
                                <i class="fas fa-rocket me-2"></i>Commencer Maintenant
                            </a>
                            <a href="#services" class="btn btn-hero btn-outline-hero">
                                <i class="fas fa-play me-2"></i>Voir les Services
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-visual fade-in-up delay-2">
                        <div class="phone-mockup">
                            <div class="phone-screen">
                                <div class="social-icons">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title">
                <h2>Nos Services Premium</h2>
                <p>Des services de qualité supérieure pour tous les réseaux sociaux populaires</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in-up">
                        <div class="service-icon" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <h4>Instagram</h4>
                        <p>Followers, likes, vues stories, commentaires de qualité premium</p>
                        <div class="service-price">À partir de 500 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in-up delay-1">
                        <div class="service-icon" style="background: linear-gradient(45deg, #ff0050, #00f2ea);">
                            <i class="fab fa-tiktok"></i>
                        </div>
                        <h4>TikTok</h4>
                        <p>Followers, likes, vues, partages pour exploser sur TikTok</p>
                        <div class="service-price">À partir de 400 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in-up delay-2">
                        <div class="service-icon" style="background: #1877f2;">
                            <i class="fab fa-facebook"></i>
                        </div>
                        <h4>Facebook</h4>
                        <p>Likes de pages, followers, réactions, partages authentiques</p>
                        <div class="service-price">À partir de 600 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in-up delay-3">
                        <div class="service-icon" style="background: #ff0000;">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h4>YouTube</h4>
                        <p>Abonnés, vues, likes, commentaires pour faire décoller ta chaîne</p>
                        <div class="service-price">À partir de 800 FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item fade-in-up">
                    <span class="number" data-count="15000">0</span>
                    <span class="label">Clients Satisfaits</span>
                </div>
                <div class="stat-item fade-in-up delay-1">
                    <span class="number" data-count="2500000">0</span>
                    <span class="label">Followers Livrés</span>
                </div>
                <div class="stat-item fade-in-up delay-2">
                    <span class="number" data-count="98">0</span>
                    <span class="label">% Taux de Réussite</span>
                </div>
                <div class="stat-item fade-in-up delay-3">
                    <span class="number" data-count="24">0</span>
                    <span class="label">Support 24h/7j</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="prix" class="cta">
        <div class="container">
            <h2>Prêt à Exploser sur les Réseaux ?</h2>
            <p>
                Rejoins des milliers de créateurs béninois qui ont choisi TarantulaSMM pour 
                booster leur présence en ligne. Paiement facile via Mobile Money !
            </p>
            <a href="register.php" class="btn btn-hero btn-outline-hero">
                <i class="fas fa-star me-2"></i>Créer mon Compte Gratuit
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-spider me-2"></i>TarantulaSMM Bénin</h5>
                    <p>Le SMM Panel de référence au Bénin. Boost tes réseaux sociaux avec nos services premium et notre support béninois 24h/7j.</p>
                    <div class="social-links">
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
                <p>&copy; 2024 TarantulaSMM Bénin. Tous droits réservés. | <a href="#">Conditions d'utilisation</a> | <a href="#">Politique de confidentialité</a></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.remove('transparent');
            } else {
                navbar.classList.add('transparent');
            }
        });
        
        // Counter animation
        function animateCounters() {
            const counters = document.querySelectorAll('[data-count]');
            
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
        
        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    
                    // Trigger counter animation for stats
                    if (entry.target.classList.contains('stats')) {
                        animateCounters();
                    }
                }
            });
        }, observerOptions);
        
        // Observe all animated elements
        document.querySelectorAll('.fade-in-up').forEach(el => {
            observer.observe(el);
        });
        
        document.querySelectorAll('.stats').forEach(el => {
            observer.observe(el);
        });
        
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
        
        // Mobile menu close on link click
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