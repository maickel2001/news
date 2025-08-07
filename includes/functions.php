<?php
/**
 * Fonctions Utilitaires - TarantulaSMM Bénin
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Empêcher l'accès direct
if (!defined('TARANTULA_ACCESS')) {
    exit('Accès direct non autorisé');
}

// ========================================
// GESTION DES SESSIONS
// ========================================

/**
 * Démarrer une session sécurisée
 */
function startSecureSession(): void 
{
    if (session_status() === PHP_SESSION_NONE) {
        // Configuration sécurisée des sessions
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        session_name('TARANTULA_SESSION');
        session_start();
        
        // Régénérer l'ID de session périodiquement
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 3600) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Détruire une session complètement
 */
function destroySession(): void 
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
}

/**
 * Vérifier si l'utilisateur est connecté
 */
function isLoggedIn(): bool 
{
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Vérifier si l'utilisateur est admin
 */
function isAdmin(): bool 
{
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_role']);
}

/**
 * Obtenir l'utilisateur actuel
 */
function getCurrentUser(): ?array 
{
    if (!isLoggedIn()) {
        return null;
    }
    
    $user = dbFetch("SELECT * FROM users WHERE id = ? AND status = 'active'", [$_SESSION['user_id']]);
    return $user ?: null;
}

/**
 * Connecter un utilisateur
 */
function loginUser(array $user): void 
{
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['login_time'] = time();
    
    // Mettre à jour la dernière connexion
    dbUpdate('users', [
        'last_login' => date('Y-m-d H:i:s'),
        'last_ip' => getClientIP()
    ], 'id = ?', [$user['id']]);
    
    // Créer une session en base
    createUserSession($user['id']);
}

/**
 * Déconnecter un utilisateur
 */
function logoutUser(): void 
{
    if (isLoggedIn()) {
        // Supprimer la session de la base
        if (isset($_SESSION['session_token'])) {
            dbDelete('user_sessions', 'session_token = ?', [$_SESSION['session_token']]);
        }
    }
    
    destroySession();
}

/**
 * Créer une session en base de données
 */
function createUserSession(int $userId): string 
{
    $token = generateSecureToken();
    $expiresAt = date('Y-m-d H:i:s', time() + (7 * 24 * 3600)); // 7 jours
    
    dbInsert('user_sessions', [
        'user_id' => $userId,
        'session_token' => $token,
        'ip_address' => getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'expires_at' => $expiresAt
    ]);
    
    $_SESSION['session_token'] = $token;
    return $token;
}

// ========================================
// SÉCURITÉ ET VALIDATION
// ========================================

/**
 * Générer un token sécurisé
 */
function generateSecureToken(int $length = 32): string 
{
    return bin2hex(random_bytes($length));
}

/**
 * Générer un hash de mot de passe
 */
function hashPassword(string $password): string 
{
    return password_hash($password, PASSWORD_ARGON2ID, [
        'memory_cost' => 65536,
        'time_cost' => 4,
        'threads' => 3
    ]);
}

/**
 * Vérifier un mot de passe
 */
function verifyPassword(string $password, string $hash): bool 
{
    return password_verify($password, $hash);
}

/**
 * Valider un email
 */
function isValidEmail(string $email): bool 
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valider un mot de passe (critères sécurisés)
 */
function isValidPassword(string $password): bool 
{
    // Au moins 8 caractères, une majuscule, une minuscule, un chiffre
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password);
}

/**
 * Valider un numéro de téléphone béninois
 */
function isValidBeninPhone(string $phone): bool 
{
    // Format béninois: +229XXXXXXXX ou XXXXXXXX
    $cleaned = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^(229)?[5-9][0-9]{7}$/', $cleaned);
}

/**
 * Nettoyer et sécuriser une chaîne
 */
function sanitizeString(string $input): string 
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Générer un token CSRF
 */
function generateCSRFToken(): string 
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = generateSecureToken();
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifier un token CSRF
 */
function verifyCSRFToken(string $token): bool 
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Protection contre le brute force
 */
