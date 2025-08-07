-- ========================================
-- TarantulaSMM Bénin - Schéma de Base de Données
-- Version: 1.0.0
-- Compatible: MySQL 5.7+
-- ========================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS tarantulasmm_benin 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE tarantulasmm_benin;

-- ========================================
-- TABLE: users (Utilisateurs)
-- ========================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    country VARCHAR(50) DEFAULT 'Bénin',
    balance DECIMAL(10,2) DEFAULT 0.00,
    total_spent DECIMAL(10,2) DEFAULT 0.00,
    total_orders INT DEFAULT 0,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    password_reset_token VARCHAR(255),
    password_reset_expires DATETIME,
    last_login DATETIME,
    last_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- ========================================
-- TABLE: user_sessions (Sessions utilisateurs)
-- ========================================
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL UNIQUE,
    ip_address VARCHAR(45),
    user_agent TEXT,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_token (session_token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- ========================================
-- TABLE: categories (Catégories de services)
-- ========================================
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) NOT NULL,
    description TEXT,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_status (status),
    INDEX idx_sort_order (sort_order)
);

-- ========================================
-- TABLE: services (Services SMM)
-- ========================================
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price_per_1000 DECIMAL(8,2) NOT NULL,
    min_quantity INT DEFAULT 100,
    max_quantity INT DEFAULT 100000,
    platform VARCHAR(50) NOT NULL,
    service_type ENUM('followers', 'likes', 'views', 'comments', 'shares', 'plays', 'reviews') NOT NULL,
    average_time VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category_id (category_id),
    INDEX idx_platform (platform),
    INDEX idx_status (status),
    INDEX idx_sort_order (sort_order)
);

-- ========================================
-- TABLE: orders (Commandes)
-- ========================================
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL UNIQUE,
    target_url VARCHAR(500) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(8,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_method ENUM('mtn_money', 'moov_money') NOT NULL,
    payment_phone VARCHAR(20) NOT NULL,
    payment_proof VARCHAR(255),
    payment_status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    admin_note TEXT,
    start_count INT DEFAULT 0,
    remains INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_service_id (service_id),
    INDEX idx_order_number (order_number),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
);

-- ========================================
-- TABLE: support_tickets (Tickets de support)
-- ========================================
CREATE TABLE support_tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ticket_number VARCHAR(20) NOT NULL UNIQUE,
    subject VARCHAR(255) NOT NULL,
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    status ENUM('open', 'in_progress', 'waiting_response', 'closed') DEFAULT 'open',
    last_reply_by ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_ticket_number (ticket_number),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
);

-- ========================================
-- TABLE: support_messages (Messages des tickets)
-- ========================================
CREATE TABLE support_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_id INT NOT NULL,
    user_id INT,
    admin_id INT,
    message TEXT NOT NULL,
    attachments JSON,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (ticket_id) REFERENCES support_tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_ticket_id (ticket_id),
    INDEX idx_created_at (created_at)
);

-- ========================================
-- TABLE: admin_users (Administrateurs)
-- ========================================
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('super_admin', 'admin', 'moderator') DEFAULT 'admin',
    permissions JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    last_login DATETIME,
    last_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
);

-- ========================================
-- TABLE: system_settings (Paramètres système)
-- ========================================
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_setting_key (setting_key)
);

-- ========================================
-- TABLE: activity_logs (Logs d'activité)
-- ========================================
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    admin_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    metadata JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- ========================================
-- DONNÉES INITIALES
-- ========================================

-- Paramètres système par défaut
INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'TarantulaSMM Bénin', 'string', 'Nom du site'),
('site_url', 'https://tarantulasmm.bj', 'string', 'URL du site'),
('contact_email', 'support@tarantulasmm.bj', 'string', 'Email de contact'),
('contact_phone', '+229 97 00 00 00', 'string', 'Téléphone de contact'),
('mtn_money_number', '97000000', 'string', 'Numéro MTN Money'),
('moov_money_number', '96000000', 'string', 'Numéro Moov Money'),
('maintenance_mode', '0', 'boolean', 'Mode maintenance'),
('registration_enabled', '1', 'boolean', 'Inscription autorisée'),
('email_verification_required', '1', 'boolean', 'Vérification email requise'),
('min_order_amount', '500', 'number', 'Montant minimum de commande (CFA)'),
('max_order_amount', '100000', 'number', 'Montant maximum de commande (CFA)');

-- Créer un admin par défaut (mot de passe: Admin123!)
INSERT INTO admin_users (username, email, password_hash, full_name, role, status) VALUES
('admin', 'admin@tarantulasmm.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur Principal', 'super_admin', 'active');

-- Catégories par défaut
INSERT INTO categories (name, icon, description, sort_order) VALUES
('Instagram', 'fab fa-instagram', 'Services de boost pour Instagram', 1),
('TikTok', 'fab fa-tiktok', 'Services de boost pour TikTok', 2),
('Facebook', 'fab fa-facebook-f', 'Services de boost pour Facebook', 3),
('YouTube', 'fab fa-youtube', 'Services de boost pour YouTube', 4),
('Spotify', 'fab fa-spotify', 'Services de boost pour Spotify', 5),
('Twitter', 'fab fa-twitter', 'Services de boost pour Twitter', 6);

