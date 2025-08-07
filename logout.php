<?php
require_once 'includes/functions.php';

// Détruire la session
session_destroy();

// Supprimer tous les cookies de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// Message de confirmation
session_start();
setMessage('Vous avez été déconnecté avec succès.', 'success');

// Redirection vers la page d'accueil
redirect('index.php');
?>