function checkBruteForce(string $identifier, int $maxAttempts = 5, int $timeWindow = 900): bool 
{
    $attempts = dbFetch(
        "SELECT COUNT(*) as count FROM activity_logs 
         WHERE action = 'login_failed' 
         AND (description LIKE ? OR ip_address = ?) 
         AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)",
        ["%{$identifier}%", getClientIP(), $timeWindow]
    );
    
    return ($attempts['count'] ?? 0) < $maxAttempts;
}

/**
 * Enregistrer une tentative de connexion échouée
 */
function logFailedLogin(string $identifier): void 
{
    dbInsert('activity_logs', [
        'action' => 'login_failed',
        'description' => "Tentative de connexion échouée pour: {$identifier}",
        'ip_address' => getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'metadata' => json_encode(['identifier' => $identifier, 'timestamp' => time()])
    ]);
}

// ========================================
// UTILITAIRES
// ========================================

/**
 * Obtenir l'adresse IP du client
 */
function getClientIP(): string 
{
    $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
               'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 
               'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            $ip = $_SERVER[$key];
            if (strpos($ip, ',') !== false) {
                $ip = explode(',', $ip)[0];
            }
            $ip = trim($ip);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Rediriger vers une page
 */
function redirect(string $url, int $statusCode = 302): void 
{
    header("Location: {$url}", true, $statusCode);
    exit;
}

/**
 * Obtenir l'URL de base du site
 */
function getBaseURL(): string 
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname($_SERVER['SCRIPT_NAME']);
    
    return $protocol . '://' . $host . ($script === '/' ? '' : $script);
}

/**
 * Formater un prix en CFA
 */
function formatPrice(float $amount): string 
{
    return number_format($amount, 0, ',', ' ') . ' CFA';
}

/**
 * Formater une date
 */
function formatDate(string $date, string $format = 'd/m/Y à H:i'): string 
{
    return date($format, strtotime($date));
}

/**
 * Calculer le temps écoulé
 */
function timeAgo(string $datetime): string 
{
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'À l\'instant';
    if ($time < 3600) return floor($time/60) . ' min';
    if ($time < 86400) return floor($time/3600) . ' h';
    if ($time < 2592000) return floor($time/86400) . ' j';
    if ($time < 31536000) return floor($time/2592000) . ' mois';
    
    return floor($time/31536000) . ' an' . (floor($time/31536000) > 1 ? 's' : '');
}

/**
 * Envoyer un email
 */
