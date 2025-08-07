<?php
/**
 * Configuration de Base de Données - TarantulaSMM Bénin
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
// CONFIGURATION ENVIRONNEMENT
// ========================================

// Détecter l'environnement (dev, staging, production)
$environment = $_SERVER['SERVER_NAME'] ?? 'localhost';

if (strpos($environment, 'localhost') !== false || strpos($environment, '127.0.0.1') !== false) {
    define('ENVIRONMENT', 'development');
} elseif (strpos($environment, 'staging') !== false || strpos($environment, 'test') !== false) {
    define('ENVIRONMENT', 'staging');
} else {
    define('ENVIRONMENT', 'production');
}

// ========================================
// PARAMÈTRES DE BASE DE DONNÉES
// ========================================

switch (ENVIRONMENT) {
    case 'development':
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'tarantulasmm_benin');
        define('DB_USER', 'root');
        define('DB_PASS', '');
        define('DB_PORT', 3306);
        define('DEBUG_MODE', true);
        break;
        
    case 'staging':
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'tarantulasmm_staging');
        define('DB_USER', 'staging_user');
        define('DB_PASS', 'staging_password_here');
        define('DB_PORT', 3306);
        define('DEBUG_MODE', true);
        break;
        
    case 'production':
        define('DB_HOST', 'localhost');
        define('DB_NAME', 'tarantulasmm_benin');
        define('DB_USER', 'tarantula_user');
        define('DB_PASS', 'production_password_here');
        define('DB_PORT', 3306);
        define('DEBUG_MODE', false);
        break;
}

// Caractères pour la base de données
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', 'utf8mb4_unicode_ci');

// ========================================
// CLASSE DE CONNEXION DATABASE
// ========================================

class Database 
{
    private static $instance = null;
    private $connection;
    private $host = DB_HOST;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $database = DB_NAME;
    private $charset = DB_CHARSET;
    
    /**
     * Constructeur privé pour Singleton
     */
    private function __construct() 
    {
        $this->connect();
    }
    
    /**
     * Empêcher le clonage
     */
    private function __clone() {}
    
    /**
     * Récupérer l'instance unique
     */
    public static function getInstance(): Database 
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Établir la connexion à la base de données
     */
    private function connect(): void 
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset};port=" . DB_PORT;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE " . DB_COLLATE,
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                PDO::ATTR_TIMEOUT => 30
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
            // Configuration SSL pour la production
            if (ENVIRONMENT === 'production') {
                $this->connection->exec("SET SESSION sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");
            }
            
        } catch (PDOException $e) {
            $this->handleConnectionError($e);
        }
    }
    
    /**
     * Récupérer la connexion PDO
     */
    public function getConnection(): PDO 
    {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }
    
    /**
     * Tester la connexion
     */
    public function testConnection(): bool 
    {
        try {
            $stmt = $this->connection->query('SELECT 1');
            return $stmt !== false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Récupérer les informations de la base de données
     */
    public function getInfo(): array 
    {
        try {
            $version = $this->connection->query('SELECT VERSION() as version')->fetch()['version'];
            $charset = $this->connection->query('SELECT @@character_set_database as charset')->fetch()['charset'];
            $collation = $this->connection->query('SELECT @@collation_database as collation')->fetch()['collation'];
            
            return [
                'host' => $this->host,
                'database' => $this->database,
                'version' => $version,
                'charset' => $charset,
                'collation' => $collation,
                'environment' => ENVIRONMENT
            ];
        } catch (PDOException $e) {
            return ['error' => 'Impossible de récupérer les informations'];
        }
    }
    
    /**
     * Exécuter une requête préparée
     */
    public function query(string $sql, array $params = []): PDOStatement 
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->handleQueryError($e, $sql, $params);
            throw $e;
        }
    }
    
    /**
     * Récupérer une seule ligne
     */
    public function fetch(string $sql, array $params = []): ?array 
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Récupérer toutes les lignes
     */
    public function fetchAll(string $sql, array $params = []): array 
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Insérer des données et retourner l'ID
     */
    public function insert(string $table, array $data): int 
    {
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);
        
        return (int) $this->connection->lastInsertId();
    }
    
    /**
     * Mettre à jour des données
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int 
    {
        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "{$key} = :{$key}";
        }
        $setClause = implode(', ', $setParts);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$where}";
        $params = array_merge($data, $whereParams);
        
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Supprimer des données
     */
    public function delete(string $table, string $where, array $params = []): int 
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Commencer une transaction
     */
    public function beginTransaction(): bool 
    {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Valider une transaction
     */
    public function commit(): bool 
    {
        return $this->connection->commit();
    }
    
    /**
     * Annuler une transaction
     */
    public function rollback(): bool 
    {
        return $this->connection->rollback();
    }
    
    /**
     * Vérifier si en transaction
     */
    public function inTransaction(): bool 
    {
        return $this->connection->inTransaction();
    }
    
    /**
     * Nettoyer les sessions expirées
     */
    public function cleanExpiredSessions(): int 
    {
        $sql = "DELETE FROM user_sessions WHERE expires_at < NOW()";
        $stmt = $this->query($sql);
        return $stmt->rowCount();
    }
    
    /**
     * Gestion des erreurs de connexion
     */
    private function handleConnectionError(PDOException $e): void 
    {
        $errorMessage = "Erreur de connexion à la base de données";
        
        if (DEBUG_MODE) {
            $errorMessage .= ": " . $e->getMessage();
        }
        
        // Log l'erreur
        error_log("DB Connection Error: " . $e->getMessage());
        
        // Afficher une page d'erreur conviviale
        http_response_code(503);
        include_once __DIR__ . '/../pages/errors/database_error.php';
        exit;
    }
    
    /**
     * Gestion des erreurs de requête
     */
    private function handleQueryError(PDOException $e, string $sql, array $params): void 
    {
        $errorInfo = [
            'message' => $e->getMessage(),
            'sql' => $sql,
            'params' => $params,
            'trace' => $e->getTraceAsString(),
            'time' => date('Y-m-d H:i:s'),
            'environment' => ENVIRONMENT
        ];
        
        // Log l'erreur
        error_log("DB Query Error: " . json_encode($errorInfo));
        
        // En développement, afficher plus de détails
        if (DEBUG_MODE) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;'>";
            echo "<h4>Erreur de Base de Données</h4>";
            echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>SQL:</strong> " . htmlspecialchars($sql) . "</p>";
            echo "<p><strong>Paramètres:</strong> " . htmlspecialchars(json_encode($params)) . "</p>";
            echo "</div>";
        }
    }
    
    /**
     * Fermer la connexion
     */
    public function close(): void 
    {
        $this->connection = null;
    }
    
    /**
     * Destructeur
     */
    public function __destruct() 
    {
        $this->close();
    }
}

