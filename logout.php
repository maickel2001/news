<?php
/**
 * Page de Déconnexion - TarantulaSMM Bénin
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Définir l'accès autorisé
define('TARANTULA_ACCESS', true);

// Inclure les fichiers nécessaires
require_once 'config/database.php';
require_once 'includes/functions.php';

// Logger la déconnexion si l'utilisateur est connecté
if (isLoggedIn()) {
    $user = getCurrentUser();
    if ($user) {
        logActivity('user_logout', "Déconnexion utilisateur: {$user['email']}", [
            'user_id' => $user['id']
        ]);
    }
}

// Déconnecter l'utilisateur
logoutUser();

// Supprimer le cookie "Se souvenir de moi" s'il existe
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

// Rediriger vers la page d'accueil
redirect('/?logout=success');
?>