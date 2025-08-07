<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs obligatoires.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Adresse email invalide.';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } elseif (!$terms) {
        $error = 'Vous devez accepter les conditions d\'utilisation.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Vérifier si l'email existe déjà
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? UNION SELECT id FROM admin_users WHERE email = ?");
            $stmt->execute([$email, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Un compte avec cette adresse email existe déjà.';
            } else {
                // Vérifier si l'email verification est nécessaire
                $email_verification_required = getSetting('email_verification_required', false);
                
                // Préparer les données utilisateur
                $user_data = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
                    'email_verified' => $email_verification_required ? 0 : 1,
                    'email_verification_token' => $email_verification_required ? generateSecureToken() : null,
                    'status' => 'active',
                    'balance' => 0.00,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Insérer l'utilisateur
                $stmt = $db->prepare("
                    INSERT INTO users (first_name, last_name, email, phone, password_hash, email_verified, email_verification_token, status, balance, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $user_data['first_name'],
                    $user_data['last_name'],
                    $user_data['email'],
                    $user_data['phone'],
                    $user_data['password_hash'],
                    $user_data['email_verified'],
                    $user_data['email_verification_token'],
                    $user_data['status'],
                    $user_data['balance'],
                    $user_data['created_at'],
                    $user_data['updated_at']
                ]);
                
                $user_id = $db->lastInsertId();
                
                // Log de l'activité
                logActivity($user_id, 'user_registered', 'Nouvel utilisateur créé depuis ' . $_SERVER['REMOTE_ADDR']);
                
                if ($email_verification_required) {
                    // Envoyer l'email de vérification
                    $verification_link = "https://{$_SERVER['HTTP_HOST']}/verify-email.php?token={$user_data['email_verification_token']}";
                    
                    $email_sent = sendEmail($email, 'Vérifiez votre compte TarantulaSMM', "
                        <h2>Bienvenue chez TarantulaSMM !</h2>
                        <p>Merci de vous être inscrit. Cliquez sur le lien ci-dessous pour vérifier votre compte :</p>
                        <p><a href='{$verification_link}' style='background: #ff6b35; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Vérifier mon compte</a></p>
                        <p>Ce lien expire dans 24 heures.</p>
                    ");
                    
                    $success = 'Compte créé avec succès ! Vérifiez votre email pour activer votre compte.';
                } else {
                    $success = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
                }
            }
        } catch (Exception $e) {
            $error = 'Erreur lors de la création du compte. Veuillez réessayer.';
            error_log("Registration error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - TarantulaSMM Bénin</title>
    
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
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
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
            background: var(--bg-light);
            min-height: 100vh;
        }
        
        /* Hero Section comme l'accueil */
        .auth-hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                        url('https://images.unsplash.com/photo-1611224923853-80b023f02d71?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80') center/cover;
            color: white;
            overflow: hidden;
        }
        
        .auth-hero::before {
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
        
        .auth-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Navigation minimaliste */
        .auth-nav {
            position: absolute;
            top: 2rem;
            left: 2rem;
            right: 2rem;
            z-index: 3;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .auth-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--secondary);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .auth-brand:hover {
            color: var(--accent);
        }
        
        .btn-home {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: var(--secondary);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Form Container */
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.3);
            max-width: 500px;
            margin: 0 auto;
            transform: translateY(0);
            animation: slideInUp 0.8s cubic-bezier(0.23, 1, 0.32, 1);
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .form-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: var(--secondary);
            margin: 0 auto 1.5rem;
            animation: iconFloat 3s ease-in-out infinite;
        }
        
        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .form-title {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .form-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }
        
        /* Form Elements */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: block;
            font-size: 0.9rem;
        }
        
        .form-control {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--secondary);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
            outline: none;
            transform: translateY(-2px);
        }
        
        .form-icon-input {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .form-group:focus-within .form-icon-input {
            color: var(--accent);
        }
        
        /* Password Toggle */
        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .password-toggle:hover {
            color: var(--accent);
        }
        
        /* Password Strength */
        .password-strength {
            margin-top: 0.5rem;
            display: none;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: var(--border);
            overflow: hidden;
            margin-bottom: 0.5rem;
        }
        
        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s ease;
            width: 0%;
        }
        
        .strength-fill.weak { background: var(--danger); width: 25%; }
        .strength-fill.fair { background: var(--warning); width: 50%; }
        .strength-fill.good { background: var(--info); width: 75%; }
        .strength-fill.strong { background: var(--success); width: 100%; }
        
        .strength-text {
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .strength-text.weak { color: var(--danger); }
        .strength-text.fair { color: var(--warning); }
        .strength-text.good { color: var(--info); }
        .strength-text.strong { color: var(--success); }
        
        /* Terms Checkbox */
        .terms-check {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        
        .terms-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 4px;
            background: var(--secondary);
            cursor: pointer;
            transition: all 0.3s ease;
            flex-shrink: 0;
            margin-top: 0.2rem;
        }
        
        .terms-check-input:checked {
            background: var(--accent);
            border-color: var(--accent);
        }
        
        .terms-check-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            cursor: pointer;
            line-height: 1.5;
        }
        
        .terms-check-label a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        
        .terms-check-label a:hover {
            text-decoration: underline;
        }
        
        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--secondary);
            border: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .btn-submit:hover::before {
            left: 100%;
        }
        
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(255, 107, 53, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(-1px);
        }
        
        /* Links */
        .form-links {
            text-align: center;
            margin-top: 2rem;
        }
        
        .form-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-link:hover {
            color: var(--accent-light);
            text-decoration: underline;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        
        .divider span {
            padding: 0 1rem;
        }
        
        /* Alert Messages */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            animation: alertSlide 0.5s ease;
        }
        
        @keyframes alertSlide {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        /* Social Logos flottants */
        .floating-social {
            position: absolute;
            z-index: 1;
        }
        
        .social-logo {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary);
            font-size: 1.5rem;
            animation: logoFloat 4s ease-in-out infinite;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        .social-logo.instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
            top: 15%;
            right: 10%;
            animation-delay: 0s;
        }
        
        .social-logo.tiktok {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
            top: 30%;
            left: 5%;
            animation-delay: 1s;
        }
        
        .social-logo.facebook {
            background: #1877f2;
            bottom: 40%;
            right: 8%;
            animation-delay: 2s;
        }
        
        .social-logo.youtube {
            background: #ff0000;
            bottom: 20%;
            left: 8%;
            animation-delay: 3s;
        }
        
        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        /* Loading State */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 0.5rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Form validation styling */
        .form-control.is-valid {
            border-color: var(--success);
        }
        
        .form-control.is-invalid {
            border-color: var(--danger);
        }
        
        .valid-feedback,
        .invalid-feedback {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }
        
        .valid-feedback {
            color: var(--success);
        }
        
        .invalid-feedback {
            color: var(--danger);
        }
        
        /* Responsive Mobile-first */
        @media (max-width: 768px) {
            .auth-hero::before {
                width: 100%;
                height: 50%;
                clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
            }
            
            .auth-nav {
                top: 1rem;
                left: 1rem;
                right: 1rem;
            }
            
            .auth-brand {
                font-size: 1.3rem;
            }
            
            .btn-home {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }
            
            .form-container {
                margin: 0 1rem;
                padding: 2rem;
                border-radius: 15px;
                max-width: none;
            }
            
            .form-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .form-title {
                font-size: 1.7rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }
            
            .floating-social {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .auth-container {
                padding: 1rem;
            }
            
            .form-container {
                padding: 1.5rem;
                margin: 0 0.5rem;
            }
            
            .form-control {
                padding: 0.9rem 0.9rem 0.9rem 2.5rem;
            }
            
            .btn-submit {
                padding: 1rem;
                font-size: 1rem;
            }
        }
        
        /* Desktop Enhancement */
        @media (min-width: 1200px) {
            .auth-container {
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 4rem;
            }
            
            .welcome-content {
                padding-right: 2rem;
            }
            
            .welcome-title {
                font-family: 'Space Grotesk', sans-serif;
                font-size: clamp(2.5rem, 4vw, 3.5rem);
                font-weight: 800;
                line-height: 1.1;
                margin-bottom: 1.5rem;
                color: var(--secondary);
            }
            
            .welcome-subtitle {
                font-size: 1.2rem;
                color: rgba(255, 255, 255, 0.9);
                line-height: 1.6;
                margin-bottom: 2rem;
            }
            
            .welcome-benefits {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1.5rem;
            }
            
            .benefit-item {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                padding: 1.5rem;
                border-radius: 15px;
                border: 1px solid rgba(255, 255, 255, 0.2);
                transition: all 0.3s ease;
            }
            
            .benefit-item:hover {
                background: rgba(255, 255, 255, 0.15);
                transform: translateY(-5px);
            }
            
            .benefit-icon {
                width: 50px;
                height: 50px;
                background: var(--accent);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--secondary);
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }
            
            .benefit-title {
                font-weight: 700;
                color: var(--secondary);
                margin-bottom: 0.5rem;
            }
            
            .benefit-desc {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.9rem;
                line-height: 1.5;
            }
        }
    </style>
