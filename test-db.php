<?php
/**
 * Page de Test Base de Données - TarantulaSMM Bénin
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

$tests = [];
$overall_status = 'success';

// Test 1: Connexion base de données
try {
    $db = Database::getInstance();
    $tests['database'] = ['status' => 'success', 'message' => 'Connexion réussie'];
} catch (Exception $e) {
    $tests['database'] = ['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
    $overall_status = 'error';
}

// Test 2: Vérification des tables
try {
    $tables = ['users', 'user_sessions', 'categories', 'services', 'orders', 'support_tickets', 'support_messages', 'admin_users', 'system_settings', 'activity_logs'];
    $existing_tables = [];
    
    foreach ($tables as $table) {
        $stmt = $db->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existing_tables[] = $table;
        }
    }
    
    $tests['tables'] = [
        'status' => count($existing_tables) === count($tables) ? 'success' : 'warning',
        'message' => count($existing_tables) . '/' . count($tables) . ' tables trouvées',
        'details' => $existing_tables
    ];
    
    if (count($existing_tables) !== count($tables)) {
        $overall_status = 'warning';
    }
} catch (Exception $e) {
    $tests['tables'] = ['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
    $overall_status = 'error';
}

// Test 3: Fonctions utilitaires
try {
    $token = generateSecureToken();
    $hash = hashPassword('test123');
    $verify = verifyPassword('test123', $hash);
    
    $tests['functions'] = [
        'status' => $verify ? 'success' : 'error',
        'message' => $verify ? 'Fonctions de sécurité OK' : 'Problème avec les fonctions'
    ];
    
    if (!$verify) {
        $overall_status = 'error';
    }
} catch (Exception $e) {
    $tests['functions'] = ['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
    $overall_status = 'error';
}

// Test 4: Compte utilisateurs existants
try {
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    
    $tests['users'] = [
        'status' => 'info',
        'message' => "$userCount utilisateur(s) dans la base"
    ];
} catch (Exception $e) {
    $tests['users'] = ['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
}

// Test 5: Configuration système
try {
    $settings = [
        'site_name' => 'TarantulaSMM Bénin',
        'email_verification_required' => false,
        'site_currency' => 'FCFA'
    ];
    
    foreach ($settings as $key => $value) {
        $stmt = $db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    
    $tests['settings'] = ['status' => 'success', 'message' => 'Paramètres système configurés'];
} catch (Exception $e) {
    $tests['settings'] = ['status' => 'error', 'message' => 'Erreur: ' . $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Base de Données - TarantulaSMM Bénin</title>
    
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
            background: #f8fafc;
        }
        
        .test-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .test-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-left: 4px solid #e5e7eb;
        }
        
        .test-card.success {
            border-left-color: #10b981;
        }
        
        .test-card.error {
            border-left-color: #ef4444;
        }
        
        .test-card.warning {
            border-left-color: #f59e0b;
        }
        
        .test-card.info {
            border-left-color: #3b82f6;
        }
        
        .status-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
        }
        
        .status-success {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .status-error {
            background: #fef2f2;
            color: #dc2626;
        }
        
        .status-warning {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-info {
            background: #dbeafe;
            color: #2563eb;
        }
        
        .overall-status {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }
        
        .overall-success {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }
        
        .overall-warning {
            background: #fef3c7;
            color: #d97706;
            border: 1px solid #fde68a;
        }
        
        .overall-error {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        
        .btn-home {
            background: var(--gradient-primary);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }
        
        .refresh-btn {
            background: #f59e0b;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: #d97706;
            color: white;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="test-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-flask me-2"></i>Test Système - TarantulaSMM</h1>
                    <p class="mb-0">Vérification complète de la base de données et des fonctionnalités</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="?" class="refresh-btn">
                        <i class="fas fa-refresh me-1"></i>Actualiser
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="container">
        <!-- Statut global -->
        <div class="overall-status overall-<?php echo $overall_status; ?>">
            <?php if ($overall_status === 'success'): ?>
                <i class="fas fa-check-circle me-2"></i>Tous les tests sont réussis ! Le système est opérationnel.
            <?php elseif ($overall_status === 'warning'): ?>
                <i class="fas fa-exclamation-triangle me-2"></i>La plupart des tests sont OK, mais certains éléments nécessitent attention.
            <?php else: ?>
                <i class="fas fa-times-circle me-2"></i>Des erreurs ont été détectées. Le système nécessite une configuration.
            <?php endif; ?>
        </div>
        
        <!-- Tests détaillés -->
        <div class="row">
            <div class="col-lg-8">
                <!-- Test connexion BDD -->
                <div class="test-card <?php echo $tests['database']['status']; ?>">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-<?php echo $tests['database']['status']; ?>">
                            <i class="fas fa-database"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Connexion Base de Données</h5>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($tests['database']['message']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Test tables -->
                <div class="test-card <?php echo $tests['tables']['status']; ?>">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-<?php echo $tests['tables']['status']; ?>">
                            <i class="fas fa-table"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">Structure des Tables</h5>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($tests['tables']['message']); ?></p>
                            <?php if (isset($tests['tables']['details'])): ?>
                                <small class="text-success">
                                    Tables trouvées: <?php echo implode(', ', $tests['tables']['details']); ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Test fonctions -->
                <div class="test-card <?php echo $tests['functions']['status']; ?>">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-<?php echo $tests['functions']['status']; ?>">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Fonctions de Sécurité</h5>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($tests['functions']['message']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Test utilisateurs -->
                <div class="test-card <?php echo $tests['users']['status']; ?>">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-<?php echo $tests['users']['status']; ?>">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Utilisateurs</h5>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($tests['users']['message']); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Test paramètres -->
                <div class="test-card <?php echo $tests['settings']['status']; ?>">
                    <div class="d-flex align-items-center">
                        <div class="status-icon status-<?php echo $tests['settings']['status']; ?>">
                            <i class="fas fa-sliders-h"></i>
                        </div>
                        <div>
                            <h5 class="mb-1">Paramètres Système</h5>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($tests['settings']['message']); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations système -->
            <div class="col-lg-4">
                <div class="test-card info">
                    <h5><i class="fas fa-info-circle me-2"></i>Informations Système</h5>
                    <hr>
                    <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>Date:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                    <p><strong>Base de données:</strong> <?php echo DB_NAME; ?></p>
                    <p><strong>Environnement:</strong> <?php echo ENVIRONMENT; ?></p>
                    
                    <h6 class="mt-3">Extensions PHP:</h6>
                    <ul class="list-unstyled">
                        <?php
                        $extensions = ['mysqli', 'pdo', 'gd', 'curl', 'json', 'mbstring'];
                        foreach ($extensions as $ext) {
                            $loaded = extension_loaded($ext);
                            echo "<li><i class='fas fa-" . ($loaded ? 'check text-success' : 'times text-danger') . " me-2'></i>$ext</li>";
                        }
                        ?>
                    </ul>
                </div>
                
                <!-- Actions rapides -->
                <div class="test-card success">
                    <h5><i class="fas fa-rocket me-2"></i>Actions Rapides</h5>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="register.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-user-plus me-1"></i>Créer un Compte
                        </a>
                        <a href="login.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-sign-in-alt me-1"></i>Se Connecter
                        </a>
                        <a href="/" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-home me-1"></i>Page d'Accueil
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Retour accueil -->
        <div class="text-center mt-4">
            <a href="/" class="btn-home">
                <i class="fas fa-home me-2"></i>Retour à l'Accueil
            </a>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>