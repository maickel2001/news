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
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Display:wght@100;200;300;400;500;600;700;800;900&family=SF+Pro+Text:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* === VARIABLES APPLE DESIGN === */
        :root {
            --apple-blue: #007AFF;
            --apple-blue-light: #5AC8FA;
            --apple-purple: #AF52DE;
            --apple-pink: #FF2D92;
            --apple-orange: #FF9500;
            --apple-yellow: #FFCC00;
            --apple-green: #30D158;
            --apple-red: #FF3B30;
            
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --glass-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
            
            --dark-glass: rgba(0, 0, 0, 0.3);
            --dark-border: rgba(255, 255, 255, 0.1);
            
            --text-primary: #1d1d1f;
            --text-secondary: #86868b;
            --text-light: rgba(255, 255, 255, 0.9);
            
            --gradient-main: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-apple: linear-gradient(135deg, var(--apple-blue) 0%, var(--apple-purple) 100%);
            --gradient-warm: linear-gradient(135deg, var(--apple-orange) 0%, var(--apple-pink) 100%);
            --gradient-cool: linear-gradient(135deg, var(--apple-blue-light) 0%, var(--apple-green) 100%);
            
            --shadow-small: 0 2px 8px rgba(0, 0, 0, 0.1);
            --shadow-medium: 0 4px 20px rgba(0, 0, 0, 0.15);
            --shadow-large: 0 8px 40px rgba(0, 0, 0, 0.2);
            --shadow-xl: 0 20px 60px rgba(0, 0, 0, 0.3);
            
            --radius-small: 12px;
            --radius-medium: 16px;
            --radius-large: 24px;
            --radius-xl: 32px;
        }
        
        /* === BASE STYLE APPLE === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.47;
            color: var(--text-primary);
            overflow-x: hidden;
            background: #f5f5f7;
        }
        
        /* === GLASSMORPHISM UTILITIES === */
        .glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
        }
        
        .glass-dark {
            background: var(--dark-glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--dark-border);
        }
        
        /* === NAVIGATION APPLE === */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            padding: 0;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: rgba(248, 248, 248, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        
        .navbar.scrolled {
            background: rgba(248, 248, 248, 0.95);
            box-shadow: var(--shadow-small);
        }
        
        .navbar-inner {
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar.scrolled .navbar-inner {
            padding: 0.75rem 0;
        }
        
        .navbar-brand {
            font-family: 'SF Pro Display', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            background: var(--gradient-apple);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--text-primary) !important;
            margin: 0 0.25rem;
            padding: 0.5rem 1rem !important;
            border-radius: var(--radius-small);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }
        
        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--gradient-apple);
            opacity: 0;
            transition: all 0.3s ease;
            border-radius: var(--radius-small);
        }
        
        .nav-link:hover::before {
            opacity: 1;
        }
        
        .nav-link:hover {
            color: white !important;
            transform: translateY(-1px);
        }
        
        .nav-link span {
            position: relative;
            z-index: 1;
        }
        
        .btn-nav {
            background: var(--gradient-apple);
            border: none;
            color: white;
            padding: 0.6rem 1.5rem;
            border-radius: var(--radius-large);
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-nav:hover {
            transform: translateY(-2px) scale(1.02);
            box-shadow: var(--shadow-medium);
            color: white;
        }
        
        /* === HERO SECTION APPLE === */
        .hero {
            min-height: 100vh;
            background: var(--gradient-main);
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
            background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
            animation: heroFloat 20s ease-in-out infinite;
        }
        
        @keyframes heroFloat {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg); 
                opacity: 1;
            }
            50% { 
                transform: translateY(-20px) rotate(2deg); 
                opacity: 0.8;
            }
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
        }
        
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius-xl);
            font-weight: 600;
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
            box-shadow: var(--glass-shadow);
        }
        
        @keyframes float {
            0%, 100% { 
                transform: translateY(0px);
                box-shadow: var(--glass-shadow);
            }
            50% { 
                transform: translateY(-10px);
                box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
            }
        }
        
        .hero-title {
            font-family: 'SF Pro Display', sans-serif;
            font-size: clamp(2.5rem, 8vw, 5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, white 0%, rgba(255, 255, 255, 0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .hero-title .highlight {
            background: var(--gradient-warm);
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
            font-size: clamp(1.1rem, 3vw, 1.5rem);
            opacity: 0.9;
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .stat-glass {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-medium);
            padding: 1.5rem 1rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: var(--glass-shadow);
        }
        
        .stat-glass:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
        }
        
        .stat-number {
            display: block;
            font-family: 'SF Pro Display', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--apple-yellow);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            font-weight: 500;
        }
        
        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-top: 3rem;
        }
        
        .btn-hero {
            padding: 1rem 2.5rem;
            border-radius: var(--radius-xl);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
        }
        
        .btn-primary-hero {
            background: var(--gradient-warm);
            color: white;
            border: none;
            box-shadow: var(--shadow-medium);
        }
        
        .btn-primary-hero:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: var(--shadow-large);
            color: white;
        }
        
        .btn-glass-hero {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            color: white;
            box-shadow: var(--glass-shadow);
        }
        
        .btn-glass-hero:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(31, 38, 135, 0.5);
            color: white;
        }
        
        .hero-visual {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .phone-container {
            position: relative;
            animation: phoneFloat 6s ease-in-out infinite;
        }
        
        @keyframes phoneFloat {
            0%, 100% { 
                transform: translateY(0px) rotateY(0deg) rotateX(0deg);
            }
            50% { 
                transform: translateY(-30px) rotateY(5deg) rotateX(2deg);
            }
        }
        
        .phone-mockup {
            width: clamp(250px, 30vw, 320px);
            height: clamp(500px, 60vw, 640px);
            background: linear-gradient(145deg, #1a1a1a, #2a2a2a);
            border-radius: var(--radius-xl);
            padding: 20px;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }
        
        .phone-mockup::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border-radius: var(--radius-xl);
            pointer-events: none;
        }
        
        .phone-screen {
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #000, #1a1a1a);
            border-radius: calc(var(--radius-xl) - 10px);
            position: relative;
            overflow: hidden;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
        }
        
        .social-bubble {
            position: absolute;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: socialFloat 4s ease-in-out infinite;
            box-shadow: var(--shadow-medium);
        }
        
        .social-bubble.instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            top: 15%;
            left: 15%;
            animation-delay: 0s;
        }
        
        .social-bubble.tiktok {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
            top: 20%;
            right: 20%;
            animation-delay: 1s;
        }
        
        .social-bubble.facebook {
            background: var(--apple-blue);
            bottom: 25%;
            left: 20%;
            animation-delay: 2s;
        }
        
        .social-bubble.youtube {
            background: var(--apple-red);
            bottom: 20%;
            right: 15%;
            animation-delay: 3s;
        }
        
        @keyframes socialFloat {
            0%, 100% { 
                transform: translateY(0px) scale(1);
                box-shadow: var(--shadow-medium);
            }
            50% { 
                transform: translateY(-15px) scale(1.1);
                box-shadow: var(--shadow-large);
            }
        }
        
        /* === SERVICES SECTION === */
        .services {
            padding: clamp(3rem, 8vw, 6rem) 0;
            background: #f5f5f7;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: clamp(3rem, 6vw, 5rem);
        }
        
        .section-title {
            font-family: 'SF Pro Display', sans-serif;
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 800;
            background: var(--gradient-apple);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .section-subtitle {
            font-size: clamp(1rem, 3vw, 1.3rem);
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .service-card {
            background: white;
            border-radius: var(--radius-large);
            padding: 2rem;
            text-align: center;
            box-shadow: var(--shadow-small);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            border: 1px solid rgba(0, 0, 0, 0.05);
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
            background: var(--gradient-apple);
        }
        
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-large);
        }
        
        .service-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            border-radius: var(--radius-medium);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            position: relative;
        }
        
        .service-card h4 {
            font-family: 'SF Pro Display', sans-serif;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text-primary);
            font-size: 1.5rem;
        }
        
        .service-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .service-price {
            font-family: 'SF Pro Display', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            background: var(--gradient-warm);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* === STATS SECTION === */
        .stats {
            padding: clamp(4rem, 8vw, 6rem) 0;
            background: var(--text-primary);
            color: white;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 3rem;
            text-align: center;
        }
        
        .stat-item {
            padding: 2rem 1rem;
        }
        
        .stat-item .number {
            font-family: 'SF Pro Display', sans-serif;
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 900;
            background: var(--gradient-cool);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .stat-item .label {
            font-size: clamp(0.9rem, 2vw, 1.2rem);
            opacity: 0.8;
            font-weight: 500;
        }
        
        /* === CTA SECTION === */
        .cta {
            padding: clamp(4rem, 8vw, 6rem) 0;
            background: var(--gradient-main);
            color: white;
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
            font-family: 'SF Pro Display', sans-serif;
            font-size: clamp(2rem, 6vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            line-height: 1.2;
        }
        
        .cta p {
            font-size: clamp(1rem, 3vw, 1.4rem);
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }
        
        /* === FOOTER === */
        .footer {
            background: var(--text-primary);
            color: white;
            padding: clamp(3rem, 6vw, 4rem) 0 2rem;
        }
        
        .footer h5 {
            font-family: 'SF Pro Display', sans-serif;
            color: var(--apple-yellow);
            margin-bottom: 1.5rem;
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .footer a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .footer a:hover {
            color: var(--apple-yellow);
            transform: translateX(5px);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 3rem;
            padding-top: 2rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .social-links a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            background: var(--gradient-apple);
            border-radius: 50%;
            text-align: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            margin-bottom: 0;
        }
        
        .social-links a:hover {
            transform: translateY(-3px) scale(1.1);
            box-shadow: var(--shadow-medium);
        }
        
        /* === ANIMATIONS === */
        .fade-in-up {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
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
        .delay-5 { animation-delay: 0.5s; }
        
        /* === RESPONSIVE DESIGN === */
        @media (max-width: 768px) {
            .hero {
                padding: 6rem 0 3rem;
                text-align: center;
            }
            
            .hero-stats {
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn-hero {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .stats-container {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }
            
            .navbar-nav {
                background: rgba(248, 248, 248, 0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border-radius: var(--radius-medium);
                margin-top: 1rem;
                padding: 1rem;
            }
            
            .nav-link {
                margin: 0.25rem 0;
            }
        }
        
        @media (max-width: 480px) {
            .hero-stats {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .phone-mockup {
                width: 220px;
                height: 440px;
            }
        }
        
        /* === PERFORMANCE OPTIMIZATIONS === */
        .hero, .services, .stats, .cta {
            will-change: transform;
        }
        
        .phone-container, .social-bubble, .stat-glass {
            will-change: transform;
        }
        
        /* === ACCESSIBILITY === */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
        
        /* === HIGH DPI DISPLAYS === */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
            .phone-mockup {
                box-shadow: var(--shadow-xl), inset 0 0 0 1px rgba(255, 255, 255, 0.1);
            }
            
            .glass, .glass-dark {
                border-width: 0.5px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Apple Style -->
    <nav class="navbar navbar-expand-lg" id="navbar">
        <div class="container">
            <div class="navbar-inner w-100">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <a class="navbar-brand" href="#">
                        <i class="fas fa-spider me-2"></i>TarantulaSMM
                    </a>
                    
                    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav me-auto ms-lg-5">
                            <li class="nav-item">
                                <a class="nav-link" href="#accueil"><span>Accueil</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#services"><span>Services</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#prix"><span>Prix</span></a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#contact"><span>Contact</span></a>
                            </li>
                        </ul>
                        
                        <div class="d-flex">
                            <a href="login.php" class="btn btn-nav">
                                <i class="fas fa-sign-in-alt"></i>
                                <span>Connexion</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section Apple Style -->
    <section id="accueil" class="hero">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6 order-lg-1 order-2">
                    <div class="hero-content">
                        <div class="hero-badge fade-in-up">
                            <i class="fas fa-crown"></i>
                            <span>#1 SMM Panel au Bénin 🇧🇯</span>
                        </div>
                        
                        <h1 class="hero-title fade-in-up delay-1">
                            Booste tes <span class="highlight">Réseaux Sociaux</span><br>
                            avec Excellence
                        </h1>
                        
                        <p class="hero-subtitle fade-in-up delay-2">
                            Le SMM Panel le plus avancé du Bénin. Une technologie de pointe pour propulser 
                            votre présence sur Instagram, TikTok, Facebook et YouTube.
                        </p>
                        
                        <div class="hero-stats fade-in-up delay-3">
                            <div class="stat-glass">
                                <span class="stat-number" data-count="25000">0</span>
                                <span class="stat-label">Clients Satisfaits</span>
                            </div>
                            <div class="stat-glass">
                                <span class="stat-number" data-count="24">0</span>
                                <span class="stat-label">Support 24/7</span>
                            </div>
                            <div class="stat-glass">
                                <span class="stat-number" data-count="99">0</span>
                                <span class="stat-label">% de Réussite</span>
                            </div>
                        </div>
                        
                        <div class="hero-buttons fade-in-up delay-4">
                            <a href="register.php" class="btn btn-hero btn-primary-hero">
                                <i class="fas fa-rocket"></i>
                                <span>Commencer Maintenant</span>
                            </a>
                            <a href="#services" class="btn btn-hero btn-glass-hero">
                                <i class="fas fa-play"></i>
                                <span>Découvrir les Services</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 order-lg-2 order-1">
                    <div class="hero-visual fade-in-up delay-2">
                        <div class="phone-container">
                            <div class="phone-mockup">
                                <div class="phone-screen">
                                    <div class="floating-elements">
                                        <div class="social-bubble instagram">
                                            <i class="fab fa-instagram"></i>
                                        </div>
                                        <div class="social-bubble tiktok">
                                            <i class="fab fa-tiktok"></i>
                                        </div>
                                        <div class="social-bubble facebook">
                                            <i class="fab fa-facebook"></i>
                                        </div>
                                        <div class="social-bubble youtube">
                                            <i class="fab fa-youtube"></i>
                                        </div>
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
            <div class="section-header">
                <h2 class="section-title fade-in-up">Services Premium</h2>
                <p class="section-subtitle fade-in-up delay-1">
                    Des solutions professionnelles pour chaque plateforme sociale
                </p>
            </div>
            
            <div class="services-grid">
                <div class="service-card fade-in-up delay-1">
                    <div class="service-icon" style="background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h4>Instagram</h4>
                    <p>Followers authentiques, likes organiques, vues stories et commentaires de qualité premium pour booster votre profil</p>
                    <div class="service-price">À partir de 500 FCFA</div>
                </div>
                
                <div class="service-card fade-in-up delay-2">
                    <div class="service-icon" style="background: linear-gradient(45deg, #ff0050, #00f2ea);">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h4>TikTok</h4>
                    <p>Followers ciblés, likes rapides, vues massives et partages pour faire exploser votre contenu viral</p>
                    <div class="service-price">À partir de 400 FCFA</div>
                </div>
                
                <div class="service-card fade-in-up delay-3">
                    <div class="service-icon" style="background: var(--apple-blue);">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h4>Facebook</h4>
                    <p>Likes de pages professionnels, followers engagés, réactions authentiques et partages organiques</p>
                    <div class="service-price">À partir de 600 FCFA</div>
                </div>
                
                <div class="service-card fade-in-up delay-4">
                    <div class="service-icon" style="background: var(--apple-red);">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h4>YouTube</h4>
                    <p>Abonnés fidèles, vues ciblées, likes authentiques et commentaires pour accélérer votre croissance</p>
                    <div class="service-price">À partir de 800 FCFA</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-container">
                <div class="stat-item fade-in-up">
                    <span class="number" data-count="25000">0</span>
                    <span class="label">Clients Satisfaits</span>
                </div>
                <div class="stat-item fade-in-up delay-1">
                    <span class="number" data-count="5000000">0</span>
                    <span class="label">Followers Livrés</span>
                </div>
                <div class="stat-item fade-in-up delay-2">
                    <span class="number" data-count="99">0</span>
                    <span class="label">% Taux de Réussite</span>
                </div>
                <div class="stat-item fade-in-up delay-3">
                    <span class="number" data-count="24">0</span>
                    <span class="label">Support Premium</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="prix" class="cta">
        <div class="container">
            <div class="cta-content">
                <h2 class="fade-in-up">Prêt à Dominer les Réseaux ?</h2>
                <p class="fade-in-up delay-1">
                    Rejoignez l'élite des créateurs béninois qui font confiance à TarantulaSMM. 
                    Une technologie de pointe, un support exceptionnel, des résultats garantis.
                </p>
                <div class="fade-in-up delay-2">
                    <a href="register.php" class="btn btn-hero btn-glass-hero">
                        <i class="fas fa-star"></i>
                        <span>Créer mon Compte Premium</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <h5><i class="fas fa-spider me-2"></i>TarantulaSMM Bénin</h5>
                    <p>La plateforme SMM la plus avancée du Bénin. Excellence technologique, support premium et résultats exceptionnels pour votre réussite digitale.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-tiktok"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5>Services</h5>
                    <a href="#">Instagram Premium</a>
                    <a href="#">TikTok Pro</a>
                    <a href="#">Facebook Business</a>
                    <a href="#">YouTube Elite</a>
                    <a href="#">Pack Créateur</a>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Support Premium</h5>
                    <a href="#">Centre d'assistance</a>
                    <a href="#">Chat en direct 24/7</a>
                    <a href="#">WhatsApp: +229 97 00 00 00</a>
                    <a href="#">Email: support@tarantulasmm.bj</a>
                    <a href="#">Assistance personnalisée</a>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5>Paiement Sécurisé</h5>
                    <p>Nous acceptons :</p>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-warning">MTN Mobile Money</span>
                        <span class="badge bg-primary">Moov Money</span>
                        <span class="badge bg-success">Paiement Instantané</span>
                    </div>
                    <p><small>Transactions 100% sécurisées et cryptées</small></p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 TarantulaSMM Bénin. Tous droits réservés. | <a href="#">Conditions Premium</a> | <a href="#">Politique de Confidentialité</a></p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Apple-style smooth scrolling
        document.documentElement.style.scrollBehavior = 'smooth';
        
        // Enhanced navbar scroll effect
        let lastScrollTop = 0;
        const navbar = document.getElementById('navbar');
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            // Hide navbar on scroll down, show on scroll up
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                navbar.style.transform = 'translateY(-100%)';
            } else {
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Enhanced counter animation with easing
        function animateCounters() {
            const counters = document.querySelectorAll('[data-count]');
            
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 2000;
                const start = performance.now();
                
                function updateCounter(currentTime) {
                    const elapsed = currentTime - start;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // Easing function (ease-out-cubic)
                    const easeOut = 1 - Math.pow(1 - progress, 3);
                    const current = Math.floor(target * easeOut);
                    
                    counter.textContent = current.toLocaleString();
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.textContent = target.toLocaleString();
                    }
                }
                
                requestAnimationFrame(updateCounter);
            });
        }
        
        // Advanced Intersection Observer with multiple thresholds
        const observerOptions = {
            threshold: [0, 0.1, 0.3, 0.5],
            rootMargin: '0px 0px -100px 0px'
        };
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.intersectionRatio >= 0.1) {
                    entry.target.style.animationPlayState = 'running';
                    
                    // Trigger counter animation for stats
                    if (entry.target.classList.contains('stats') && entry.intersectionRatio >= 0.3) {
                        animateCounters();
                        observer.unobserve(entry.target); // Only animate once
                    }
                }
            });
        }, observerOptions);
        
        // Observe all animated elements
        document.querySelectorAll('.fade-in-up').forEach(el => {
            el.style.animationPlayState = 'paused';
            observer.observe(el);
        });
        
        document.querySelectorAll('.stats').forEach(el => {
            observer.observe(el);
        });
        
        // Enhanced smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    const offsetTop = target.offsetTop - 80; // Account for fixed navbar
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Mobile menu enhancements
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.addEventListener('click', () => {
                if (navbarCollapse.classList.contains('show')) {
                    bootstrap.Collapse.getInstance(navbarCollapse).hide();
                }
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navbarCollapse.classList.contains('show') && 
                !navbarCollapse.contains(e.target) && 
                !navbarToggler.contains(e.target)) {
                bootstrap.Collapse.getInstance(navbarCollapse).hide();
            }
        });
        
        // Parallax effect for hero background
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            const heroHeight = hero.offsetHeight;
            
            if (scrolled < heroHeight) {
                const parallaxSpeed = scrolled * 0.5;
                hero.style.transform = `translateY(${parallaxSpeed}px)`;
            }
        });
        
        // Add loading animation
        window.addEventListener('load', () => {
            document.body.classList.add('loaded');
            
            // Start animations after a brief delay
            setTimeout(() => {
                document.querySelectorAll('.fade-in-up').forEach((el, index) => {
                    setTimeout(() => {
                        el.style.animationPlayState = 'running';
                    }, index * 100);
                });
            }, 300);
        });
        
        // Performance optimization: debounce scroll events
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        
        // Optimized scroll handler
        const optimizedScrollHandler = debounce(() => {
            // Any additional scroll-based animations can go here
        }, 10);
        
        window.addEventListener('scroll', optimizedScrollHandler);
        
        // Touch device optimizations
        if ('ontouchstart' in window) {
            document.body.classList.add('touch-device');
            
            // Improve touch interactions
            document.querySelectorAll('.btn, .service-card, .stat-glass').forEach(el => {
                el.addEventListener('touchstart', function() {
                    this.classList.add('touch-active');
                });
                
                el.addEventListener('touchend', function() {
                    setTimeout(() => {
                        this.classList.remove('touch-active');
                    }, 150);
                });
            });
        }
        
        // Dark mode detection and adaptation
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            // Add dark mode enhancements if needed
            document.body.classList.add('prefers-dark');
        }
        
        // Reduced motion respect
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.body.classList.add('reduced-motion');
        }
    </script>
</body>
</html>