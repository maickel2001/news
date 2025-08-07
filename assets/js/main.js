// JavaScript principal pour le site SMM
document.addEventListener('DOMContentLoaded', function() {
    // Navigation mobile
    const navToggle = document.querySelector('.nav-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (navToggle) {
        navToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    }
    
    // Fermer le menu mobile en cliquant sur un lien
    const navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
        });
    });
    
    // Calculateur de prix automatique
    const serviceSelect = document.getElementById('service_id');
    const quantityInput = document.getElementById('quantity');
    const totalAmountElement = document.getElementById('total_amount');
    const totalAmountInput = document.getElementById('total_amount_hidden');
    
    if (serviceSelect && quantityInput && totalAmountElement) {
        function calculateTotal() {
            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price) || 0;
            const minQty = parseInt(selectedOption.dataset.minQty) || 1;
            const maxQty = parseInt(selectedOption.dataset.maxQty) || 10000;
            const quantity = parseInt(quantityInput.value) || 0;
            
            // Mettre à jour les limites de quantité
            quantityInput.min = minQty;
            quantityInput.max = maxQty;
            
            // Valider la quantité
            if (quantity < minQty) {
                quantityInput.value = minQty;
            } else if (quantity > maxQty) {
                quantityInput.value = maxQty;
            }
            
            const finalQuantity = parseInt(quantityInput.value) || minQty;
            const total = price * finalQuantity;
            
            // Formatter le prix en FCFA avec séparateurs
            const formattedTotal = formatPrice(total);
            totalAmountElement.textContent = formattedTotal;
            
            if (totalAmountInput) {
                totalAmountInput.value = total;
            }
        }
        
        serviceSelect.addEventListener('change', calculateTotal);
        quantityInput.addEventListener('input', calculateTotal);
        
        // Calcul initial
        calculateTotal();
    }
    
    // Auto-hide alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Confirmation pour les actions de suppression
    const deleteButtons = document.querySelectorAll('.btn-danger[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.dataset.confirm || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Prévisualisation des images uploadées
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const file = this.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let preview = document.getElementById('image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'image-preview';
                        preview.className = 'mt-2';
                        input.parentNode.appendChild(preview);
                    }
                    
                    preview.innerHTML = `
                        <img src="${e.target.result}" 
                             alt="Prévisualisation" 
                             style="max-width: 200px; max-height: 200px; border-radius: 8px; box-shadow: var(--shadow);">
                    `;
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Loading state pour les formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitButton = form.querySelector('button[type="submit"], input[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                const originalText = submitButton.textContent;
                submitButton.innerHTML = '<span class="loading"></span> Traitement...';
                
                // Restaurer le bouton après un délai si nécessaire
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }, 10000);
            }
        });
    });
    
    // Filtres pour les tableaux
    const filterSelects = document.querySelectorAll('.filter-select');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const filterColumn = this.dataset.column;
            const table = document.querySelector(this.dataset.target);
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const cell = row.querySelector(`td[data-column="${filterColumn}"]`);
                    if (cell) {
                        const cellValue = cell.textContent.toLowerCase();
                        if (filterValue === '' || cellValue.includes(filterValue)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            }
        });
    });
    
    // Recherche en temps réel
    const searchInputs = document.querySelectorAll('.search-input');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetTable = document.querySelector(this.dataset.target);
            
            if (targetTable) {
                const rows = targetTable.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    if (rowText.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });
    
    // Copier au presse-papiers
    const copyButtons = document.querySelectorAll('.copy-btn');
    copyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const text = this.dataset.text;
            navigator.clipboard.writeText(text).then(() => {
                // Feedback visuel
                const originalIcon = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check"></i>';
                this.classList.add('btn-success');
                
                setTimeout(() => {
                    this.innerHTML = originalIcon;
                    this.classList.remove('btn-success');
                }, 2000);
            });
        });
    });
    
    // Animation d'apparition au scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    const animateElements = document.querySelectorAll('.service-card, .card');
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });
    
    // Graphiques simples pour les statistiques (si Chart.js est disponible)
    if (typeof Chart !== 'undefined') {
        const statsChartCanvas = document.getElementById('statsChart');
        if (statsChartCanvas) {
            const ctx = statsChartCanvas.getContext('2d');
            
            // Données exemple - à remplacer par des données réelles via PHP
            const chartData = {
                labels: ['En attente', 'En cours', 'Terminées', 'Annulées'],
                datasets: [{
                    label: 'Commandes',
                    data: [10, 5, 25, 2],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(23, 162, 184, 0.8)',
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(23, 162, 184, 1)',
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 2
                }]
            };
            
            new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            labels: {
                                color: '#ffffff'
                            }
                        }
                    }
                }
            });
        }
    }
});

// Fonctions utilitaires
function formatPrice(price) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price) + ' FCFA';
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.maxWidth = '400px';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// API pour les services dynamiques
async function loadServices(categoryId) {
    try {
        const response = await fetch(`api/services.php?category_id=${categoryId}`);
        const services = await response.json();
        
        const serviceSelect = document.getElementById('service_id');
        if (serviceSelect) {
            serviceSelect.innerHTML = '<option value="">Sélectionner un service...</option>';
            
            services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.id;
                option.textContent = `${service.name} - ${formatPrice(service.price)}`;
                option.dataset.price = service.price;
                option.dataset.minQty = service.min_quantity;
                option.dataset.maxQty = service.max_quantity;
                serviceSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des services:', error);
        showNotification('Erreur lors du chargement des services', 'danger');
    }
}

// Validation des formulaires côté client
function validateForm(form) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
        } else {
            field.classList.remove('error');
        }
    });
    
    // Validation spécifique pour les emails
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (field.value && !emailRegex.test(field.value)) {
            field.classList.add('error');
            isValid = false;
        }
    });
    
    // Validation pour les URLs
    const urlFields = form.querySelectorAll('input[data-url]');
    urlFields.forEach(field => {
        const urlRegex = /^https?:\/\/.+/;
        if (field.value && !urlRegex.test(field.value)) {
            field.classList.add('error');
            isValid = false;
            showNotification('Veuillez entrer une URL valide (commençant par http:// ou https://)', 'warning');
        }
    });
    
    return isValid;
}

// Styles pour les champs d'erreur
const style = document.createElement('style');
style.textContent = `
    .form-control.error {
        border-color: var(--danger-color) !important;
        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
    }
`;
document.head.appendChild(style);