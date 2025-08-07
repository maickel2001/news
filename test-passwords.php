<?php
/**
 * Test des mots de passe - TarantulaSMM Bénin
 */

// Définir l'accès autorisé
define('TARANTULA_ACCESS', true);

// Inclure les fichiers nécessaires
require_once 'config/database.php';
require_once 'includes/functions.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Mots de Passe - TarantulaSMM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Test des Mots de Passe</h2>
        
        <?php
        try {
            $db = Database::getInstance();
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'><h5>Admins dans admin_users</h5></div>";
            echo "<div class='card-body'>";
            
            $admins = $db->query("SELECT id, username, email, password_hash FROM admin_users")->fetchAll();
            
            if (empty($admins)) {
                echo "<p class='text-warning'>Aucun admin trouvé!</p>";
            } else {
                foreach ($admins as $admin) {
                    echo "<div class='mb-2'>";
                    echo "<strong>Admin #{$admin['id']}</strong><br>";
                    echo "Username: {$admin['username']}<br>";
                    echo "Email: {$admin['email']}<br>";
                    echo "Hash: " . substr($admin['password_hash'], 0, 50) . "...<br>";
                    
                    // Test du mot de passe
                    $testPassword = 'Admin123!';
                    $verified = password_verify($testPassword, $admin['password_hash']);
                    echo "Test mot de passe 'Admin123!': " . ($verified ? "<span class='text-success'>✅ CORRECT</span>" : "<span class='text-danger'>❌ INCORRECT</span>");
                    echo "</div><hr>";
                }
            }
            
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'><h5>Utilisateurs dans users</h5></div>";
            echo "<div class='card-body'>";
            
            $users = $db->query("SELECT id, email, first_name, last_name, password_hash FROM users LIMIT 5")->fetchAll();
            
            if (empty($users)) {
                echo "<p class='text-warning'>Aucun utilisateur trouvé!</p>";
            } else {
                foreach ($users as $user) {
                    echo "<div class='mb-2'>";
                    echo "<strong>User #{$user['id']}</strong><br>";
                    echo "Email: {$user['email']}<br>";
                    echo "Nom: {$user['first_name']} {$user['last_name']}<br>";
                    echo "Hash: " . substr($user['password_hash'], 0, 50) . "...<br>";
                    
                    // Test du mot de passe
                    $testPassword = 'Demo123!';
                    $verified = password_verify($testPassword, $user['password_hash']);
                    echo "Test mot de passe 'Demo123!': " . ($verified ? "<span class='text-success'>✅ CORRECT</span>" : "<span class='text-danger'>❌ INCORRECT</span>");
                    echo "</div><hr>";
                }
            }
            
            echo "</div></div>";
            
            // Test de création de hash
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'><h5>Test de création de hash</h5></div>";
            echo "<div class='card-body'>";
            
            $testHash1 = password_hash('Admin123!', PASSWORD_ARGON2ID);
            $testHash2 = password_hash('Demo123!', PASSWORD_ARGON2ID);
            
            echo "<p><strong>Hash pour 'Admin123!' :</strong><br><code>{$testHash1}</code></p>";
            echo "<p><strong>Vérification :</strong> " . (password_verify('Admin123!', $testHash1) ? "✅" : "❌") . "</p>";
            
            echo "<p><strong>Hash pour 'Demo123!' :</strong><br><code>{$testHash2}</code></p>";
            echo "<p><strong>Vérification :</strong> " . (password_verify('Demo123!', $testHash2) ? "✅" : "❌") . "</p>";
            
            echo "</div></div>";
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>Erreur: " . $e->getMessage() . "</div>";
        }
        ?>
        
        <a href="/" class="btn btn-primary">Retour à l'accueil</a>
        <a href="login.php" class="btn btn-success">Tester la connexion</a>
    </div>
</body>
</html>