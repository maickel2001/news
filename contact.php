<?php
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $subject = sanitizeInput($_POST['subject']);
    $message = sanitizeInput($_POST['message']);
    
    // Validation simple
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        setMessage('Veuillez remplir tous les champs.', 'danger');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setMessage('Adresse email invalide.', 'danger');
    } else {
        // Ici vous pourriez envoyer un email à l'admin
        // ou enregistrer le message dans la base de données
        
        // Pour cet exemple, on affiche juste un message de succès
        setMessage('Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.', 'success');
    }
} else {
    setMessage('Méthode non autorisée.', 'danger');
}

// Redirection vers la page d'accueil
redirect('index.php#contact');
?>