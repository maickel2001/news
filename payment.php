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

// Récupérer les numéros de paiement depuis les paramètres
$mtnNumber = getSiteSetting('mtn_money_number', '+226 XX XX XX XX');
$moovNumber = getSiteSetting('moov_money_number', '+226 XX XX XX XX');

// Traitement de l'upload de preuve de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validation CSRF
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Token de sécurité invalide';
    }
    
    $paymentMethod = sanitize($_POST['payment_method'] ?? '');
    
    if (empty($paymentMethod) || !in_array($paymentMethod, ['mtn_money', 'moov_money'])) {
        $errors[] = 'Veuillez sélectionner une méthode de paiement';
    }
    
    // Vérifier si un fichier a été uploadé
    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Veuillez télécharger une preuve de paiement';
    } else {
        $file = $_FILES['payment_proof'];
        
        // Vérifier les erreurs d'upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur lors du téléchargement du fichier';
        } else {
            // Vérifier la taille du fichier (5MB max)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                $errors[] = 'Le fichier est trop volumineux (maximum 5MB)';
            }
            
            // Vérifier le type de fichier
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $allowedTypes)) {
                $errors[] = 'Type de fichier non autorisé. Utilisez JPG ou PNG uniquement';
            }
        }
    }
    
    if (empty($errors)) {
        // Générer un nom unique pour le fichier
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'proof_' . $orderNumber . '_' . time() . '.' . $fileExtension;
        $filePath = 'uploads/' . $fileName;
        
        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Mettre à jour la commande
            $updateData = [
                'payment_proof' => $fileName,
                'payment_method' => $paymentMethod,
                'status' => 'processing'
            ];
            
            $updated = $db->update('orders', $updateData, 'order_number = :order_number', ['order_number' => $orderNumber]);
            
            if ($updated) {
                setFlashMessage('success', 'Preuve de paiement téléchargée avec succès! Votre commande sera traitée sous peu.');
                redirect('order-confirmation.php?order=' . $orderNumber);
            } else {
                $errors[] = 'Erreur lors de la mise à jour de la commande';
            }
        } else {
            $errors[] = 'Erreur lors du téléchargement du fichier';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - SMM Boost</title>
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
                <div class="col-md-10 mx-auto">
                    
                    <!-- Résumé de la commande -->
                    <div style="background: var(--card-bg); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color); margin-bottom: 2rem;">
                        <h2 style="text-align: center; margin-bottom: 1.5rem;">
                            <i class="fas fa-receipt"></i> Résumé de la commande
                        </h2>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                            <div>
                                <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Détails du service</h4>
                                <p><strong>Commande:</strong> #<?= htmlspecialchars($order['order_number']) ?></p>
                                <p><strong>Service:</strong> <?= htmlspecialchars($order['service_name']) ?></p>
                                <p><strong>Plateforme:</strong> <?= htmlspecialchars($order['category_name']) ?></p>
                                <p><strong>Quantité:</strong> <?= number_format($order['quantity']) ?></p>
                                <p><strong>URL:</strong> <a href="<?= htmlspecialchars($order['target_url']) ?>" target="_blank" style="color: var(--primary-color);"><?= htmlspecialchars(substr($order['target_url'], 0, 50)) ?>...</a></p>
                            </div>
                            <div>
                                <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Facturation</h4>
                                <p><strong>Prix unitaire:</strong> <?= formatPrice($order['price_per_unit']) ?></p>
                                <p><strong>Quantité:</strong> <?= number_format($order['quantity']) ?></p>
                                <hr style="border-color: var(--border-color); margin: 1rem 0;">
                                <p style="font-size: 1.2rem; color: var(--primary-color);"><strong>Total à payer: <?= formatPrice($order['total_amount']) ?></strong></p>
                            </div>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        
                        <!-- Instructions de paiement -->
                        <div style="background: var(--card-bg); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color);">
                            <h3 style="text-align: center; margin-bottom: 2rem;">
                                <i class="fas fa-mobile-alt"></i> Instructions de paiement
                            </h3>
                            
                            <div class="payment-methods">
                                <!-- MTN Money -->
                                <div class="payment-option" style="background: var(--dark-bg); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; border: 2px solid transparent; cursor: pointer;" onclick="selectPaymentMethod('mtn_money', this)">
                                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                                        <div style="width: 40px; height: 40px; background: #FFCC00; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                            <i class="fas fa-mobile-alt" style="color: #000;"></i>
                                        </div>
                                        <h4 style="margin: 0;">MTN Mobile Money</h4>
                                    </div>
                                    <p style="margin-bottom: 0.5rem;"><strong>Étapes:</strong></p>
                                    <ol style="margin: 0; padding-left: 1.5rem; color: var(--text-gray);">
                                        <li>Composez *133# sur votre téléphone</li>
                                        <li>Sélectionnez "Transfert d'argent"</li>
                                        <li>Envoyez <strong><?= formatPrice($order['total_amount']) ?></strong> au numéro:</li>
                                        <li style="font-size: 1.1rem; color: var(--primary-color); font-weight: bold;"><?= htmlspecialchars($mtnNumber) ?></li>
                                        <li>Confirmez la transaction avec votre code PIN</li>
                                    </ol>
                                </div>
                                
                                <!-- Moov Money -->
                                <div class="payment-option" style="background: var(--dark-bg); padding: 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; border: 2px solid transparent; cursor: pointer;" onclick="selectPaymentMethod('moov_money', this)">
                                    <div style="display: flex; align-items: center; margin-bottom: 1rem;">
                                        <div style="width: 40px; height: 40px; background: #0066CC; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem;">
                                            <i class="fas fa-mobile-alt" style="color: #fff;"></i>
                                        </div>
                                        <h4 style="margin: 0;">Moov Money</h4>
                                    </div>
                                    <p style="margin-bottom: 0.5rem;"><strong>Étapes:</strong></p>
                                    <ol style="margin: 0; padding-left: 1.5rem; color: var(--text-gray);">
                                        <li>Composez *555# sur votre téléphone</li>
                                        <li>Sélectionnez "Transfert"</li>
                                        <li>Envoyez <strong><?= formatPrice($order['total_amount']) ?></strong> au numéro:</li>
                                        <li style="font-size: 1.1rem; color: var(--primary-color); font-weight: bold;"><?= htmlspecialchars($moovNumber) ?></li>
                                        <li>Confirmez avec votre code secret</li>
                                    </ol>
                                </div>
                            </div>
                            
                            <div style="background: rgba(255, 193, 7, 0.1); border: 1px solid var(--warning-color); border-radius: 8px; padding: 1rem; margin-top: 1.5rem;">
                                <p style="margin: 0; color: var(--warning-color);">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Important:</strong> Gardez bien le SMS de confirmation de votre paiement, vous en aurez besoin pour télécharger la preuve.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Upload de preuve -->
                        <div style="background: var(--card-bg); padding: 2rem; border-radius: 15px; border: 1px solid var(--border-color);">
                            <h3 style="text-align: center; margin-bottom: 2rem;">
                                <i class="fas fa-upload"></i> Preuve de paiement
                            </h3>
                            
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
                            
                            <form method="POST" enctype="multipart/form-data" id="paymentForm">
                                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                                <input type="hidden" name="payment_method" id="selected_payment_method">
                                
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-credit-card"></i> Méthode de paiement sélectionnée
                                    </label>
                                    <div id="payment_method_display" style="padding: 1rem; background: var(--dark-bg); border-radius: 8px; color: var(--text-muted);">
                                        Sélectionnez une méthode de paiement ci-dessus
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="payment_proof">
                                        <i class="fas fa-camera"></i> Preuve de paiement (capture d'écran)
                                    </label>
                                    <div class="file-upload">
                                        <input type="file" name="payment_proof" id="payment_proof" accept="image/jpeg,image/jpg,image/png" required>
                                        <div class="file-upload-btn">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <div>Cliquez pour télécharger une image</div>
                                            <small>JPG, PNG (max 5MB)</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="background: rgba(40, 167, 69, 0.1); border: 1px solid var(--success-color); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                                    <p style="margin: 0; color: var(--success-color);">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Conseil:</strong> Prenez une capture d'écran claire du SMS de confirmation ou de l'historique de transaction de votre application mobile money.
                                    </p>
                                </div>
                                
                                <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;" disabled id="submitBtn">
                                    <i class="fas fa-check"></i> Confirmer le paiement
                                </button>
                            </form>
                            
                            <div style="text-align: center; margin-top: 1.5rem;">
                                <p style="color: var(--text-muted); font-size: 0.9rem;">
                                    Une fois votre preuve téléchargée, votre commande sera traitée dans les plus brefs délais.
                                </p>
                            </div>
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

    <script>
        let selectedPaymentMethod = null;
        
        function selectPaymentMethod(method, element) {
            // Réinitialiser toutes les options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.style.borderColor = 'transparent';
            });
            
            // Mettre en surbrillance l'option sélectionnée
            element.style.borderColor = 'var(--primary-color)';
            
            // Stocker la méthode sélectionnée
            selectedPaymentMethod = method;
            document.getElementById('selected_payment_method').value = method;
            
            // Mettre à jour l'affichage
            const methodNames = {
                'mtn_money': 'MTN Mobile Money',
                'moov_money': 'Moov Money'
            };
            
            document.getElementById('payment_method_display').innerHTML = `
                <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                ${methodNames[method]}
            `;
            
            // Activer le bouton de soumission si un fichier est également sélectionné
            updateSubmitButton();
        }
        
        function updateSubmitButton() {
            const fileInput = document.getElementById('payment_proof');
            const submitBtn = document.getElementById('submitBtn');
            
            if (selectedPaymentMethod && fileInput.files.length > 0) {
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
            } else {
                submitBtn.disabled = true;
                submitBtn.style.opacity = '0.6';
            }
        }
        
        // Gestion de l'upload de fichier
        document.getElementById('payment_proof').addEventListener('change', function(e) {
            updateSubmitButton();
            
            // Prévisualisation du fichier
            const file = e.target.files[0];
            if (file) {
                const fileUploadBtn = document.querySelector('.file-upload-btn');
                fileUploadBtn.innerHTML = `
                    <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                    <div>Fichier sélectionné: ${file.name}</div>
                    <small>${(file.size / 1024 / 1024).toFixed(2)} MB</small>
                `;
                fileUploadBtn.style.borderColor = 'var(--success-color)';
                fileUploadBtn.style.color = 'var(--success-color)';
            }
        });
        
        // Validation du formulaire
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            if (!selectedPaymentMethod) {
                e.preventDefault();
                alert('Veuillez sélectionner une méthode de paiement');
                return;
            }
            
            const fileInput = document.getElementById('payment_proof');
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Veuillez télécharger une preuve de paiement');
                return;
            }
            
            // Vérifier la taille du fichier (5MB max)
            const file = fileInput.files[0];
            if (file.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Le fichier est trop volumineux. Maximum 5MB autorisé.');
                return;
            }
        });
    </script>
    
    <script src="assets/js/script.js"></script>
</body>
</html>