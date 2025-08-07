-- Base de données SMM Site
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS smm_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smm_site;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    is_admin TINYINT(1) DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des catégories de services
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    active TINYINT(1) DEFAULT 1,
    order_position INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    min_quantity INT DEFAULT 1,
    max_quantity INT DEFAULT 10000,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Table des commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    target_url VARCHAR(500) NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_proof VARCHAR(255),
    payment_method ENUM('mtn_money', 'moov_money') DEFAULT 'mtn_money',
    admin_notes TEXT,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Table des paramètres du site
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion des catégories par défaut
INSERT INTO categories (name, description, icon, order_position) VALUES
('Instagram', 'Services pour Instagram - Followers, Likes, Vues', 'fab fa-instagram', 1),
('Facebook', 'Services pour Facebook - Likes, Followers, Partages', 'fab fa-facebook', 2),
('TikTok', 'Services pour TikTok - Followers, Likes, Vues', 'fab fa-tiktok', 3),
('YouTube', 'Services pour YouTube - Abonnés, Vues, Likes', 'fab fa-youtube', 4),
('Twitter', 'Services pour Twitter - Followers, Likes, Retweets', 'fab fa-twitter', 5);

-- Insertion des services par défaut
INSERT INTO services (category_id, name, description, price, min_quantity, max_quantity) VALUES
-- Instagram
(1, 'Instagram Followers', 'Followers Instagram de qualité', 50, 100, 10000),
(1, 'Instagram Likes', 'Likes Instagram rapides', 25, 50, 5000),
(1, 'Instagram Vues Reels', 'Vues pour vos Reels Instagram', 30, 100, 100000),
(1, 'Instagram Commentaires', 'Commentaires positifs Instagram', 100, 5, 500),

-- Facebook
(2, 'Facebook Likes Page', 'Likes pour votre page Facebook', 75, 50, 5000),
(2, 'Facebook Followers', 'Followers Facebook actifs', 60, 100, 10000),
(2, 'Facebook Partages', 'Partages pour vos publications', 150, 10, 1000),

-- TikTok
(3, 'TikTok Followers', 'Followers TikTok réels', 80, 100, 10000),
(3, 'TikTok Likes', 'Likes TikTok instantanés', 40, 100, 50000),
(3, 'TikTok Vues', 'Vues pour vos vidéos TikTok', 20, 1000, 1000000),

-- YouTube
(4, 'YouTube Abonnés', 'Abonnés YouTube de qualité', 200, 50, 5000),
(4, 'YouTube Vues', 'Vues YouTube ciblées', 100, 1000, 100000),
(4, 'YouTube Likes', 'Likes YouTube authentiques', 150, 20, 2000),

-- Twitter
(5, 'Twitter Followers', 'Followers Twitter actifs', 90, 100, 10000),
(5, 'Twitter Likes', 'Likes Twitter rapides', 60, 50, 5000),
(5, 'Twitter Retweets', 'Retweets de qualité', 120, 10, 1000);

-- Insertion de l'administrateur par défaut
-- Mot de passe : admin123 (à changer après installation)
INSERT INTO users (email, password, full_name, is_admin) VALUES
('admin@smmsite.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 1);

-- Insertion des paramètres du site
INSERT INTO site_settings (setting_key, setting_value, description) VALUES
('site_name', 'SMM Pro Services', 'Nom du site'),
('site_description', 'Votre partenaire pour booster votre présence sur les réseaux sociaux', 'Description du site'),
('contact_email', 'contact@smmsite.com', 'Email de contact'),
('contact_phone', '+226 70 00 00 00', 'Téléphone de contact'),
('mtn_money_number', '70 00 00 00', 'Numéro MTN Money'),
('moov_money_number', '60 00 00 00', 'Numéro Moov Money'),
('payment_instructions', 'Envoyez le montant exact via Mobile Money puis uploadez votre preuve de paiement.', 'Instructions de paiement');

-- Création des index pour optimiser les performances
CREATE INDEX idx_orders_user_id ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_created_at ON orders(created_at);
CREATE INDEX idx_services_category_id ON services(category_id);
CREATE INDEX idx_services_active ON services(active);
CREATE INDEX idx_categories_active ON categories(active);