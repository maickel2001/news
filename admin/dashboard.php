<?php
require_once 'includes/auth.php';

// Récupérer les statistiques
$statsStmt = $db->query("SELECT * FROM order_stats");
$stats = $statsStmt->fetch();

// Commandes récentes
$recentOrdersStmt = $db->query("
    SELECT o.*, s.name as service_name, c.name as category_name 
    FROM orders o 
    JOIN services s ON o.service_id = s.id 
    JOIN categories c ON s.category_id = c.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
");
$recentOrders = $recentOrdersStmt->fetchAll();

// Services populaires
$popularServicesStmt = $db->query("SELECT * FROM popular_services LIMIT 5");
$popularServices = $popularServicesStmt->fetchAll();

// Statistiques par jour (7 derniers jours)
$dailyStatsStmt = $db->query("
    SELECT 
        DATE(created_at) as date,
        COUNT(*) as orders_count,
        SUM(total_amount) as daily_revenue
    FROM orders 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date DESC
");
$dailyStats = $dailyStatsStmt->fetchAll();

$statusLabels = [
    'pending' => 'En attente',
    'processing' => 'En cours',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée'
];

$statusColors = [
    'pending' => '#ffc107',
    'processing' => '#00ff88',
    'completed' => '#28a745',
    'cancelled' => '#dc3545'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin SMM Boost</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-header {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        .admin-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .admin-nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
            margin: 0;
            padding: 0;
        }
        .admin-nav a {
            color: var(--text-light);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: var(--transition);
        }
        .admin-nav a:hover, .admin-nav a.active {
            background: var(--primary-color);
            color: var(--dark-bg);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            text-align: center;
        }
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: var(--text-gray);
            font-size: 0.9rem;
        }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }
        .card {
            background: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            padding: 1.5rem;
        }
        .card h3 {
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        .table th {
            background: var(--dark-bg);
            color: var(--primary-color);
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .admin-nav ul {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container">
            <nav class="admin-nav">
                <div>
                    <h2 style="margin: 0; color: var(--primary-color);">
                        <i class="fas fa-shield-alt"></i> Admin Panel
                    </h2>
                </div>
                <ul>
                    <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
                    <li><a href="services.php"><i class="fas fa-cogs"></i> Services</a></li>
                    <li><a href="categories.php"><i class="fas fa-tags"></i> Catégories</a></li>
                </ul>
                <div>
                    <span style="color: var(--text-gray); margin-right: 1rem;">
                        <i class="fas fa-user"></i> <?= htmlspecialchars(getAdminName()) ?>
                    </span>
                    <a href="logout.php" style="color: var(--danger-color); text-decoration: none;">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main style="padding: 2rem 0;">
        <div class="container">
            <h1 style="margin-bottom: 2rem;">
                <i class="fas fa-chart-line"></i> Tableau de bord
            </h1>
            
            <!-- Statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--primary-color);">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-value"><?= $stats['total_orders'] ?? 0 ?></div>
                    <div class="stat-label">Total commandes</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--warning-color);">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?= $stats['pending_orders'] ?? 0 ?></div>
                    <div class="stat-label">En attente</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-value"><?= $stats['completed_orders'] ?? 0 ?></div>
                    <div class="stat-label">Terminées</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="color: var(--primary-color);">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-value"><?= formatPrice($stats['total_revenue'] ?? 0) ?></div>
                    <div class="stat-label">Chiffre d'affaires</div>
                </div>
            </div>
            
            <div class="content-grid">
                <!-- Commandes récentes -->
                <div class="card">
                    <h3><i class="fas fa-list"></i> Commandes récentes</h3>
                    <?php if (empty($recentOrders)): ?>
                        <p style="color: var(--text-gray); text-align: center; padding: 2rem;">
                            Aucune commande pour le moment
                        </p>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $order): ?>
                            <div class="order-item">
                                <div>
                                    <strong>#<?= htmlspecialchars($order['order_number']) ?></strong><br>
                                    <small style="color: var(--text-gray);">
                                        <?= htmlspecialchars($order['service_name']) ?> - 
                                        <?= number_format($order['quantity']) ?> unités
                                    </small>
                                </div>
                                <div style="text-align: right;">
                                    <div style="color: <?= $statusColors[$order['status']] ?>; font-size: 0.8rem; font-weight: bold; margin-bottom: 0.25rem;">
                                        <?= $statusLabels[$order['status']] ?>
                                    </div>
                                    <div style="font-weight: bold;"><?= formatPrice($order['total_amount']) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="orders.php" class="btn btn-primary">
                                <i class="fas fa-eye"></i> Voir toutes les commandes
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Services populaires -->
                <div class="card">
                    <h3><i class="fas fa-star"></i> Services populaires</h3>
                    <?php if (empty($popularServices)): ?>
                        <p style="color: var(--text-gray); text-align: center; padding: 2rem;">
                            Aucune donnée disponible
                        </p>
                    <?php else: ?>
                        <?php foreach ($popularServices as $service): ?>
                            <div style="margin-bottom: 1rem; padding: 1rem; background: var(--dark-bg); border-radius: 8px;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong><?= htmlspecialchars($service['name']) ?></strong><br>
                                        <small style="color: var(--text-gray);"><?= htmlspecialchars($service['category_name']) ?></small>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="color: var(--primary-color); font-weight: bold;">
                                            <?= $service['order_count'] ?> commandes
                                        </div>
                                        <small style="color: var(--text-gray);">
                                            <?= formatPrice($service['total_revenue'] ?? 0) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Statistiques journalières -->
            <?php if (!empty($dailyStats)): ?>
                <div class="card" style="margin-top: 2rem;">
                    <h3><i class="fas fa-calendar-alt"></i> Activité des 7 derniers jours</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Commandes</th>
                                <th>Chiffre d'affaires</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dailyStats as $day): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($day['date'])) ?></td>
                                    <td>
                                        <span style="color: var(--primary-color); font-weight: bold;">
                                            <?= $day['orders_count'] ?>
                                        </span>
                                    </td>
                                    <td><?= formatPrice($day['daily_revenue']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/script.js"></script>
</body>
</html>