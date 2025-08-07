<?php
/**
 * Script d'Installation - TarantulaSMM Bénin
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Définir l'accès autorisé
define('TARANTULA_ACCESS', true);

// Configuration temporaire pour l'installation
$dbConfig = [
    'host' => 'localhost',
    'port' => 3306,
    'charset' => 'utf8mb4',
    'username' => 'root',
    'password' => '',
    'database' => 'tarantulasmm_benin'
];

$installationSteps = [];
$errors = [];
$success = false;

// Fonction pour exécuter le schéma SQL
function executeSchemaFile($connection, $filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("Fichier de schéma introuvable: $filePath");
    }
    
    $sql = file_get_contents($filePath);
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $connection->exec($statement);
            } catch (PDOException $e) {
                // Ignorer les erreurs "table exists already" et similaires
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate entry') === false) {
                    throw $e;
                }
            }
        }
    }
}

// Traitement de l'installation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    try {
        // Étape 1: Connexion à MySQL sans base de données
        $installationSteps[] = "Connexion à MySQL...";
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
        $connection = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $installationSteps[] = "✅ Connexion MySQL réussie";
        
        // Étape 2: Créer la base de données
        $installationSteps[] = "Création de la base de données...";
        $connection->exec("CREATE DATABASE IF NOT EXISTS {$dbConfig['database']} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $connection->exec("USE {$dbConfig['database']}");
        $installationSteps[] = "✅ Base de données créée/sélectionnée";
        
        // Étape 3: Exécuter le schéma SQL
        $installationSteps[] = "Création des tables...";
        executeSchemaFile($connection, __DIR__ . '/database/schema.sql');
        $installationSteps[] = "✅ Tables créées avec succès";
        
        // Étape 4: Créer l'utilisateur de démonstration
        $installationSteps[] = "Création de l'utilisateur de démonstration...";
        
        // Hasher le mot de passe de démonstration
        $demoPassword = password_hash('Demo123!', PASSWORD_ARGON2ID, [
            'memory_cost' => 65536,
            'time_cost' => 4,
            'threads' => 3
        ]);
        
        // Insérer l'utilisateur de démonstration
        $stmt = $connection->prepare("
            INSERT IGNORE INTO users (email, password_hash, first_name, last_name, phone, status, email_verified) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            'demo@tarantulasmm.bj',
            $demoPassword,
            'Utilisateur',
            'Démonstration',
            '+22997000000',
            'active',
            1
        ]);
        $installationSteps[] = "✅ Utilisateur de démonstration créé";
        
        // Étape 5: Vérification des tables
        $installationSteps[] = "Vérification des tables...";
        $tables = $connection->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        $expectedTables = [
            'users', 'user_sessions', 'categories', 'services', 'orders', 
            'support_tickets', 'support_messages', 'admin_users', 
            'system_settings', 'activity_logs'
        ];
        
        $missingTables = array_diff($expectedTables, $tables);
        if (empty($missingTables)) {
            $installationSteps[] = "✅ Toutes les tables sont présentes (" . count($tables) . " tables)";
        } else {
            throw new Exception("Tables manquantes: " . implode(', ', $missingTables));
        }
        
        // Étape 6: Test de connexion avec les fonctions personnalisées
        $installationSteps[] = "Test des fonctions personnalisées...";
        require_once 'config/database.php';
        require_once 'includes/functions.php';
        
        $testUser = dbFetch("SELECT COUNT(*) as count FROM users");
        $installationSteps[] = "✅ Fonctions de base de données opérationnelles";
        
        $success = true;
        $installationSteps[] = "🎉 Installation terminée avec succès !";
        
    } catch (Exception $e) {
        $errors[] = "Erreur: " . $e->getMessage();
        $installationSteps[] = "❌ Installation échouée";
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        
        .install-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 600px;
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
            padding: 0.5rem 0;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.9rem;
        }
        
        .config-info {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-install {
            background: var(--gradient-primary);
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .alert-success {
            border-left: 4px solid #10b981;
        }
        
        .alert-danger {
            border-left: 4px solid #ef4444;
        }
        
        .installation-log {
            background: #1e293b;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.85rem;
            max-height: 300px;
            overflow-y: auto;
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
                            <i class="fas fa-spider me-2"></i>Installation TarantulaSMM
                        </h1>
                        <p class="mb-0 opacity-90">Initialisation de la base de données</p>
                    </div>
                    
                    <!-- Corps -->
                    <div class="install-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle me-2"></i>Erreurs d'installation</h5>
                                <?php foreach ($errors as $error): ?>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle me-2"></i>Installation réussie !</h5>
                                <p class="mb-0">
                                    La base de données a été initialisée avec succès. 
                                    Vous pouvez maintenant utiliser les identifiants de démonstration pour vous connecter.
                                </p>
                            </div>
                            
                            <div class="text-center mt-4">
                                <a href="login.php" class="btn btn-install">
                                    <i class="fas fa-sign-in-alt me-2"></i>Aller à la connexion
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($installationSteps)): ?>
                            <div class="mb-4">
                                <h5>Journal d'installation:</h5>
                                <div class="installation-log">
                                    <?php foreach ($installationSteps as $step): ?>
                                        <div class="step-item"><?php echo htmlspecialchars($step); ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!$success && empty($_POST)): ?>
                            <!-- Configuration actuelle -->
                            <div class="config-info">
                                <h6><i class="fas fa-database me-2"></i>Configuration de la base de données</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <strong>Hôte:</strong> <?php echo $dbConfig['host']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Port:</strong> <?php echo $dbConfig['port']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Utilisateur:</strong> <?php echo $dbConfig['username']; ?>
                                    </div>
                                    <div class="col-sm-6">
                                        <strong>Base:</strong> <?php echo $dbConfig['database']; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Prérequis -->
                            <div class="mb-4">
                                <h6><i class="fas fa-list-check me-2"></i>Prérequis vérifiés</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <span class="<?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'text-success' : 'text-danger'; ?>">
                                            <i class="fas fa-<?php echo version_compare(PHP_VERSION, '7.4', '>=') ? 'check' : 'times'; ?> me-1"></i>
                                            PHP <?php echo PHP_VERSION; ?>
                                        </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="<?php echo extension_loaded('pdo_mysql') ? 'text-success' : 'text-danger'; ?>">
                                            <i class="fas fa-<?php echo extension_loaded('pdo_mysql') ? 'check' : 'times'; ?> me-1"></i>
                                            PDO MySQL
                                        </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="<?php echo extension_loaded('mbstring') ? 'text-success' : 'text-danger'; ?>">
                                            <i class="fas fa-<?php echo extension_loaded('mbstring') ? 'check' : 'times'; ?> me-1"></i>
                                            mbstring
                                        </span>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="<?php echo file_exists('database/schema.sql') ? 'text-success' : 'text-danger'; ?>">
                                            <i class="fas fa-<?php echo file_exists('database/schema.sql') ? 'check' : 'times'; ?> me-1"></i>
                                            Schema SQL
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informations importantes -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Informations importantes</h6>
                                <ul class="mb-0">
                                    <li>Cette installation créera la base de données et toutes les tables nécessaires</li>
                                    <li>Un utilisateur de démonstration sera créé avec les identifiants:</li>
                                    <ul>
                                        <li><strong>Email:</strong> demo@tarantulasmm.bj</li>
                                        <li><strong>Mot de passe:</strong> Demo123!</li>
                                    </ul>
                                    <li>Assurez-vous que MySQL est démarré et accessible</li>
                                </ul>
                            </div>
                            
                            <!-- Bouton d'installation -->
                            <form method="POST">
                                <button type="submit" name="install" class="btn btn-install">
                                    <i class="fas fa-play me-2"></i>Démarrer l'installation
                                </button>
                            </form>
                        <?php endif; ?>
                        
                        <!-- Lien vers l'accueil -->
                        <div class="text-center mt-4">
                            <a href="/" class="text-muted text-decoration-none">
                                <i class="fas fa-home me-1"></i>Retour à l'accueil
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-scroll du journal d'installation
        const log = document.querySelector('.installation-log');
        if (log) {
            log.scrollTop = log.scrollHeight;
        }
    </script>
</body>
</html>