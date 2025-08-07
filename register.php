<?php
/**
 * Page d'Inscription - TarantulaSMM Bénin
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

// Variables pour le formulaire
$errors = [];
$success = false;
$formData = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'phone' => '',
    'password' => '',
    'password_confirm' => ''
];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier le rate limiting
    if (!checkRateLimit('register', 3, 300)) { // 3 tentatives par 5 minutes
        $errors['general'] = 'Trop de tentatives. Veuillez attendre 5 minutes.';
    } else {
        // Vérifier le token CSRF
        if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
            $errors['general'] = 'Token de sécurité invalide. Veuillez recharger la page.';
        } else {
            // Nettoyer et récupérer les données
            $formData = sanitizeInput($_POST);
            
            // Valider les données
            $errors = validateRegistrationData($formData);
            
            // Si pas d'erreurs, créer l'utilisateur
            if (empty($errors)) {
                try {
                    // Hasher le mot de passe
                    $passwordHash = hashPassword($formData['password']);
                    
                    // Générer un token de vérification email
                    $emailVerificationToken = generateSecureToken();
                    
                    // Préparer les données utilisateur
                    $userData = [
                        'email' => $formData['email'],
                        'password_hash' => $passwordHash,
                        'first_name' => $formData['first_name'],
                        'last_name' => $formData['last_name'],
                        'phone' => $formData['phone'] ?: null,
                        'email_verification_token' => $emailVerificationToken,
                        'status' => getSetting('email_verification_required', true) ? 'inactive' : 'active'
                    ];
                    
                    // Insérer l'utilisateur
                    $userId = dbInsert('users', $userData);
                    
                    if ($userId) {
                        // Logger l'activité
                        logActivity('user_registered', "Nouvel utilisateur inscrit: {$formData['email']}", [
                            'user_id' => $userId,
                            'email' => $formData['email']
                        ]);
                        
                        // Envoyer l'email de vérification si requis
                        if (getSetting('email_verification_required', true)) {
                            $verificationLink = getBaseURL() . "/verify-email.php?token=" . $emailVerificationToken;
                            $emailSubject = "Vérifiez votre compte TarantulaSMM";
                            $emailMessage = "
                                <h2>Bienvenue sur TarantulaSMM Bénin !</h2>
                                <p>Bonjour {$formData['first_name']},</p>
                                <p>Merci de vous être inscrit sur notre plateforme. Pour activer votre compte, veuillez cliquer sur le lien ci-dessous :</p>
                                <p><a href='{$verificationLink}' style='background: #6366f1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>Vérifier mon email</a></p>
                                <p>Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :</p>
                                <p>{$verificationLink}</p>
                                <p>Ce lien expire dans 24 heures.</p>
                                <hr>
                                <p><small>TarantulaSMM Bénin - Boost tes réseaux sociaux</small></p>
                            ";
                            
                            sendEmail($formData['email'], $emailSubject, $emailMessage);
                        }
                        
                        $success = true;
                        
                        // Si la vérification email n'est pas requise, connecter directement
                        if (!getSetting('email_verification_required', true)) {
                            $user = dbFetch("SELECT * FROM users WHERE id = ?", [$userId]);
                            loginUser($user);
                            redirect('/dashboard');
                        }
                    } else {
                        $errors['general'] = 'Erreur lors de la création du compte. Veuillez réessayer.';
                    }
                    
                } catch (Exception $e) {
                    error_log("Registration error: " . $e->getMessage());
                    $errors['general'] = 'Erreur système. Veuillez réessayer plus tard.';
                }
            }
        }
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
    <title>Inscription - TarantulaSMM Bénin</title>
    <meta name="description" content="Créez votre compte TarantulaSMM pour booster vos réseaux sociaux au Bénin. Inscription rapide et sécurisée.">
    
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
            max-width: 500px;
            width: 100%;
        }
        
        .auth-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
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
        
        .btn-register {
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
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }
        
        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-register .spinner-border {
            width: 1.2rem;
            height: 1.2rem;
        }
        
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }
        
        .strength-meter {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.5rem;
        }
        
        .strength-meter-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-weak { background: #ef4444; width: 33%; }
        .strength-medium { background: #f59e0b; width: 66%; }
        .strength-strong { background: #10b981; width: 100%; }
        
        .error-message {
            background: #fef2f2;
            color: #b91c1c;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            border-left: 4px solid #ef4444;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .success-message {
            background: #f0fdf4;
            color: #166534;
            padding: 1.5rem;
            border-radius: 8px;
            border-left: 4px solid #10b981;
            margin-bottom: 1.5rem;
            text-align: center;
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
        
        @media (max-width: 576px) {
            .auth-body {
                padding: 1.5rem;
            }
            
            .auth-header {
                padding: 1.5rem;
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
                                <i class="fas fa-spider me-2"></i>Inscription
                            </h1>
                            <p class="mb-0 opacity-90">Créez votre compte TarantulaSMM</p>
                        </div>
                        
                        <!-- Corps du formulaire -->
                        <div class="auth-body">
                            <?php if (!empty($errors['general'])): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <?php echo htmlspecialchars($errors['general']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="success-message">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <h5 class="mb-2">Inscription réussie !</h5>
                                    <?php if (getSetting('email_verification_required', true)): ?>
                                        <p class="mb-0">Un email de vérification a été envoyé à votre adresse. Veuillez vérifier votre boîte de réception et cliquer sur le lien pour activer votre compte.</p>
                                    <?php else: ?>
                                        <p class="mb-0">Votre compte a été créé avec succès. Vous allez être redirigé vers votre tableau de bord.</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <form method="POST" action="" id="registerForm" novalidate>
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                    
                                    <!-- Prénom -->
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control<?php echo isset($errors['first_name']) ? ' is-invalid' : ''; ?>" 
                                            id="first_name" 
                                            name="first_name" 
                                            placeholder="Prénom"
                                            value="<?php echo htmlspecialchars($formData['first_name']); ?>"
                                            required
                                        >
                                        <label for="first_name">
                                            <i class="fas fa-user me-2"></i>Prénom
                                        </label>
                                        <?php if (isset($errors['first_name'])): ?>
                                            <div class="field-error"><?php echo $errors['first_name']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Nom -->
                                    <div class="form-floating">
                                        <input 
                                            type="text" 
                                            class="form-control<?php echo isset($errors['last_name']) ? ' is-invalid' : ''; ?>" 
                                            id="last_name" 
                                            name="last_name" 
                                            placeholder="Nom"
                                            value="<?php echo htmlspecialchars($formData['last_name']); ?>"
                                            required
                                        >
                                        <label for="last_name">
                                            <i class="fas fa-user me-2"></i>Nom
                                        </label>
                                        <?php if (isset($errors['last_name'])): ?>
                                            <div class="field-error"><?php echo $errors['last_name']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
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
                                        >
                                        <label for="email">
                                            <i class="fas fa-envelope me-2"></i>Adresse Email
                                        </label>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="field-error"><?php echo $errors['email']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Téléphone -->
                                    <div class="form-floating">
                                        <input 
                                            type="tel" 
                                            class="form-control<?php echo isset($errors['phone']) ? ' is-invalid' : ''; ?>" 
                                            id="phone" 
                                            name="phone" 
                                            placeholder="Téléphone"
                                            value="<?php echo htmlspecialchars($formData['phone']); ?>"
                                        >
                                        <label for="phone">
                                            <i class="fas fa-phone me-2"></i>Téléphone (optionnel)
                                        </label>
                                        <div class="form-text">Format: +229XXXXXXXX ou XXXXXXXX</div>
                                        <?php if (isset($errors['phone'])): ?>
                                            <div class="field-error"><?php echo $errors['phone']; ?></div>
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
                                        >
                                        <label for="password">
                                            <i class="fas fa-lock me-2"></i>Mot de passe
                                        </label>
                                        <div class="password-strength">
                                            <div class="strength-meter">
                                                <div class="strength-meter-fill" id="strengthMeter"></div>
                                            </div>
                                            <small id="strengthText" class="text-muted">
                                                Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre
                                            </small>
                                        </div>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="field-error"><?php echo $errors['password']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Confirmation mot de passe -->
                                    <div class="form-floating">
                                        <input 
                                            type="password" 
                                            class="form-control<?php echo isset($errors['password_confirm']) ? ' is-invalid' : ''; ?>" 
                                            id="password_confirm" 
                                            name="password_confirm" 
                                            placeholder="Confirmer le mot de passe"
                                            required
                                        >
                                        <label for="password_confirm">
                                            <i class="fas fa-lock me-2"></i>Confirmer le mot de passe
                                        </label>
                                        <?php if (isset($errors['password_confirm'])): ?>
                                            <div class="field-error"><?php echo $errors['password_confirm']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Bouton d'inscription -->
                                    <button type="submit" class="btn btn-register text-white" id="submitBtn">
                                        <span class="btn-text">
                                            <i class="fas fa-user-plus me-2"></i>Créer mon compte
                                        </span>
                                        <span class="btn-loading d-none">
                                            <span class="spinner-border spinner-border-sm me-2"></span>
                                            Création en cours...
                                        </span>
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <!-- Liens -->
                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Déjà un compte ? 
                                    <a href="login.php" class="text-decoration-none fw-semibold" style="color: var(--primary-color);">
                                        Se connecter
                                    </a>
                                </p>
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
            const form = document.getElementById('registerForm');
            const submitBtn = document.getElementById('submitBtn');
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirm');
            const strengthMeter = document.getElementById('strengthMeter');
            const strengthText = document.getElementById('strengthText');
            
            // Validation en temps réel du mot de passe
            if (passwordInput) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    const strength = calculatePasswordStrength(password);
                    updatePasswordStrength(strength);
                });
            }
            
            // Validation de confirmation du mot de passe
            if (confirmPasswordInput) {
                confirmPasswordInput.addEventListener('input', function() {
                    const password = passwordInput.value;
                    const confirm = this.value;
                    
                    if (confirm && password !== confirm) {
                        this.classList.add('is-invalid');
                        showFieldError(this, 'Les mots de passe ne correspondent pas');
                    } else {
                        this.classList.remove('is-invalid');
                        hideFieldError(this);
                    }
                });
            }
            
            // Validation du formulaire
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Validation de base de tous les champs requis
                    const requiredFields = form.querySelectorAll('input[required]');
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            showFieldError(field, 'Ce champ est requis');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                            hideFieldError(field);
                        }
                    });
                    
                    // Validation spécifique de l'email
                    const emailField = document.getElementById('email');
                    if (emailField && emailField.value) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(emailField.value)) {
                            emailField.classList.add('is-invalid');
                            showFieldError(emailField, 'Format d\'email invalide');
                            isValid = false;
                        }
                    }
                    
                    // Validation du mot de passe
                    if (passwordInput && passwordInput.value) {
                        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/;
                        if (!passwordRegex.test(passwordInput.value)) {
                            passwordInput.classList.add('is-invalid');
                            showFieldError(passwordInput, 'Mot de passe trop faible');
                            isValid = false;
                        }
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                        return;
                    }
                    
                    // Afficher le loader
                    showLoading();
                });
            }
            
            function calculatePasswordStrength(password) {
                let score = 0;
                
                // Longueur
                if (password.length >= 8) score += 1;
                if (password.length >= 12) score += 1;
                
                // Caractères
                if (/[a-z]/.test(password)) score += 1;
                if (/[A-Z]/.test(password)) score += 1;
                if (/[0-9]/.test(password)) score += 1;
                if (/[@$!%*?&]/.test(password)) score += 1;
                
                return Math.min(score, 3);
            }
            
            function updatePasswordStrength(strength) {
                const classes = ['strength-weak', 'strength-medium', 'strength-strong'];
                const texts = ['Faible', 'Moyen', 'Fort'];
                const colors = ['#ef4444', '#f59e0b', '#10b981'];
                
                strengthMeter.className = 'strength-meter-fill';
                
                if (strength > 0) {
                    strengthMeter.classList.add(classes[strength - 1]);
                    strengthText.textContent = 'Force du mot de passe: ' + texts[strength - 1];
                    strengthText.style.color = colors[strength - 1];
                } else {
                    strengthText.textContent = 'Au moins 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre';
                    strengthText.style.color = '#64748b';
                }
            }
            
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
        });
    </script>
</body>
</html>