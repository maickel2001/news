<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Si vous voulez détruire complètement la session, supprimez aussi le cookie de session
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// Détruire la session
session_destroy();

// Redirection vers la page de connexion
header('Location: login.php');
exit();
?>