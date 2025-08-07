<?php
session_start();
require_once 'config/database.php';

// Vérifier si un numéro de commande est fourni
$orderNumber = sanitize($_GET['order'] ?? '');

if (empty($orderNumber)) {
    redirect('order.php');
}

// Récupérer les détails de la commande
$orderStmt = $db->query("
    SELECT o.*, s.name as service_name, c.name as category_name 
    FROM orders o 
    JOIN services s ON o.service_id = s.id 
    JOIN categories c ON s.category_id = c.id 
    WHERE o.order_number = ?
", [$orderNumber]);

$order = $orderStmt->fetch();

if (!$order) {
    redirect('order.php');
}

// Statut en français
$statusLabels = [
    'pending' => 'En attente',
    'processing' => 'En cours de traitement',
    'completed' => 'Terminée',
    'cancelled' => 'Annulée'
];

$statusColors = [
    'pending' => 'var(--warning-color)',
    'processing' => 'var(--primary-color)',
    'completed' => 'var(--success-color)',
    'cancelled' => 'var(--danger-color)'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande - SMM Boost</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <h2><i class="fas fa-rocket"></i> SMM Boost</h2>
                </div>
                <ul class="nav-menu">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="index.php#services">Services</a></li>
                    <li><a href="index.php#contact">Contact</a></li>
                    <li><a href="order.php" class="btn-order">Commander</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main style="padding-top: 120px; padding-bottom: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    
                    <?php 
                    $flashMessages = getFlashMessages();
                    if (!empty($flashMessages)):
                        foreach ($flashMessages as $type => $messages):
                            foreach ($messages as $message):
                    ?>
                        <div class="alert alert-<?= $type ?>">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php 
                            endforeach;
                        endforeach;
                    endif;
                    ?>
                    
                    <!-- Confirmation principale -->
                    <div style="background: var(--card-bg); padding: 3rem; border-radius: 20px; border: 1px solid var(--border-color); text-align: center; margin-bottom: 2rem;">
                        <div style="color: var(--success-color); font-size: 4rem; margin-bottom: 1rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h1 style="color: var(--success-color); margin-bottom: 1rem;">Commande confirmée !</h1>
                        <p style="font-size: 1.2rem; color: var(--text-gray); margin-bottom: 2rem;">
                            Votre preuve de paiement a été reçue avec succès. Notre équipe va traiter votre commande dans les plus brefs délais.
                        </p>
                        <div style="background: var(--dark-bg); padding: 1rem; border-radius: 10px; display: inline-block;">
                            <p style="margin: 0; color: var(--text-light);">
                                <strong>Numéro de commande:</strong> 
                                <span style="color: var(--primary-color); font-size: 1.2rem;">#<?= htmlspecialchars($order['order_number']) ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Détails de la commande -->
                    <div style="background: var(--card-bg); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 2rem; text-align: center;">
                            <i class="fas fa-receipt"></i> Détails de votre commande
                        </h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                            <div>
                                <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Service commandé</h4>
                                <p><strong>Plateforme:</strong> <?= htmlspecialchars($order['category_name']) ?></p>
                                <p><strong>Service:</strong> <?= htmlspecialchars($order['service_name']) ?></p>
                                <p><strong>Quantité:</strong> <?= number_format($order['quantity']) ?></p>
                                <p><strong>URL cible:</strong> <br><a href="<?= htmlspecialchars($order['target_url']) ?>" target="_blank" style="color: var(--primary-color); word-break: break-all;"><?= htmlspecialchars($order['target_url']) ?></a></p>
                            </div>
                            
                            <div>
                                <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Statut et paiement</h4>
                                <p>
                                    <strong>Statut:</strong> 
                                    <span style="color: <?= $statusColors[$order['status']] ?>; font-weight: bold;">
                                        <i class="fas fa-circle" style="font-size: 0.7rem;"></i>
                                        <?= $statusLabels[$order['status']] ?>
                                    </span>
                                </p>
                                <p><strong>Méthode de paiement:</strong> 
                                    <?php if ($order['payment_method'] === 'mtn_money'): ?>
                                        <span style="color: #FFCC00;">MTN Mobile Money</span>
                                    <?php elseif ($order['payment_method'] === 'moov_money'): ?>
                                        <span style="color: #0066CC;">Moov Money</span>
                                    <?php else: ?>
                                        Non spécifiée
                                    <?php endif; ?>
                                </p>
                                <p><strong>Montant total:</strong> <span style="color: var(--primary-color); font-size: 1.2rem; font-weight: bold;"><?= formatPrice($order['total_amount']) ?></span></p>
                                <p><strong>Date de commande:</strong> <?= date('d/m/Y à H:i', strtotime($order['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Prochaines étapes -->
                    <div style="background: var(--card-bg); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 2rem; text-align: center;">
                            <i class="fas fa-clock"></i> Prochaines étapes
                        </h3>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                            <div style="text-align: center; padding: 1rem;">
                                <div style="width: 60px; height: 60px; background: var(--primary-color); color: var(--dark-bg); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; font-weight: bold;">
                                    1
                                </div>
                                <h4 style="margin-bottom: 0.5rem;">Vérification</h4>
                                <p style="color: var(--text-gray); font-size: 0.9rem;">Notre équipe vérifie votre preuve de paiement</p>
                            </div>
                            
                            <div style="text-align: center; padding: 1rem;">
                                <div style="width: 60px; height: 60px; background: var(--border-color); color: var(--text-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; font-weight: bold;">
                                    2
                                </div>
                                <h4 style="margin-bottom: 0.5rem;">Traitement</h4>
                                <p style="color: var(--text-gray); font-size: 0.9rem;">Lancement du service sur votre profil/publication</p>
                            </div>
                            
                            <div style="text-align: center; padding: 1rem;">
                                <div style="width: 60px; height: 60px; background: var(--border-color); color: var(--text-gray); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 1.5rem; font-weight: bold;">
                                    3
                                </div>
                                <h4 style="margin-bottom: 0.5rem;">Livraison</h4>
                                <p style="color: var(--text-gray); font-size: 0.9rem;">Vous recevez vos followers/likes/vues</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations importantes -->
                    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid var(--primary-color); border-radius: 15px; padding: 2rem; margin-bottom: 2rem;">
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">
                            <i class="fas fa-info-circle"></i> Informations importantes
                        </h4>
                        <ul style="color: var(--text-light); margin: 0; padding-left: 1.5rem;">
                            <li style="margin-bottom: 0.5rem;">Le traitement de votre commande peut prendre entre 1 à 24 heures</li>
                            <li style="margin-bottom: 0.5rem;">Vous recevrez les résultats de manière progressive</li>
                            <li style="margin-bottom: 0.5rem;">Conservez votre numéro de commande pour tout suivi</li>
                            <?php if (!empty($order['customer_email'])): ?>
                                <li style="margin-bottom: 0.5rem;">Des mises à jour seront envoyées à: <?= htmlspecialchars($order['customer_email']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Actions -->
                    <div style="text-align: center;">
                        <a href="order.php" class="btn btn-primary" style="margin-right: 1rem;">
                            <i class="fas fa-plus"></i> Nouvelle commande
                        </a>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-home"></i> Retour à l'accueil
                        </a>
                    </div>
                    
                    <!-- Contact support -->
                    <div style="text-align: center; margin-top: 2rem; padding: 1.5rem; background: var(--secondary-color); border-radius: 10px;">
                        <h4 style="margin-bottom: 1rem;">Besoin d'aide ?</h4>
                        <p style="color: var(--text-gray); margin-bottom: 1rem;">
                            Notre équipe support est disponible pour vous aider
                        </p>
                        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                            <a href="mailto:<?= getSiteSetting('site_email', 'contact@smmboost.com') ?>" style="color: var(--primary-color); text-decoration: none;">
                                <i class="fas fa-envelope"></i> Email: <?= getSiteSetting('site_email', 'contact@smmboost.com') ?>
                            </a>
                            <span style="color: var(--text-muted);">|</span>
                            <span style="color: var(--primary-color);">
                                <i class="fas fa-phone"></i> Tél: <?= getSiteSetting('site_phone', '+226 XX XX XX XX') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-rocket"></i> SMM Boost</h3>
                    <p>Votre partenaire pour booster votre présence sur les réseaux sociaux</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 SMM Boost. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>