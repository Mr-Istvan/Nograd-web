<?php
require_once __DIR__ . '/init.php';

// MEGOLDÁS: Meghatározzuk a weboldal alapútvonalát. 
// Localhoston a mappa neve kell, éles szerveren (nethely) pedig elég a / jel.
$base_url = ($_SERVER['HTTP_HOST'] == 'localhost') ? '/nograd-web/' : '/';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Hiba 404 - Rossz útvonalon vagy!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/fontAwesome.css">
    
    <style>
        body { 
            /* Fix útvonal a háttérképhez */
            background: url('<?php echo $base_url; ?>img/turaz_1.jpg') no-repeat center center fixed; 
            background-size: cover;
            color: white; 
            font-family: 'Open Sans', sans-serif; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            margin: 0; 
        }
        
        .error-card { 
            background: rgba(0, 0, 0, 0.7); 
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 40px; 
            border-radius: 20px; 
            border: 2px solid #0d6efd; 
            max-width: 600px; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        .error-image-container {
            margin-bottom: 25px;
            border-radius: 15px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.2);
        }

        .error-image {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .btn-group-404 {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-nandy {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: bold;
            text-transform: uppercase;
            transition: 0.3s;
            text-decoration: none !important;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: white;
        }

        .btn-nandy-back { background: #0d6efd; }
        .btn-nandy-home { background: #28a745; }

        .btn-nandy:hover { 
            transform: translateY(-3px);
            filter: brightness(1.2);
            color: white;
        }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-image-container">
            <img src="<?php echo $base_url; ?>img/404_1.jpg" class="error-image" alt="Hiba 404">
        </div>
        
        <h2>Hoppá! Rossz ösvényre tévedtél.</h2>
        <p>Ez az oldal nem található a Nógrád vármegyei térképen. Térj vissza a biztonságos útra!</p>
        
        <div class="btn-group-404">
            <a href="javascript:history.back()" class="btn-nandy btn-nandy-back">
                <i class="fa fa-arrow-left"></i> Vissza
            </a>
            <a href="<?php echo $base_url; ?>index.php" class="btn-nandy btn-nandy-home">
                <i class="fa fa-home"></i> Főoldal
            </a>
        </div>
    </div>
</body>
</html>