-- Services exemple pour Instagram
INSERT INTO services (category_id, name, description, price_per_1000, min_quantity, max_quantity, platform, service_type, average_time) VALUES
(1, 'Followers Instagram [Qualité Premium]', 'Followers de haute qualité avec comptes réels', 2500.00, 100, 10000, 'Instagram', 'followers', '24-48 heures'),
(1, 'Likes Instagram [Rapide]', 'Likes Instagram livraison rapide', 800.00, 100, 50000, 'Instagram', 'likes', '1-6 heures'),
(1, 'Vues Stories Instagram', 'Vues pour vos stories Instagram', 500.00, 100, 100000, 'Instagram', 'views', '1-3 heures'),
(1, 'Commentaires Instagram [Français]', 'Commentaires en français par de vrais comptes', 15000.00, 10, 1000, 'Instagram', 'comments', '24-72 heures');

-- Services exemple pour TikTok
INSERT INTO services (category_id, name, description, price_per_1000, min_quantity, max_quantity, platform, service_type, average_time) VALUES
(2, 'Followers TikTok [Premium]', 'Followers TikTok de qualité premium', 3000.00, 100, 50000, 'TikTok', 'followers', '24-48 heures'),
(2, 'Likes TikTok [Super Rapide]', 'Likes TikTok livraison instantanée', 600.00, 100, 100000, 'TikTok', 'likes', '5-30 minutes'),
(2, 'Vues TikTok [Haute Qualité]', 'Vues TikTok avec rétention élevée', 400.00, 1000, 1000000, 'TikTok', 'views', '1-6 heures'),
(2, 'Partages TikTok', 'Partages pour vos vidéos TikTok', 2000.00, 50, 10000, 'TikTok', 'shares', '12-24 heures');

-- ========================================
-- VUES UTILES
-- ========================================

-- Vue pour les statistiques utilisateur
CREATE VIEW user_stats AS
SELECT 
    u.id,
    u.email,
    u.first_name,
    u.last_name,
    u.total_orders,
    u.total_spent,
    u.balance,
    COUNT(o.id) as pending_orders,
    u.created_at,
    u.last_login
FROM users u
LEFT JOIN orders o ON u.id = o.user_id AND o.status = 'pending'
GROUP BY u.id;

-- Vue pour les statistiques des commandes
CREATE VIEW order_stats AS
SELECT 
    DATE(created_at) as order_date,
    COUNT(*) as total_orders,
    SUM(price) as total_revenue,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders
FROM orders
GROUP BY DATE(created_at)
ORDER BY order_date DESC;

-- ========================================
-- PROCÉDURES STOCKÉES
-- ========================================

DELIMITER //

-- Procédure pour nettoyer les sessions expirées
CREATE PROCEDURE CleanExpiredSessions()
BEGIN
    DELETE FROM user_sessions WHERE expires_at < NOW();
END //

-- Procédure pour calculer le total dépensé d'un utilisateur
CREATE PROCEDURE UpdateUserTotalSpent(IN userId INT)
BEGIN
    UPDATE users 
    SET total_spent = (
        SELECT COALESCE(SUM(price), 0) 
        FROM orders 
        WHERE user_id = userId AND status = 'completed'
    ),
    total_orders = (
        SELECT COUNT(*) 
        FROM orders 
        WHERE user_id = userId AND status = 'completed'
    )
    WHERE id = userId;
END //

DELIMITER ;

-- ========================================
-- TRIGGERS
-- ========================================

-- Trigger pour générer automatiquement le numéro de commande
DELIMITER //
CREATE TRIGGER generate_order_number 
BEFORE INSERT ON orders
FOR EACH ROW
BEGIN
    IF NEW.order_number IS NULL OR NEW.order_number = '' THEN
        SET NEW.order_number = CONCAT('TSB', YEAR(NOW()), LPAD(FLOOR(RAND() * 999999), 6, '0'));
    END IF;
END //
DELIMITER ;

-- Trigger pour générer automatiquement le numéro de ticket
DELIMITER //
CREATE TRIGGER generate_ticket_number 
BEFORE INSERT ON support_tickets
FOR EACH ROW
BEGIN
    IF NEW.ticket_number IS NULL OR NEW.ticket_number = '' THEN
        SET NEW.ticket_number = CONCAT('TKT', LPAD(FLOOR(RAND() * 999999), 6, '0'));
    END IF;
END //
DELIMITER ;

-- ========================================
-- INDEX OPTIMISATIONS
-- ========================================

-- Index composites pour optimiser les requêtes courantes
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_orders_date_status ON orders(created_at, status);
CREATE INDEX idx_services_category_status ON services(category_id, status);
CREATE INDEX idx_users_email_status ON users(email, status);

-- ========================================
-- FINALISATION
-- ========================================

-- Mettre à jour les statistiques des tables
ANALYZE TABLE users, orders, services, categories, support_tickets;

-- Afficher un résumé de la création
SELECT 'Base de données TarantulaSMM Bénin créée avec succès!' as Status;