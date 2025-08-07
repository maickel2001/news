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

// Récupérer l'ID de la commande
$order_id = $_GET['order'] ?? null;
if (!$order_id) {
    header('Location: orders.php');
    exit();
}

try {
    $db = Database::getInstance();
    
    // Récupérer les détails de la commande avec le service
    $stmt = $db->prepare("
        SELECT o.*, s.name as service_name, s.platform, s.category, s.delivery_time,
               t.amount as transaction_amount, t.created_at as payment_date
        FROM orders o 
        JOIN services s ON o.service_id = s.id 
        LEFT JOIN transactions t ON o.id = t.order_id AND t.type = 'debit' 
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user['id']]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$order) {
        header('Location: orders.php?error=order_not_found');
        exit();
    }
    
} catch (Exception $e) {
    header('Location: orders.php?error=system_error');
    exit();
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

function getStatusDescription($status) {
    $descriptions = [
        'pending' => 'Votre commande a été reçue et sera traitée sous peu.',
        'processing' => 'Votre commande est en cours de traitement par nos équipes.',
        'completed' => 'Votre commande a été livrée avec succès !',
        'cancelled' => 'Cette commande a été annulée.',
        'refunded' => 'Cette commande a été remboursée.'
    ];
    return $descriptions[$status] ?? 'Statut de la commande mis à jour.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Commande #<?= $order['id'] ?> - TarantulaSMM</title>
    
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
        
        /* Navigation */
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
        
        .btn-nav {
            background: var(--primary);
            color: var(--secondary);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-nav:hover {
            background: var(--accent);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Page Layout */
        .confirmation-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        /* Success Header */
        .success-header {
            background: linear-gradient(135deg, var(--success), #32d74b);
            color: var(--secondary);
            border-radius: 15px;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-hover);
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1.5rem;
            animation: successPulse 2s ease-in-out infinite;
        }
        
        @keyframes successPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .success-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            margin-bottom: 1rem;
        }
        
        .success-header p {
            font-size: 1.2rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .order-number {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            display: inline-block;
            margin-top: 1rem;
            font-size: 1.1rem;
        }
        
        /* Order Details Card */
        .order-details-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        
        .order-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }
        
        .service-info {
            display: flex;
            align-items: center;
            gap: 1rem;
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
        
        .service-details h3 {
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
        
        .status-badge {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .status-badge.success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border: 2px solid var(--success);
        }
        
        .status-badge.warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border: 2px solid var(--warning);
        }
        
        .status-badge.info {
            background: rgba(23, 162, 184, 0.1);
            color: var(--info);
            border: 2px solid var(--info);
        }
        
        .status-badge.danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border: 2px solid var(--danger);
        }
        
        /* Order Details Grid */
        .order-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .detail-item {
            background: var(--bg-light);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid var(--border);
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.1rem;
        }
        
        .detail-value.price {
            color: var(--accent);
            font-size: 1.3rem;
        }
        
        .detail-value.link {
            word-break: break-all;
            font-size: 0.9rem;
            color: var(--info);
        }
        
        /* Timeline */
        .order-timeline {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        
        .timeline-header {
            margin-bottom: 2rem;
        }
        
        .timeline-header h5 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .timeline-header p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
            background: var(--bg-light);
            border-radius: 10px;
            border-left: 4px solid var(--border);
        }
        
        .timeline-item.active {
            border-left-color: var(--accent);
            background: rgba(255, 107, 53, 0.05);
        }
        
        .timeline-item.completed {
            border-left-color: var(--success);
            background: rgba(40, 167, 69, 0.05);
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: -8px;
            top: 20px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--border);
        }
        
        .timeline-item.active::before {
            background: var(--accent);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.2);
        }
        
        .timeline-item.completed::before {
            background: var(--success);
        }
        
        .timeline-title {
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .timeline-description {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .timeline-date {
            color: var(--text-light);
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        /* Actions Card */
        .actions-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            text-align: center;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .action-btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 2px solid transparent;
        }
        
        .action-btn-primary {
            background: var(--accent);
            color: var(--secondary);
        }
        
        .action-btn-primary:hover {
            background: var(--accent-light);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        .action-btn-secondary {
            background: var(--bg-light);
            color: var(--text-primary);
            border-color: var(--border);
        }
        
        .action-btn-secondary:hover {
            background: var(--primary);
            color: var(--secondary);
            border-color: var(--primary);
        }
        
        .action-btn-outline {
            background: transparent;
            color: var(--info);
            border-color: var(--info);
        }
        
        .action-btn-outline:hover {
            background: var(--info);
            color: var(--secondary);
        }
        
        /* Support Notice */
        .support-notice {
            background: linear-gradient(135deg, var(--info), #20a8d8);
            color: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }
        
        .support-notice h6 {
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .support-notice p {
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }
        
        .support-contacts {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }
        
        .support-contact {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            color: var(--secondary);
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .support-contact:hover {
            background: rgba(255, 255, 255, 0.2);
            color: var(--secondary);
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .confirmation-container {
                padding: 0 0.5rem;
            }
            
            .success-header {
                padding: 2rem 1rem;
            }
            
            .success-icon {
                width: 80px;
                height: 80px;
                font-size: 2rem;
            }
            
            .order-details-card,
            .order-timeline,
            .actions-card {
                padding: 1.5rem;
            }
            
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .order-details-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .timeline {
                padding-left: 1.5rem;
            }
            
            .actions-grid {
                grid-template-columns: 1fr;
            }
            
            .support-contacts {
                flex-direction: column;
                gap: 1rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .navbar,
            .actions-card,
            .support-notice {
                display: none;
            }
            
            .confirmation-container {
                margin: 0;
                padding: 0;
            }
            
            .order-details-card,
            .order-timeline {
                box-shadow: none;
                border: 1px solid #ddd;
            }
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
                    <a href="dashboard.php" class="btn-nav">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="confirmation-container">
        <!-- Success Header -->
        <div class="success-header fade-in">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Commande Confirmée !</h1>
            <p>Votre commande a été créée avec succès et est en cours de traitement</p>
            <div class="order-number">
                Commande #<?= $order['id'] ?>
            </div>
        </div>

        <!-- Order Details -->
        <div class="order-details-card fade-in">
            <div class="order-header">
                <div class="service-info">
                    <div class="platform-icon <?= getPlatformColor($order['platform']) ?>">
                        <i class="<?= getPlatformIcon($order['platform']) ?>"></i>
                    </div>
                    <div class="service-details">
                        <h3><?= htmlspecialchars($order['service_name']) ?></h3>
                        <span class="service-category">
                            <?= ucfirst($order['category']) ?>
                        </span>
                    </div>
                </div>
                
                <div class="status-badge <?= getStatusColor($order['status']) ?>">
                    <i class="fas fa-clock me-2"></i>
                    <?= getStatusLabel($order['status']) ?>
                </div>
            </div>
            
            <div class="order-details-grid">
                <div class="detail-item">
                    <div class="detail-label">Lien de la page</div>
                    <div class="detail-value link">
                        <a href="<?= htmlspecialchars($order['link']) ?>" target="_blank" rel="noopener">
                            <?= htmlspecialchars($order['link']) ?>
                        </a>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Quantité</div>
                    <div class="detail-value">
                        <?= number_format($order['quantity']) ?> unités
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Montant total</div>
                    <div class="detail-value price">
                        <?= formatPrice($order['total_amount']) ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Délai de livraison</div>
                    <div class="detail-value">
                        <?= htmlspecialchars($order['delivery_time']) ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Date de commande</div>
                    <div class="detail-value">
                        <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Paiement</div>
                    <div class="detail-value">
                        <?= $order['payment_date'] ? 'Payé le ' . date('d/m/Y', strtotime($order['payment_date'])) : 'En attente' ?>
                    </div>
                </div>
            </div>
            
            <?php if ($order['comments']): ?>
            <div class="detail-item">
                <div class="detail-label">Instructions spéciales</div>
                <div class="detail-value">
                    <?= nl2br(htmlspecialchars($order['comments'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Order Timeline -->
        <div class="order-timeline fade-in">
            <div class="timeline-header">
                <h5><i class="fas fa-history me-2"></i>Suivi de Commande</h5>
                <p><?= getStatusDescription($order['status']) ?></p>
            </div>
            
            <div class="timeline">
                <div class="timeline-item completed">
                    <div class="timeline-title">Commande Créée</div>
                    <div class="timeline-description">Votre commande a été enregistrée avec succès</div>
                    <div class="timeline-date"><?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?></div>
                </div>
                
                <div class="timeline-item completed">
                    <div class="timeline-title">Paiement Confirmé</div>
                    <div class="timeline-description">Le paiement de <?= formatPrice($order['total_amount']) ?> a été traité</div>
                    <div class="timeline-date"><?= date('d/m/Y à H:i', strtotime($order['payment_date'])) ?></div>
                </div>
                
                <div class="timeline-item <?= in_array($order['status'], ['processing', 'completed']) ? 'completed' : ($order['status'] === 'pending' ? 'active' : '') ?>">
                    <div class="timeline-title">Traitement en Cours</div>
                    <div class="timeline-description">Nos équipes travaillent sur votre commande</div>
                    <div class="timeline-date">
                        <?= $order['status'] === 'processing' ? 'En cours...' : 'En attente' ?>
                    </div>
                </div>
                
                <div class="timeline-item <?= $order['status'] === 'completed' ? 'completed' : '' ?>">
                    <div class="timeline-title">Livraison Terminée</div>
                    <div class="timeline-description">Votre commande a été livrée avec succès</div>
                    <div class="timeline-date">
                        <?= $order['status'] === 'completed' ? date('d/m/Y à H:i', strtotime($order['updated_at'])) : 'Prévue sous ' . $order['delivery_time'] ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-card fade-in">
            <h5><i class="fas fa-cogs me-2"></i>Actions Disponibles</h5>
            <p>Que souhaitez-vous faire maintenant ?</p>
            
            <div class="actions-grid">
                <a href="orders.php" class="action-btn action-btn-primary">
                    <i class="fas fa-list"></i>
                    Mes Commandes
                </a>
                
                <a href="services.php" class="action-btn action-btn-secondary">
                    <i class="fas fa-plus"></i>
                    Nouvelle Commande
                </a>
                
                <a href="support.php?order=<?= $order['id'] ?>" class="action-btn action-btn-outline">
                    <i class="fas fa-headset"></i>
                    Support
                </a>
                
                <a href="javascript:window.print()" class="action-btn action-btn-outline">
                    <i class="fas fa-print"></i>
                    Imprimer
                </a>
            </div>
        </div>

        <!-- Support Notice -->
        <div class="support-notice fade-in">
            <h6><i class="fas fa-life-ring me-2"></i>Besoin d'Aide ?</h6>
            <p>Notre équipe support est disponible 24h/7j pour vous accompagner</p>
            
            <div class="support-contacts">
                <a href="support.php" class="support-contact">
                    <i class="fas fa-comments"></i>
                    Chat en Direct
                </a>
                
                <a href="https://wa.me/22997000000" class="support-contact">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
                
                <a href="mailto:support@tarantulasmm.bj" class="support-contact">
                    <i class="fas fa-envelope"></i>
                    Email Support
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-refresh status every 30 seconds if order is not completed
        <?php if (!in_array($order['status'], ['completed', 'cancelled', 'refunded'])): ?>
        setInterval(function() {
            // Only refresh if the page is visible
            if (!document.hidden) {
                location.reload();
            }
        }, 30000);
        <?php endif; ?>
        
        // Copy order number to clipboard
        document.querySelector('.order-number').addEventListener('click', function() {
            const orderNumber = '<?= $order['id'] ?>';
            navigator.clipboard.writeText(orderNumber).then(function() {
                // Show temporary success message
                const originalText = this.textContent;
                this.textContent = 'Copié !';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 2000);
            }.bind(this));
        });
        
        // Share order details
        if (navigator.share) {
            const shareBtn = document.createElement('a');
            shareBtn.className = 'action-btn action-btn-outline';
            shareBtn.innerHTML = '<i class="fas fa-share"></i> Partager';
            shareBtn.addEventListener('click', function() {
                navigator.share({
                    title: 'Ma commande TarantulaSMM',
                    text: `Commande #<?= $order['id'] ?> - <?= htmlspecialchars($order['service_name']) ?>`,
                    url: window.location.href
                });
            });
            
            document.querySelector('.actions-grid').appendChild(shareBtn);
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>