</head>
<body>
    <div class="auth-hero">
        <!-- Navigation -->
        <div class="auth-nav">
            <a href="/" class="auth-brand">
                <i class="fas fa-spider me-2"></i>TarantulaSMM
            </a>
            <a href="/" class="btn-home">
                <i class="fas fa-home me-2"></i>Accueil
            </a>
        </div>

        <!-- Logos sociaux flottants -->
        <div class="floating-social">
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
        </div>

        <div class="auth-container">
            <!-- Welcome Content (Desktop only) -->
            <div class="welcome-content d-none d-xl-block">
                <h1 class="welcome-title">
                    Rejoignez <span style="color: var(--accent);">TarantulaSMM</span> Bénin
                </h1>
                <p class="welcome-subtitle">
                    Créez votre compte et accédez aux meilleurs services SMM du Bénin. Boostez votre présence sur tous les réseaux sociaux !
                </p>
                
                <div class="welcome-benefits">
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div class="benefit-title">Livraison Express</div>
                        <div class="benefit-desc">Services livrés en quelques minutes pour un boost immédiat</div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <div class="benefit-title">100% Sécurisé</div>
                        <div class="benefit-desc">Vos données et transactions sont entièrement protégées</div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <div class="benefit-title">Mobile Money</div>
                        <div class="benefit-desc">Paiement facile avec MTN Money et Moov Money</div>
                    </div>
                    
                    <div class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="benefit-title">Qualité Premium</div>
                        <div class="benefit-desc">Services de la plus haute qualité pour tous vos réseaux</div>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="form-container">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h2 class="form-title">Inscription</h2>
                    <p class="form-subtitle">Créez votre compte TarantulaSMM gratuitement</p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
                <?php endif; ?>

                <form method="POST" id="registerForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Prénom *</label>
                            <div style="position: relative;">
                                <i class="fas fa-user form-icon-input"></i>
                                <input type="text" name="first_name" class="form-control" 
                                       placeholder="Votre prénom" 
                                       value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nom *</label>
                            <div style="position: relative;">
                                <i class="fas fa-user form-icon-input"></i>
                                <input type="text" name="last_name" class="form-control" 
                                       placeholder="Votre nom" 
                                       value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adresse Email *</label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope form-icon-input"></i>
                            <input type="email" name="email" id="email" class="form-control" 
                                   placeholder="votre@email.com" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Téléphone (optionnel)</label>
                        <div style="position: relative;">
                            <i class="fas fa-phone form-icon-input"></i>
                            <input type="tel" name="phone" class="form-control" 
                                   placeholder="+229 XX XX XX XX" 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de Passe *</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock form-icon-input"></i>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="••••••••" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="toggleIcon1"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar">
                                <div class="strength-fill" id="strengthFill"></div>
                            </div>
                            <div class="strength-text" id="strengthText">Entrez un mot de passe</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmer le Mot de Passe *</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock form-icon-input"></i>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" 
                                   placeholder="••••••••" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye" id="toggleIcon2"></i>
                            </button>
                        </div>
                    </div>

                    <div class="terms-check">
                        <input type="checkbox" name="terms" id="terms" class="terms-check-input" required>
                        <label for="terms" class="terms-check-label">
                            J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> et la 
                            <a href="#" target="_blank">politique de confidentialité</a> de TarantulaSMM. 
                            Je confirme avoir au moins 16 ans.
                        </label>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-user-plus me-2"></i>
                        Créer mon Compte
                    </button>
                </form>

                <div class="divider">
                    <span>Déjà membre ?</span>
                </div>

                <div class="form-links">
                    <a href="login.php" class="form-link">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(fieldId === 'password' ? 'toggleIcon1' : 'toggleIcon2');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Password strength checker
        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('passwordStrength');
            const strengthFill = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthIndicator.style.display = 'none';
                return;
            }
            
            strengthIndicator.style.display = 'block';
            
            let score = 0;
            let feedback = '';
            
            // Length check
            if (password.length >= 8) score += 1;
            if (password.length >= 12) score += 1;
            
            // Character variety checks
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;
            
            // Determine strength
            if (score < 3) {
                strengthFill.className = 'strength-fill weak';
                strengthText.className = 'strength-text weak';
                feedback = 'Mot de passe faible';
            } else if (score < 4) {
                strengthFill.className = 'strength-fill fair';
                strengthText.className = 'strength-text fair';
                feedback = 'Mot de passe moyen';
            } else if (score < 5) {
                strengthFill.className = 'strength-fill good';
                strengthText.className = 'strength-text good';
                feedback = 'Bon mot de passe';
            } else {
                strengthFill.className = 'strength-fill strong';
                strengthText.className = 'strength-text strong';
                feedback = 'Mot de passe très fort';
            }
            
            strengthText.textContent = feedback;
        }
        
        // Form validation
        const form = document.getElementById('registerForm');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        // Real-time password strength checking
        passwordInput.addEventListener('input', function() {
            checkPasswordStrength(this.value);
            validatePasswordMatch();
        });
        
        confirmPasswordInput.addEventListener('input', validatePasswordMatch);
        
        function validatePasswordMatch() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                } else {
                    confirmPasswordInput.classList.remove('is-valid');
                    confirmPasswordInput.classList.add('is-invalid');
                }
            } else {
                confirmPasswordInput.classList.remove('is-valid', 'is-invalid');
            }
        }
        
        // Email validation
        emailInput.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && emailRegex.test(this.value)) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if (this.value) {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const terms = document.getElementById('terms').checked;
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 6 caractères');
                return;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas');
                return;
            }
            
            if (!terms) {
                e.preventDefault();
                alert('Vous devez accepter les conditions d\'utilisation');
                return;
            }
            
            // Show loading
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<span class="spinner"></span>Création en cours...';
            submitBtn.disabled = true;
        });
        
        // Auto-focus first field
        window.addEventListener('load', () => {
            document.querySelector('input[name="first_name"]').focus();
        });
        
        // Social logos animation
        document.querySelectorAll('.social-logo').forEach(logo => {
            logo.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.1)';
            });
            
            logo.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
        
        // Phone number formatting (Benin format)
        const phoneInput = document.querySelector('input[name="phone"]');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            if (value.startsWith('229')) {
                // Already has country code
                if (value.length <= 11) {
                    value = value.replace(/(\d{3})(\d{2})(\d{2})(\d{2})(\d{2})/, '+$1 $2 $3 $4 $5');
                }
            } else if (value.length === 8) {
                // Add Benin country code
                value = '+229 ' + value.replace(/(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4');
            }
            
            e.target.value = value;
        });
    </script>
</body>
</html>