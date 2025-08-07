<?php
require_once '../includes/functions.php';

requireLogin();
if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$pdo = getDBConnection();

// Filtres
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Construction de la requête
$where_conditions = ["o.user_id = ?"];
$params = [$user_id];

if ($status_filter) {
    $where_conditions[] = "o.status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(s.name LIKE ? OR o.target_url LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = implode(' AND ', $where_conditions);

// Compter le total pour la pagination
$count_query = "
    SELECT COUNT(*) as total
    FROM orders o
    JOIN services s ON o.service_id = s.id
    WHERE $where_clause
";
$stmt = $pdo->prepare($count_query);
$stmt->execute($params);
$total_orders = $stmt->fetch()['total'];
$total_pages = ceil($total_orders / $per_page);

// Récupérer les commandes
$query = "
    SELECT o.*, s.name as service_name, c.name as category_name
    FROM orders o
    JOIN services s ON o.service_id = s.id
    JOIN categories c ON s.category_id = c.id
    WHERE $where_clause
    ORDER BY o.created_at DESC
    LIMIT $per_page OFFSET $offset
";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Commandes - SMM Pro Services</title>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="order.php">Nouvelle commande</a></li>
                <li><a href="orders.php" class="text-primary">Mes commandes</a></li>
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
                    <h1 class="text-primary">Mes Commandes</h1>
                    <p class="text-muted">Gérez et suivez toutes vos commandes</p>
                </div>
                <a href="order.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Nouvelle commande
                </a>
            </div>

            <!-- Filtres et recherche -->
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter"></i>
                        Filtres et recherche
                    </h3>
                </div>
                
                <form method="GET" action="">
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label for="status" class="form-label">Statut</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                    <option value="processing" <?php echo $status_filter === 'processing' ? 'selected' : ''; ?>>En cours</option>
                                    <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Terminée</option>
                                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="search" class="form-label">Recherche</label>
                                <input 
                                    type="text" 
                                    id="search" 
                                    name="search" 
                                    class="form-control" 
                                    placeholder="Rechercher par service ou lien..."
                                    value="<?php echo htmlspecialchars($search); ?>"
                                >
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="form-group" style="margin-top: 1.8rem;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                    Filtrer
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($status_filter || $search): ?>
                    <div class="mt-2">
                        <a href="orders.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-times"></i>
                            Effacer les filtres
                        </a>
                    </div>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Résultats -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i>
                        Commandes (<?php echo $total_orders; ?> résultat<?php echo $total_orders > 1 ? 's' : ''; ?>)
                    </h3>
                    <?php if ($total_orders > 0): ?>
                    <div class="text-muted">
                        Page <?php echo $page; ?> sur <?php echo $total_pages; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
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
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><strong>#<?php echo $order['id']; ?></strong></td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($order['service_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($order['category_name']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo htmlspecialchars($order['target_url']); ?>" target="_blank" class="text-primary" style="font-size: 0.9rem;">
                                        <?php echo substr(htmlspecialchars($order['target_url']), 0, 40) . (strlen($order['target_url']) > 40 ? '...' : ''); ?>
                                        <i class="fas fa-external-link-alt" style="font-size: 0.7rem; margin-left: 0.3rem;"></i>
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
                                <td>
                                    <div>
                                        <strong><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></strong><br>
                                        <small class="text-muted"><?php echo date('H:i', strtotime($order['created_at'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i>
                                        Voir
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <nav aria-label="Navigation des pages">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>">
                                    <i class="fas fa-chevron-left"></i>
                                    Précédent
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo urlencode($status_filter); ?>&search=<?php echo urlencode($search); ?>">
                                    Suivant
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center p-4">
                    <div class="service-icon" style="color: var(--text-muted);">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="text-muted">Aucune commande trouvée</h3>
                    <?php if ($status_filter || $search): ?>
                        <p class="text-muted">Aucun résultat pour les critères sélectionnés</p>
                        <a href="orders.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Effacer les filtres
                        </a>
                    <?php else: ?>
                        <p class="text-muted">Vous n'avez pas encore passé de commande</p>
                        <a href="order.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Passer ma première commande
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Statistiques rapides -->
            <?php if ($total_orders > 0): ?>
            <div class="row mt-4">
                <?php
                // Statistiques rapides
                $stmt = $pdo->prepare("
                    SELECT 
                        status,
                        COUNT(*) as count,
                        SUM(total_amount) as total_amount
                    FROM orders 
                    WHERE user_id = ?
                    GROUP BY status
                ");
                $stmt->execute([$user_id]);
                $quick_stats = [];
                while ($row = $stmt->fetch()) {
                    $quick_stats[$row['status']] = $row;
                }
                ?>
                
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--warning-color); font-size: 1.5rem;">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h4><?php echo $quick_stats['pending']['count'] ?? 0; ?></h4>
                        <p class="text-muted" style="font-size: 0.9rem;">En attente</p>
                    </div>
                </div>
                
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--info-color); font-size: 1.5rem;">
                            <i class="fas fa-cog fa-spin"></i>
                        </div>
                        <h4><?php echo $quick_stats['processing']['count'] ?? 0; ?></h4>
                        <p class="text-muted" style="font-size: 0.9rem;">En cours</p>
                    </div>
                </div>
                
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--success-color); font-size: 1.5rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h4><?php echo $quick_stats['completed']['count'] ?? 0; ?></h4>
                        <p class="text-muted" style="font-size: 0.9rem;">Terminées</p>
                    </div>
                </div>
                
                <div class="col-3">
                    <div class="card text-center">
                        <div class="service-icon" style="color: var(--primary-color); font-size: 1.5rem;">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h4 style="font-size: 1rem;"><?php echo formatPrice(($quick_stats['completed']['total_amount'] ?? 0)); ?></h4>
                        <p class="text-muted" style="font-size: 0.9rem;">Total dépensé</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <script src="../assets/js/main.js"></script>
    
    <style>
        .pagination .page-link {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .pagination .page-link:hover {
            background: var(--primary-color);
            color: var(--dark-bg);
            border-color: var(--primary-color);
        }
        
        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            color: var(--dark-bg);
            border-color: var(--primary-color);
        }
        
        .pagination {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
    </style>
</body>
</html>