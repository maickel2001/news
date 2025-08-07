<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'smm_site');
define('DB_USER', 'root');
define('DB_PASS', '');

// Paramètres de sécurité
define('HASH_SALT', 'smm_site_2024_secure_salt');

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mettre à 1 en HTTPS

// Fonction de connexion à la base de données
function getDBConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

// Fonction pour échapper les données
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Fonction pour hasher les mots de passe
function hashPassword($password) {
    return password_hash($password . HASH_SALT, PASSWORD_DEFAULT);
}

// Fonction pour vérifier les mots de passe
function verifyPassword($password, $hash) {
    return password_verify($password . HASH_SALT, $hash);
}
?>