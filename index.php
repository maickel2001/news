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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #000000;
            --secondary: #ffffff;
            --accent: #ff6b35;
            --accent-light: #ff8660;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --text-light: #999999;
            --bg-light: #fafafa;
            --bg-dark: #0a0a0a;
            --border: #e0e0e0;
            --shadow: 0 4px 60px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 8px 80px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            line-height: 1.7;
            color: var(--text-primary);
            background: var(--secondary);
        }
        
        /* Navigation Minimaliste */
        .navbar {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 1.5rem 0;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }
        
        .navbar.scrolled {
            padding: 1rem 0;
            box-shadow: var(--shadow);
        }
        
        .navbar-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary) !important;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            color: var(--accent) !important;
        }
        
        .nav-link {
            font-weight: 400;
            color: var(--text-primary) !important;
            margin: 0 1rem;
            padding: 0.5rem 0 !important;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .nav-link:hover {
            color: var(--accent) !important;
            border-bottom-color: var(--accent);
        }
        
        .btn-nav {
            background: var(--primary);
            color: var(--secondary);
            border: 2px solid var(--primary);
            padding: 0.75rem 2rem;
            border-radius: 0;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .btn-nav:hover {
            background: var(--secondary);
            color: var(--primary);
        }
        
        /* Hero Section Créative */
        .hero {
            min-height: 100vh;
            background: var(--secondary);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: var(--primary);
            clip-path: polygon(30% 0%, 100% 0%, 100% 100%, 0% 100%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-tag {
            display: inline-block;
            background: var(--accent);
            color: var(--secondary);
            padding: 0.5rem 1.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2rem;
            clip-path: polygon(0 0, calc(100% - 10px) 0, 100% 100%, 10px 100%);
        }
        
        .hero-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 800;
            line-height: 0.9;
            margin-bottom: 2rem;
            color: var(--primary);
        }
        
        .hero-title .accent {
            color: var(--accent);
            position: relative;
        }
        
        .hero-title .accent::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: var(--accent);
            opacity: 0.3;
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            color: var(--text-secondary);
            max-width: 500px;
            margin-bottom: 3rem;
            line-height: 1.6;
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn-hero {
            padding: 1.2rem 3rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-hero:hover::before {
            left: 100%;
        }
        
        .btn-primary-hero {
            background: var(--accent);
            color: var(--secondary);
            border: 2px solid var(--accent);
        }
        
        .btn-primary-hero:hover {
            background: var(--accent-light);
            border-color: var(--accent-light);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-secondary-hero {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }
        
        .btn-secondary-hero:hover {
            background: var(--primary);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .hero-visual {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        
        .social-logos {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .social-logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--secondary);
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: var(--shadow);
            animation: logoFloat 3s ease-in-out infinite;
        }
        
        .social-logo:nth-child(odd) {
            animation-delay: 0.5s;
        }
        
        .social-logo:nth-child(even) {
            animation-delay: 1s;
        }
        
        .social-logo.instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }
        
        .social-logo.tiktok {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
        }
        
        .social-logo.facebook {
            background: #1877f2;
        }
        
        .social-logo.youtube {
            background: #ff0000;
        }
        
        .social-logo.twitter {
            background: #1da1f2;
        }
        
        .social-logo.linkedin {
            background: #0e76a8;
        }
        
        .social-logo:hover {
            transform: translateY(-10px) scale(1.1);
            box-shadow: var(--shadow-hover);
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        /* Section Services Créative */
        .services {
            padding: 8rem 0;
            background: var(--bg-light);
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }
        
        .section-tag {
            display: inline-block;
            background: var(--primary);
            color: var(--secondary);
            padding: 0.5rem 2rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .section-subtitle {
            font-size: 1.3rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background: var(--secondary);
            padding: 3rem;
            border: 1px solid var(--border);
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }
        
        .service-icon {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .service-card:hover .service-icon {
            background: var(--accent);
            transform: rotate(5deg);
        }
        
        .service-card h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .service-card p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.7;
        }
        
        .service-price {
            font-weight: 700;
            color: var(--accent);
            font-size: 1.1rem;
        }
        
        /* Section Stats Unique */
        .stats {
            padding: 6rem 0;
            background: var(--primary);
            color: var(--secondary);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 2rem 0;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-item:last-child {
            border-right: none;
        }
        
        .stat-number {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 800;
            color: var(--accent);
            display: block;
            margin-bottom: 1rem;
        }
        
        .stat-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }
        
        /* Section CTA Moderne */
        .cta {
            padding: 8rem 0;
            background: var(--bg-light);
            text-align: center;
            position: relative;
        }
        
        .cta::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            transform: translateY(-50%);
        }
        
        .cta-content {
            background: var(--secondary);
            padding: 4rem;
            border: 1px solid var(--border);
            position: relative;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .cta h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 3rem;
            line-height: 1.7;
        }
        
        /* Footer Minimaliste */
        .footer {
            background: var(--primary);
            color: var(--secondary);
            padding: 5rem 0 2rem;
        }
        
        .footer-content {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 3rem;
            margin-bottom: 2rem;
        }
        
        .footer h5 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 0.8rem;
            font-size: 0.95rem;
        }
        
        .footer a:hover {
            color: var(--accent);
            padding-left: 10px;
        }
        
        .footer-bottom {
            text-align: center;
            font-size: 0.9rem;
            opacity: 0.7;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .social-links a {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--secondary) !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            margin-bottom: 0 !important;
            padding-left: 0 !important;
        }
        
        .social-links a:hover {
            background: var(--accent);
            transform: translateY(-3px);
            padding-left: 0 !important;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero::before {
                width: 100%;
                height: 50%;
                clip-path: polygon(0 0, 100% 0, 100% 70%, 0 100%);
            }
            
            .hero {
                text-align: center;
                padding: 6rem 0;
            }
            
            .social-logos {
                grid-template-columns: repeat(2, 1fr);
                max-width: 300px;
                gap: 1.5rem;
            }
            
            .social-logo {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .hero-buttons {
                justify-content: center;
                flex-direction: column;
                align-items: center;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 300px;
            }
            
            .navbar-nav {
                text-align: center;
                padding: 2rem 0;
            }
            
            .nav-link {
                margin: 0.5rem 0;
            }
            
            .stat-item {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                padding-bottom: 2rem;
                margin-bottom: 2rem;
            }
            
            .stat-item:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }
            
            .cta-content {
                padding: 2rem;
            }
        }
        
        /* Animations */
        .slide-up {
            opacity: 0;
            transform: translateY(50px);
            transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
        }
        
        .slide-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .slide-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
        }
        
        .slide-left.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .slide-right {
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.8s cubic-bezier(0.23, 1, 0.32, 1);
        }
        
        .slide-right.visible {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <!-- Navigation Minimaliste -->
    <nav class="navbar navbar-expand-lg fixed-top" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-spider me-2"></i>TARANTULA<strong>SMM</strong>
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
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
                    <a href="login.php" class="btn btn-nav">
                        Connexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section Créative -->
    <section id="accueil" class="hero">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content slide-left">
                        <div class="hero-tag">
                            SMM Panel Premium
                        </div>
                        
                        <h1 class="hero-title">
                            DOMINEZ<br>
                            LES <span class="accent">RÉSEAUX</span><br>
                            SOCIAUX
                        </h1>
                        
                        <p class="hero-subtitle">
                            La plateforme SMM révolutionnaire qui propulse votre influence digitale. 
                            Croissance authentique, résultats garantis, support béninois d'exception.
                        </p>
                        
                        <div class="hero-buttons">
                            <a href="register.php" class="btn btn-hero btn-primary-hero">
                                <i class="fas fa-rocket me-2"></i>Démarrer Maintenant
                            </a>
                            <a href="#services" class="btn btn-hero btn-secondary-hero">
                                Explorer les Services
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-visual slide-right">
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

    <!-- Section Services Créative -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header slide-up">
                <div class="section-tag">Nos Solutions</div>
                <h2 class="section-title">SERVICES PREMIUM</h2>
                <p class="section-subtitle">
                    Des outils puissants pour transformer votre présence digitale en machine à succès
                </p>
            </div>
            
            <div class="services-grid">
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3>Instagram Elite</h3>
                    <p>Croissance organique ultra-ciblée avec followers authentiques, engagement premium et analyse complète de performance.</p>
                    <div class="service-price">À partir de 500 FCFA</div>
                </div>
                
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3>TikTok Explosion</h3>
                    <p>Stratégies virales avancées pour maximiser votre portée avec du contenu optimisé et des trends analysis.</p>
                    <div class="service-price">À partir de 400 FCFA</div>
                </div>
                
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3>Facebook Pro</h3>
                    <p>Solutions business complètes pour pages professionnelles avec ciblage démographique précis.</p>
                    <div class="service-price">À partir de 600 FCFA</div>
                </div>
                
                <div class="service-card slide-up">
                    <div class="service-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3>YouTube Master</h3>
                    <p>Optimisation de chaîne complète avec algorithme YouTube et monétisation accélérée.</p>
                    <div class="service-price">À partir de 800 FCFA</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Stats Unique -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid slide-up">
                <div class="stat-item">
                    <span class="stat-number" data-count="25000">0</span>
                    <div class="stat-label">Créateurs Actifs</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="10">0</span>
                    <div class="stat-label">Millions d'Interactions</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="99">0</span>
                    <div class="stat-label">% Satisfaction Client</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="24">0</span>
                    <div class="stat-label">Support Non-Stop</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section CTA Moderne -->
    <section id="prix" class="cta">
        <div class="container">
            <div class="cta-content slide-up">
                <h2>PRÊT POUR LE SUCCÈS ?</h2>
                <p>
                    Rejoignez l'élite des créateurs de contenu qui dominent leurs marchés. 
                    Votre révolution digitale commence aujourd'hui.
                </p>
                <a href="register.php" class="btn btn-hero btn-primary-hero">
                    <i class="fas fa-crown me-2"></i>Devenir Premium
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Minimaliste -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="row">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <h5>TarantulaSMM</h5>
                        <p style="color: rgba(255, 255, 255, 0.7); line-height: 1.7;">
                            La révolution SMM au Bénin. Excellence technologique, 
                            expertise locale, résultats extraordinaires.
                        </p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-tiktok"></i></a>
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 mb-4">
                        <h5>Solutions</h5>
                        <a href="#">Instagram Elite</a>
                        <a href="#">TikTok Explosion</a>
                        <a href="#">Facebook Pro</a>
                        <a href="#">YouTube Master</a>
                        <a href="#">Analytics</a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5>Support Elite</h5>
                        <a href="#">Centre d'Excellence</a>
                        <a href="#">Chat Premium 24/7</a>
                        <a href="#">WhatsApp: +229 97 00 00 00</a>
                        <a href="#">expert@tarantulasmm.bj</a>
                        <a href="#">Formation Personnalisée</a>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 mb-4">
                        <h5>Paiements</h5>
                        <div style="margin-bottom: 1rem;">
                            <span class="badge" style="background: #ff6b35; margin-right: 0.5rem;">MTN Mobile Money</span>
                            <span class="badge" style="background: #0066cc;">Moov Money</span>
                        </div>
                        <a href="#">Sécurité Bancaire</a>
                        <a href="#">Cryptage SSL</a>
                        <a href="#">Protection Données</a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 TarantulaSMM Bénin. Innovation • Excellence • Résultats</p>
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
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
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
        
        // Intersection Observer
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    
                    if (entry.target.classList.contains('stats')) {
                        setTimeout(animateCounters, 500);
                    }
                }
            });
        }, observerOptions);
        
        // Observer elements
        document.querySelectorAll('.slide-up, .slide-left, .slide-right, .stats').forEach(el => {
            observer.observe(el);
        });
        
        // Smooth scroll
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
        
        // Mobile menu
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(navbarCollapse).hide();
                }
            });
        });
        
        // Loading animation
        window.addEventListener('load', () => {
            setTimeout(() => {
                document.querySelectorAll('.slide-left, .slide-right').forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('visible');
                    }, index * 300);
                });
            }, 200);
        });
    </script>
</body>
</html>