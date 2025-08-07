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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember_me = isset($_POST['remember_me']);
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Vérifier d'abord dans admin_users
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE email = ?");
            $stmt->execute([$email]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Admin login
                loginUser($admin, 'admin', $remember_me);
                
                // Mettre à jour last_login et last_ip pour admin
                $stmt = $db->prepare("UPDATE admin_users SET last_login = NOW(), last_ip = ? WHERE id = ?");
                $stmt->execute([$_SERVER['REMOTE_ADDR'], $admin['id']]);
                
                logActivity($admin['id'], 'admin_login', 'Connexion administrateur depuis ' . $_SERVER['REMOTE_ADDR']);
                header('Location: admin/dashboard.php');
                exit();
            } else {
                // Vérifier dans users
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($user && password_verify($password, $user['password'])) {
                    if ($user['email_verified'] == 0) {
                        $error = 'Veuillez vérifier votre email avant de vous connecter.';
                    } else {
                        // User login
                        loginUser($user, 'user', $remember_me);
                        
                        // Mettre à jour last_login et last_ip pour user
                        $stmt = $db->prepare("UPDATE users SET last_login = NOW(), last_ip = ? WHERE id = ?");
                        $stmt->execute([$_SERVER['REMOTE_ADDR'], $user['id']]);
                        
                        logActivity($user['id'], 'user_login', 'Connexion utilisateur depuis ' . $_SERVER['REMOTE_ADDR']);
                        header('Location: dashboard.php');
                        exit();
                    }
                } else {
                    $error = 'Email ou mot de passe incorrect.';
                }
            }
        } catch (Exception $e) {
            $error = 'Erreur de connexion. Veuillez réessayer.';
            error_log("Login error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - TarantulaSMM Bénin</title>
    
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
            max-width: 450px;
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
        
        /* Remember Me */
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .form-check-input {
            width: 20px;
            height: 20px;
            border: 2px solid var(--border);
            border-radius: 4px;
            background: var(--secondary);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-check-input:checked {
            background: var(--accent);
            border-color: var(--accent);
        }
        
        .form-check-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            cursor: pointer;
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
            
            .welcome-features {
                list-style: none;
                padding: 0;
            }
            
            .welcome-features li {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1rem;
                color: rgba(255, 255, 255, 0.8);
                font-size: 1.1rem;
            }
            
            .welcome-features i {
                width: 40px;
                height: 40px;
                background: rgba(255, 255, 255, 0.1);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: var(--accent);
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
                    Connectez-vous à <span style="color: var(--accent);">TarantulaSMM</span>
                </h1>
                <p class="welcome-subtitle">
                    Accédez à votre tableau de bord et gérez vos services SMM avec la plateforme #1 au Bénin.
                </p>
                <ul class="welcome-features">
                    <li>
                        <i class="fas fa-bolt"></i>
                        Livraison ultra-rapide garantie
                    </li>
                    <li>
                        <i class="fas fa-shield-alt"></i>
                        Sécurité et confidentialité maximales
                    </li>
                    <li>
                        <i class="fas fa-headset"></i>
                        Support client 24h/7j en français
                    </li>
                    <li>
                        <i class="fas fa-mobile-alt"></i>
                        Paiement Mobile Money MTN/Moov
                    </li>
                </ul>
            </div>

            <!-- Form Container -->
            <div class="form-container">
                <div class="form-header">
                    <div class="form-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h2 class="form-title">Connexion</h2>
                    <p class="form-subtitle">Accédez à votre compte TarantulaSMM</p>
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

                <form method="POST" id="loginForm">
                    <div class="form-group">
                        <label class="form-label">Adresse Email</label>
                        <div style="position: relative;">
                            <i class="fas fa-envelope form-icon-input"></i>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="votre@email.com" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de Passe</label>
                        <div style="position: relative;">
                            <i class="fas fa-lock form-icon-input"></i>
                            <input type="password" name="password" id="password" class="form-control" 
                                   placeholder="••••••••" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" name="remember_me" id="remember_me" class="form-check-input">
                        <label for="remember_me" class="form-check-label">
                            Se souvenir de moi
                        </label>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Se Connecter
                    </button>
                </form>

                <div class="divider">
                    <span>Nouveau sur TarantulaSMM ?</span>
                </div>

                <div class="form-links">
                    <a href="register.php" class="form-link">
                        <i class="fas fa-user-plus me-2"></i>Créer un compte
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
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
        
        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.innerHTML = '<span class="spinner"></span>Connexion en cours...';
            submitBtn.disabled = true;
            
            // Re-enable button after 5 seconds as fallback
            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt me-2"></i>Se Connecter';
            }, 5000);
        });
        
        // Enhanced form validation
        const emailInput = document.querySelector('input[name="email"]');
        const passwordInput = document.querySelector('input[name="password"]');
        
        emailInput.addEventListener('blur', function() {
            if (this.value && !this.value.includes('@')) {
                this.setCustomValidity('Veuillez entrer une adresse email valide');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
        
        passwordInput.addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 6) {
                this.setCustomValidity('Le mot de passe doit contenir au moins 6 caractères');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Auto-focus first empty field
        window.addEventListener('load', () => {
            const firstEmptyInput = document.querySelector('input:not([value]):not([type="checkbox"])');
            if (firstEmptyInput) {
                firstEmptyInput.focus();
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'Enter') {
                document.getElementById('loginForm').submit();
            }
        });
        
        // Social logos animation on hover
        document.querySelectorAll('.social-logo').forEach(logo => {
            logo.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.1)';
            });
            
            logo.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
    </script>
</body>
</html>