function sendEmail(string $to, string $subject, string $message, array $options = []): bool 
{
    // Headers de base
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: ' . ($options['from'] ?? 'TarantulaSMM Bénin <noreply@tarantulasmm.bj>'),
        'Reply-To: ' . ($options['reply_to'] ?? 'support@tarantulasmm.bj'),
        'X-Mailer: TarantulaSMM v1.0',
        'X-Priority: ' . ($options['priority'] ?? '3')
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Logger une activité
 */
function logActivity(string $action, string $description, array $metadata = []): void 
{
    $data = [
        'action' => $action,
        'description' => $description,
        'ip_address' => getClientIP(),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'metadata' => json_encode($metadata)
    ];
    
    if (isLoggedIn()) {
        $data['user_id'] = $_SESSION['user_id'];
    }
    
    if (isAdmin()) {
        $data['admin_id'] = $_SESSION['admin_id'];
    }
    
    dbInsert('activity_logs', $data);
}

/**
 * Obtenir les paramètres système
 */
function getSetting(string $key, $default = null) 
{
    static $settings = null;
    
    if ($settings === null) {
        $results = dbFetchAll("SELECT setting_key, setting_value, setting_type FROM system_settings");
        $settings = [];
        
        foreach ($results as $setting) {
            $value = $setting['setting_value'];
            
            // Convertir selon le type
            switch ($setting['setting_type']) {
                case 'boolean':
                    $value = (bool) $value;
                    break;
                case 'number':
                    $value = is_numeric($value) ? (float) $value : 0;
                    break;
                case 'json':
                    $value = json_decode($value, true) ?: [];
                    break;
            }
            
            $settings[$setting['setting_key']] = $value;
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * Mettre à jour un paramètre système
 */
function updateSetting(string $key, $value, string $type = 'string'): bool 
{
    // Convertir la valeur selon le type
    switch ($type) {
        case 'boolean':
            $value = $value ? '1' : '0';
            break;
        case 'json':
            $value = json_encode($value);
            break;
        default:
            $value = (string) $value;
    }
    
    $exists = dbFetch("SELECT id FROM system_settings WHERE setting_key = ?", [$key]);
    
    if ($exists) {
        return dbUpdate('system_settings', [
            'setting_value' => $value,
            'setting_type' => $type
        ], 'setting_key = ?', [$key]) > 0;
    } else {
        return dbInsert('system_settings', [
            'setting_key' => $key,
            'setting_value' => $value,
            'setting_type' => $type
        ]) > 0;
    }
}

// ========================================
// VALIDATION DES FORMULAIRES
// ========================================

/**
 * Valider les données d'inscription
 */
function validateRegistrationData(array $data): array 
{
    $errors = [];
    
    // Email
    if (empty($data['email'])) {
        $errors['email'] = 'L\'email est requis';
    } elseif (!isValidEmail($data['email'])) {
        $errors['email'] = 'L\'email n\'est pas valide';
    } elseif (dbFetch("SELECT id FROM users WHERE email = ?", [$data['email']])) {
        $errors['email'] = 'Cet email est déjà utilisé';
    }
    
    // Mot de passe
    if (empty($data['password'])) {
        $errors['password'] = 'Le mot de passe est requis';
    } elseif (!isValidPassword($data['password'])) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre';
    }
    
    // Confirmation mot de passe
    if (empty($data['password_confirm'])) {
        $errors['password_confirm'] = 'La confirmation du mot de passe est requise';
    } elseif ($data['password'] !== $data['password_confirm']) {
        $errors['password_confirm'] = 'Les mots de passe ne correspondent pas';
    }
    
    // Prénom
    if (empty($data['first_name'])) {
        $errors['first_name'] = 'Le prénom est requis';
    } elseif (strlen($data['first_name']) < 2) {
        $errors['first_name'] = 'Le prénom doit contenir au moins 2 caractères';
    }
    
    // Nom
    if (empty($data['last_name'])) {
        $errors['last_name'] = 'Le nom est requis';
    } elseif (strlen($data['last_name']) < 2) {
        $errors['last_name'] = 'Le nom doit contenir au moins 2 caractères';
    }
    
    // Téléphone (optionnel mais doit être valide si fourni)
    if (!empty($data['phone']) && !isValidBeninPhone($data['phone'])) {
        $errors['phone'] = 'Le numéro de téléphone n\'est pas valide';
    }
    
    return $errors;
}

/**
 * Valider les données de connexion
 */
function validateLoginData(array $data): array 
{
    $errors = [];
    
    if (empty($data['email'])) {
        $errors['email'] = 'L\'email est requis';
    } elseif (!isValidEmail($data['email'])) {
        $errors['email'] = 'L\'email n\'est pas valide';
    }
    
    if (empty($data['password'])) {
        $errors['password'] = 'Le mot de passe est requis';
    }
    
    return $errors;
}

// ========================================
// PROTECTION CONTRE LES ATTAQUES
// ========================================

/**
 * Nettoyer les données POST/GET
 */
function sanitizeInput(array $data): array 
{
    $cleaned = [];
    
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $cleaned[$key] = sanitizeInput($value);
        } else {
            $cleaned[$key] = sanitizeString($value);
        }
    }
    
    return $cleaned;
}

/**
 * Rate limiting simple
 */
function checkRateLimit(string $action, int $maxRequests = 10, int $timeWindow = 60): bool 
{
    $ip = getClientIP();
    $key = "rate_limit_{$action}_{$ip}";
    
    if (!isset($_SESSION[$key])) {
        $_SESSION[$key] = ['count' => 0, 'timestamp' => time()];
    }
    
    $rateData = $_SESSION[$key];
    
    // Réinitialiser si la fenêtre de temps est dépassée
    if (time() - $rateData['timestamp'] > $timeWindow) {
        $_SESSION[$key] = ['count' => 1, 'timestamp' => time()];
        return true;
    }
    
    // Vérifier la limite
    if ($rateData['count'] >= $maxRequests) {
        return false;
    }
    
    // Incrémenter le compteur
    $_SESSION[$key]['count']++;
    return true;
}

// ========================================
// INITIALISATION AUTOMATIQUE
// ========================================

// Démarrer automatiquement la session sécurisée
startSecureSession();