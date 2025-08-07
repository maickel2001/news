<?php
require_once '../includes/functions.php';

requireLogin();
if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

$user_id = $_SESSION['user_id'];
$pdo = getDBConnection();

// Récupérer les catégories et services
$categories = getServicesByCategory();

// Récupérer les paramètres du site pour les numéros Mobile Money
$stmt = $pdo->query("SELECT setting_key, setting_value FROM site_settings WHERE setting_key IN ('mtn_money_number', 'moov_money_number', 'payment_instructions')");
$settings = [];
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = (int)$_POST['service_id'];
    $target_url = sanitizeInput($_POST['target_url']);
    $quantity = (int)$_POST['quantity'];
    $payment_method = sanitizeInput($_POST['payment_method']);
    
    // Validation
    if (empty($service_id)) {
        $errors[] = 'Veuillez sélectionner un service.';
    }
    
    if (empty($target_url)) {
        $errors[] = 'Veuillez saisir le lien à promouvoir.';
    } elseif (!filter_var($target_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Veuillez saisir une URL valide.';
    }
    
    if ($quantity <= 0) {
        $errors[] = 'La quantité doit être supérieure à 0.';
    }
    
    if (!in_array($payment_method, ['mtn_money', 'moov_money'])) {
        $errors[] = 'Veuillez sélectionner un moyen de paiement valide.';
    }
    
    // Vérifier le service et calculer le montant
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ? AND active = 1");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();
        
        if (!$service) {
            $errors[] = 'Service non trouvé ou indisponible.';
        } else {
            // Vérifier les limites de quantité
            if ($quantity < $service['min_quantity']) {
                $errors[] = "La quantité minimum pour ce service est de " . $service['min_quantity'] . ".";
            } elseif ($quantity > $service['max_quantity']) {
                $errors[] = "La quantité maximum pour ce service est de " . $service['max_quantity'] . ".";
            }
        }
    }
    
    // Traitement de l'upload de la preuve de paiement
    $payment_proof = null;
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $payment_proof = uploadFile($_FILES['payment_proof'], '../uploads/payment_proofs/');
        if (!$payment_proof) {
            $errors[] = 'Erreur lors de l\'upload de la preuve de paiement. Veuillez vérifier que le fichier est une image valide (JPG, PNG) de moins de 5MB.';
        }
    } else {
        $errors[] = 'Veuillez uploader une preuve de paiement.';
    }
    
    // Créer la commande
    if (empty($errors)) {
        try {
            $total_amount = $service['price'] * $quantity;
            
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, service_id, target_url, quantity, unit_price, total_amount, payment_method, payment_proof, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $user_id,
                $service_id,
                $target_url,
                $quantity,
                $service['price'],
                $total_amount,
                $payment_method,
                $payment_proof
            ]);
            
            $order_id = $pdo->lastInsertId();
            
            setMessage('Commande créée avec succès ! Votre commande #' . $order_id . ' est en attente de validation.', 'success');
            redirect('order_details.php?id=' . $order_id);
            
        } catch (PDOException $e) {
            $errors[] = 'Erreur lors de la création de la commande. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Commande - SMM Pro Services</title>
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
                <li><a href="order.php" class="text-primary">Nouvelle commande</a></li>
                <li><a href="orders.php">Mes commandes</a></li>
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
                    <h1 class="text-primary">Nouvelle Commande</h1>
                    <p class="text-muted">Sélectionnez votre service et passez votre commande</p>
                </div>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Retour au dashboard
                </a>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Formulaire de commande -->
                <div class="col-8">
                    <div class="card">
                        <h3 class="card-title">
                            <i class="fas fa-shopping-cart"></i>
                            Détails de la commande
                        </h3>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="category_id" class="form-label">
                                    <i class="fas fa-list"></i>
                                    Catégorie de service
                                </label>
                                <select id="category_id" class="form-control" onchange="loadServicesByCategory(this.value)">
                                    <option value="">Sélectionner une catégorie...</option>
                                    <?php foreach ($categories as $cat_id => $category): ?>
                                        <option value="<?php echo $cat_id; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="service_id" class="form-label">
                                    <i class="fas fa-cog"></i>
                                    Service
                                </label>
                                <select id="service_id" name="service_id" class="form-control" required>
                                    <option value="">Sélectionner d'abord une catégorie...</option>
                                </select>
                                <small class="text-muted">Le prix sera affiché après sélection du service</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="target_url" class="form-label">
                                    <i class="fas fa-link"></i>
                                    Lien à promouvoir
                                </label>
                                <input 
                                    type="url" 
                                    id="target_url" 
                                    name="target_url" 
                                    class="form-control" 
                                    placeholder="https://instagram.com/p/votre-post"
                                    value="<?php echo isset($_POST['target_url']) ? htmlspecialchars($_POST['target_url']) : ''; ?>"
                                    data-url="true"
                                    required
                                >
                                <small class="text-muted">Copiez-collez le lien complet de votre post/profil</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="quantity" class="form-label">
                                    <i class="fas fa-hashtag"></i>
                                    Quantité
                                </label>
                                <input 
                                    type="number" 
                                    id="quantity" 
                                    name="quantity" 
                                    class="form-control" 
                                    min="1" 
                                    value="<?php echo isset($_POST['quantity']) ? (int)$_POST['quantity'] : 100; ?>"
                                    required
                                >
                                <small class="text-muted">Quantité souhaitée (les limites seront ajustées selon le service)</small>
                            </div>
                            
                            <!-- Calcul du montant -->
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-calculator"></i>
                                    Montant total
                                </label>
                                <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius); border: 2px solid var(--primary-color);">
                                    <div class="text-center">
                                        <h2 class="text-primary" id="total_amount">0 FCFA</h2>
                                        <small class="text-muted">Montant à payer</small>
                                    </div>
                                </div>
                                <input type="hidden" id="total_amount_hidden" name="total_amount">
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_method" class="form-label">
                                    <i class="fas fa-credit-card"></i>
                                    Moyen de paiement
                                </label>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="d-flex align-items-center p-3" style="border: 1px solid var(--border-color); border-radius: var(--border-radius); cursor: pointer;">
                                            <input type="radio" name="payment_method" value="mtn_money" required style="margin-right: 0.5rem;">
                                            <i class="fas fa-mobile-alt" style="color: #FFD700; margin-right: 0.5rem;"></i>
                                            <div>
                                                <strong>MTN Money</strong><br>
                                                <small class="text-muted"><?php echo $settings['mtn_money_number'] ?? '70 00 00 00'; ?></small>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="d-flex align-items-center p-3" style="border: 1px solid var(--border-color); border-radius: var(--border-radius); cursor: pointer;">
                                            <input type="radio" name="payment_method" value="moov_money" style="margin-right: 0.5rem;">
                                            <i class="fas fa-mobile-alt" style="color: #FF6B35; margin-right: 0.5rem;"></i>
                                            <div>
                                                <strong>Moov Money</strong><br>
                                                <small class="text-muted"><?php echo $settings['moov_money_number'] ?? '60 00 00 00'; ?></small>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_proof" class="form-label">
                                    <i class="fas fa-image"></i>
                                    Preuve de paiement *
                                </label>
                                <input 
                                    type="file" 
                                    id="payment_proof" 
                                    name="payment_proof" 
                                    class="form-control" 
                                    accept=".jpg,.jpeg,.png"
                                    required
                                >
                                <small class="text-muted">Upload une capture d'écran de votre reçu Mobile Money (JPG, PNG, max 5MB)</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-shopping-cart"></i>
                                Valider la commande
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Instructions de paiement -->
                <div class="col-4">
                    <div class="card">
                        <h3 class="card-title">
                            <i class="fas fa-info-circle"></i>
                            Instructions de paiement
                        </h3>
                        
                        <div style="background: var(--darker-bg); padding: 1rem; border-radius: var(--border-radius); margin-bottom: 1rem;">
                            <h4 class="text-primary mb-2">Étapes à suivre :</h4>
                            <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                <div class="d-flex align-items-start mb-2">
                                    <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">1</span>
                                    <span>Sélectionnez votre service et saisissez les détails</span>
                                </div>
                                <div class="d-flex align-items-start mb-2">
                                    <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">2</span>
                                    <span>Envoyez le montant exact via MTN ou Moov Money</span>
                                </div>
                                <div class="d-flex align-items-start mb-2">
                                    <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">3</span>
                                    <span>Prenez une capture d'écran du reçu</span>
                                </div>
                                <div class="d-flex align-items-start">
                                    <span style="background: var(--primary-color); color: var(--dark-bg); width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.8rem; margin-right: 0.5rem; flex-shrink: 0;">4</span>
                                    <span>Uploadez la preuve et validez votre commande</span>
                                </div>
                            </div>
                        </div>
                        
                        <div style="background: var(--success-color); background: rgba(40, 167, 69, 0.1); padding: 1rem; border-radius: var(--border-radius); margin-bottom: 1rem;">
                            <h4 style="color: var(--success-color); margin-bottom: 0.5rem;">
                                <i class="fas fa-shield-alt"></i>
                                Paiement sécurisé
                            </h4>
                            <p style="font-size: 0.9rem; color: var(--text-secondary); margin: 0;">
                                Tous vos paiements sont vérifiés manuellement pour votre sécurité. 
                                Votre commande sera traitée dès réception du paiement.
                            </p>
                        </div>
                        
                        <div style="background: var(--info-color); background: rgba(23, 162, 184, 0.1); padding: 1rem; border-radius: var(--border-radius);">
                            <h4 style="color: var(--info-color); margin-bottom: 0.5rem;">
                                <i class="fas fa-clock"></i>
                                Délais de traitement
                            </h4>
                            <ul style="font-size: 0.9rem; color: var(--text-secondary); margin: 0; padding-left: 1.2rem;">
                                <li>Validation du paiement : 1-6h</li>
                                <li>Démarrage des services : 24-48h</li>
                                <li>Livraison complète : 1-7 jours</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Services populaires -->
                    <div class="card mt-3">
                        <h3 class="card-title">
                            <i class="fas fa-fire"></i>
                            Services populaires
                        </h3>
                        
                        <?php 
                        // Récupérer quelques services populaires
                        $stmt = $pdo->query("
                            SELECT s.name, s.price, c.name as category_name, c.icon
                            FROM services s
                            JOIN categories c ON s.category_id = c.id
                            WHERE s.active = 1 AND c.active = 1
                            ORDER BY RAND()
                            LIMIT 4
                        ");
                        $popular_services = $stmt->fetchAll();
                        ?>
                        
                        <?php foreach ($popular_services as $service): ?>
                        <div class="d-flex align-items-center mb-2 p-2" style="background: var(--darker-bg); border-radius: 8px;">
                            <i class="<?php echo $service['icon']; ?>" style="color: var(--primary-color); margin-right: 0.5rem; width: 20px;"></i>
                            <div style="flex: 1;">
                                <strong style="font-size: 0.9rem;"><?php echo htmlspecialchars($service['name']); ?></strong><br>
                                <small class="text-muted"><?php echo htmlspecialchars($service['category_name']); ?></small>
                            </div>
                            <span class="text-primary" style="font-size: 0.9rem; font-weight: 600;">
                                <?php echo formatPrice($service['price']); ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="../assets/js/main.js"></script>
    
    <script>
        // Chargement des services par catégorie
        function loadServicesByCategory(categoryId) {
            const serviceSelect = document.getElementById('service_id');
            
            if (!categoryId) {
                serviceSelect.innerHTML = '<option value="">Sélectionner d\'abord une catégorie...</option>';
                return;
            }
            
            // Charger les services de la catégorie sélectionnée
            const categories = <?php echo json_encode($categories); ?>;
            const category = categories[categoryId];
            
            if (category && category.services) {
                serviceSelect.innerHTML = '<option value="">Sélectionner un service...</option>';
                
                category.services.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.name} - ${formatPrice(service.price)}`;
                    option.dataset.price = service.price;
                    option.dataset.minQty = service.min_quantity || 1;
                    option.dataset.maxQty = service.max_quantity || 10000;
                    serviceSelect.appendChild(option);
                });
            } else {
                serviceSelect.innerHTML = '<option value="">Aucun service disponible</option>';
            }
            
            // Recalculer le total
            calculateTotal();
        }
        
        // Formatage du prix (fonction dupliquée pour ce contexte)
        function formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(price) + ' FCFA';
        }
        
        // Validation de formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'danger');
            }
        });
        
        // Style pour les labels radio sélectionnés
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(r => {
                    r.closest('label').style.borderColor = 'var(--border-color)';
                });
                if (this.checked) {
                    this.closest('label').style.borderColor = 'var(--primary-color)';
                }
            });
        });
    </script>
</body>
</html>