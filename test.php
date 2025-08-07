<?php
/**
 * Page de test - TarantulaSMM Bénin
 * Vérification du bon fonctionnement du serveur
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test - TarantulaSMM Bénin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0"><i class="fas fa-spider me-2"></i>Test TarantulaSMM Bénin</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>🔧 Tests Serveur</h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        PHP Version
                                        <span class="badge bg-success"><?php echo phpversion(); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        Date/Heure
                                        <span class="badge bg-info"><?php echo date('d/m/Y H:i:s'); ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        Timezone
                                        <span class="badge bg-secondary"><?php echo date_default_timezone_get(); ?></span>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <h5>📁 Tests Fichiers</h5>
                                <ul class="list-group list-group-flush">
                                    <?php
                                    $files_to_check = [
                                        'index.php' => 'Page d\'accueil',
                                        '.htaccess' => 'Configuration Apache',
                                        'assets/css/style.css' => 'CSS Principal',
                                        'assets/js/main.js' => 'JavaScript Principal'
                                    ];
                                    
                                    foreach ($files_to_check as $file => $description) {
                                        $exists = file_exists($file);
                                        $badge_class = $exists ? 'bg-success' : 'bg-danger';
                                        $status = $exists ? '✅' : '❌';
                                        echo "<li class='list-group-item d-flex justify-content-between'>";
                                        echo "{$description}";
                                        echo "<span class='badge {$badge_class}'>{$status}</span>";
                                        echo "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-12">
                                <h5>🌐 Extensions PHP</h5>
                                <div class="row">
                                    <?php
                                    $required_extensions = ['mysqli', 'gd', 'curl', 'json', 'mbstring'];
                                    
                                    foreach ($required_extensions as $ext) {
                                        $loaded = extension_loaded($ext);
                                        $badge_class = $loaded ? 'bg-success' : 'bg-warning';
                                        $status = $loaded ? '✅ Installé' : '⚠️ Manquant';
                                        
                                        echo "<div class='col-md-4 mb-2'>";
                                        echo "<span class='badge {$badge_class} w-100'>{$ext}: {$status}</span>";
                                        echo "</div>";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="text-center">
                            <a href="index.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-home me-2"></i>Voir la Page d'Accueil
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-footer text-muted text-center">
                        <small>
                            <i class="fas fa-heart text-danger"></i> 
                            TarantulaSMM Bénin - Test réalisé le <?php echo date('d/m/Y à H:i:s'); ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>