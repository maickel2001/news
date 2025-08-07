<?php
/**
 * Installation TarantulaSMM Bénin - Initialisation Base de Données
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Gestion d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Définir l'accès autorisé
define('TARANTULA_ACCESS', true);

$installation_status = [];
$overall_success = true;

// Vérifier si l'installation a été demandée
$install_requested = isset($_POST['install']) && $_POST['install'] === 'true';

if ($install_requested) {
    try {
        // Configuration de la base de données
        $host = 'localhost';
        $dbname = 'u634930929_Ino';
        $username = 'u634930929_Ino';
        $password = 'Ino1234@';
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
        ]);
        
        $installation_status[] = ['step' => 'Connexion DB', 'status' => 'success', 'message' => 'Connexion établie'];
        
        // Lire et exécuter le schéma SQL
        $sql_file = 'database/schema.sql';
        if (!file_exists($sql_file)) {
            throw new Exception("Fichier schema.sql introuvable dans database/");
        }
        
        $sql_content = file_get_contents($sql_file);
        
        // Supprimer les lignes de commentaires et diviser en requêtes
        $sql_lines = explode("\n", $sql_content);
        $sql_queries = [];
        $current_query = '';
        
        foreach ($sql_lines as $line) {
            $line = trim($line);
            if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
                continue;
            }
            
            // Ignorer les commandes USE et CREATE DATABASE
            if (stripos($line, 'USE ') === 0 || stripos($line, 'CREATE DATABASE') === 0) {
                continue;
            }
            
            $current_query .= $line . "\n";
            
            if (substr($line, -1) === ';') {
                $sql_queries[] = trim($current_query);
                $current_query = '';
            }
        }
        
        // Exécuter chaque requête
        $executed = 0;
        $errors = 0;
        
        foreach ($sql_queries as $query) {
            if (empty(trim($query))) continue;
            
            try {
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                $stmt->closeCursor(); // Libérer le curseur
                $executed++;
            } catch (PDOException $e) {
                // Ignorer les erreurs de "table exists already"
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate key name') === false) {
                    $errors++;
                    error_log("SQL Error: " . $e->getMessage() . " - Query: " . substr($query, 0, 100));
                }
            }
        }
        
        $installation_status[] = ['step' => 'Tables', 'status' => 'success', 'message' => "$executed requêtes exécutées ($errors erreurs ignorées)"];
        
        // Créer un utilisateur admin par défaut
        $admin_email = 'admin@tarantulasmm.bj';
        $admin_password = password_hash('Admin123!', PASSWORD_ARGON2ID);
        
        try {
            // Vérifier d'abord si l'utilisateur existe
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $checkStmt->execute([$admin_email]);
            $existingUser = $checkStmt->fetch();
            $checkStmt->closeCursor();
            
            if ($existingUser) {
                // Mettre à jour l'utilisateur existant
                $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ?, user_type = 'admin', status = 'active' WHERE email = ?");
                $updateStmt->execute([$admin_password, $admin_email]);
                $updateStmt->closeCursor();
                $installation_status[] = ['step' => 'Admin', 'status' => 'success', 'message' => 'Compte admin mis à jour: admin@tarantulasmm.bj / Admin123!'];
            } else {
                // Créer un nouvel utilisateur admin
                $insertStmt = $pdo->prepare("INSERT INTO users (email, password_hash, first_name, last_name, status, email_verified, user_type) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $insertStmt->execute([$admin_email, $admin_password, 'Admin', 'TarantulaSMM', 'active', 1, 'admin']);
                $insertStmt->closeCursor();
                $installation_status[] = ['step' => 'Admin', 'status' => 'success', 'message' => 'Compte admin créé: admin@tarantulasmm.bj / Admin123!'];
            }
        } catch (PDOException $e) {
            $installation_status[] = ['step' => 'Admin', 'status' => 'warning', 'message' => 'Erreur admin: ' . $e->getMessage()];
        }
        
        // Insérer des paramètres système
        $settings = [
            'site_name' => 'TarantulaSMM Bénin',
            'site_description' => 'SMM Panel #1 au Bénin',
            'site_currency' => 'FCFA',
            'email_verification_required' => 'false',
            'maintenance_mode' => 'false'
        ];
        
        foreach ($settings as $key => $value) {
            try {
                $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$key, $value, $value]);
                $stmt->closeCursor();
            } catch (PDOException $e) {
                // Ignorer les erreurs de paramètres
                error_log("Settings error: " . $e->getMessage());
            }
        }
        
        $installation_status[] = ['step' => 'Configuration', 'status' => 'success', 'message' => 'Paramètres système configurés'];
        $installation_status[] = ['step' => 'Finalisation', 'status' => 'success', 'message' => 'Installation terminée avec succès !'];
        
    } catch (Exception $e) {
        $installation_status[] = ['step' => 'Erreur', 'status' => 'error', 'message' => $e->getMessage()];
        $overall_success = false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - TarantulaSMM Bénin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .install-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 700px;
            width: 100%;
        }
        
        .install-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .install-body {
            padding: 2rem;
        }
        
        .step-item {
            border-left: 4px solid #e5e7eb;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8fafc;
            border-radius: 0 8px 8px 0;
        }
        
        .step-item.success {
            border-left-color: #10b981;
            background: #f0fdf4;
        }
        
        .step-item.error {
            border-left-color: #ef4444;
            background: #fef2f2;
        }
        
        .step-item.warning {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }
        
        .btn-install {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .btn-install:disabled {
            opacity: 0.7;
            transform: none;
        }
        
        .warning-box {
            background: #fef3c7;
            border: 1px solid #fbbf24;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        
        .success-box {
            background: #dcfce7;
            border: 1px solid #22c55e;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="install-card">
                    <!-- En-tête -->
                    <div class="install-header">
                        <h1 class="h3 mb-2">
                            <i class="fas fa-magic me-2"></i>Installation TarantulaSMM
                        </h1>
                        <p class="mb-0 opacity-90">Configuration de la base de données</p>
                    </div>
                    
                    <!-- Corps -->
                    <div class="install-body">
                        <?php if (!$install_requested): ?>
                            <!-- Interface d'installation -->
                            <div class="warning-box">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Attention</h5>
                                <p class="mb-0">Cette installation va créer les tables nécessaires dans votre base de données. Si les tables existent déjà, elles ne seront pas supprimées.</p>
                            </div>
                            
                            <h5>Que fait cette installation ?</h5>
                            <ul>
                                <li>Création des tables de base de données</li>
                                <li>Configuration des paramètres système</li>
                                <li>Création d'un compte administrateur</li>
                                <li>Insertion des données de base</li>
                            </ul>
                            
                            <div class="alert alert-info">
                                <strong>Compte administrateur :</strong><br>
                                Email: admin@tarantulasmm.bj<br>
                                Mot de passe: Admin123!
                            </div>
                            
                            <form method="POST" action="">
                                <input type="hidden" name="install" value="true">
                                <button type="submit" class="btn-install">
                                    <i class="fas fa-play me-2"></i>Lancer l'Installation
                                </button>
                            </form>
                            
                        <?php else: ?>
                            <!-- Résultats de l'installation -->
                            <?php if ($overall_success): ?>
                                <div class="success-box">
                                    <h4 class="text-success"><i class="fas fa-check-circle me-2"></i>Installation Réussie !</h4>
                                    <p class="mb-3">TarantulaSMM a été installé avec succès.</p>
                                    <a href="login.php" class="btn btn-success me-2">
                                        <i class="fas fa-sign-in-alt me-1"></i>Se Connecter
                                    </a>
                                    <a href="/" class="btn btn-outline-primary">
                                        <i class="fas fa-home me-1"></i>Accueil
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <h5>Étapes d'Installation :</h5>
                            
                            <?php foreach ($installation_status as $step): ?>
                                <div class="step-item <?php echo $step['status']; ?>">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-<?php echo $step['status'] === 'success' ? 'check' : ($step['status'] === 'error' ? 'times' : 'exclamation'); ?> me-2"></i>
                                        <div>
                                            <strong><?php echo htmlspecialchars($step['step']); ?></strong>
                                            <div class="text-muted"><?php echo htmlspecialchars($step['message']); ?></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if (!$overall_success): ?>
                                <div class="mt-3">
                                    <a href="?" class="btn btn-warning">
                                        <i class="fas fa-refresh me-1"></i>Réessayer
                                    </a>
                                    <a href="test-db.php" class="btn btn-outline-info">
                                        <i class="fas fa-vial me-1"></i>Tester le Système
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <!-- Liens utiles -->
                        <div class="text-center mt-4 pt-3 border-top">
                            <small class="text-muted">
                                <a href="test-db.php" class="text-decoration-none me-3">
                                    <i class="fas fa-vial me-1"></i>Test Système
                                </a>
                                <a href="/" class="text-decoration-none">
                                    <i class="fas fa-home me-1"></i>Accueil
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>