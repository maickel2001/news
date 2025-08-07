<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit();
}

// Récupérer les statistiques utilisateur
try {
    $db = Database::getInstance();
    
    // Stats commandes
    $stmt = $db->prepare("SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
        SUM(total_amount) as total_spent
        FROM orders WHERE user_id = ?");
    $stmt->execute([$user['id']]);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Dernières commandes
    $stmt = $db->prepare("SELECT o.*, s.name as service_name, s.platform 
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute([$user['id']]);
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Services populaires
    $stmt = $db->prepare("SELECT s.*, COUNT(o.id) as order_count 
        FROM services s 
        LEFT JOIN orders o ON s.id = o.service_id 
        WHERE s.status = 'active' 
        GROUP BY s.id 
        ORDER BY order_count DESC, s.created_at DESC 
        LIMIT 6");
    $stmt->execute();
    $popular_services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $stats = ['total_orders' => 0, 'completed_orders' => 0, 'pending_orders' => 0, 'processing_orders' => 0, 'total_spent' => 0];
    $recent_orders = [];
    $popular_services = [];
}

// Fonction pour formater le prix
function formatPrice($amount) {
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

// Fonction pour l'icône de plateforme
function getPlatformIcon($platform) {
    $icons = [
        'instagram' => 'fab fa-instagram',
        'tiktok' => 'fab fa-tiktok', 
        'facebook' => 'fab fa-facebook',
        'youtube' => 'fab fa-youtube',
        'twitter' => 'fab fa-twitter',
        'linkedin' => 'fab fa-linkedin'
    ];
    return $icons[strtolower($platform)] ?? 'fas fa-share-alt';
}

// Fonction pour la couleur du statut
function getStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'processing' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger',
        'refunded' => 'secondary'
    ];
    return $colors[$status] ?? 'secondary';
}

// Fonction pour le libellé du statut
function getStatusLabel($status) {
    $labels = [
        'pending' => 'En attente',
        'processing' => 'En cours',
        'completed' => 'Terminé',
        'cancelled' => 'Annulé',
        'refunded' => 'Remboursé'
    ];
    return $labels[$status] ?? ucfirst($status);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TarantulaSMM Bénin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #000000;
            --secondary: #ffffff;
            --accent: #ff6b35;
            --accent-light: #ff8660;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --text-light: #999999;
            --bg-light: #fafafa;
            --bg-dark: #0a0a0a;
            --border: #e0e0e0;
            --shadow: 0 4px 60px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 8px 80px rgba(0, 0, 0, 0.1);
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Outfit', sans-serif;
            line-height: 1.7;
            color: var(--text-primary);
            background: var(--bg-light);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--primary);
            color: var(--secondary);
            padding: 2rem 0;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar-brand {
            text-align: center;
            margin-bottom: 3rem;
            padding: 0 2rem;
        }
        
        .sidebar-brand h4 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--accent);
            margin: 0;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 2rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--secondary);
            background: rgba(255, 255, 255, 0.1);
            border-left-color: var(--accent);
        }
        
        .nav-link i {
            width: 20px;
            text-align: center;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }
        
        /* Header */
        .header {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .welcome-text h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .welcome-text p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        
        .stat-icon.primary { background: var(--accent); }
        .stat-icon.success { background: var(--success); }
        .stat-icon.warning { background: var(--warning); }
        .stat-icon.info { background: var(--info); }
        
        .stat-number {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        /* Content Cards */
        .content-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        
        .content-card h5 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .service-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        
        .service-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .platform-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: var(--secondary);
        }
        
        .platform-icon.instagram {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }
        
        .platform-icon.tiktok {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
        }
        
        .platform-icon.facebook {
            background: #1877f2;
        }
        
        .platform-icon.youtube {
            background: #ff0000;
        }
        
        .platform-icon.twitter {
            background: #1da1f2;
        }
        
        .platform-icon.linkedin {
            background: #0e76a8;
        }
        
        .service-info h6 {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 0.25rem;
        }
        
        .service-price {
            font-weight: 700;
            color: var(--accent);
            font-size: 1.1rem;
        }
        
        .service-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .btn-order {
            background: var(--accent);
            color: var(--secondary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-order:hover {
            background: var(--accent-light);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Orders Table */
        .orders-table {
            width: 100%;
            margin-top: 1rem;
        }
        
        .orders-table th {
            background: var(--bg-light);
            color: var(--text-primary);
            font-weight: 600;
            padding: 1rem;
            border: none;
        }
        
        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }
        
        .order-service {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .order-platform {
            width: 35px;
            height: 35px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            color: var(--secondary);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-badge.success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
        }
        
        .status-badge.warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
        }
        
        .status-badge.info {
            background: rgba(23, 162, 184, 0.1);
            color: var(--info);
        }
        
        .status-badge.danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
        }
        
        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .orders-table {
                font-size: 0.8rem;
            }
            
            .orders-table th,
            .orders-table td {
                padding: 0.5rem;
            }
        }
        
        /* Sidebar Toggle Button */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 1rem;
            left: 1rem;
            z-index: 1001;
            background: var(--primary);
            color: var(--secondary);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 12px;
            font-size: 1.2rem;
        }
        
        @media (max-width: 768px) {
            .sidebar-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .fade-in:nth-child(1) { animation-delay: 0.1s; }
        .fade-in:nth-child(2) { animation-delay: 0.2s; }
        .fade-in:nth-child(3) { animation-delay: 0.3s; }
        .fade-in:nth-child(4) { animation-delay: 0.4s; }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-spider me-2"></i>TarantulaSMM</h4>
            <small>Panel Utilisateur</small>
        </div>
        
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Tableau de Bord</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="services.php" class="nav-link">
                    <i class="fas fa-star"></i>
                    <span>Services</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="orders.php" class="nav-link">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Mes Commandes</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="payments.php" class="nav-link">
                    <i class="fas fa-credit-card"></i>
                    <span>Paiements</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Mon Profil</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="support.php" class="nav-link">
                    <i class="fas fa-headset"></i>
                    <span>Support</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/" class="nav-link">
                    <i class="fas fa-globe"></i>
                    <span>Site Web</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <a href="logout.php" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header fade-in">
            <div class="d-flex justify-content-between align-items-center">
                <div class="welcome-text">
                    <h1>Salut, <?= htmlspecialchars($user['first_name'] ?? $user['full_name']) ?> ! 👋</h1>
                    <p>Bienvenue sur votre tableau de bord TarantulaSMM. Gérez vos services et suivez vos commandes.</p>
                </div>
                <div class="d-none d-md-flex align-items-center gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block">Solde disponible</small>
                        <span class="h5 mb-0 text-success"><?= formatPrice($user['balance'] ?? 0) ?></span>
                    </div>
                    <a href="profile.php" class="btn btn-outline-primary">
                        <i class="fas fa-user me-2"></i>Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card fade-in">
                <div class="stat-icon primary">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-number"><?= $stats['total_orders'] ?></div>
                <div class="stat-label">Total Commandes</div>
            </div>
            
            <div class="stat-card fade-in">
                <div class="stat-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?= $stats['completed_orders'] ?></div>
                <div class="stat-label">Commandes Terminées</div>
            </div>
            
            <div class="stat-card fade-in">
                <div class="stat-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?= $stats['pending_orders'] + $stats['processing_orders'] ?></div>
                <div class="stat-label">En Cours</div>
            </div>
            
            <div class="stat-card fade-in">
                <div class="stat-icon info">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-number"><?= formatPrice($stats['total_spent'] ?? 0) ?></div>
                <div class="stat-label">Total Dépensé</div>
            </div>
        </div>

        <div class="row">
            <!-- Services Populaires -->
            <div class="col-lg-8">
                <div class="content-card fade-in">
                    <h5><i class="fas fa-fire text-danger me-2"></i>Services Populaires</h5>
                    <div class="services-grid">
                        <?php foreach (array_slice($popular_services, 0, 4) as $service): ?>
                        <div class="service-card" onclick="orderService(<?= $service['id'] ?>)">
                            <div class="service-header">
                                <div class="platform-icon <?= strtolower($service['platform']) ?>">
                                    <i class="<?= getPlatformIcon($service['platform']) ?>"></i>
                                </div>
                                <div class="service-info">
                                    <h6><?= htmlspecialchars($service['name']) ?></h6>
                                    <div class="service-price"><?= formatPrice($service['price']) ?></div>
                                </div>
                            </div>
                            <div class="service-description">
                                <?= htmlspecialchars($service['description']) ?>
                            </div>
                            <button class="btn-order">
                                <i class="fas fa-cart-plus me-2"></i>Commander
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($popular_services) > 4): ?>
                    <div class="text-center mt-3">
                        <a href="services.php" class="btn btn-outline-primary">
                            <i class="fas fa-eye me-2"></i>Voir Tous les Services
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Commandes Récentes -->
            <div class="col-lg-4">
                <div class="content-card fade-in">
                    <h5><i class="fas fa-history me-2"></i>Commandes Récentes</h5>
                    <?php if (!empty($recent_orders)): ?>
                    <div class="table-responsive">
                        <table class="orders-table">
                            <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td>
                                    <div class="order-service">
                                        <div class="order-platform <?= strtolower($order['platform']) ?>">
                                            <i class="<?= getPlatformIcon($order['platform']) ?>"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($order['service_name']) ?></div>
                                            <small class="text-muted"><?= formatPrice($order['total_amount']) ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge <?= getStatusColor($order['status']) ?>">
                                        <?= getStatusLabel($order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="orders.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>Voir Toutes
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucune commande pour le moment</p>
                        <a href="services.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Première Commande
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Actions Rapides -->
        <div class="content-card fade-in">
            <h5><i class="fas fa-bolt me-2"></i>Actions Rapides</h5>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <a href="services.php" class="btn btn-primary w-100 p-3">
                        <i class="fas fa-star d-block mb-2 fa-2x"></i>
                        <small>Commander un Service</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="orders.php" class="btn btn-info w-100 p-3">
                        <i class="fas fa-list d-block mb-2 fa-2x"></i>
                        <small>Mes Commandes</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="payments.php" class="btn btn-success w-100 p-3">
                        <i class="fas fa-plus d-block mb-2 fa-2x"></i>
                        <small>Ajouter du Crédit</small>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="support.php" class="btn btn-warning w-100 p-3">
                        <i class="fas fa-headset d-block mb-2 fa-2x"></i>
                        <small>Contacter Support</small>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('show');
            }
        });
        
        // Order Service Function
        function orderService(serviceId) {
            window.location.href = `order.php?service=${serviceId}`;
        }
        
        // Real-time notifications (placeholder)
        function checkNotifications() {
            // This would typically make an AJAX call to check for new notifications
            console.log('Checking for notifications...');
        }
        
        // Check notifications every 30 seconds
        setInterval(checkNotifications, 30000);
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>