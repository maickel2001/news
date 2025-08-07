<?php
// Fichier de protection pour les pages admin
session_start();
require_once '../config/database.php';

// Vérifier si l'utilisateur est connecté en tant qu'admin
if (!isAdmin()) {
    redirect('login.php');
}

// Fonctions spécifiques à l'admin
function requireAdminRole($requiredRole = 'admin') {
    if ($_SESSION['admin_role'] !== $requiredRole && $_SESSION['admin_role'] !== 'admin') {
        die('Accès refusé. Permissions insuffisantes.');
    }
}

function getAdminName() {
    return $_SESSION['admin_name'] ?? 'Admin';
}

function getAdminRole() {
    return $_SESSION['admin_role'] ?? 'admin';
}
?>