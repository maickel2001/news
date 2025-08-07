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

// Récupérer l'ID du service
$service_id = $_GET['service'] ?? null;
if (!$service_id) {
    header('Location: services.php');
    exit();
}

$error = '';
$success = '';

try {
    $db = Database::getInstance();
    
    // Récupérer les détails du service
    $stmt = $db->prepare("SELECT * FROM services WHERE id = ? AND status = 'active'");
    $stmt->execute([$service_id]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        header('Location: services.php?error=service_not_found');
        exit();
    }
    
    // Traitement du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $link = trim($_POST['link'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);
        $comments = trim($_POST['comments'] ?? '');
        
        // Validation
        if (empty($link)) {
            $error = 'Le lien est obligatoire.';
        } elseif (!filter_var($link, FILTER_VALIDATE_URL)) {
            $error = 'Le lien doit être une URL valide.';
        } elseif ($quantity < $service['min_quantity']) {
            $error = "La quantité minimale est de " . number_format($service['min_quantity']) . ".";
        } elseif ($quantity > $service['max_quantity']) {
            $error = "La quantité maximale est de " . number_format($service['max_quantity']) . ".";
        } else {
            // Calculer le prix total
            $total_amount = $quantity * $service['price'] / 1000; // Prix pour 1000 unités
            
            // Vérifier le solde
            if ($user['balance'] < $total_amount) {
                $error = "Solde insuffisant. Votre solde actuel est de " . formatPrice($user['balance']) . 
                        " mais cette commande coûte " . formatPrice($total_amount) . ".";
            } else {
                // Créer la commande
                $db->beginTransaction();
                
                try {
                    // Insérer la commande
                    $stmt = $db->prepare("
                        INSERT INTO orders (user_id, service_id, link, quantity, total_amount, status, comments, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())
                    ");
                    $stmt->execute([$user['id'], $service_id, $link, $quantity, $total_amount, $comments]);
                    $order_id = $db->lastInsertId();
                    
                    // Débiter le solde utilisateur
                    $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
                    $stmt->execute([$total_amount, $user['id']]);
                    
                    // Enregistrer la transaction
                    $stmt = $db->prepare("
                        INSERT INTO transactions (user_id, order_id, type, amount, description, status, created_at) 
                        VALUES (?, ?, 'debit', ?, ?, 'completed', NOW())
                    ");
                    $description = "Commande #{$order_id} - " . $service['name'];
                    $stmt->execute([$user['id'], $order_id, $total_amount, $description]);
                    
                    // Log de l'activité
                    logActivity($user['id'], 'order_created', "Nouvelle commande #{$order_id} créée pour " . $service['name']);
                    
                    $db->commit();
                    
                    // Redirection vers la page de confirmation
                    header("Location: order-confirmation.php?order={$order_id}");
                    exit();
                    
                } catch (Exception $e) {
                    $db->rollBack();
                    $error = "Erreur lors de la création de la commande. Veuillez réessayer.";
                    error_log("Order creation error: " . $e->getMessage());
                }
            }
        }
    }
    
} catch (Exception $e) {
    $error = "Erreur de système. Veuillez réessayer plus tard.";
    error_log("Order page error: " . $e->getMessage());
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander - <?= htmlspecialchars($service['name']) ?> - TarantulaSMM</title>
    
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
        
        /* Page Layout */
        .order-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .page-header {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            text-align: center;
        }
        
        .page-header h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .page-header p {
            color: var(--text-secondary);
            margin: 0;
        }
        
        /* Service Info Card */
        .service-info-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 2rem;
        }
        
        .service-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .platform-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
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
            display: inline-block;
            margin-bottom: 1rem;
        }
        
        .service-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .service-specs {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .spec-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            background: var(--bg-light);
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        
        .spec-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .spec-value {
            font-weight: 700;
            color: var(--accent);
        }
        
        /* Order Form */
        .order-form-card {
            background: var(--secondary);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h5 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .form-control {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            background: var(--secondary);
            color: var(--text-primary);
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
            outline: none;
        }
        
        .form-text {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .input-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            max-width: 200px;
        }
        
        .quantity-btn {
            background: var(--accent);
            color: var(--secondary);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .quantity-btn:hover {
            background: var(--accent-light);
            transform: scale(1.05);
        }
        
        .quantity-btn:disabled {
            background: var(--border);
            color: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }
        
        #quantity {
            text-align: center;
            font-weight: 600;
            max-width: 120px;
        }
        
        /* Price Calculator */
        .price-calculator {
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            color: var(--secondary);
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .price-calculator h6 {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .price-breakdown {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .price-breakdown:last-child {
            border-bottom: none;
            margin-bottom: 0;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .total-price {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 2rem;
            font-weight: 800;
            text-align: center;
            margin: 1rem 0;
        }
        
        .balance-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            margin-top: 1rem;
        }
        
        .balance-sufficient {
            color: #90EE90;
        }
        
        .balance-insufficient {
            color: #FFB6C1;
        }
        
        /* Alert Messages */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        /* Submit Button */
        .btn-submit {
            background: var(--accent);
            color: var(--secondary);
            border: none;
            padding: 1rem 3rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-submit:hover:not(:disabled) {
            background: var(--accent-light);
            color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }
        
        .btn-submit:disabled {
            background: var(--border);
            color: var(--text-light);
            cursor: not-allowed;
            transform: none;
        }
        
        /* Security Notice */
        .security-notice {
            background: var(--bg-light);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .security-notice h6 {
            color: var(--primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .security-notice ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        
        .security-notice li {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .order-container {
                padding: 0 0.5rem;
            }
            
            .page-header {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .service-info-card,
            .order-form-card {
                padding: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .service-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .platform-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .service-specs {
                grid-template-columns: 1fr;
            }
            
            .price-calculator {
                padding: 1.5rem;
            }
            
            .input-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .quantity-controls {
                max-width: none;
                justify-content: center;
            }
        }
        
        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            display: inline-block;
            margin-right: 0.5rem;
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
                    <a href="services.php" class="btn-back">
                        <i class="fas fa-arrow-left me-2"></i>Services
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="order-container">
        <!-- Page Header -->
        <div class="page-header fade-in">
            <h1><i class="fas fa-shopping-cart me-3"></i>Passer une Commande</h1>
            <p>Configurez votre commande et procédez au paiement sécurisé</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger fade-in">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Service Info -->
            <div class="col-lg-6">
                <div class="service-info-card fade-in">
                    <div class="service-header">
                        <div class="platform-icon <?= getPlatformColor($service['platform']) ?>">
                            <i class="<?= getPlatformIcon($service['platform']) ?>"></i>
                        </div>
                        <div class="service-details">
                            <h3><?= htmlspecialchars($service['name']) ?></h3>
                            <span class="service-category">
                                <?= ucfirst($service['category']) ?>
                            </span>
                            <div class="service-description">
                                <?= htmlspecialchars($service['description']) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-specs">
                        <div class="spec-item">
                            <span class="spec-label">Prix unitaire</span>
                            <span class="spec-value"><?= formatPrice($service['price']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Quantité min</span>
                            <span class="spec-value"><?= number_format($service['min_quantity']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Quantité max</span>
                            <span class="spec-value"><?= number_format($service['max_quantity']) ?></span>
                        </div>
                        <div class="spec-item">
                            <span class="spec-label">Délai de livraison</span>
                            <span class="spec-value"><?= $service['delivery_time'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Form -->
            <div class="col-lg-6">
                <div class="order-form-card fade-in">
                    <form method="POST" id="orderForm">
                        <!-- Link Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-link me-2"></i>Lien de la Page/Profil</h5>
                            <div class="form-group">
                                <label class="form-label">URL complète *</label>
                                <input type="url" name="link" id="link" class="form-control" 
                                       placeholder="https://www.<?= strtolower($service['platform']) ?>.com/..." 
                                       value="<?= htmlspecialchars($_POST['link'] ?? '') ?>" required>
                                <div class="form-text">
                                    Collez l'URL complète de votre page <?= ucfirst($service['platform']) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-calculator me-2"></i>Quantité</h5>
                            <div class="form-group">
                                <label class="form-label">Nombre d'unités *</label>
                                <div class="input-group">
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn" id="decreaseBtn">-</button>
                                        <input type="number" name="quantity" id="quantity" class="form-control" 
                                               min="<?= $service['min_quantity'] ?>" 
                                               max="<?= $service['max_quantity'] ?>" 
                                               value="<?= $_POST['quantity'] ?? $service['min_quantity'] ?>" required>
                                        <button type="button" class="quantity-btn" id="increaseBtn">+</button>
                                    </div>
                                </div>
                                <div class="form-text">
                                    Entre <?= number_format($service['min_quantity']) ?> et <?= number_format($service['max_quantity']) ?> unités
                                </div>
                            </div>
                        </div>

                        <!-- Price Calculator -->
                        <div class="price-calculator">
                            <h6><i class="fas fa-calculator me-2"></i>Calcul du Prix</h6>
                            <div class="price-breakdown">
                                <span>Quantité:</span>
                                <span id="calculatedQuantity"><?= number_format($_POST['quantity'] ?? $service['min_quantity']) ?></span>
                            </div>
                            <div class="price-breakdown">
                                <span>Prix unitaire (pour 1000):</span>
                                <span><?= formatPrice($service['price']) ?></span>
                            </div>
                            <div class="price-breakdown">
                                <span><strong>Total:</strong></span>
                                <span id="calculatedTotal"><strong><?= formatPrice((($_POST['quantity'] ?? $service['min_quantity']) * $service['price']) / 1000) ?></strong></span>
                            </div>
                            
                            <div class="balance-info">
                                <div>Votre solde: <strong><?= formatPrice($user['balance']) ?></strong></div>
                                <div id="balanceStatus" class="balance-sufficient">
                                    <i class="fas fa-check-circle me-1"></i>Solde suffisant
                                </div>
                            </div>
                        </div>

                        <!-- Comments Section -->
                        <div class="form-section">
                            <h5><i class="fas fa-comment me-2"></i>Instructions Spéciales</h5>
                            <div class="form-group">
                                <label class="form-label">Commentaires (optionnel)</label>
                                <textarea name="comments" id="comments" class="form-control" rows="3" 
                                          placeholder="Ajoutez des instructions spéciales pour votre commande..."><?= htmlspecialchars($_POST['comments'] ?? '') ?></textarea>
                                <div class="form-text">
                                    Précisez toute information importante pour le traitement de votre commande
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <i class="fas fa-credit-card me-2"></i>
                            Confirmer la Commande
                        </button>
                    </form>

                    <!-- Security Notice -->
                    <div class="security-notice">
                        <h6><i class="fas fa-shield-alt me-2"></i>Garanties de Sécurité</h6>
                        <ul>
                            <li>Vos données sont cryptées et sécurisées</li>
                            <li>Livraison garantie sous <?= $service['delivery_time'] ?></li>
                            <li>Support client 24h/7j disponible</li>
                            <li>Remboursement en cas de problème</li>
                            <?php if ($service['refill_guaranteed']): ?>
                            <li>Garantie de remplissage automatique</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const servicePrice = <?= $service['price'] ?>;
        const minQuantity = <?= $service['min_quantity'] ?>;
        const maxQuantity = <?= $service['max_quantity'] ?>;
        const userBalance = <?= $user['balance'] ?>;
        
        const quantityInput = document.getElementById('quantity');
        const decreaseBtn = document.getElementById('decreaseBtn');
        const increaseBtn = document.getElementById('increaseBtn');
        const calculatedQuantity = document.getElementById('calculatedQuantity');
        const calculatedTotal = document.getElementById('calculatedTotal');
        const balanceStatus = document.getElementById('balanceStatus');
        const submitBtn = document.getElementById('submitBtn');
        
        function formatPrice(amount) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(amount)) + ' FCFA';
        }
        
        function updatePrice() {
            const quantity = parseInt(quantityInput.value) || minQuantity;
            const total = (quantity * servicePrice) / 1000;
            
            calculatedQuantity.textContent = new Intl.NumberFormat('fr-FR').format(quantity);
            calculatedTotal.innerHTML = '<strong>' + formatPrice(total) + '</strong>';
            
            // Update balance status
            if (total <= userBalance) {
                balanceStatus.className = 'balance-sufficient';
                balanceStatus.innerHTML = '<i class="fas fa-check-circle me-1"></i>Solde suffisant';
                submitBtn.disabled = false;
            } else {
                balanceStatus.className = 'balance-insufficient';
                balanceStatus.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Solde insuffisant (manque ' + 
                    formatPrice(total - userBalance) + ')';
                submitBtn.disabled = true;
            }
            
            // Update button states
            decreaseBtn.disabled = quantity <= minQuantity;
            increaseBtn.disabled = quantity >= maxQuantity;
        }
        
        // Quantity controls
        decreaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value) || minQuantity;
            if (currentValue > minQuantity) {
                quantityInput.value = currentValue - 1;
                updatePrice();
            }
        });
        
        increaseBtn.addEventListener('click', () => {
            const currentValue = parseInt(quantityInput.value) || minQuantity;
            if (currentValue < maxQuantity) {
                quantityInput.value = currentValue + 1;
                updatePrice();
            }
        });
        
        quantityInput.addEventListener('input', () => {
            let value = parseInt(quantityInput.value);
            
            if (isNaN(value) || value < minQuantity) {
                value = minQuantity;
            } else if (value > maxQuantity) {
                value = maxQuantity;
            }
            
            quantityInput.value = value;
            updatePrice();
        });
        
        // Form validation
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            const link = document.getElementById('link').value.trim();
            const quantity = parseInt(quantityInput.value);
            
            if (!link || !link.startsWith('http')) {
                e.preventDefault();
                alert('Veuillez entrer une URL valide commençant par http:// ou https://');
                return;
            }
            
            if (quantity < minQuantity || quantity > maxQuantity) {
                e.preventDefault();
                alert(`La quantité doit être entre ${minQuantity.toLocaleString()} et ${maxQuantity.toLocaleString()}`);
                return;
            }
            
            const total = (quantity * servicePrice) / 1000;
            if (total > userBalance) {
                e.preventDefault();
                alert('Solde insuffisant pour cette commande');
                return;
            }
            
            // Add loading state
            submitBtn.innerHTML = '<span class="spinner"></span>Traitement en cours...';
            submitBtn.disabled = true;
        });
        
        // Platform-specific URL validation
        const platformPatterns = {
            'instagram': /instagram\.com/i,
            'tiktok': /tiktok\.com/i,
            'facebook': /facebook\.com/i,
            'youtube': /youtube\.com|youtu\.be/i,
            'twitter': /twitter\.com|x\.com/i,
            'linkedin': /linkedin\.com/i
        };
        
        document.getElementById('link').addEventListener('blur', function() {
            const link = this.value.trim();
            const platform = '<?= strtolower($service['platform']) ?>';
            
            if (link && platformPatterns[platform] && !platformPatterns[platform].test(link)) {
                this.setCustomValidity(`Veuillez entrer une URL ${platform.charAt(0).toUpperCase() + platform.slice(1)} valide`);
                this.reportValidity();
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Initialize
        updatePrice();
        
        // Auto-save form data
        const formInputs = document.querySelectorAll('#orderForm input, #orderForm textarea');
        formInputs.forEach(input => {
            input.addEventListener('input', () => {
                localStorage.setItem('orderForm_' + input.name, input.value);
            });
            
            // Restore saved data
            const savedValue = localStorage.getItem('orderForm_' + input.name);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }
        });
        
        // Clear saved data on successful submission
        document.getElementById('orderForm').addEventListener('submit', () => {
            formInputs.forEach(input => {
                localStorage.removeItem('orderForm_' + input.name);
            });
        });
    </script>
</body>
</html>