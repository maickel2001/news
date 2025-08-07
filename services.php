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

// Récupérer les filtres
$platform = $_GET['platform'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'popular';
$search = $_GET['search'] ?? '';

try {
    $db = Database::getInstance();
    
    // Construction de la requête avec filtres
    $where_conditions = ["s.status = 'active'"];
    $params = [];
    
    if ($platform) {
        $where_conditions[] = "s.platform = ?";
        $params[] = $platform;
    }
    
    if ($category) {
        $where_conditions[] = "s.category = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $where_conditions[] = "(s.name LIKE ? OR s.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Ordre de tri
    $order_by = match($sort) {
        'price_low' => 'ORDER BY s.price ASC',
        'price_high' => 'ORDER BY s.price DESC',
        'newest' => 'ORDER BY s.created_at DESC',
        'name' => 'ORDER BY s.name ASC',
        'popular' => 'ORDER BY order_count DESC, s.created_at DESC',
        default => 'ORDER BY order_count DESC, s.created_at DESC'
    };
    
    // Récupérer les services avec compteur de commandes
    $query = "SELECT s.*, COUNT(o.id) as order_count 
              FROM services s 
              LEFT JOIN orders o ON s.id = o.service_id 
              WHERE $where_clause 
              GROUP BY s.id 
              $order_by";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Récupérer les plateformes disponibles
    $stmt = $db->prepare("SELECT DISTINCT platform FROM services WHERE status = 'active' ORDER BY platform");
    $stmt->execute();
    $platforms = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Récupérer les catégories disponibles
    $stmt = $db->prepare("SELECT DISTINCT category FROM services WHERE status = 'active' ORDER BY category");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
} catch (Exception $e) {
    $services = [];
    $platforms = [];
    $categories = [];
}

// Fonctions utilitaires
function formatPrice($amount) {
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

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

function getPlatformColor($platform) {
    $colors = [
        'instagram' => 'instagram',
        'tiktok' => 'tiktok',
        'facebook' => 'facebook',
        'youtube' => 'youtube',
        'twitter' => 'twitter',
        'linkedin' => 'linkedin'
    ];
    return $colors[strtolower($platform)] ?? 'primary';
}

function getCategoryIcon($category) {
    $icons = [
        'followers' => 'fas fa-users',
        'likes' => 'fas fa-heart',
        'views' => 'fas fa-eye',
        'comments' => 'fas fa-comments',
        'shares' => 'fas fa-share',
        'subscribers' => 'fas fa-user-plus'
    ];
    return $icons[strtolower($category)] ?? 'fas fa-star';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services SMM - TarantulaSMM Bénin</title>
    
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
        
        /* Navigation (copié du dashboard) */
        .navbar {
            background: var(--secondary);
            box-shadow: var(--shadow);
            padding: 1rem 0;
            border-bottom: 1px solid var(--border);
        }
        
        .navbar-brand {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary) !important;
            text-decoration: none;
        }
        
        .navbar-brand:hover {
            color: var(--accent) !important;
        }
        
        .btn-back {
            background: var(--primary);
            color: var(--secondary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-back:hover {
            background: var(--accent);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Page Header */
        .page-header {
            background: var(--secondary);
            border-radius: 15px;
            padding: 3rem 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .page-header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Filters Section */
        .filters-section {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .filter-group {
            margin-bottom: 1.5rem;
        }
        
        .filter-group label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .filter-select {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            background: var(--secondary);
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .filter-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
            outline: none;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-input {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem 0.75rem 3rem;
            background: var(--secondary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .search-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
            outline: none;
        }
        
        .search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }
        
        .btn-filter {
            background: var(--accent);
            color: var(--secondary);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-filter:hover {
            background: var(--accent-light);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .btn-clear {
            background: var(--border);
            color: var(--text-primary);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 0.5rem;
        }
        
        .btn-clear:hover {
            background: var(--text-secondary);
            color: var(--secondary);
        }
        
        /* Platform Tabs */
        .platform-tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .platform-tab {
            padding: 1rem 2rem;
            border: 2px solid var(--border);
            border-radius: 12px;
            background: var(--secondary);
            color: var(--text-primary);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }
        
        .platform-tab:hover, .platform-tab.active {
            color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        
        .platform-tab.instagram.active, .platform-tab.instagram:hover {
            background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888);
        }
        
        .platform-tab.tiktok.active, .platform-tab.tiktok:hover {
            background: linear-gradient(45deg, #ff0050, #00f2ea);
        }
        
        .platform-tab.facebook.active, .platform-tab.facebook:hover {
            background: #1877f2;
        }
        
        .platform-tab.youtube.active, .platform-tab.youtube:hover {
            background: #ff0000;
        }
        
        .platform-tab.twitter.active, .platform-tab.twitter:hover {
            background: #1da1f2;
        }
        
        .platform-tab.linkedin.active, .platform-tab.linkedin:hover {
            background: #0e76a8;
        }
        
        /* Services Grid */
        .services-container {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .service-card {
            background: var(--secondary);
            border: 2px solid var(--border);
            border-radius: 15px;
            padding: 2rem;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--accent);
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
            border-color: var(--accent);
        }
        
        .service-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .platform-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
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
        
        .service-info h5 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .service-category {
            background: var(--bg-light);
            color: var(--text-secondary);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .service-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .service-features {
            list-style: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .service-features li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .service-features .fa-check {
            color: var(--success);
            font-size: 0.8rem;
        }
        
        .service-footer {
            display: flex;
            justify-content: between;
            align-items: center;
            gap: 1rem;
        }
        
        .service-price {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--accent);
        }
        
        .service-min-max {
            font-size: 0.8rem;
            color: var(--text-light);
            margin-top: 0.25rem;
        }
        
        .btn-order {
            background: var(--accent);
            color: var(--secondary);
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            flex: 1;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-order:hover {
            background: var(--accent-light);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .order-count {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-light);
            font-size: 0.8rem;
            margin-top: 1rem;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: var(--text-light);
        }
        
        /* Results Counter */
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .results-count {
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        .sort-select {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 0.5rem 1rem;
            background: var(--secondary);
            color: var(--text-primary);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .page-header {
                padding: 2rem 1rem;
                margin: 1rem 0;
            }
            
            .filters-section {
                padding: 1rem;
            }
            
            .platform-tabs {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
            
            .platform-tab {
                flex-shrink: 0;
                padding: 0.75rem 1.5rem;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .service-card {
                padding: 1.5rem;
            }
            
            .service-footer {
                flex-direction: column;
                align-items: stretch;
            }
            
            .results-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 4rem;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border);
            border-top: 4px solid var(--accent);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Fade In Animation */
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
        .fade-in:nth-child(5) { animation-delay: 0.5s; }
        .fade-in:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a class="navbar-brand" href="dashboard.php">
                    <i class="fas fa-spider me-2"></i>TarantulaSMM
                </a>
                
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted d-none d-md-inline">
                        Solde: <strong class="text-success"><?= formatPrice($user['balance'] ?? 0) ?></strong>
                    </span>
                    <a href="dashboard.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Tableau de Bord
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <h1><i class="fas fa-star me-3"></i>Services SMM Premium</h1>
            <p>Découvrez notre gamme complète de services pour booster votre présence sur les réseaux sociaux. Qualité garantie, livraison rapide, support 24/7.</p>
        </div>

        <!-- Platform Tabs -->
        <div class="platform-tabs fade-in">
            <a href="?<?= http_build_query(array_merge($_GET, ['platform' => ''])) ?>" 
               class="platform-tab <?= !$platform ? 'active' : '' ?>">
                <i class="fas fa-globe"></i>
                Tous
            </a>
            <?php foreach ($platforms as $p): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['platform' => $p])) ?>" 
               class="platform-tab <?= strtolower($p) ?> <?= $platform === $p ? 'active' : '' ?>">
                <i class="<?= getPlatformIcon($p) ?>"></i>
                <?= ucfirst($p) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filters-section fade-in">
                    <h5><i class="fas fa-filter me-2"></i>Filtres</h5>
                    
                    <form method="GET" action="">
                        <!-- Search -->
                        <div class="filter-group">
                            <label>Rechercher</label>
                            <div class="search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" class="search-input" 
                                       value="<?= htmlspecialchars($search) ?>" 
                                       placeholder="Nom du service...">
                            </div>
                        </div>
                        
                        <!-- Platform -->
                        <?php if (!$platform): ?>
                        <div class="filter-group">
                            <label>Plateforme</label>
                            <select name="platform" class="filter-select form-control">
                                <option value="">Toutes les plateformes</option>
                                <?php foreach ($platforms as $p): ?>
                                <option value="<?= $p ?>" <?= $platform === $p ? 'selected' : '' ?>>
                                    <?= ucfirst($p) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <input type="hidden" name="platform" value="<?= $platform ?>">
                        <?php endif; ?>
                        
                        <!-- Category -->
                        <div class="filter-group">
                            <label>Catégorie</label>
                            <select name="category" class="filter-select form-control">
                                <option value="">Toutes les catégories</option>
                                <?php foreach ($categories as $c): ?>
                                <option value="<?= $c ?>" <?= $category === $c ? 'selected' : '' ?>>
                                    <?= ucfirst($c) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Sort -->
                        <div class="filter-group">
                            <label>Trier par</label>
                            <select name="sort" class="filter-select form-control">
                                <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Plus populaires</option>
                                <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Prix croissant</option>
                                <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Prix décroissant</option>
                                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Plus récents</option>
                                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Nom A-Z</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn-filter">
                            <i class="fas fa-search me-2"></i>Appliquer
                        </button>
                        
                        <a href="services.php" class="btn-clear">
                            <i class="fas fa-times me-2"></i>Effacer
                        </a>
                    </form>
                </div>
            </div>

            <!-- Services Grid -->
            <div class="col-lg-9">
                <div class="services-container fade-in">
                    <div class="results-header">
                        <div class="results-count">
                            <i class="fas fa-list me-2"></i>
                            <?= count($services) ?> service(s) trouvé(s)
                            <?php if ($platform): ?>
                                pour <strong><?= ucfirst($platform) ?></strong>
                            <?php endif; ?>
                            <?php if ($category): ?>
                                dans <strong><?= ucfirst($category) ?></strong>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($services)): ?>
                    <div class="services-grid">
                        <?php foreach ($services as $index => $service): ?>
                        <div class="service-card fade-in" onclick="orderService(<?= $service['id'] ?>)">
                            <div class="service-header">
                                <div class="platform-icon <?= getPlatformColor($service['platform']) ?>">
                                    <i class="<?= getPlatformIcon($service['platform']) ?>"></i>
                                </div>
                                <div class="service-info">
                                    <h5><?= htmlspecialchars($service['name']) ?></h5>
                                    <span class="service-category">
                                        <i class="<?= getCategoryIcon($service['category']) ?> me-1"></i>
                                        <?= ucfirst($service['category']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="service-description">
                                <?= htmlspecialchars($service['description']) ?>
                            </div>
                            
                            <ul class="service-features">
                                <li><i class="fas fa-check"></i> Livraison rapide et sécurisée</li>
                                <li><i class="fas fa-check"></i> Qualité premium garantie</li>
                                <li><i class="fas fa-check"></i> Support client 24/7</li>
                                <?php if ($service['refill_guaranteed']): ?>
                                <li><i class="fas fa-check"></i> Garantie de remplissage</li>
                                <?php endif; ?>
                            </ul>
                            
                            <div class="service-footer">
                                <div class="price-info">
                                    <div class="service-price"><?= formatPrice($service['price']) ?></div>
                                    <div class="service-min-max">
                                        Min: <?= number_format($service['min_quantity']) ?> | 
                                        Max: <?= number_format($service['max_quantity']) ?>
                                    </div>
                                </div>
                                <a href="order.php?service=<?= $service['id'] ?>" class="btn-order">
                                    <i class="fas fa-cart-plus me-2"></i>Commander
                                </a>
                            </div>
                            
                            <?php if ($service['order_count'] > 0): ?>
                            <div class="order-count">
                                <i class="fas fa-fire"></i>
                                <?= $service['order_count'] ?> commande(s)
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h4>Aucun service trouvé</h4>
                        <p>Essayez de modifier vos critères de recherche ou de parcourir une autre catégorie.</p>
                        <a href="services.php" class="btn btn-primary mt-3">
                            <i class="fas fa-refresh me-2"></i>Voir tous les services
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Order Service Function
        function orderService(serviceId) {
            window.location.href = `order.php?service=${serviceId}`;
        }
        
        // Auto-submit form on select change
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                if (this.name !== 'sort') return;
                this.form.submit();
            });
        });
        
        // Search on Enter
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
        
        // Service card click handler
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Don't trigger if clicking on the order button
                if (e.target.closest('.btn-order')) return;
                
                const serviceId = this.getAttribute('onclick').match(/\d+/)[0];
                orderService(serviceId);
            });
        });
        
        // Smooth scroll for platform tabs
        document.querySelectorAll('.platform-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                // Add loading indicator
                const spinner = document.createElement('div');
                spinner.className = 'loading';
                spinner.innerHTML = '<div class="spinner"></div>';
                
                const servicesContainer = document.querySelector('.services-grid');
                if (servicesContainer) {
                    servicesContainer.style.opacity = '0.5';
                }
            });
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>