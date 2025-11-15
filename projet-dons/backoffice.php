<?php
// Page d'accueil du backoffice
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Backoffice - Mind Arena</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: 'Arial', sans-serif; 
            margin: 0; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .header { 
            background: rgba(8, 22, 36, 0.95); 
            color: white; 
            padding: 40px 20px; 
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        
        .header h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            background: linear-gradient(45deg, #fff, #b01ba5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        .container { 
            max-width: 1200px; 
            margin: 50px auto; 
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .card { 
            background: rgba(255,255,255,0.95); 
            padding: 40px 30px; 
            border-radius: 15px; 
            box-shadow: 0 8px 30px rgba(0,0,0,0.2);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(176, 27, 165, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(45deg, #b01ba5, #667eea);
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        
        .btn { 
            display: inline-block; 
            padding: 15px 30px; 
            margin: 20px 10px 0 10px; 
            background: #b01ba5; 
            color: white; 
            text-decoration: none; 
            border-radius: 8px; 
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 15px rgba(176, 27, 165, 0.4);
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn:hover { 
            background: #d93ee7; 
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(176, 27, 165, 0.6);
        }
        
        .btn-site {
            background: #4CAF50;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        }
        
        .btn-site:hover {
            background: #45a049;
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.6);
        }
        
        h2 { 
            color: #333; 
            margin-bottom: 20px; 
            font-size: 1.8rem;
        }
        
        p { 
            color: #666; 
            line-height: 1.6; 
            font-size: 1.1rem;
        }
        
        .icon { 
            font-size: 3.5rem; 
            margin-bottom: 20px; 
            display: block;
        }
        
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .stat {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #b01ba5;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
                margin: 30px auto;
                padding: 15px;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            
            .card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🚀 Backoffice Mind Arena</h1>
        <p>Administration des dons et organisations</p>
    </div>
    
    <div class="container">
        <div class="card">
            <span class="icon">💰</span>
            <h2>Gestion des Dons</h2>
            <p>Consulter, ajouter, modifier ou supprimer des dons. Suivez l'ensemble des contributions et générez des rapports.</p>
            <a href="View/backoffice/don/donList.php" class="btn">📊 Gérer les Dons</a>
        </div>
        
        <div class="card">
            <span class="icon">🏢</span>
            <h2>Gestion des Organisations</h2>
            <p>Administrer les associations bénéficiaires, leurs informations et suivre leur activité.</p>
            <a href="View/backoffice/organisation/organisationList.php" class="btn">👥 Gérer les Organisations</a>
        </div>
        
        <div class="card">
            <span class="icon">🌐</span>
            <h2>Site Public</h2>
            <p>Retourner sur le site principal pour voir l'interface utilisateur et tester l'expérience visiteur.</p>
            <a href="View/frontoffice/index.php" class="btn btn-site">👀 Voir le Site Public</a>
        </div>
    </div>
    
    <script>
        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });
    </script>
</body>
</html>