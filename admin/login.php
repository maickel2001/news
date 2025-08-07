<?php
session_start();
require_once '../config/database.php';

// Rediriger si déjà connecté
if (isAdmin()) {
    redirect('dashboard.php');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide';
    }
    
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email)) {
        $errors[] = 'Email requis';
    } elseif (!validateEmail($email)) {
        $errors[] = 'Email invalide';
    }
    
    if (empty($password)) {
        $errors[] = 'Mot de passe requis';
    }
    
    if (empty($errors)) {
        // Vérifier les identifiants
        $stmt = $db->query("SELECT * FROM admin_users WHERE email = ? AND status = 'active'", [$email]);
        $admin = $stmt->fetch();
        
        if ($admin && password_verify($password, $admin['password'])) {
            // Connexion réussie
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_logged_in'] = true;
            
            // Mettre à jour la dernière connexion
            $db->update('admin_users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $admin['id']]);
            
            redirect('dashboard.php');
        } else {
            $errors[] = 'Email ou mot de passe incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - SMM Boost</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--secondary-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: var(--card-bg);
            padding: 3rem;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .login-header p {
            color: var(--text-gray);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-shield-alt"></i> Admin Panel</h1>
            <p>Connectez-vous pour accéder au tableau de bord</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <ul style="margin: 0; padding-left: 1.5rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" id="loginForm">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input type="email" name="email" id="email" class="form-control" 
                       placeholder="admin@smmboost.com" required 
                       value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Mot de passe
                </label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" class="form-control" 
                           placeholder="••••••••" required>
                    <button type="button" onclick="togglePassword()" 
                            style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-gray); cursor: pointer;">
                        <i class="fas fa-eye" id="passwordToggle"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>
        </form>
        
        <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
            <p style="color: var(--text-muted); font-size: 0.9rem;">
                <i class="fas fa-info-circle"></i> 
                Compte par défaut: admin@smmboost.com / admin123
            </p>
            <a href="../index.php" style="color: var(--primary-color); text-decoration: none;">
                <i class="fas fa-home"></i> Retour au site
            </a>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye';
            }
        }
        
        // Auto-focus sur le premier champ
        document.getElementById('email').focus();
        
        // Validation du formulaire
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs');
                return;
            }
            
            // Validation email basique
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Format d\'email invalide');
                return;
            }
        });
    </script>
</body>
</html>