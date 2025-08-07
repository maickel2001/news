<?php
/**
 * Page 404 - TarantulaSMM Bénin
 * 
 * @author TarantulaSMM Team
 * @version 1.0.0
 * @since 2024
 */

// Définir l'en-tête 404
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Non Trouvée - TarantulaSMM Bénin</title>
    <meta name="description" content="La page que vous recherchez est introuvable. Retournez à l'accueil de TarantulaSMM Bénin.">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #6366f1;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gradient-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 0;
        }
        
        .error-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
            text-align: center;
            padding: 3rem 2rem;
        }
        
        .error-number {
            font-size: 8rem;
            font-weight: 800;
            background: var(--gradient-primary);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 1rem;
        }
        
        .error-spider {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 2rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .error-description {
            font-size: 1.1rem;
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        
        .btn-home {
            background: var(--gradient-primary);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
            color: white;
        }
        
        .btn-back {
            background: #6b7280;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        
        .btn-back:hover {
            background: #4b5563;
            color: white;
        }
        
        .quick-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
        }
        
        .quick-links h6 {
            color: #374151;
            margin-bottom: 1rem;
        }
        
        .quick-links a {
            color: var(--primary-color);
            text-decoration: none;
            margin: 0 1rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .quick-links a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }
        
        @media (max-width: 576px) {
            .error-number {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.5rem;
            }
            
            .error-card {
                padding: 2rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="error-card">
                    <!-- Icône araignée -->
                    <div class="error-spider">
                        <i class="fas fa-spider"></i>
                    </div>
                    
                    <!-- Numéro 404 -->
                    <div class="error-number">404</div>
                    
                    <!-- Titre -->
                    <h1 class="error-title">Page Non Trouvée</h1>
                    
                    <!-- Description -->
                    <p class="error-description">
                        Oups ! La page que vous recherchez semble s'être envolée dans la toile. 
                        Elle a peut-être été déplacée, supprimée ou l'URL est incorrecte.
                    </p>
                    
                    <!-- Boutons d'action -->
                    <div class="mb-4">
                        <a href="/" class="btn-home">
                            <i class="fas fa-home me-2"></i>Retour à l'Accueil
                        </a>
                        <a href="javascript:history.back()" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>Page Précédente
                        </a>
                    </div>
                    
                    <!-- Liens rapides -->
                    <div class="quick-links">
                        <h6>Liens Utiles :</h6>
                        <a href="/login">
                            <i class="fas fa-sign-in-alt me-1"></i>Connexion
                        </a>
                        <a href="/register">
                            <i class="fas fa-user-plus me-1"></i>Inscription
                        </a>
                        <a href="mailto:support@tarantulasmm.bj">
                            <i class="fas fa-envelope me-1"></i>Support
                        </a>
                    </div>
                    
                    <!-- Info de contact -->
                    <div class="mt-4">
                        <small class="text-muted">
                            <i class="fas fa-question-circle me-1"></i>
                            Besoin d'aide ? Contactez notre support béninois 24h/24 :
                            <a href="https://wa.me/22997000000" class="text-decoration-none">
                                <i class="fab fa-whatsapp me-1"></i>+229 97 00 00 00
                            </a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>