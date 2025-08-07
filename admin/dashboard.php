<?php
require_once '../includes/functions.php';

requireLogin();
requireAdmin();

$pdo = getDBConnection();
$stats = getAdminStats();

// Récupérer les commandes récentes
$stmt = $pdo->query("
    SELECT o.*, s.name as service_name, c.name as category_name, u.full_name as client_name, u.email as client_email
    FROM orders o
    JOIN services s ON o.service_id = s.id
    JOIN categories c ON s.category_id = c.id
    JOIN users u ON o.user_id = u.id
    ORDER BY o.created_at DESC
    LIMIT 10
");
$recent_orders = $stmt->fetchAll();

// Récupérer les commandes par statut pour le graphique
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count
    FROM orders 
    GROUP BY status
");
$order_stats = [];
while ($row = $stmt->fetch()) {
    $order_stats[$row['status']] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SMM Pro Services</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <nav class="navbar container">
            <a href="../index.php" class="logo">
                <i class="fas fa-rocket"></i>
                SMM Pro - Admin
            </a>
            
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="text-primary">Dashboard</a></li>
                <li><a href="orders.php">Commandes</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="clients.php">Clients</a></li>
                <li><a href="settings.php">Paramètres</a></li>
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
                    <h1 class="text-primary">Dashboard Administrateur</h1>
                    <p class="text-muted">Bienvenue, <?php echo htmlspecialchars($_SESSION['user_name']); ?> !</p>
                </div>
                <div>
                    <a href="orders.php?status=pending" class="btn btn-warning btn-sm">
                        <i class="fas fa-clock"></i>
                        <?php echo $stats['pending_orders']; ?> en attente
                    </a>
                    <a href="orders.php" class="btn btn-primary">
                        <i class="fas fa-list"></i>
                        Gérer les commandes
                    </a>
                </div>
            </div>

            <?php displayMessage(); ?>

            <!-- Statistiques principales -->
            <div class="row mb-4">
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--info-color);">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3><?php echo $stats['total_orders']; ?></h3>
                        <p class="text-muted">Total commandes</p>
                        <a href="orders.php" class="btn btn-sm btn-secondary">Voir tout</a>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--warning-color);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3><?php echo $stats['pending_orders']; ?></h3>
                        <p class="text-muted">En attente</p>
                        <a href="orders.php?status=pending" class="btn btn-sm btn-warning">Traiter</a>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--success-color);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3><?php echo $stats['completed_orders']; ?></h3>
                        <p class="text-muted">Terminées</p>
                        <a href="orders.php?status=completed" class="btn btn-sm btn-success">Voir</a>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--primary-color);">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h3><?php echo formatPrice($stats['total_revenue']); ?></h3>
                        <p class="text-muted">Revenus totaux</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Statistiques détaillées -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie"></i>
                            Répartition des commandes
                        </h3>
                        <canvas id="ordersChart" width="300" height="300"></canvas>
                    </div>
                </div>

                <!-- Actions rapides -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">Actions rapides</h3>
                        <div class="d-flex flex-column" style="gap: 1rem;">
                            <a href="orders.php?status=pending" class="btn btn-warning">
                                <i class="fas fa-clock"></i>
                                Traiter les commandes en attente (<?php echo $stats['pending_orders']; ?>)
                            </a>
                            <a href="services.php" class="btn btn-primary">
                                <i class="fas fa-cogs"></i>
                                Gérer les services
                            </a>
                            <a href="services.php?action=add" class="btn btn-secondary">
                                <i class="fas fa-plus"></i>
                                Ajouter un service
                            </a>
                            <a href="clients.php" class="btn btn-secondary">
                                <i class="fas fa-users"></i>
                                Gérer les clients (<?php echo $stats['total_clients']; ?>)
                            </a>
                            <a href="settings.php" class="btn btn-secondary">
                                <i class="fas fa-cog"></i>
                                Paramètres du site
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Informations système
                        </h3>
                        <div style="font-size: 0.9rem; color: var(--text-secondary);">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total clients :</span>
                                <strong><?php echo $stats['total_clients']; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Commandes en cours :</span>
                                <strong><?php echo $stats['processing_orders']; ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Version PHP :</span>
                                <strong><?php echo phpversion(); ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Espace disque :</span>
                                <strong><?php echo round(disk_free_space('.') / 1024 / 1024 / 1024, 1); ?> GB</strong>
                            </div>
                        </div>
                        
                        <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius); margin-top: 1rem;">
                            <h4 class="text-primary mb-2">Statut système</h4>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-circle" style="color: var(--success-color); margin-right: 0.5rem; font-size: 0.7rem;"></i>
                                <span style="font-size: 0.9rem;">Base de données : Connectée</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-circle" style="color: var(--success-color); margin-right: 0.5rem; font-size: 0.7rem;"></i>
                                <span style="font-size: 0.9rem;">Upload : Fonctionnel</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-circle" style="color: var(--success-color); margin-right: 0.5rem; font-size: 0.7rem;"></i>
                                <span style="font-size: 0.9rem;">Sessions : Actives</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commandes récentes -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i>
                        Commandes récentes
                    </h3>
                    <a href="orders.php" class="btn btn-sm btn-primary">Voir toutes</a>
                </div>
                
                <?php if (!empty($recent_orders)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['id']; ?></strong></td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($order['client_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['client_email']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($order['service_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['category_name']); ?></small>
                                    </div>
                                </td>
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
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($order['status'] === 'pending'): ?>
                                        <a href="orders.php?action=approve&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center p-4">
                    <div class="service-icon" style="color: var(--text-muted);">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h3 class="text-muted">Aucune commande pour le moment</h3>
                    <p class="text-muted">Les nouvelles commandes apparaîtront ici</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Activité récente -->
            <div class="row mt-4">
                <div class="col-6">
                    <div class="card">
                        <h3 class="card-title">
                            <i class="fas fa-chart-line"></i>
                            Tendances cette semaine
                        </h3>
                        
                        <?php
                        // Récupérer les statistiques de la semaine
                        $stmt = $pdo->query("
                            SELECT DATE(created_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue
                            FROM orders 
                            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                            GROUP BY DATE(created_at)
                            ORDER BY date DESC
                        ");
                        $weekly_stats = $stmt->fetchAll();
                        ?>
                        
                        <?php if (!empty($weekly_stats)): ?>
                            <?php foreach ($weekly_stats as $day): ?>
                            <div class="d-flex justify-content-between align-items-center mb-2 p-2" style="background: var(--darker-bg); border-radius: 8px;">
                                <div>
                                    <strong><?php echo date('d/m/Y', strtotime($day['date'])); ?></strong><br>
                                    <small class="text-muted"><?php echo $day['orders']; ?> commandes</small>
                                </div>
                                <span class="text-primary" style="font-weight: 600;">
                                    <?php echo formatPrice($day['revenue']); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted text-center">Aucune activité cette semaine</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-6">
                    <div class="card">
                        <h3 class="card-title">
                            <i class="fas fa-bullhorn"></i>
                            Actions importantes
                        </h3>
                        
                        <div style="font-size: 0.9rem;">
                            <?php if ($stats['pending_orders'] > 0): ?>
                            <div class="alert alert-warning" style="margin-bottom: 1rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong><?php echo $stats['pending_orders']; ?> commandes</strong> en attente de traitement
                                <a href="orders.php?status=pending" class="btn btn-sm btn-warning" style="float: right;">Traiter</a>
                            </div>
                            <?php endif; ?>
                            
                            <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius); margin-bottom: 1rem;">
                                <h4 class="text-primary mb-2">
                                    <i class="fas fa-tasks"></i>
                                    À faire aujourd'hui
                                </h4>
                                <ul style="margin: 0; padding-left: 1.2rem;">
                                    <li>Vérifier les nouvelles commandes</li>
                                    <li>Répondre aux messages clients</li>
                                    <li>Mettre à jour les statuts des commandes</li>
                                    <li>Vérifier les paiements Mobile Money</li>
                                </ul>
                            </div>
                            
                            <div style="background: var(--info-color); background: rgba(23, 162, 184, 0.1); padding: 1rem; border-radius: var(--border-radius);">
                                <h4 style="color: var(--info-color); margin-bottom: 0.5rem;">
                                    <i class="fas fa-lightbulb"></i>
                                    Conseil du jour
                                </h4>
                                <p style="margin: 0; color: var(--text-secondary); font-size: 0.9rem;">
                                    Traitez les commandes rapidement pour maintenir la satisfaction client. 
                                    Un service rapide génère plus de commandes !
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/main.js"></script>
    
    <script>
        // Graphique des commandes
        const ctx = document.getElementById('ordersChart').getContext('2d');
        const orderData = <?php echo json_encode($order_stats); ?>;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [
                    'En attente',
                    'En cours', 
                    'Terminées',
                    'Annulées'
                ],
                datasets: [{
                    data: [
                        orderData.pending || 0,
                        orderData.processing || 0,
                        orderData.completed || 0,
                        orderData.cancelled || 0
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#ffffff',
                            padding: 20
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>