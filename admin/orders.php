<?php
require_once 'includes/auth.php';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlashMessage('danger', 'Token de sécurité invalide');
    } else {
        $action = $_POST['action'] ?? '';
        $orderId = (int)($_POST['order_id'] ?? 0);
        
        if ($action === 'update_status' && $orderId > 0) {
            $newStatus = $_POST['status'] ?? '';
            $notes = sanitize($_POST['notes'] ?? '');
            $cancelReason = sanitize($_POST['cancel_reason'] ?? '');
            
            $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];
            
            if (in_array($newStatus, $validStatuses)) {
                $updateData = ['status' => $newStatus];
                
                if (!empty($notes)) {
                    $updateData['notes'] = $notes;
                }
                
                if ($newStatus === 'cancelled' && !empty($cancelReason)) {
                    $updateData['cancel_reason'] = $cancelReason;
                }
                
                if ($newStatus === 'completed') {
                    $updateData['completed_at'] = date('Y-m-d H:i:s');
                }
                
                $updated = $db->update('orders', $updateData, 'id = :id', ['id' => $orderId]);
                
                if ($updated) {
                    setFlashMessage('success', 'Statut de la commande mis à jour avec succès');
                } else {
                    setFlashMessage('danger', 'Erreur lors de la mise à jour');
                }
            } else {
                setFlashMessage('danger', 'Statut invalide');
            }
        }
    }
    
    redirect('orders.php');
}

