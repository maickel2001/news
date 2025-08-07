<?php
/**
 * Page de Vérification Email - TarantulaSMM Bénin
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

$status = 'pending';
$message = '';
$userEmail = '';

// Vérifier si un token est fourni
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $status = 'error';
    $message = 'Token de vérification manquant ou invalide.';
} else {
    $token = sanitizeString($_GET['token']);
    
    try {
        // Chercher l'utilisateur avec ce token
        $user = dbFetch(
            "SELECT * FROM users WHERE email_verification_token = ? AND status = 'inactive'",
            [$token]
        );
        
        if (!$user) {
            $status = 'error';
            $message = 'Token de vérification invalide ou compte déjà activé.';
        } else {
            // Vérifier si le token n'est pas expiré (24 heures)
            $tokenAge = time() - strtotime($user['created_at']);
            if ($tokenAge > 86400) { // 24 heures
                $status = 'expired';
                $message = 'Le lien de vérification a expiré. Veuillez vous inscrire à nouveau.';
                $userEmail = $user['email'];
            } else {
                // Activer le compte
                $updated = dbUpdate('users', [
                    'status' => 'active',
                    'email_verified' => 1,
                    'email_verification_token' => null
                ], 'id = ?', [$user['id']]);
                
                if ($updated) {
                    $status = 'success';
                    $message = 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.';
                    $userEmail = $user['email'];
                    
                    // Logger l'activité
                    logActivity('email_verified', "Email vérifié pour: {$user['email']}", [
                        'user_id' => $user['id']
                    ]);
                    
                    // Envoyer un email de bienvenue
                    $welcomeSubject = "Bienvenue sur TarantulaSMM Bénin !";
                    $welcomeMessage = "
                        <h2>Bienvenue {$user['first_name']} !</h2>
                        <p>Votre compte TarantulaSMM a été activé avec succès.</p>
                        <p>Vous pouvez maintenant :</p>
                        <ul>
                            <li>Commander des services de boost pour vos réseaux sociaux</li>
                            <li>Suivre vos commandes en temps réel</li>
                            <li>Contacter notre support béninois 24h/24</li>
                        </ul>
                        <p><a href='" . getBaseURL() . "/login.php' style='background: #6366f1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px;'>Se connecter maintenant</a></p>
                        <hr>
                        <p><small>TarantulaSMM Bénin - Boost tes réseaux sociaux</small></p>
                    ";
                    
                    sendEmail($user['email'], $welcomeSubject, $welcomeMessage);
                } else {
                    $status = 'error';
                    $message = 'Erreur lors de l\'activation du compte. Veuillez réessayer.';
                }
            }
        }
        
    } catch (Exception $e) {
        error_log("Email verification error: " . $e->getMessage());
        $status = 'error';
        $message = 'Erreur système. Veuillez réessayer plus tard.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Email - TarantulaSMM Bénin</title>
    <meta name="description" content="Vérification de votre adresse email pour activer votre compte TarantulaSMM.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* Variables CSS intégrées */
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
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .verify-page {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .verify-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        
        .verify-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .verify-body {
            padding: 2.5rem;
            text-align: center;
        }
        
        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .status-success {
            background: #f0fdf4;
            color: var(--success-color);
        }
        
        .status-error {
            background: #fef2f2;
            color: var(--danger-color);
        }
        
        .status-expired {
            background: #fffbeb;
            color: #d97706;
        }
        
        .status-pending {
            background: #f8fafc;
            color: #64748b;
        }
        
        .status-message {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .btn-verify {
            background: var(--gradient-primary);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .btn-secondary {
            background: #6b7280;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn-secondary:hover {
            background: #4b5563;
            color: white;
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
        
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 576px) {
            .verify-body {
                padding: 1.5rem;
            }
            
            .verify-header {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="verify-page">
        <!-- Bouton retour -->
        <a href="/" class="back-to-home">
            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
        </a>
        
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="verify-card">
                        <!-- En-tête -->
                        <div class="verify-header">
                            <h1 class="h3 mb-2">
                                <i class="fas fa-envelope-check me-2"></i>Vérification Email
                            </h1>
                            <p class="mb-0 opacity-90">Activation de votre compte</p>
                        </div>
                        
                        <!-- Corps -->
                        <div class="verify-body">
                            <?php if ($status === 'pending'): ?>
                                <!-- État en cours -->
                                <div class="spinner"></div>
                                <div class="status-icon status-pending">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="status-message">
                                    Vérification en cours...
                                </div>
                                
                            <?php elseif ($status === 'success'): ?>
                                <!-- Succès -->
                                <div class="status-icon status-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <h4 class="text-success mb-3">Compte Activé !</h4>
                                <div class="status-message">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                                
                                <?php if ($userEmail): ?>
                                    <div class="alert alert-info">
                                        <small>
                                            <i class="fas fa-info-circle me-1"></i>
                                            Compte activé pour : <strong><?php echo htmlspecialchars($userEmail); ?></strong>
                                        </small>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mt-4">
                                    <a href="login.php" class="btn-verify">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </a>
                                </div>
                                
                            <?php elseif ($status === 'expired'): ?>
                                <!-- Expiré -->
                                <div class="status-icon status-expired">
                                    <i class="fas fa-hourglass-end"></i>
                                </div>
                                <h4 class="text-warning mb-3">Lien Expiré</h4>
                                <div class="status-message">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="register.php" class="btn-verify">
                                        <i class="fas fa-user-plus me-2"></i>S'inscrire à nouveau
                                    </a>
                                    <a href="/" class="btn-secondary">
                                        <i class="fas fa-home me-2"></i>Accueil
                                    </a>
                                </div>
                                
                            <?php else: ?>
                                <!-- Erreur -->
                                <div class="status-icon status-error">
                                    <i class="fas fa-times"></i>
                                </div>
                                <h4 class="text-danger mb-3">Erreur de Vérification</h4>
                                <div class="status-message">
                                    <?php echo htmlspecialchars($message); ?>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="register.php" class="btn-verify">
                                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                                    </a>
                                    <a href="login.php" class="btn-secondary">
                                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Aide -->
                            <div class="mt-4 pt-4 border-top">
                                <small class="text-muted">
                                    <i class="fas fa-question-circle me-1"></i>
                                    Besoin d'aide ? 
                                    <a href="mailto:support@tarantulasmm.bj" class="text-decoration-none">
                                        Contactez le support
                                    </a>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-redirection après succès
        <?php if ($status === 'success'): ?>
            setTimeout(function() {
                const loginBtn = document.querySelector('a[href="login.php"]');
                if (loginBtn) {
                    loginBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Redirection...';
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1000);
                }
            }, 3000);
        <?php endif; ?>
    </script>
</body>
</html>