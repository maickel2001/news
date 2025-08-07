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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
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
            --benin-green: #00b74a;
            --benin-red: #e8112d;
            --benin-yellow: #ffd700;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* Navigation Élégante */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            padding: 1rem 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        .navbar.scrolled {
            padding: 0.5rem 0;
            background: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        }
        
        .navbar-brand {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark) !important;
            margin: 0 0.5rem;
            padding: 0.75rem 1rem !important;
            border-radius: 25px;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            transform: translateX(-50%);
            transition: width 0.3s ease;
        }
        
        .nav-link:hover::before {
            width: 80%;
        }
        
        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }
        
        /* Hero Section Élégante */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                        url('https://images.unsplash.com/photo-1611224923853-80b023f02d71?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            color: white;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            z-index: 1;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .hero-title .highlight {
            background: linear-gradient(135deg, var(--benin-yellow), #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmer 2s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 3vw, 1.4rem);
            opacity: 0.95;
            margin-bottom: 2.5rem;
            max-width: 600px;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .btn-hero {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-hero-primary {
            background: linear-gradient(135deg, var(--benin-yellow), #ff6b6b);
            color: white;
            border: none;
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
        }
        
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 215, 0, 0.6);
            color: white;
        }
        
        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
        }
        
        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
            color: white;
        }
        
        .hero-visual {
            position: relative;
            z-index: 2;
        }
        
        .phone-mockup {
            position: relative;
            max-width: 300px;
            margin: 0 auto;
            animation: phoneFloat 6s ease-in-out infinite;
        }
        
        @keyframes phoneFloat {
            0%, 100% { transform: translateY(0px) rotateY(0deg); }
            50% { transform: translateY(-20px) rotateY(5deg); }
        }
        
        .phone-mockup img {
            width: 100%;
            height: auto;
            border-radius: 25px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .phone-mockup:hover img {
            transform: scale(1.05);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
        }
        
        .floating-icons {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }
        
        .social-icon {
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            animation: iconFloat 4s ease-in-out infinite;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        .social-icon.instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            top: 10%;
            right: -20px;
            animation-delay: 0s;
        }
        
        .social-icon.tiktok {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
            top: 30%;
            left: -20px;
            animation-delay: 1s;
        }
        
        .social-icon.facebook {
            background: #1877f2;
            bottom: 30%;
            right: -20px;
            animation-delay: 2s;
        }
        
        .social-icon.youtube {
            background: #ff0000;
            bottom: 10%;
            left: -20px;
            animation-delay: 3s;
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px) scale(1); }
            50% { transform: translateY(-15px) scale(1.1); }
        }
        
        /* Section Statistiques Élégante */
        .stats {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            position: relative;
        }
        
        .stats::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="%23667eea" opacity="0.1"/><circle cx="75" cy="75" r="2" fill="%23764ba2" opacity="0.1"/><circle cx="75" cy="25" r="1.5" fill="%23f093fb" opacity="0.1"/><circle cx="25" cy="75" r="1.5" fill="%23667eea" opacity="0.1"/></svg>') repeat;
        }
        
        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            border: 1px solid rgba(102, 126, 234, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        
        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.2);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
            font-size: 1rem;
        }
        
        /* Section Services Élégante */
        .services {
            padding: 6rem 0;
            background: white;
            position: relative;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }
        
        .section-title h2 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .section-title .underline {
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            margin: 0 auto 1.5rem;
            border-radius: 2px;
        }
        
        .service-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
            transition: all 0.4s ease;
            height: 100%;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.03), transparent);
            transform: rotate(45deg);
            transition: all 0.4s ease;
            opacity: 0;
        }
        
        .service-card:hover::before {
            opacity: 1;
            animation: shine 1s ease;
        }
        
        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }
        
        .service-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 25px 70px rgba(102, 126, 234, 0.15);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            position: relative;
            z-index: 1;
        }
        
        .service-card h4 {
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
            font-size: 1.3rem;
            position: relative;
            z-index: 1;
        }
        
        .service-card p {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        
        .service-price {
            font-size: 1.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: relative;
            z-index: 1;
        }
        
        /* Section CTA Élégante */
        .cta {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95)),
                        url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            color: white;
            padding: 6rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .cta::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        }
        
        .cta-content {
            position: relative;
            z-index: 2;
        }
        
        .cta h2 {
            font-size: clamp(2rem, 5vw, 3rem);
            font-weight: 800;
            margin-bottom: 1.5rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .cta p {
            font-size: clamp(1rem, 3vw, 1.3rem);
            opacity: 0.95;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        /* Footer Élégant */
        .footer {
            background: var(--dark);
            color: white;
            padding: 4rem 0 2rem;
            position: relative;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }
        
        .footer h5 {
            background: linear-gradient(135deg, var(--benin-yellow), #ff6b6b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .footer a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .footer a:hover {
            color: var(--benin-yellow);
            transform: translateX(5px);
        }
        
        .footer-bottom {
            border-top: 1px solid #333;
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
            color: #999;
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            color: white !important;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            transform: translateX(0) !important;
        }
        
        .social-links a:hover {
            transform: translateY(-3px) scale(1.1) !important;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        /* Responsive Élégant */
        @media (max-width: 768px) {
            .hero {
                padding: 6rem 0 3rem;
                text-align: center;
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
            
            .floating-icons {
                display: none;
            }
            
            .stat-card {
                margin-bottom: 2rem;
            }
            
            .service-card {
                margin-bottom: 2rem;
            }
            
            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border-radius: 15px;
                margin-top: 1rem;
                padding: 1.5rem;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            }
        }
        
        @media (max-width: 480px) {
            .phone-mockup {
                max-width: 250px;
            }
            
            .stat-card,
            .service-card {
                padding: 2rem 1.5rem;
            }
            
            .social-links {
                justify-content: center;
            }
        }
        
        /* Animations Élégantes */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .fade-in-left {
            opacity: 0;
            transform: translateX(-50px);
            transition: all 0.8s ease;
        }
        
        .fade-in-left.visible {
            opacity: 1;
            transform: translateX(0);
        }
        
        .fade-in-right {
            opacity: 0;
            transform: translateX(50px);
            transition: all 0.8s ease;
        }
        
        .fade-in-right.visible {
            opacity: 1;
            transform: translateX(0);
        }
    </style>
</head>
<body>
    <!-- Navigation Élégante -->
    <nav class="navbar navbar-expand-lg fixed-top" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-spider me-2"></i>TarantulaSMM
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
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Connexion
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section Élégante -->
    <section id="accueil" class="hero">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content fade-in-left">
                        <div class="hero-badge">
                            <i class="fas fa-crown"></i>
                            <span>#1 SMM Panel au Bénin 🇧🇯</span>
                        </div>
                        
                        <h1 class="hero-title">
                            Booste tes <span class="highlight">Réseaux Sociaux</span><br>
                            comme un Pro !
                        </h1>
                        
                        <p class="hero-subtitle">
                            La plateforme SMM la plus performante du Bénin. Développe ton influence sur Instagram, TikTok, Facebook et YouTube avec nos services premium et notre support local 24h/7j.
                        </p>
                        
                        <div class="hero-buttons">
                            <a href="register.php" class="btn btn-hero btn-hero-primary">
                                <i class="fas fa-rocket me-2"></i>Commencer Maintenant
                            </a>
                            <a href="#services" class="btn btn-hero btn-hero-secondary">
                                <i class="fas fa-play me-2"></i>Découvrir nos Services
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <div class="hero-visual fade-in-right">
                        <div class="phone-mockup">
                            <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" alt="SMM Panel Interface" class="img-fluid">
                            
                            <div class="floating-icons">
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
    </section>

    <!-- Section Statistiques Élégante -->
    <section class="stats">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="stat-number" data-count="25000">0</span>
                        <div class="stat-label">Clients Satisfaits</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <span class="stat-number">24h</span>
                        <div class="stat-label">Support Premium</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="stat-number" data-count="99">0</span>
                        <div class="stat-label">% Taux de Réussite</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card fade-in">
                        <div class="stat-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <span class="stat-number" data-count="50">0</span>
                        <div class="stat-label">Services Disponibles</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section Services Élégante -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-title fade-in">
                <h2>Nos Services Premium</h2>
                <div class="underline"></div>
                <p class="lead">Des solutions professionnelles pour dominer chaque plateforme sociale</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);">
                            <i class="fab fa-instagram"></i>
                        </div>
                        <h4>Instagram Pro</h4>
                        <p>Followers authentiques, likes organiques, vues stories et commentaires de qualité premium pour exploser sur Instagram</p>
                        <div class="service-price">À partir de 500 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: linear-gradient(45deg, #ff0050, #00f2ea);">
                            <i class="fab fa-tiktok"></i>
                        </div>
                        <h4>TikTok Viral</h4>
                        <p>Followers ciblés, likes massifs, vues explosives et partages pour rendre ton contenu viral sur TikTok</p>
                        <div class="service-price">À partir de 400 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: #1877f2;">
                            <i class="fab fa-facebook"></i>
                        </div>
                        <h4>Facebook Business</h4>
                        <p>Likes de pages professionnels, followers engagés, réactions authentiques et partages organiques</p>
                        <div class="service-price">À partir de 600 FCFA</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="service-card fade-in">
                        <div class="service-icon" style="background: #ff0000;">
                            <i class="fab fa-youtube"></i>
                        </div>
                        <h4>YouTube Elite</h4>
                        <p>Abonnés fidèles, vues ciblées, likes authentiques et commentaires pour faire exploser ta chaîne</p>
                        <div class="service-price">À partir de 800 FCFA</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section CTA Élégante -->
    <section id="prix" class="cta">
        <div class="container">
            <div class="cta-content fade-in">
                <h2>Prêt à Dominer les Réseaux ?</h2>
                <p>
                    Rejoins l'élite des créateurs béninois qui ont choisi TarantulaSMM pour propulser leur influence. 
                    Paiement simple et sécurisé via Mobile Money MTN/Moov.
                </p>
                <a href="register.php" class="btn btn-hero btn-hero-primary">
                    <i class="fas fa-star me-2"></i>Créer mon Compte Premium
                </a>
            </div>
        </div>
    </section>

    <!-- Footer Élégant -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-spider me-2"></i>TarantulaSMM Bénin</h5>
                    <p>La plateforme SMM de référence au Bénin. Propulse ton influence avec nos services premium et notre équipe d'experts béninois disponible 24h/7j.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Instagram Pro</a></li>
                        <li><a href="#">TikTok Viral</a></li>
                        <li><a href="#">Facebook Business</a></li>
                        <li><a href="#">YouTube Elite</a></li>
                        <li><a href="#">Pack Créateur</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Support Premium</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Centre d'assistance</a></li>
                        <li><a href="#">Chat en direct 24/7</a></li>
                        <li><a href="#">WhatsApp: +229 97 00 00 00</a></li>
                        <li><a href="#">Email: support@tarantulasmm.bj</a></li>
                        <li><a href="#">FAQ Complète</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Paiement Sécurisé</h5>
                    <p>Moyens de paiement acceptés :</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-warning">MTN Mobile Money</span>
                        <span class="badge bg-primary">Moov Money</span>
                        <span class="badge bg-success">Paiement Instantané</span>
                    </div>
                    <p><small>Transactions 100% sécurisées et cryptées</small></p>
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
        // Navbar scroll effect élégant
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Animation des compteurs
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
        
        // Intersection Observer pour animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    
                    // Déclencher l'animation des compteurs
                    if (entry.target.classList.contains('stats')) {
                        setTimeout(animateCounters, 500);
                    }
                }
            });
        }, observerOptions);
        
        // Observer tous les éléments animés
        document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .stats').forEach(el => {
            observer.observe(el);
        });
        
        // Smooth scroll élégant
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
        
        // Menu mobile élégant
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                if (navbarCollapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(navbarCollapse).hide();
                }
            });
        });
        
        // Parallax léger pour le hero
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            if (hero && scrolled < hero.offsetHeight) {
                hero.style.transform = `translateY(${scrolled * 0.5}px)`;
            }
        });
        
        // Animation au chargement
        window.addEventListener('load', () => {
            document.body.style.opacity = '1';
            setTimeout(() => {
                document.querySelectorAll('.fade-in-left, .fade-in-right').forEach((el, index) => {
                    setTimeout(() => {
                        el.classList.add('visible');
                    }, index * 200);
                });
            }, 300);
        });
    </script>
</body>
</html>