// Filtres
$statusFilter = $_GET['status'] ?? '';
$searchQuery = sanitize($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Construction de la requête
$whereConditions = [];
$params = [];

if (!empty($statusFilter)) {
    $whereConditions[] = "o.status = ?";
    $params[] = $statusFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(o.order_number LIKE ? OR o.target_url LIKE ? OR o.customer_email LIKE ?)";
    $searchTerm = "%{$searchQuery}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Compter le total
$countStmt = $db->query("
    SELECT COUNT(*) as total 
    FROM orders o 
    JOIN services s ON o.service_id = s.id 
    JOIN categories c ON s.category_id = c.id 
    {$whereClause}
", $params);
$totalOrders = $countStmt->fetch()['total'];
$totalPages = ceil($totalOrders / $perPage);

// Récupérer les commandes
$ordersStmt = $db->query("
    SELECT o.*, s.name as service_name, c.name as category_name 
    FROM orders o 
    JOIN services s ON o.service_id = s.id 
    JOIN categories c ON s.category_id = c.id 
    {$whereClause}
    ORDER BY o.created_at DESC 
    LIMIT {$perPage} OFFSET {$offset}
", $params);
$orders = $ordersStmt->fetchAll();

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
    <title>Gestion des Commandes - Admin SMM Boost</title>
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
        .filters {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 15px;
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1rem;
            align-items: end;
        }
        .table-container {
            background: var(--card-bg);
            border-radius: 15px;
            border: 1px solid var(--border-color);
            overflow: hidden;
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
        .table tr:hover {
            background: rgba(0, 255, 136, 0.05);
        }
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.8rem;
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }
        .pagination a,
        .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-light);
        }
        .pagination a:hover {
            background: var(--primary-color);
            color: var(--dark-bg);
        }
        .pagination .current {
            background: var(--primary-color);
            color: var(--dark-bg);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
        }
        .modal-content {
            background: var(--card-bg);
            margin: 5% auto;
            padding: 2rem;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            border: 1px solid var(--border-color);
        }
        .close {
            color: var(--text-gray);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: var(--danger-color);
        }
        @media (max-width: 768px) {
            .filters {
                grid-template-columns: 1fr;
            }
            .admin-nav ul {
                flex-direction: column;
                gap: 1rem;
            }
            .table {
                font-size: 0.8rem;
            }
            .action-buttons {
                flex-direction: column;
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
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="orders.php" class="active"><i class="fas fa-shopping-cart"></i> Commandes</a></li>
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
                <i class="fas fa-shopping-cart"></i> Gestion des commandes
                <span style="color: var(--text-gray); font-size: 1rem; font-weight: normal;">
                    (<?= $totalOrders ?> commandes)
                </span>
            </h1>
            
            <!-- Messages flash -->
            <?php 
            $flashMessages = getFlashMessages();
            foreach ($flashMessages as $type => $messages):
                foreach ($messages as $message):
            ?>
                <div class="alert alert-<?= $type ?>">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php 
                endforeach;
            endforeach;
            ?>
            
            <!-- Filtres -->
            <form class="filters" method="GET">
                <div class="form-group" style="margin: 0;">
                    <label for="status">Statut</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        <?php foreach ($statusLabels as $value => $label): ?>
                            <option value="<?= $value ?>" <?= $statusFilter === $value ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group" style="margin: 0;">
                    <label for="search">Recherche</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Numéro, email, URL..." value="<?= htmlspecialchars($searchQuery) ?>">
                </div>
                
                <div style="grid-column: span 2;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="orders.php" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Réinitialiser
                    </a>
                </div>
            </form>
            
            <!-- Tableau des commandes -->
            <div class="table-container">
                <?php if (empty($orders)): ?>
                    <div style="padding: 3rem; text-align: center; color: var(--text-gray);">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                        <p>Aucune commande trouvée</p>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Commande</th>
                                <th>Service</th>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>
                                        <strong>#<?= htmlspecialchars($order['order_number']) ?></strong><br>
                                        <small style="color: var(--text-gray);">
                                            <?= number_format($order['quantity']) ?> unités
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($order['service_name']) ?></strong><br>
                                        <small style="color: var(--text-gray);">
                                            <?= htmlspecialchars($order['category_name']) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php if (!empty($order['customer_email'])): ?>
                                            <a href="mailto:<?= htmlspecialchars($order['customer_email']) ?>" 
                                               style="color: var(--primary-color);">
                                                <?= htmlspecialchars($order['customer_email']) ?>
                                            </a><br>
                                        <?php endif; ?>
                                        <?php if (!empty($order['customer_phone'])): ?>
                                            <small style="color: var(--text-gray);">
                                                <?= htmlspecialchars($order['customer_phone']) ?>
                                            </small>
                                        <?php endif; ?>
                                        <?php if (empty($order['customer_email']) && empty($order['customer_phone'])): ?>
                                            <span style="color: var(--text-muted);">Non renseigné</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= formatPrice($order['total_amount']) ?></strong><br>
                                        <small style="color: var(--text-gray);">
                                            <?= formatPrice($order['price_per_unit']) ?> / unité
                                        </small>
                                    </td>
                                    <td>
                                        <span class="status-badge" 
                                              style="background: <?= $statusColors[$order['status']] ?>; color: #fff;">
                                            <?= $statusLabels[$order['status']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?= date('d/m/Y', strtotime($order['created_at'])) ?></strong><br>
                                        <small style="color: var(--text-gray);">
                                            <?= date('H:i', strtotime($order['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="viewOrder(<?= $order['id'] ?>)" 
                                                    class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="editOrder(<?= $order['id'] ?>)" 
                                                    class="btn btn-secondary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if (!empty($order['payment_proof'])): ?>
                                                <a href="../uploads/<?= htmlspecialchars($order['payment_proof']) ?>" 
                                                   target="_blank" class="btn btn-success btn-sm" 
                                                   title="Voir la preuve de paiement">
                                                    <i class="fas fa-image"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($searchQuery) ?>">
                            <i class="fas fa-chevron-left"></i> Précédent
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <?php if ($i === $page): ?>
                            <span class="current"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($searchQuery) ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>&status=<?= urlencode($statusFilter) ?>&search=<?= urlencode($searchQuery) ?>">
                            Suivant <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal pour éditer une commande -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3><i class="fas fa-edit"></i> Modifier la commande</h3>
            <div id="editForm"></div>
        </div>
    </div>

    <!-- Modal pour voir les détails -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3><i class="fas fa-eye"></i> Détails de la commande</h3>
            <div id="viewContent"></div>
        </div>
    </div>

    <script>
        const orders = <?= json_encode($orders) ?>;
        
        function viewOrder(orderId) {
            const order = orders.find(o => o.id == orderId);
            if (!order) return;
            
            const statusLabels = {
                'pending': 'En attente',
                'processing': 'En cours',
                'completed': 'Terminée',
                'cancelled': 'Annulée'
            };
            
            const content = `
                <div style="line-height: 2;">
                    <p><strong>Numéro:</strong> #${order.order_number}</p>
                    <p><strong>Service:</strong> ${order.service_name} (${order.category_name})</p>
                    <p><strong>URL:</strong> <a href="${order.target_url}" target="_blank" style="color: var(--primary-color);">${order.target_url}</a></p>
                    <p><strong>Quantité:</strong> ${parseInt(order.quantity).toLocaleString()}</p>
                    <p><strong>Prix unitaire:</strong> ${formatPrice(order.price_per_unit)}</p>
                    <p><strong>Total:</strong> ${formatPrice(order.total_amount)}</p>
                    <p><strong>Statut:</strong> ${statusLabels[order.status]}</p>
                    <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleString('fr-FR')}</p>
                    ${order.customer_email ? `<p><strong>Email:</strong> ${order.customer_email}</p>` : ''}
                    ${order.customer_phone ? `<p><strong>Téléphone:</strong> ${order.customer_phone}</p>` : ''}
                    ${order.payment_method ? `<p><strong>Paiement:</strong> ${order.payment_method === 'mtn_money' ? 'MTN Money' : 'Moov Money'}</p>` : ''}
                    ${order.notes ? `<p><strong>Notes:</strong> ${order.notes}</p>` : ''}
                    ${order.cancel_reason ? `<p><strong>Motif d'annulation:</strong> ${order.cancel_reason}</p>` : ''}
                </div>
            `;
            
            document.getElementById('viewContent').innerHTML = content;
            document.getElementById('viewModal').style.display = 'block';
        }
        
        function editOrder(orderId) {
            const order = orders.find(o => o.id == orderId);
            if (!order) return;
            
            const form = `
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="order_id" value="${order.id}">
                    
                    <div class="form-group">
                        <label for="status">Statut</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>En attente</option>
                            <option value="processing" ${order.status === 'processing' ? 'selected' : ''}>En cours</option>
                            <option value="completed" ${order.status === 'completed' ? 'selected' : ''}>Terminée</option>
                            <option value="cancelled" ${order.status === 'cancelled' ? 'selected' : ''}>Annulée</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Notes (optionnel)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3">${order.notes || ''}</textarea>
                    </div>
                    
                    <div class="form-group" id="cancelReasonGroup" style="display: ${order.status === 'cancelled' ? 'block' : 'none'};">
                        <label for="cancel_reason">Motif d'annulation</label>
                        <textarea name="cancel_reason" id="cancel_reason" class="form-control" rows="2">${order.cancel_reason || ''}</textarea>
                    </div>
                    
                    <div style="text-align: right; margin-top: 1.5rem;">
                        <button type="button" onclick="closeModal()" class="btn btn-secondary">Annuler</button>
                        <button type="submit" class="btn btn-primary">Sauvegarder</button>
                    </div>
                </form>
            `;
            
            document.getElementById('editForm').innerHTML = form;
            document.getElementById('viewModal').style.display = 'none';
            document.getElementById('editModal').style.display = 'block';
            
            // Gestion du champ motif d'annulation
            document.getElementById('status').addEventListener('change', function() {
                const cancelGroup = document.getElementById('cancelReasonGroup');
                if (this.value === 'cancelled') {
                    cancelGroup.style.display = 'block';
                } else {
                    cancelGroup.style.display = 'none';
                }
            });
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            document.getElementById('viewModal').style.display = 'none';
        }
        
        function formatPrice(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
        }
        
        // Fermer les modaux en cliquant à l'extérieur
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target === editModal) {
                editModal.style.display = 'none';
            }
            if (event.target === viewModal) {
                viewModal.style.display = 'none';
            }
        }
    </script>
    
    <script src="../assets/js/script.js"></script>
</body>
</html>