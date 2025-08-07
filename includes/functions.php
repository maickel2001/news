<?php
require_once 'config/database.php';

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Fonction pour vérifier si l'utilisateur est connecté
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Fonction pour vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Fonction pour rediriger
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fonction pour afficher les messages flash
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = $_SESSION['message_type'] ?? 'info';
        $message = $_SESSION['message'];
        echo "<div class='alert alert-{$type}'>{$message}</div>";
        unset($_SESSION['message'], $_SESSION['message_type']);
    }
}

// Fonction pour définir un message flash
function setMessage($message, $type = 'info') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
}

// Fonction pour formater les prix en FCFA
function formatPrice($price) {
    return number_format($price, 0, ',', ' ') . ' FCFA';
}

// Fonction pour upload de fichiers
function uploadFile($file, $destination_dir = 'uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
        return false;
    }
    
    if (!file_exists($destination_dir)) {
        mkdir($destination_dir, 0755, true);
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $destination = $destination_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return $filename;
    }
    
    return false;
}

// Fonction pour obtenir tous les services par catégorie
function getServicesByCategory() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("
        SELECT c.id as category_id, c.name as category_name, 
               s.id as service_id, s.name as service_name, s.price, s.description
        FROM categories c 
        LEFT JOIN services s ON c.id = s.category_id 
        WHERE c.active = 1 AND (s.active = 1 OR s.active IS NULL)
        ORDER BY c.order_position, s.name
    ");
    
    $result = [];
    while ($row = $stmt->fetch()) {
        if (!isset($result[$row['category_id']])) {
            $result[$row['category_id']] = [
                'name' => $row['category_name'],
                'services' => []
            ];
        }
        if ($row['service_id']) {
            $result[$row['category_id']]['services'][] = [
                'id' => $row['service_id'],
                'name' => $row['service_name'],
                'price' => $row['price'],
                'description' => $row['description']
            ];
        }
    }
    
    return $result;
}

// Fonction pour obtenir les statistiques admin
function getAdminStats() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Total des commandes
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $stmt->fetch()['total'];
    
    // Commandes en attente
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $stmt->fetch()['pending'];
    
    // Commandes en cours
    $stmt = $pdo->query("SELECT COUNT(*) as processing FROM orders WHERE status = 'processing'");
    $stats['processing_orders'] = $stmt->fetch()['processing'];
    
    // Commandes terminées
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM orders WHERE status = 'completed'");
    $stats['completed_orders'] = $stmt->fetch()['completed'];
    
    // Revenus totaux
    $stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'");
    $stats['total_revenue'] = $stmt->fetch()['revenue'] ?? 0;
    
    // Nombre de clients
    $stmt = $pdo->query("SELECT COUNT(*) as clients FROM users WHERE is_admin = 0");
    $stats['total_clients'] = $stmt->fetch()['clients'];
    
    return $stats;
}

// Fonction pour vérifier les permissions
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('index.php');
    }
}
?>