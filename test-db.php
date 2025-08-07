<?php
/**
 * Test de Connexion Base de Données - TarantulaSMM Bénin
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Définir l'accès autorisé
define('TARANTULA_ACCESS', true);

$testResults = [];
$overallStatus = 'success';

// Test 1: Connexion directe avec les paramètres
try {
    $testResults[] = [
        'name' => 'Connexion à la base de données',
        'status' => 'testing',
        'message' => 'Test de connexion en cours...'
    ];
    
    $dsn = "mysql:host=localhost;dbname=u634930929_Ino;port=3306;charset=utf8mb4";
    $connection = new PDO($dsn, 'u634930929_Ino', 'Ino1234@', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $testResults[count($testResults) - 1] = [
        'name' => 'Connexion à la base de données',
        'status' => 'success',
        'message' => 'Connexion établie avec succès'
    ];
    
} catch (Exception $e) {
    $testResults[count($testResults) - 1] = [
        'name' => 'Connexion à la base de données',
        'status' => 'error',
        'message' => 'Erreur: ' . $e->getMessage()
    ];
    $overallStatus = 'error';
}

// Test 2: Vérification des informations de la base
if (isset($connection)) {
    try {
        $info = $connection->query('SELECT VERSION() as version, DATABASE() as database')->fetch();
        $testResults[] = [
            'name' => 'Informations MySQL',
            'status' => 'success',
            'message' => "Version: {$info['version']}, Base: {$info['database']}"
        ];
    } catch (Exception $e) {
        $testResults[] = [
            'name' => 'Informations MySQL',
            'status' => 'error',
            'message' => 'Erreur: ' . $e->getMessage()
        ];
        $overallStatus = 'error';
    }
}

// Test 3: Liste des tables existantes
if (isset($connection)) {
    try {
        $tables = $connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $testResults[] = [
            'name' => 'Tables existantes',
            'status' => 'info',
            'message' => count($tables) > 0 ? 
                'Trouvé ' . count($tables) . ' tables: ' . implode(', ', array_slice($tables, 0, 5)) . 
                (count($tables) > 5 ? '...' : '') :
                'Aucune table trouvée'
        ];
    } catch (Exception $e) {
        $testResults[] = [
            'name' => 'Tables existantes',
            'status' => 'error',
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
}

// Test 4: Vérification des tables TarantulaSMM
if (isset($connection)) {
    $tarantulasTables = [
        'users', 'user_sessions', 'categories', 'services', 
        'orders', 'support_tickets', 'support_messages', 
        'admin_users', 'system_settings', 'activity_logs'
    ];
    
    $existingTables = [];
    $missingTables = [];
    
    foreach ($tarantulasTables as $table) {
        try {
            $result = $connection->query("SHOW TABLES LIKE '$table'")->fetch();
            if ($result) {
                $existingTables[] = $table;
            } else {
                $missingTables[] = $table;
            }
        } catch (Exception $e) {
            $missingTables[] = $table;
        }
    }
    
    if (empty($missingTables)) {
        $testResults[] = [
            'name' => 'Tables TarantulaSMM',
            'status' => 'success',
            'message' => 'Toutes les tables TarantulaSMM sont présentes (' . count($existingTables) . '/10)'
        ];
    } else {
        $testResults[] = [
            'name' => 'Tables TarantulaSMM',
            'status' => 'warning',
            'message' => count($existingTables) . '/10 tables présentes. Manquantes: ' . implode(', ', $missingTables)
        ];
        if ($overallStatus !== 'error') {
            $overallStatus = 'warning';
        }
    }
}

// Test 5: Test des fonctions personnalisées
if (!empty($existingTables)) {
    try {
        require_once 'config/database.php';
        require_once 'includes/functions.php';
        
        $testQuery = getDB()->fetch("SELECT 1 as test");
        
        $testResults[] = [
            'name' => 'Fonctions TarantulaSMM',
            'status' => 'success',
            'message' => 'Classes et fonctions chargées avec succès'
        ];
    } catch (Exception $e) {
        $testResults[] = [
            'name' => 'Fonctions TarantulaSMM',
            'status' => 'warning',
            'message' => 'Erreur lors du chargement: ' . $e->getMessage()
        ];
        if ($overallStatus !== 'error') {
            $overallStatus = 'warning';
        }
    }
}

// Test 6: Vérification de l'utilisateur de démonstration
if (in_array('users', $existingTables ?? [])) {
    try {
        $demoUser = $connection->query("SELECT * FROM users WHERE email = 'demo@tarantulasmm.bj'")->fetch();
        
        if ($demoUser) {
            $testResults[] = [
                'name' => 'Utilisateur de démonstration',
                'status' => 'success',
                'message' => "Utilisateur démo trouvé: {$demoUser['first_name']} {$demoUser['last_name']}"
            ];
        } else {
            $testResults[] = [
                'name' => 'Utilisateur de démonstration',
                'status' => 'warning',
                'message' => 'Utilisateur de démonstration non trouvé'
            ];
            if ($overallStatus !== 'error') {
                $overallStatus = 'warning';
            }
        }
    } catch (Exception $e) {
        $testResults[] = [
            'name' => 'Utilisateur de démonstration',
            'status' => 'error',
            'message' => 'Erreur: ' . $e->getMessage()
        ];
    }
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .test-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 800px;
        }
        
        .test-header {
            background: var(--gradient-primary);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .test-body {
            padding: 2rem;
        }
        
        .test-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        
        .test-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .test-success { border-left: 4px solid #10b981; }
        .test-warning { border-left: 4px solid #f59e0b; }
        .test-error { border-left: 4px solid #ef4444; }
        .test-info { border-left: 4px solid #3b82f6; }
        
        .status-icon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            margin-right: 0.5rem;
        }
        
        .status-success { background: #10b981; color: white; }
        .status-warning { background: #f59e0b; color: white; }
        .status-error { background: #ef4444; color: white; }
        .status-info { background: #3b82f6; color: white; }
        
        .overall-status {
            text-align: center;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 600;
        }
        
        .overall-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .overall-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .overall-error { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
        
        .btn-test {
            background: var(--gradient-primary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .db-config {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="test-card">
                    <!-- En-tête -->
                    <div class="test-header">
                        <h1 class="h3 mb-2">
                            <i class="fas fa-database me-2"></i>Test Base de Données
                        </h1>
                        <p class="mb-0 opacity-90">Vérification de la connexion et configuration</p>
                    </div>
                    
                    <!-- Corps -->
                    <div class="test-body">
                        <!-- Configuration actuelle -->
                        <div class="db-config">
                            <h6><i class="fas fa-cog me-2"></i>Configuration Base de Données</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Hôte:</strong> localhost<br>
                                    <strong>Base:</strong> u634930929_Ino
                                </div>
                                <div class="col-md-6">
                                    <strong>Utilisateur:</strong> u634930929_Ino<br>
                                    <strong>Port:</strong> 3306
                                </div>
                            </div>
                        </div>
                        
                        <!-- Statut global -->
                        <div class="overall-status overall-<?php echo $overallStatus; ?>">
                            <?php if ($overallStatus === 'success'): ?>
                                <i class="fas fa-check-circle me-2"></i>Tous les tests sont réussis !
                            <?php elseif ($overallStatus === 'warning'): ?>
                                <i class="fas fa-exclamation-triangle me-2"></i>Tests réussis avec quelques avertissements
                            <?php else: ?>
                                <i class="fas fa-times-circle me-2"></i>Des erreurs ont été détectées
                            <?php endif; ?>
                        </div>
                        
                        <!-- Résultats des tests -->
                        <h5><i class="fas fa-list-check me-2"></i>Résultats des Tests</h5>
                        
                        <?php foreach ($testResults as $test): ?>
                            <div class="test-item test-<?php echo $test['status']; ?>">
                                <div class="d-flex align-items-center">
                                    <span class="status-icon status-<?php echo $test['status']; ?>">
                                        <?php if ($test['status'] === 'success'): ?>
                                            <i class="fas fa-check"></i>
                                        <?php elseif ($test['status'] === 'warning'): ?>
                                            <i class="fas fa-exclamation"></i>
                                        <?php elseif ($test['status'] === 'error'): ?>
                                            <i class="fas fa-times"></i>
                                        <?php else: ?>
                                            <i class="fas fa-info"></i>
                                        <?php endif; ?>
                                    </span>
                                    <div class="flex-grow-1">
                                        <strong><?php echo htmlspecialchars($test['name']); ?></strong>
                                        <div class="text-muted"><?php echo htmlspecialchars($test['message']); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <!-- Actions -->
                        <div class="row mt-4">
                            <div class="col-md-6 mb-3">
                                <?php if (in_array('users', $existingTables ?? [])): ?>
                                    <a href="login.php" class="btn btn-test w-100">
                                        <i class="fas fa-sign-in-alt me-2"></i>Aller à la Connexion
                                    </a>
                                <?php else: ?>
                                    <a href="install.php" class="btn btn-test w-100">
                                        <i class="fas fa-play me-2"></i>Installer les Tables
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="javascript:window.location.reload()" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-sync-alt me-2"></i>Actualiser les Tests
                                </a>
                            </div>
                        </div>
                        
                        <!-- Liens utiles -->
                        <div class="text-center mt-4">
                            <a href="/" class="text-muted text-decoration-none me-3">
                                <i class="fas fa-home me-1"></i>Accueil
                            </a>
                            <a href="test.php" class="text-muted text-decoration-none">
                                <i class="fas fa-vial me-1"></i>Test Général
                            </a>
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