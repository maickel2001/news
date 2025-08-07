/**
 * TarantulaSMM Bénin - JavaScript Principal
 * Gestion des animations, interactions et effets
 */

(function() {
    'use strict';

    // ========================================
    // VARIABLES GLOBALES
    // ========================================
    
    let navbar = null;
    let countersAnimated = false;

    // ========================================
    // INITIALISATION
    // ========================================
    
    document.addEventListener('DOMContentLoaded', function() {
        initializeComponents();
        setupEventListeners();
        initializeAnimations();
    });

    // ========================================
    // COMPOSANTS PRINCIPAUX
    // ========================================
    
    function initializeComponents() {
        navbar = document.querySelector('.navbar');
        setupSmoothScrolling();
        setupMobileMenu();
    }

    function setupEventListeners() {
        // Effet scroll navbar
        window.addEventListener('scroll', handleNavbarScroll);
        window.addEventListener('scroll', handleScrollAnimations);
        window.addEventListener('resize', handleResize);
        
        // Délégation d'événements pour les liens
        document.addEventListener('click', handleLinkClicks);
    }

    function initializeAnimations() {
        fadeInOnScroll();
        setTimeout(() => {
            if (!countersAnimated) {
                animateCounters();
            }
        }, 500);
    }

    // ========================================
    // NAVIGATION
    // ========================================
    
    function handleNavbarScroll() {
        if (!navbar) return;
        
        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }

    function setupSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                
                if (target) {
                    const offset = 80; // Hauteur de la navbar
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    function setupMobileMenu() {
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                const navbarCollapse = document.querySelector('.navbar-collapse');
                
                if (navbarCollapse && navbarCollapse.classList.contains('show')) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    }

    // ========================================
    // ANIMATIONS
    // ========================================
    
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number[data-count]');
        
        if (counters.length === 0) return;
        
        countersAnimated = true;
        
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += increment;
                
                if (current >= target) {
                    counter.textContent = formatNumber(target);
                    clearInterval(timer);
                } else {
                    counter.textContent = formatNumber(Math.floor(current));
                }
            }, 16);
        });
    }

    function fadeInOnScroll() {
        const elements = document.querySelectorAll('.fade-in-up');
        
        elements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('visible');
            }
        });
    }

    function handleScrollAnimations() {
        // Animation des éléments au scroll
        fadeInOnScroll();
        
        // Animation des compteurs quand ils deviennent visibles
        if (!countersAnimated) {
            const statsSection = document.querySelector('.stats-section');
            if (statsSection) {
                const rect = statsSection.getBoundingClientRect();
                if (rect.top < window.innerHeight && rect.bottom > 0) {
                    animateCounters();
                }
            }
        }
    }

    // ========================================
    // UTILITAIRES
    // ========================================
    
    function formatNumber(num) {
        return num.toLocaleString('fr-FR');
    }

    function handleLinkClicks(e) {
        const target = e.target.closest('a');
        if (!target) return;
        
        // Ajouter effet de clic visuel
        if (target.classList.contains('btn') || target.classList.contains('btn-cta')) {
            target.style.transform = 'scale(0.95)';
            setTimeout(() => {
                target.style.transform = '';
            }, 150);
        }
    }

    function handleResize() {
        // Recalculer les animations si nécessaire
        fadeInOnScroll();
    }

    // ========================================
    // EFFETS VISUELS AVANCÉS
    // ========================================
    
    function createParticles() {
        const heroSection = document.querySelector('.hero-section');
        if (!heroSection) return;
        
        // Créer des particules flottantes (optionnel)
        for (let i = 0; i < 5; i++) {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.cssText = `
                position: absolute;
                width: 4px;
                height: 4px;
                background: rgba(255,255,255,0.3);
                border-radius: 50%;
                animation: float-particle ${5 + Math.random() * 5}s infinite ease-in-out;
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation-delay: ${Math.random() * 2}s;
            `;
            heroSection.appendChild(particle);
        }
    }

    // ========================================
    // GESTION D'ERREURS
    // ========================================
    
    window.addEventListener('error', function(e) {
        console.warn('Erreur JavaScript détectée:', e.error);
    });

    // ========================================
    // API PUBLIQUE
    // ========================================
    
    // Exposer certaines fonctions pour usage externe
    window.TarantulaSMM = {
        animateCounters: animateCounters,
        fadeInOnScroll: fadeInOnScroll,
        formatNumber: formatNumber
    };

})();

// ========================================
// STYLES CSS DYNAMIQUES
// ========================================

// Ajouter des styles pour les particules flottantes
if (!document.querySelector('#particle-styles')) {
    const style = document.createElement('style');
    style.id = 'particle-styles';
    style.textContent = `
        @keyframes float-particle {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
                opacity: 0.3;
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
                opacity: 0.8;
            }
        }
        
        .particle {
            pointer-events: none;
            z-index: 1;
        }
    `;
    document.head.appendChild(style);
}

// ========================================
// OPTIMISATIONS PERFORMANCE
// ========================================

// Débounce pour les événements de scroll
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Appliquer le debounce aux événements scroll
if (typeof window.handleScrollAnimations === 'function') {
    window.addEventListener('scroll', debounce(window.handleScrollAnimations, 10));
}