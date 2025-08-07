<?php
require_once '../includes/functions.php';

// Vérifier que l'utilisateur est connecté et n'est pas admin
requireLogin();
if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$pdo = getDBConnection();

// Récupérer les statistiques de l'utilisateur
$stats = [];

// Total des commandes
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$stats['total_orders'] = $stmt->fetch()['total'];

// Commandes en attente
$stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM orders WHERE user_id = ? AND status = 'pending'");
$stmt->execute([$user_id]);
$stats['pending_orders'] = $stmt->fetch()['pending'];

// Commandes en cours
$stmt = $pdo->prepare("SELECT COUNT(*) as processing FROM orders WHERE user_id = ? AND status = 'processing'");
$stmt->execute([$user_id]);
$stats['processing_orders'] = $stmt->fetch()['processing'];

// Commandes terminées
$stmt = $pdo->prepare("SELECT COUNT(*) as completed FROM orders WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$stats['completed_orders'] = $stmt->fetch()['completed'];

// Montant total dépensé
$stmt = $pdo->prepare("SELECT SUM(total_amount) as total_spent FROM orders WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$user_id]);
$stats['total_spent'] = $stmt->fetch()['total_spent'] ?? 0;

// Récupérer les dernières commandes
$stmt = $pdo->prepare("
    SELECT o.*, s.name as service_name, c.name as category_name
    FROM orders o
    JOIN services s ON o.service_id = s.id
    JOIN categories c ON s.category_id = c.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Client - SMM Pro Services</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="../index.php" class="logo">
                <i class="fas fa-rocket"></i>
                SMM Pro
            </a>
            
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="text-primary">Dashboard</a></li>
                <li><a href="order.php">Nouvelle commande</a></li>
                <li><a href="orders.php">Mes commandes</a></li>
                <li><a href="profile.php">Mon profil</a></li>
                <li><a href="../logout.php">Déconnexion</a></li>
            </ul>
            
            <button class="nav-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </nav>
    </header>

    <main>
        <div class="container mt-3">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="text-primary">Dashboard Client</h1>
                    <p class="text-muted">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !</p>
                </div>
                <a href="order.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nouvelle commande
                </a>
            </div>

            <?php displayMessage(); ?>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--info-color);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p class="text-muted">Total commandes</p>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--warning-color);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3><?php echo $stats['pending_orders']; ?></h3>
                        <p class="text-muted">En attente</p>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--info-color);">
                            <i class="fas fa-cog fa-spin"></i>
                        </div>
                        <h3><?php echo $stats['processing_orders']; ?></h3>
                        <p class="text-muted">En cours</p>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--success-color);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3><?php echo $stats['completed_orders']; ?></h3>
                        <p class="text-muted">Terminées</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Actions rapides -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">Actions rapides</h3>
                        <div class="d-flex flex-column" style="gap: 1rem;">
                            <a href="order.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Passer une nouvelle commande
                            </a>
                            <a href="orders.php" class="btn btn-secondary">
                                <i class="fas fa-list"></i>
                                Voir toutes mes commandes
                            </a>
                            <a href="../index.php#services" class="btn btn-secondary">
                                <i class="fas fa-star"></i>
                                Découvrir nos services
                            </a>
                            <a href="../index.php#contact" class="btn btn-secondary">
                                <i class="fas fa-headset"></i>
                                Contacter le support
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Résumé financier -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">Résumé financier</h3>
                        <div class="text-center">
                            <div class="service-icon" style="color: var(--primary-color);">
                                <i class="fas fa-coins"></i>
                            </div>
                            <h2 class="text-primary"><?php echo formatPrice($stats['total_spent']); ?></h2>
                            <p class="text-muted">Total dépensé</p>
                        </div>
                        
                        <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius); margin-top: 1rem;">
                            <h4 class="text-primary mb-2">Moyens de paiement</h4>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="fas fa-mobile-alt" style="color: #FFD700; margin-right: 0.5rem;"></i>
                                    <span>MTN Money</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-mobile-alt" style="color: #FF6B35; margin-right: 0.5rem;"></i>
                                    <span>Moov Money</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guide d'utilisation -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">Comment ça marche ?</h3>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">
                            <div class="d-flex align-items-start mb-2">
                                <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">1</span>
                                <span>Choisissez votre service</span>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">2</span>
                                <span>Saisissez votre lien</span>
                            </div>
                            <div class="d-flex align-items-start mb-2">
                                <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">3</span>
                                <span>Payez via Mobile Money</span>
                            </div>
                            <div class="d-flex align-items-start">
                                <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">4</span>
                                <span>Uploadez votre reçu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commandes récentes -->
            <?php if (!empty($recent_orders)): ?>
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Dernières commandes</h3>
                    <a href="orders.php" class="btn btn-sm btn-secondary">Voir tout</a>
                </div>
                
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Lien</th>
                                <th>Quantité</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($order['service_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['category_name']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($order['target_url']); ?>" target="_blank" class="text-primary" style="font-size: 0.9rem;">
                                        <?php echo substr(htmlspecialchars($order['target_url']), 0, 30) . (strlen($order['target_url']) > 30 ? '...' : ''); ?>
                                    </a>
                                </td>
                                <td><?php echo number_format($order['quantity']); ?></td>
                                <td class="text-primary font-weight-bold"><?php echo formatPrice($order['total_amount']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $order['status']; ?>">
                                        <?php 
                                        $status_labels = [
                                            'pending' => 'En attente',
                                            'processing' => 'En cours',
                                            'completed' => 'Terminée',
                                            'cancelled' => 'Annulée'
                                        ];
                                        echo $status_labels[$order['status']] ?? $order['status']; 
                                        ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php else: ?>
            <div class="card mt-4 text-center">
                <div class="service-icon" style="color: var(--text-muted);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="text-muted">Aucune commande pour le moment</h3>
                <p class="text-muted">Commencez dès maintenant et boostez votre présence sociale !</p>
                <a href="order.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Passer ma première commande
                </a>
            </div>
            <?php endif; ?>

            <!-- Conseils et astuces -->
            <div class="card mt-4">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb text-primary"></i>
                    Conseils pour maximiser vos résultats
                </h3>
                <div class="row">
                    <div class="col-4">
                        <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius);">
                            <h4 class="text-primary mb-2">
                                <i class="fas fa-target"></i>
                                Contenu de qualité
                            </h4>
                            <p style="font-size: 0.9rem; color: var(--text-secondary);">
                                Assurez-vous que votre contenu est attrayant et de haute qualité avant de booster votre engagement.
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius);">
                            <h4 class="text-primary mb-2">
                                <i class="fas fa-clock"></i>
                                Timing optimal
                            </h4>
                            <p style="font-size: 0.9rem; color: var(--text-secondary);">
                                Publiez votre contenu aux heures de pointe de votre audience pour maximiser l'impact.
                            </p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius);">
                            <h4 class="text-primary mb-2">
                                <i class="fas fa-chart-line"></i>
                                Progression graduelle
                            </h4>
                            <p style="font-size: 0.9rem; color: var(--text-secondary);">
                                Augmentez progressivement vos commandes pour un résultat plus naturel et durable.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/main.js"></script>
</body>
</html>