<?php
session_start();
require_once 'config/database.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMM Boost - Augmentez votre présence sur les réseaux sociaux</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-brand">
                    <h2><i class="fas fa-rocket"></i> SMM Boost</h2>
                </div>
                <ul class="nav-menu">
                    <li><a href="#home">Accueil</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#how-it-works">Comment ça marche</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="order.php" class="btn-order">Commander</a></li>
                </ul>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Boostez votre présence sur les réseaux sociaux</h1>
                <p>Obtenez plus de followers, likes et vues sur Instagram, TikTok, YouTube et Facebook rapidement et en toute sécurité.</p>
                <div class="hero-buttons">
                    <a href="order.php" class="btn btn-primary">Commencer maintenant</a>
                    <a href="#services" class="btn btn-secondary">Voir nos services</a>
                </div>
            </div>
            <div class="hero-image">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <h2>Nos Services</h2>
            <p class="section-subtitle">Choisissez parmi nos services premium pour booster votre présence</p>
            
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3>Instagram</h3>
                    <ul>
                        <li>Followers Instagram</li>
                        <li>Likes Instagram</li>
                        <li>Vues Stories</li>
                        <li>Commentaires</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <h3>TikTok</h3>
                    <ul>
                        <li>Followers TikTok</li>
                        <li>Likes TikTok</li>
                        <li>Vues vidéos</li>
                        <li>Partages</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3>YouTube</h3>
                    <ul>
                        <li>Abonnés YouTube</li>
                        <li>Vues vidéos</li>
                        <li>Likes vidéos</li>
                        <li>Commentaires</li>
                    </ul>
                </div>

                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3>Facebook</h3>
                    <ul>
                        <li>Likes pages</li>
                        <li>Followers</li>
                        <li>Likes publications</li>
                        <li>Partages</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how-it-works" class="how-it-works">
        <div class="container">
            <h2>Comment ça marche</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Choisissez votre service</h3>
                    <p>Sélectionnez le réseau social et le type de service souhaité</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Passez commande</h3>
                    <p>Renseignez le lien à promouvoir et la quantité désirée</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Effectuez le paiement</h3>
                    <p>Payez via Mobile Money et envoyez votre preuve de paiement</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Recevez vos résultats</h3>
                    <p>Votre commande est traitée rapidement et efficacement</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2>Contactez-nous</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>contact@smmboost.com</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>+226 XX XX XX XX</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-clock"></i>
                        <span>24h/24 - 7j/7</span>
                    </div>
                </div>
                <div class="contact-form">
                    <form>
                        <input type="text" placeholder="Votre nom" required>
                        <input type="email" placeholder="Votre email" required>
                        <textarea placeholder="Votre message" rows="5" required></textarea>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3><i class="fas fa-rocket"></i> SMM Boost</h3>
                    <p>Votre partenaire pour booster votre présence sur les réseaux sociaux</p>
                </div>
                <div class="footer-links">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="#home">Accueil</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="order.php">Commander</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h4>Suivez-nous</h4>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 SMM Boost. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
</body>
</html>