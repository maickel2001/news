<?php
session_start();
require_once 'config/database.php';

// Récupérer les catégories et services
$categoriesStmt = $db->query("SELECT * FROM categories WHERE status = 'active' ORDER BY name");
$categories = $categoriesStmt->fetchAll();

$servicesStmt = $db->query("
    SELECT s.*, c.name as category_name 
    FROM services s 
    JOIN categories c ON s.category_id = c.id 
    WHERE s.status = 'active' 
    ORDER BY c.name, s.name
");
$services = $servicesStmt->fetchAll();

// Organiser les services par catégorie
$servicesByCategory = [];
foreach ($services as $service) {
    $servicesByCategory[$service['category_id']][] = $service;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validation CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide';
    }
    
    $serviceId = sanitize($_POST['service_id'] ?? '');
    $targetUrl = sanitize($_POST['target_url'] ?? '');
    $quantity = (int)($_POST['quantity'] ?? 0);
    $customerEmail = sanitize($_POST['customer_email'] ?? '');
    $customerPhone = sanitize($_POST['customer_phone'] ?? '');
    
    // Validation
    if (empty($serviceId)) {
        $errors[] = 'Veuillez sélectionner un service';
    }
    
    if (empty($targetUrl) || !validateUrl($targetUrl)) {
        $errors[] = 'Veuillez saisir une URL valide';
    }
    
    if ($quantity <= 0) {
        $errors[] = 'Veuillez saisir une quantité valide';
    }
    
    if (!empty($customerEmail) && !validateEmail($customerEmail)) {
        $errors[] = 'Adresse email invalide';
    }
    
    // Vérifier que le service existe et récupérer ses détails
    $serviceStmt = $db->query("SELECT * FROM services WHERE id = ? AND status = 'active'", [$serviceId]);
    $service = $serviceStmt->fetch();
    
    if (!$service) {
        $errors[] = 'Service non trouvé';
    } else {
        // Vérifier les limites de quantité
        if ($quantity < $service['min_quantity']) {
            $errors[] = "Quantité minimale: {$service['min_quantity']}";
        }
        if ($quantity > $service['max_quantity']) {
            $errors[] = "Quantité maximale: {$service['max_quantity']}";
        }
    }
    
    if (empty($errors)) {
        // Calculer le montant total
        $totalAmount = $service['price'] * $quantity;
        
        // Générer un numéro de commande unique
        $orderNumber = 'SMM' . date('Ymd') . sprintf('%04d', rand(1, 9999));
        
        // Vérifier l'unicité du numéro de commande
        $checkStmt = $db->query("SELECT id FROM orders WHERE order_number = ?", [$orderNumber]);
        while ($checkStmt->fetch()) {
            $orderNumber = 'SMM' . date('Ymd') . sprintf('%04d', rand(1, 9999));
            $checkStmt = $db->query("SELECT id FROM orders WHERE order_number = ?", [$orderNumber]);
        }
        
        // Insérer la commande
        $orderData = [
            'order_number' => $orderNumber,
            'service_id' => $serviceId,
            'target_url' => $targetUrl,
            'quantity' => $quantity,
            'price_per_unit' => $service['price'],
            'total_amount' => $totalAmount,
            'customer_email' => $customerEmail ?: null,
            'customer_phone' => $customerPhone ?: null,
            'status' => 'pending'
        ];
        
        $orderId = $db->insert('orders', $orderData);
        
        if ($orderId) {
            // Rediriger vers la page de paiement
            redirect("payment.php?order=" . $orderNumber);
        } else {
            $errors[] = 'Erreur lors de la création de la commande';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commander - SMM Boost</title>
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
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main style="padding-top: 120px; padding-bottom: 80px;">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="order-form-container" style="background: var(--card-bg); padding: 3rem; border-radius: 20px; border: 1px solid var(--border-color);">
                        <h1 style="text-align: center; margin-bottom: 2rem;">
                            <i class="fas fa-shopping-cart"></i> Passer une commande
                        </h1>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <ul style="margin: 0; padding-left: 1.5rem;">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" id="orderForm">
                            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                            
                            <!-- Sélection de la catégorie -->
                            <div class="form-group">
                                <label for="category_id">
                                    <i class="fas fa-tags"></i> Plateforme
                                </label>
                                <select id="category_id" class="form-control" required>
                                    <option value="">Choisissez une plateforme</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>">
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Sélection du service -->
                            <div class="form-group">
                                <label for="service_id">
                                    <i class="fas fa-cog"></i> Service
                                </label>
                                <select name="service_id" id="service_id" class="form-control" required>
                                    <option value="">Sélectionnez d'abord une plateforme</option>
                                </select>
                            </div>
                            
                            <!-- URL cible -->
                            <div class="form-group">
                                <label for="target_url">
                                    <i class="fas fa-link"></i> Lien à promouvoir
                                </label>
                                <input type="url" name="target_url" id="target_url" class="form-control" 
                                       placeholder="https://..." required>
                                <small style="color: var(--text-muted); font-size: 0.875rem;">
                                    Entrez le lien de votre profil, page ou publication
                                </small>
                            </div>
                            
                            <!-- Quantité -->
                            <div class="form-group">
                                <label for="quantity">
                                    <i class="fas fa-calculator"></i> Quantité
                                </label>
                                <input type="number" name="quantity" id="quantity" class="form-control" 
                                       min="1" placeholder="Ex: 1000" required>
                                <div id="quantity-info" style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.5rem;"></div>
                            </div>
                            
                            <!-- Affichage du prix -->
                            <div id="price-display" class="price-display" style="display: none;">
                                <i class="fas fa-money-bill-wave"></i>
                                Total: <span id="total-price">0 FCFA</span>
                            </div>
                            
                            <!-- Informations de contact (optionnel) -->
                            <div class="form-group">
                                <label for="customer_email">
                                    <i class="fas fa-envelope"></i> Email (optionnel)
                                </label>
                                <input type="email" name="customer_email" id="customer_email" class="form-control" 
                                       placeholder="votre@email.com">
                                <small style="color: var(--text-muted); font-size: 0.875rem;">
                                    Pour recevoir des mises à jour sur votre commande
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label for="customer_phone">
                                    <i class="fas fa-phone"></i> Téléphone (optionnel)
                                </label>
                                <input type="tel" name="customer_phone" id="customer_phone" class="form-control" 
                                       placeholder="+226 XX XX XX XX">
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                                <i class="fas fa-credit-card"></i> Procéder au paiement
                            </button>
                        </form>
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

    <script>
        // Services organisés par catégorie (PHP vers JavaScript)
        const servicesByCategory = <?= json_encode($servicesByCategory) ?>;
        
        // Éléments du DOM
        const categorySelect = document.getElementById('category_id');
        const serviceSelect = document.getElementById('service_id');
        const quantityInput = document.getElementById('quantity');
        const quantityInfo = document.getElementById('quantity-info');
        const priceDisplay = document.getElementById('price-display');
        const totalPrice = document.getElementById('total-price');
        
        let currentService = null;
        
        // Gestion du changement de catégorie
        categorySelect.addEventListener('change', function() {
            const categoryId = this.value;
            serviceSelect.innerHTML = '<option value="">Choisissez un service</option>';
            
            if (categoryId && servicesByCategory[categoryId]) {
                servicesByCategory[categoryId].forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.name} - ${formatPrice(service.price)} par unité`;
                    option.dataset.service = JSON.stringify(service);
                    serviceSelect.appendChild(option);
                });
            }
            
            resetPriceDisplay();
        });
        
        // Gestion du changement de service
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            
            if (selectedOption.dataset.service) {
                currentService = JSON.parse(selectedOption.dataset.service);
                updateQuantityInfo();
                updatePriceDisplay();
                
                // Mettre à jour les limites de quantité
                quantityInput.min = currentService.min_quantity;
                quantityInput.max = currentService.max_quantity;
                quantityInput.placeholder = `Min: ${currentService.min_quantity}, Max: ${currentService.max_quantity}`;
            } else {
                currentService = null;
                resetPriceDisplay();
            }
        });
        
        // Gestion du changement de quantité
        quantityInput.addEventListener('input', updatePriceDisplay);
        
        function updateQuantityInfo() {
            if (currentService) {
                quantityInfo.innerHTML = `
                    Minimum: ${currentService.min_quantity.toLocaleString()} - 
                    Maximum: ${currentService.max_quantity.toLocaleString()}
                `;
            } else {
                quantityInfo.innerHTML = '';
            }
        }
        
        function updatePriceDisplay() {
            if (currentService && quantityInput.value) {
                const quantity = parseInt(quantityInput.value);
                const total = currentService.price * quantity;
                
                totalPrice.textContent = formatPrice(total);
                priceDisplay.style.display = 'block';
                
                // Validation des limites
                if (quantity < currentService.min_quantity) {
                    quantityInput.style.borderColor = 'var(--danger-color)';
                } else if (quantity > currentService.max_quantity) {
                    quantityInput.style.borderColor = 'var(--danger-color)';
                } else {
                    quantityInput.style.borderColor = 'var(--border-color)';
                }
            } else {
                priceDisplay.style.display = 'none';
            }
        }
        
        function resetPriceDisplay() {
            currentService = null;
            quantityInfo.innerHTML = '';
            priceDisplay.style.display = 'none';
            quantityInput.style.borderColor = 'var(--border-color)';
        }
        
        function formatPrice(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount) + ' FCFA';
        }
        
        // Validation du formulaire
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            if (!currentService) {
                e.preventDefault();
                alert('Veuillez sélectionner un service');
                return;
            }
            
            const quantity = parseInt(quantityInput.value);
            if (quantity < currentService.min_quantity || quantity > currentService.max_quantity) {
                e.preventDefault();
                alert(`La quantité doit être entre ${currentService.min_quantity} et ${currentService.max_quantity}`);
                return;
            }
        });
    </script>
    
    <script src="assets/js/script.js"></script>
</body>
</html>