// ========================================
// FONCTIONS UTILITAIRES
// ========================================

/**
 * Récupérer l'instance de base de données
 */
function getDB(): Database 
{
    return Database::getInstance();
}

/**
 * Exécuter une requête simple
 */
function dbQuery(string $sql, array $params = []): PDOStatement 
{
    return getDB()->query($sql, $params);
}

/**
 * Récupérer une ligne
 */
function dbFetch(string $sql, array $params = []): ?array 
{
    return getDB()->fetch($sql, $params);
}

/**
 * Récupérer toutes les lignes
 */
function dbFetchAll(string $sql, array $params = []): array 
{
    return getDB()->fetchAll($sql, $params);
}

/**
 * Insérer des données
 */
function dbInsert(string $table, array $data): int 
{
    return getDB()->insert($table, $data);
}

/**
 * Mettre à jour des données
 */
function dbUpdate(string $table, array $data, string $where, array $whereParams = []): int 
{
    return getDB()->update($table, $data, $where, $whereParams);
}

/**
 * Supprimer des données
 */
function dbDelete(string $table, string $where, array $params = []): int 
{
    return getDB()->delete($table, $where, $params);
}

// ========================================
// INITIALISATION AUTOMATIQUE
// ========================================

// Nettoyer les sessions expirées automatiquement (1% de chance à chaque chargement)
if (mt_rand(1, 100) === 1) {
    try {
        getDB()->cleanExpiredSessions();
    } catch (Exception $e) {
        // Ignorer silencieusement les erreurs de nettoyage
    }
}