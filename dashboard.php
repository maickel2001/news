<?php
/**
 * Tableau de Bord Temporaire - TarantulaSMM Bénin
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

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    redirect('/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

// Récupérer les informations de l'utilisateur
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - TarantulaSMM Bénin</title>
    
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
        
        .navbar {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .dashboard-container {
            padding: 2rem 0;
        }
        
        .welcome-card {
            background: var(--gradient-primary);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .btn-logout {
            background: #ef4444;
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #dc2626;
            color: white;
        }
        
        .coming-soon {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }
        
        .coming-soon i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-spider me-2"></i>TarantulaSMM
            </a>
            
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Bonjour, <strong><?php echo htmlspecialchars($user['first_name']); ?></strong>
                </span>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Contenu principal -->
    <div class="dashboard-container">
        <div class="container">
            <!-- Carte de bienvenue -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2>Bienvenue, <?php echo htmlspecialchars($user['first_name']); ?> !</h2>
                        <p class="mb-0">
                            Votre connexion a réussi. Le tableau de bord complet sera disponible dans l'étape 3.
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <i class="fas fa-user-check" style="font-size: 3rem; opacity: 0.7;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Informations utilisateur -->
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-user me-2"></i>Informations Personnelles</h5>
                        <p><strong>Nom complet :</strong> <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                        <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <?php if ($user['phone']): ?>
                            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                        <?php endif; ?>
                        <p><strong>Statut :</strong> 
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Actif
                            </span>
                        </p>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><i class="fas fa-chart-line me-2"></i>Statistiques</h5>
                        <p><strong>Membre depuis :</strong> <?php echo formatDate($user['created_at'], 'd/m/Y'); ?></p>
                        <p><strong>Dernière connexion :</strong> 
                            <?php echo $user['last_login'] ? formatDate($user['last_login']) : 'Première connexion'; ?>
                        </p>
                        <p><strong>Total commandes :</strong> <?php echo $user['total_orders']; ?></p>
                        <p><strong>Total dépensé :</strong> <?php echo formatPrice($user['total_spent']); ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Section "Bientôt disponible" -->
            <div class="info-card">
                <div class="coming-soon">
                    <i class="fas fa-tools"></i>
                    <h4>Tableau de Bord Complet - Bientôt Disponible</h4>
                    <p>
                        Le tableau de bord complet avec commandes, services, et support sera disponible dans l'étape 3.<br>
                        Votre système d'authentification fonctionne parfaitement !
                    </p>
                    
                    <div class="mt-4">
                        <a href="/" class="btn btn-primary me-2">
                            <i class="fas fa-home me-1"></i>Retour à l'Accueil
                        </a>
                        <a href="test-db.php" class="btn btn-outline-primary">
                            <i class="fas fa-database me-1"></i>Test Base de Données
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>