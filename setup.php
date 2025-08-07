<?php
/**
 * Script de configuration initiale - SMM Pro Services
 * À exécuter après l'installation pour sécuriser le site
 */

require_once 'config/database.php';

// Vérifier si le script a déjà été exécuté
if (file_exists('.setup_completed')) {
    die('Configuration déjà effectuée. Supprimez le fichier .setup_completed pour relancer.');
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_email = sanitizeInput($_POST['admin_email']);
    $admin_password = $_POST['admin_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_name = sanitizeInput($_POST['admin_name']);
    
    // Validation
    if (empty($admin_email) || empty($admin_password) || empty($admin_name)) {
        $error = 'Tous les champs sont requis.';
    } elseif (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email invalide.';
    } elseif (strlen($admin_password) < 8) {
        $error = 'Le mot de passe doit contenir au moins 8 caractères.';
    } elseif ($admin_password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        try {
            $pdo = getDBConnection();
            
            // Mettre à jour l'admin par défaut
            $hashedPassword = hashPassword($admin_password);
            $stmt = $pdo->prepare("
                UPDATE users 
                SET email = ?, password = ?, full_name = ?, updated_at = NOW() 
                WHERE is_admin = 1 
                LIMIT 1
            ");
            $stmt->execute([$admin_email, $hashedPassword, $admin_name]);
            
            if ($stmt->rowCount() > 0) {
                // Marquer la configuration comme terminée
                file_put_contents('.setup_completed', date('Y-m-d H:i:s'));
                $message = 'Configuration terminée avec succès ! Vous pouvez maintenant vous connecter avec vos nouveaux identifiants.';
            } else {
                $error = 'Erreur lors de la mise à jour. Vérifiez que la base de données est correctement importée.';
            }
        } catch (Exception $e) {
            $error = 'Erreur de base de données : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Initiale - SMM Pro Services</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0f23 0%, #16213e 100%);
            color: #ffffff;
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .setup-container {
            background: #1a1a2e;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            border: 1px solid #333;
        }
        .setup-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .setup-header h1 {
            color: #00ff88;
            margin-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            background: #16213e;
            border: 1px solid #333;
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: #00ff88;
            box-shadow: 0 0 0 3px rgba(0, 255, 136, 0.1);
        }
        .btn {
            background: linear-gradient(135deg, #00ff88, #00cc6a);
            color: #0f0f23;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            font-size: 1rem;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 255, 136, 0.3);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid #28a745;
        }
        .alert-error {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid #dc3545;
        }
        .security-note {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            color: #ffc107;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <div class="setup-header">
            <h1>🚀 SMM Pro Services</h1>
            <p>Configuration initiale de sécurité</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                ✅ <?php echo $message; ?>
                <br><br>
                <a href="login.php" style="color: #28a745; text-decoration: none; font-weight: 600;">
                    → Se connecter maintenant
                </a>
            </div>
        <?php else: ?>
            <div class="security-note">
                ⚠️ <strong>Important :</strong> Vous devez configurer un nouveau compte administrateur pour sécuriser votre site. Les identifiants par défaut seront remplacés.
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    ❌ <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="admin_name" class="form-label">Nom complet de l'administrateur</label>
                    <input 
                        type="text" 
                        id="admin_name" 
                        name="admin_name" 
                        class="form-control" 
                        value="<?php echo isset($_POST['admin_name']) ? htmlspecialchars($_POST['admin_name']) : ''; ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="admin_email" class="form-label">Nouvel email administrateur</label>
                    <input 
                        type="email" 
                        id="admin_email" 
                        name="admin_email" 
                        class="form-control" 
                        value="<?php echo isset($_POST['admin_email']) ? htmlspecialchars($_POST['admin_email']) : ''; ?>"
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="admin_password" class="form-label">Nouveau mot de passe (min. 8 caractères)</label>
                    <input 
                        type="password" 
                        id="admin_password" 
                        name="admin_password" 
                        class="form-control" 
                        required
                    >
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Confirmer le mot de passe</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-control" 
                        required
                    >
                </div>
                
                <button type="submit" class="btn">
                    🔒 Configurer le compte administrateur
                </button>
            </form>
            
            <div style="margin-top: 1.5rem; font-size: 0.9rem; color: #888; text-align: center;">
                Ce script se désactivera automatiquement après configuration.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>