-- SMM Boost Database Installation Script
-- Créé pour un site de vente de services SMM

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Base de données: `smm_boost`
CREATE DATABASE IF NOT EXISTS `smm_boost` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smm_boost`;

-- --------------------------------------------------------

-- Structure de la table `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données pour la table `categories`
INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Instagram', 'Services pour Instagram', 'fab fa-instagram', 'active', NOW(), NOW()),
(2, 'TikTok', 'Services pour TikTok', 'fab fa-tiktok', 'active', NOW(), NOW()),
(3, 'YouTube', 'Services pour YouTube', 'fab fa-youtube', 'active', NOW(), NOW()),
(4, 'Facebook', 'Services pour Facebook', 'fab fa-facebook', 'active', NOW(), NOW());

-- --------------------------------------------------------

-- Structure de la table `services`
CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `min_quantity` int(11) DEFAULT 10,
  `max_quantity` int(11) DEFAULT 100000,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `fk_services_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Données pour la table `services`
INSERT INTO `services` (`id`, `category_id`, `name`, `description`, `price`, `min_quantity`, `max_quantity`, `status`, `created_at`, `updated_at`) VALUES
-- Instagram Services
(1, 1, 'Followers Instagram', 'Followers Instagram de qualité', 10.00, 50, 50000, 'active', NOW(), NOW()),
(2, 1, 'Likes Instagram', 'Likes pour vos publications Instagram', 5.00, 10, 10000, 'active', NOW(), NOW()),
(3, 1, 'Vues Stories Instagram', 'Vues pour vos stories Instagram', 3.00, 100, 20000, 'active', NOW(), NOW()),
(4, 1, 'Commentaires Instagram', 'Commentaires authentiques pour Instagram', 15.00, 5, 1000, 'active', NOW(), NOW()),

-- TikTok Services
(5, 2, 'Followers TikTok', 'Followers TikTok authentiques', 15.00, 50, 30000, 'active', NOW(), NOW()),
(6, 2, 'Likes TikTok', 'Likes pour vos vidéos TikTok', 8.00, 10, 20000, 'active', NOW(), NOW()),
(7, 2, 'Vues TikTok', 'Vues pour vos vidéos TikTok', 4.00, 100, 100000, 'active', NOW(), NOW()),
(8, 2, 'Partages TikTok', 'Partages pour vos vidéos TikTok', 12.00, 5, 5000, 'active', NOW(), NOW()),

-- YouTube Services
(9, 3, 'Abonnés YouTube', 'Abonnés YouTube de qualité', 20.00, 25, 25000, 'active', NOW(), NOW()),
(10, 3, 'Vues YouTube', 'Vues pour vos vidéos YouTube', 12.00, 100, 500000, 'active', NOW(), NOW()),
(11, 3, 'Likes YouTube', 'Likes pour vos vidéos YouTube', 8.00, 10, 10000, 'active', NOW(), NOW()),
(12, 3, 'Commentaires YouTube', 'Commentaires authentiques YouTube', 25.00, 3, 500, 'active', NOW(), NOW()),

-- Facebook Services
(13, 4, 'Likes Pages Facebook', 'Likes pour vos pages Facebook', 8.00, 25, 15000, 'active', NOW(), NOW()),
(14, 4, 'Followers Facebook', 'Followers pour vos profils Facebook', 10.00, 50, 20000, 'active', NOW(), NOW()),
(15, 4, 'Likes Publications Facebook', 'Likes pour vos publications Facebook', 6.00, 10, 8000, 'active', NOW(), NOW()),
(16, 4, 'Partages Facebook', 'Partages pour vos publications Facebook', 15.00, 5, 3000, 'active', NOW(), NOW());

-- --------------------------------------------------------

-- Structure de la table `admin_users`
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('admin','moderator') DEFAULT 'admin',
  `status` enum('active','inactive') DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO `admin_users` (`id`, `email`, `password`, `name`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin@smmboost.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'admin', 'active', NOW(), NOW());

-- --------------------------------------------------------

-- Structure de la table `orders`
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(20) NOT NULL,
  `service_id` int(11) NOT NULL,
  `target_url` varchar(500) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `customer_email` varchar(100) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `payment_method` enum('mtn_money','moov_money') DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `cancel_reason` text DEFAULT NULL,
  `start_count` int(11) DEFAULT NULL,
  `remains` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `service_id` (`service_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `fk_orders_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Structure de la table `order_status_history`
CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `old_status` varchar(20) DEFAULT NULL,
  `new_status` varchar(20) NOT NULL,
  `comment` text DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `changed_by` (`changed_by`),
  CONSTRAINT `fk_order_history_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_history_admin` FOREIGN KEY (`changed_by`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

-- Structure de la table `settings`
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text NOT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Configuration par défaut
INSERT INTO `settings` (`setting_key`, `setting_value`, `description`) VALUES
('site_name', 'SMM Boost', 'Nom du site'),
('site_email', 'contact@smmboost.com', 'Email de contact du site'),
('site_phone', '+226 XX XX XX XX', 'Numéro de téléphone'),
('mtn_money_number', '+226 XX XX XX XX', 'Numéro MTN Money pour les paiements'),
('moov_money_number', '+226 XX XX XX XX', 'Numéro Moov Money pour les paiements'),
('currency', 'FCFA', 'Devise utilisée'),
('maintenance_mode', '0', 'Mode maintenance (0=désactivé, 1=activé)'),
('max_file_size', '5242880', 'Taille maximale des fichiers (5MB en bytes)'),
('allowed_file_types', 'jpg,jpeg,png', 'Types de fichiers autorisés pour les preuves de paiement');

-- --------------------------------------------------------

-- Vue pour les statistiques des commandes
CREATE VIEW `order_stats` AS
SELECT 
    COUNT(*) as total_orders,
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
    COUNT(CASE WHEN status = 'processing' THEN 1 END) as processing_orders,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
    SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_revenue,
    SUM(CASE WHEN status IN ('pending', 'processing') THEN total_amount ELSE 0 END) as pending_revenue
FROM orders;

-- --------------------------------------------------------

-- Vue pour les services populaires
CREATE VIEW `popular_services` AS
SELECT 
    s.id,
    s.name,
    c.name as category_name,
    COUNT(o.id) as order_count,
    SUM(o.total_amount) as total_revenue
FROM services s
LEFT JOIN orders o ON s.id = o.service_id
LEFT JOIN categories c ON s.category_id = c.id
GROUP BY s.id, s.name, c.name
ORDER BY order_count DESC;

-- --------------------------------------------------------

-- Index pour optimiser les performances
CREATE INDEX idx_orders_date ON orders(created_at);
CREATE INDEX idx_orders_status_date ON orders(status, created_at);
CREATE INDEX idx_services_category_status ON services(category_id, status);

-- --------------------------------------------------------

-- Triggers pour l'historique des changements de statut
DELIMITER $$

CREATE TRIGGER tr_order_status_history 
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO order_status_history (order_id, old_status, new_status, created_at)
        VALUES (NEW.id, OLD.status, NEW.status, NOW());
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

-- Procédure stockée pour générer un numéro de commande unique
DELIMITER $$

CREATE FUNCTION generate_order_number() RETURNS VARCHAR(20)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE order_num VARCHAR(20);
    DECLARE counter INT DEFAULT 1;
    DECLARE today VARCHAR(8);
    
    SET today = DATE_FORMAT(NOW(), '%Y%m%d');
    
    REPEAT
        SET order_num = CONCAT('SMM', today, LPAD(counter, 4, '0'));
        SET counter = counter + 1;
    UNTIL NOT EXISTS (SELECT 1 FROM orders WHERE order_number = order_num)
    END REPEAT;
    
    RETURN order_num;
END$$

DELIMITER ;

COMMIT;