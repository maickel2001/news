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

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = sanitizeInput($_POST['full_name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($full_name)) {
        $errors[] = 'Le nom complet est requis.';
    }
    
    if (empty($email)) {
        $errors[] = 'L\'email est requis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format d\'email invalide.';
    }
    
    if (empty($password)) {
        $errors[] = 'Le mot de passe est requis.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
    }
    
    if ($password !== $confirm_password) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    
    // Vérifier si l'email existe déjà
    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Un compte avec cet email existe déjà.';
        }
    }
    
    // Créer le compte
    if (empty($errors)) {
        try {
            $hashedPassword = hashPassword($password);
            $stmt = $pdo->prepare("
                INSERT INTO users (email, password, full_name, phone, is_admin, status) 
                VALUES (?, ?, ?, ?, 0, 'active')
            ");
            $stmt->execute([$email, $hashedPassword, $full_name, $phone]);
            
            // Connexion automatique
            $user_id = $pdo->lastInsertId();
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $full_name;
            $_SESSION['is_admin'] = 0;
            
            setMessage('Compte créé avec succès ! Bienvenue sur SMM Pro Services.', 'success');
            redirect('client/dashboard.php');
            
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de la création du compte. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - SMM Pro Services</title>
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
                <li><a href="login.php">Se connecter</a></li>
            </ul>
            
            <button class="nav-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main>
        <div class="container" style="max-width: 600px; margin-top: 2rem;">
            <div class="card">
                <div class="card-header text-center">
                    <h1 class="card-title">
                        <i class="fas fa-user-plus"></i>
                        Créer un compte
                    </h1>
                    <p class="text-muted">Rejoignez SMM Pro Services et boostez votre présence sociale</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i>
                        <ul style="margin: 0; padding-left: 1.5rem;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="full_name" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Nom complet
                                </label>
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    name="full_name" 
                                    class="form-control" 
                                    placeholder="Votre nom complet"
                                    value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="col-6">
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
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Téléphone (optionnel)
                                </label>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    class="form-control" 
                                    placeholder="+226 70 00 00 00"
                                    value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                >
                            </div>
                        </div>
                        
                        <div class="col-6">
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
                                    placeholder="Au moins 6 caractères"
                                    required
                                >
                                <small class="text-muted">Minimum 6 caractères</small>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Confirmer le mot de passe
                                </label>
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    class="form-control" 
                                    placeholder="Répétez votre mot de passe"
                                    required
                                >
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="d-flex align-items-center" style="cursor: pointer;">
                            <input type="checkbox" required style="margin-right: 0.5rem;">
                            <span class="text-muted" style="font-size: 0.9rem;">
                                J'accepte les <a href="#" class="text-primary">conditions d'utilisation</a> 
                                et la <a href="#" class="text-primary">politique de confidentialité</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-user-plus"></i>
                        Créer mon compte
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <p class="text-muted">
                        Déjà un compte ? 
                        <a href="login.php" class="text-primary">Se connecter</a>
                    </p>
                </div>
                
                <!-- Avantages de l'inscription -->
                <div style="background: var(--darker-bg); padding: 1.5rem; border-radius: var(--border-radius); margin-top: 2rem;">
                    <h4 class="text-primary mb-2">Pourquoi créer un compte ?</h4>
                    <div style="color: var(--text-secondary); font-size: 0.9rem;">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-primary" style="margin-right: 0.5rem;"></i>
                            <span>Accès à tous nos services SMM</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-primary" style="margin-right: 0.5rem;"></i>
                            <span>Suivi en temps réel de vos commandes</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-primary" style="margin-right: 0.5rem;"></i>
                            <span>Historique complet de vos achats</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check text-primary" style="margin-right: 0.5rem;"></i>
                            <span>Support client prioritaire</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/main.js"></script>
    
    <script>
        // Validation côté client
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>