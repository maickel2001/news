<?php
/**
 * Page de Connexion - TarantulaSMM Bénin
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Définir l'accès autorisé
define('TARANTULA_ACCESS', true);

// Inclure les fichiers nécessaires
require_once 'config/database.php';
require_once 'includes/functions.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirect('/dashboard');
}

// Variables pour le formulaire
$errors = [];
$success = false;
$formData = [
    'email' => '',
    'password' => '',
    'remember_me' => false
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le rate limiting pour les tentatives de connexion
    if (!checkRateLimit('login', 5, 900)) { // 5 tentatives par 15 minutes
        $errors['general'] = 'Trop de tentatives de connexion. Veuillez attendre 15 minutes.';
    } else {
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $errors['general'] = 'Token de sécurité invalide. Veuillez recharger la page.';
        } else {
            // Nettoyer et récupérer les données
            $formData = sanitizeInput($_POST);
            $formData['remember_me'] = isset($_POST['remember_me']);
            
            // Valider les données
            $errors = validateLoginData($formData);
            
            // Si pas d'erreurs, tenter la connexion
            if (empty($errors)) {
                // Vérifier la protection contre le brute force
                if (!checkBruteForce($formData['email'])) {
                    $errors['general'] = 'Trop de tentatives de connexion échouées pour cet email. Veuillez attendre 15 minutes.';
                    logFailedLogin($formData['email']);
                } else {
                    try {
                        // Récupérer l'utilisateur
                        $user = dbFetch(
                            "SELECT * FROM users WHERE email = ? AND status != 'suspended'",
                            [$formData['email']]
                        );
                        
                        if ($user && verifyPassword($formData['password'], $user['password_hash'])) {
                            // Vérifier si le compte est actif
                            if ($user['status'] === 'inactive') {
                                if ($user['email_verified'] == 0) {
                                    $errors['general'] = 'Votre compte n\'est pas encore vérifié. Veuillez vérifier votre email.';
                                } else {
                                    $errors['general'] = 'Votre compte est temporairement désactivé. Contactez le support.';
                                }
                            } else {
                                // Connexion réussie
                                loginUser($user);
                                
                                // Gestion du "Se souvenir de moi"
                                if ($formData['remember_me']) {
                                    $rememberToken = generateSecureToken();
                                    $expires = time() + (30 * 24 * 3600); // 30 jours
                                    
                                    // Stocker le token dans la base
                                    dbUpdate('users', [
                                        'remember_token' => $rememberToken,
                                        'remember_expires' => date('Y-m-d H:i:s', $expires)
                                    ], 'id = ?', [$user['id']]);
                                    
                                    // Définir le cookie
                                    setcookie('remember_token', $rememberToken, $expires, '/', '', true, true);
                                }
                                
                                // Logger l'activité
                                logActivity('user_login', "Connexion utilisateur: {$user['email']}", [
                                    'user_id' => $user['id'],
                                    'remember_me' => $formData['remember_me']
                                ]);
                                
                                // Redirection après connexion
                                $redirectTo = $_GET['redirect'] ?? '/dashboard';
                                redirect($redirectTo);
                            }
                        } else {
                            // Identifiants incorrects
                            $errors['general'] = 'Email ou mot de passe incorrect.';
                            logFailedLogin($formData['email']);
                        }
                        
                    } catch (Exception $e) {
                        error_log("Login error: " . $e->getMessage());
                        $errors['general'] = 'Erreur système. Veuillez réessayer plus tard.';
                    }
                }
            }
        }
    }
}

// Vérification du token "Se souvenir de moi" si présent
if (!isLoggedIn() && isset($_COOKIE['remember_token'])) {
    $rememberToken = $_COOKIE['remember_token'];
    $user = dbFetch(
        "SELECT * FROM users WHERE remember_token = ? AND remember_expires > NOW() AND status = 'active'",
        [$rememberToken]
    );
    
    if ($user) {
        loginUser($user);
        redirect('/dashboard');
    } else {
        // Token invalide ou expiré, supprimer le cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
}

// Générer le token CSRF
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - TarantulaSMM Bénin</title>
    <meta name="description" content="Connectez-vous à votre compte TarantulaSMM pour gérer vos commandes et booster vos réseaux sociaux.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- CSS externe -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        .auth-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .auth-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .auth-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }
        
        .auth-body {
            padding: 2.5rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-floating .form-control {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            height: 58px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.25);
        }
        
        .form-floating label {
            color: #64748b;
            font-weight: 500;
        }
        
        .btn-login {
            background: var(--gradient-primary);
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-login .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .error-message {
            background: #fef2f2;
            color: #b91c1c;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border-left: 4px solid #ef4444;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .field-error {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-control.is-invalid {
            border-color: #ef4444;
        }
        
        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 25px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .back-to-home:hover {
            background: rgba(255,255,255,0.2);
            color: white;
            transform: translateX(-5px);
        }
        
        .social-login {
            border-top: 1px solid #e2e8f0;
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .demo-credentials {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }
        
        .demo-credentials h6 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        @media (max-width: 576px) {
            .auth-body {
                padding: 1.5rem;
            }
            
            .auth-header {
                padding: 1.5rem;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <div class="auth-page">
        <!-- Bouton retour -->
        <a href="/" class="back-to-home">
            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
        </a>
        
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="auth-card">
                        <!-- En-tête -->
                        <div class="auth-header">
                            <h1 class="h3 mb-2">
                                <i class="fas fa-spider me-2"></i>Connexion
                            </h1>
                            <p class="mb-0 opacity-90">Accédez à votre compte TarantulaSMM</p>
                        </div>
                        
                        <!-- Corps du formulaire -->
                        <div class="auth-body">
                            <?php if (!empty($errors['general'])): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo htmlspecialchars($errors['general']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Identifiants de démonstration -->
                            <div class="demo-credentials">
                                <h6><i class="fas fa-info-circle me-1"></i>Compte de démonstration</h6>
                                <div><strong>Email:</strong> demo@tarantulasmm.bj</div>
                                <div><strong>Mot de passe:</strong> Demo123!</div>
                            </div>
                            
                            <form method="POST" action="" id="loginForm" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <!-- Email -->
                                <div class="form-floating">
                                    <input 
                                        type="email" 
                                        class="form-control<?php echo isset($errors['email']) ? ' is-invalid' : ''; ?>" 
                                        id="email" 
                                        name="email" 
                                        placeholder="Email"
                                        value="<?php echo htmlspecialchars($formData['email']); ?>"
                                        required
                                        autocomplete="username"
                                    >
                                    <label for="email">
                                        <i class="fas fa-envelope me-2"></i>Adresse Email
                                    </label>
                                    <?php if (isset($errors['email'])): ?>
                                        <div class="field-error"><?php echo $errors['email']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Mot de passe -->
                                <div class="form-floating">
                                    <input 
                                        type="password" 
                                        class="form-control<?php echo isset($errors['password']) ? ' is-invalid' : ''; ?>" 
                                        id="password" 
                                        name="password" 
                                        placeholder="Mot de passe"
                                        required
                                        autocomplete="current-password"
                                    >
                                    <label for="password">
                                        <i class="fas fa-lock me-2"></i>Mot de passe
                                    </label>
                                    <?php if (isset($errors['password'])): ?>
                                        <div class="field-error"><?php echo $errors['password']; ?></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Se souvenir et mot de passe oublié -->
                                <div class="remember-forgot">
                                    <div class="form-check">
                                        <input 
                                            type="checkbox" 
                                            class="form-check-input" 
                                            id="remember_me" 
                                            name="remember_me"
                                            <?php echo $formData['remember_me'] ? 'checked' : ''; ?>
                                        >
                                        <label class="form-check-label" for="remember_me">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    
                                    <a href="forgot-password.php" class="forgot-password">
                                        Mot de passe oublié ?
                                    </a>
                                </div>
                                
                                <!-- Bouton de connexion -->
                                <button type="submit" class="btn btn-login text-white" id="submitBtn">
                                    <span class="btn-text">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </span>
                                    <span class="btn-loading d-none">
                                        <span class="spinner-border spinner-border-sm me-2"></span>
                                        Connexion...
                                    </span>
                                </button>
                            </form>
                            
                            <!-- Liens -->
                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Pas encore de compte ? 
                                    <a href="register.php" class="text-decoration-none fw-semibold" style="color: var(--primary-color);">
                                        S'inscrire gratuitement
                                    </a>
                                </p>
                            </div>
                            
                            <!-- Section sociale (future extension) -->
                            <div class="social-login d-none">
                                <div class="text-center mb-3">
                                    <small class="text-muted">Ou se connecter avec</small>
                                </div>
                                
                                <div class="row g-2">
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100">
                                            <i class="fab fa-google me-2"></i>Google
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100">
                                            <i class="fab fa-facebook-f me-2"></i>Facebook
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript pour la validation et UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            // Auto-fill avec les données de démonstration
            const demoCredentials = document.querySelector('.demo-credentials');
            if (demoCredentials) {
                demoCredentials.addEventListener('click', function() {
                    emailInput.value = 'demo@tarantulasmm.bj';
                    passwordInput.value = 'Demo123!';
                    
                    // Effet visuel
                    emailInput.focus();
                    emailInput.blur();
                    passwordInput.focus();
                    passwordInput.blur();
                });
                
                // Ajouter un style cursor pointer
                demoCredentials.style.cursor = 'pointer';
                demoCredentials.title = 'Cliquer pour remplir automatiquement';
            }
            
            // Validation du formulaire
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Validation de l'email
                    if (!emailInput.value.trim()) {
                        emailInput.classList.add('is-invalid');
                        showFieldError(emailInput, 'L\'email est requis');
                        isValid = false;
                    } else {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(emailInput.value)) {
                            emailInput.classList.add('is-invalid');
                            showFieldError(emailInput, 'Format d\'email invalide');
                            isValid = false;
                        } else {
                            emailInput.classList.remove('is-invalid');
                            hideFieldError(emailInput);
                        }
                    }
                    
                    // Validation du mot de passe
                    if (!passwordInput.value.trim()) {
                        passwordInput.classList.add('is-invalid');
                        showFieldError(passwordInput, 'Le mot de passe est requis');
                        isValid = false;
                    } else {
                        passwordInput.classList.remove('is-invalid');
                        hideFieldError(passwordInput);
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Afficher le loader
                    showLoading();
                });
            }
            
            // Validation en temps réel
            emailInput.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailRegex.test(this.value)) {
                        this.classList.remove('is-invalid');
                        hideFieldError(this);
                    }
                }
            });
            
            passwordInput.addEventListener('input', function() {
                if (this.classList.contains('is-invalid') && this.value.trim()) {
                    this.classList.remove('is-invalid');
                    hideFieldError(this);
                }
            });
            
            function showFieldError(field, message) {
                hideFieldError(field);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'field-error';
                errorDiv.textContent = message;
                field.parentNode.appendChild(errorDiv);
            }
            
            function hideFieldError(field) {
                const existingError = field.parentNode.querySelector('.field-error');
                if (existingError) {
                    existingError.remove();
                }
            }
            
            function showLoading() {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.querySelector('.btn-text').classList.add('d-none');
                    submitBtn.querySelector('.btn-loading').classList.remove('d-none');
                }
            }
            
            // Gestion des raccourcis clavier
            document.addEventListener('keydown', function(e) {
                // Ctrl+Enter pour soumettre le formulaire
                if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>