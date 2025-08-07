<?php
require_once 'includes/functions.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('admin/dashboard.php');
    } else {
        redirect('client/dashboard.php');
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs.';
    } else {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, email, password, full_name, is_admin, status FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && verifyPassword($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['is_admin'] = $user['is_admin'];
            
            // Redirection selon le type d'utilisateur
            if ($user['is_admin']) {
                redirect('admin/dashboard.php');
            } else {
                redirect('client/dashboard.php');
            }
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SMM Pro Services</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="index.php" class="logo">
                <i class="fas fa-rocket"></i>
                SMM Pro
            </a>
            
            <ul class="nav-menu">
                <li><a href="index.php">Accueil</a></li>
                <li><a href="index.php#services">Services</a></li>
                <li><a href="index.php#contact">Contact</a></li>
                <li><a href="register.php">S'inscrire</a></li>
            </ul>
            
            <button class="nav-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main>
        <div class="container" style="max-width: 500px; margin-top: 2rem;">
            <div class="card">
                <div class="card-header text-center">
                    <h1 class="card-title">
                        <i class="fas fa-sign-in-alt"></i>
                        Connexion
                    </h1>
                    <p class="text-muted">Connectez-vous à votre compte</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Adresse email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-control" 
                            placeholder="votre@email.com"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            Mot de passe
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            placeholder="Votre mot de passe"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i>
                        Se connecter
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <p class="text-muted">
                        Pas encore de compte ? 
                        <a href="register.php" class="text-primary">S'inscrire</a>
                    </p>
                </div>
                
                <!-- Informations de test -->
                <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius); margin-top: 2rem;">
                    <h4 class="text-primary mb-2">Comptes de test</h4>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">
                        <p><strong>Admin :</strong><br>
                        Email: admin@smmsite.com<br>
                        Mot de passe: admin123</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
</body>
</html>