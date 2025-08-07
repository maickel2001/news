<?php
// Configuration de la base de données
class Database {
    private $host = 'localhost';
    private $dbname = 'smm_boost';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    private $pdo;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données: ' . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->pdo;
    }
    
    // Méthode pour exécuter une requête SELECT
    public function query($sql, $params = []) {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('Erreur SQL: ' . $e->getMessage());
            return false;
        }
    }
    
    // Méthode pour insérer des données
    public function insert($table, $data) {
        try {
            $columns = implode(',', array_keys($data));
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            $stmt->execute();
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log('Erreur INSERT: ' . $e->getMessage());
            return false;
        }
    }
    
    // Méthode pour mettre à jour des données
    public function update($table, $data, $where, $whereParams = []) {
        try {
            $setParts = [];
            foreach ($data as $key => $value) {
                $setParts[] = "{$key} = :{$key}";
            }
            $setClause = implode(', ', $setParts);
            
            $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
            $stmt = $this->pdo->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            foreach ($whereParams as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Erreur UPDATE: ' . $e->getMessage());
            return false;
        }
    }
    
    // Méthode pour supprimer des données
    public function delete($table, $where, $params = []) {
        try {
            $sql = "DELETE FROM {$table} WHERE {$where}";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log('Erreur DELETE: ' . $e->getMessage());
            return false;
        }
    }
    
    // Méthode pour commencer une transaction
    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }
    
    // Méthode pour valider une transaction
    public function commit() {
        return $this->pdo->commit();
    }
    
    // Méthode pour annuler une transaction
    public function rollback() {
        return $this->pdo->rollback();
    }
}

// Instance globale de la base de données
try {
    $db = new Database();
    $pdo = $db->getConnection();
} catch (Exception $e) {
    die('Impossible de se connecter à la base de données. Veuillez vérifier la configuration.');
}

// Fonctions utilitaires

// Sécuriser les entrées utilisateur
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Valider une adresse email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Valider une URL
function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// Générer un token CSRF
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Vérifier un token CSRF
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Formater le prix en FCFA
function formatPrice($amount) {
    return number_format($amount, 0, ',', ' ') . ' FCFA';
}

// Générer un slug unique pour les fichiers
function generateUniqueSlug($originalName) {
    $pathinfo = pathinfo($originalName);
    $extension = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
    return uniqid() . '_' . time() . $extension;
}

// Vérifier si l'utilisateur est admin
function isAdmin() {
    return isset($_SESSION['admin_id']) && $_SESSION['admin_logged_in'] === true;
}

// Redirection sécurisée
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Afficher un message flash
function setFlashMessage($type, $message) {
    $_SESSION['flash'][$type][] = $message;
}

// Récupérer et supprimer les messages flash
function getFlashMessages($type = null) {
    if ($type) {
        $messages = isset($_SESSION['flash'][$type]) ? $_SESSION['flash'][$type] : [];
        unset($_SESSION['flash'][$type]);
        return $messages;
    } else {
        $messages = isset($_SESSION['flash']) ? $_SESSION['flash'] : [];
        unset($_SESSION['flash']);
        return $messages;
    }
}

// Loguer les erreurs
function logError($message, $file = null, $line = null) {
    $logMessage = date('Y-m-d H:i:s') . ' - ' . $message;
    if ($file) {
        $logMessage .= ' in ' . $file;
    }
    if ($line) {
        $logMessage .= ' on line ' . $line;
    }
    error_log($logMessage . PHP_EOL, 3, 'logs/error.log');
}

// Fonction pour obtenir les paramètres du site
function getSiteSetting($key, $default = '') {
    global $db;
    $stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
    $result = $stmt->fetch();
    return $result ? $result['setting_value'] : $default;
}

// Fonction pour mettre à jour un paramètre du site
function updateSiteSetting($key, $value) {
    global $db;
    return $db->update('settings', ['setting_value' => $value], 'setting_key = :key', ['key' => $key]);
}
?>