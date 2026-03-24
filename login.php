<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cache tiltása az adatmaradékok ellen
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// A feldolgozó behívása (Mivel a process-ben nincs 'üres' átirányítás, nem lesz hurok)
include "login_process.php"; 
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés - Nógrád</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <style>
        body { background: #121212; font-family: 'Open Sans', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .login-box { width: calc(100% - 30px); max-width: 400px; margin: 20px auto; padding: clamp(20px, 5vw, 40px); background: rgba(255, 255, 255, 0.05); border-radius: 15px; border: 1px solid rgba(250, 250, 250, 0.1); box-shadow: 0 15px 35px rgba(0,0,0,0.5); backdrop-filter: blur(10px); }
        .login-box h2 { color: #fff; text-align: center; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; font-size: clamp(20px, 6vw, 32px) !important; margin-bottom: clamp(15px, 4vw, 30px) !important; }
        .login-box h2 em { font-style: normal; color: #45489a; }
        .form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; height: clamp(40px, 10vw, 50px) !important; font-size: clamp(14px, 4vw, 16px) !important; margin-bottom: 15px; }
        .form-control:focus { background: rgba(255,255,255,0.2); border-color: #45489a; color: #fff; box-shadow: none; }
        .btn-sentra { background-color: #45489a; color: #fff; border: none; width: 100% !important; height: clamp(45px, 10vw, 55px) !important; font-weight: 600; text-transform: uppercase; transition: 0.3s; }
        .btn-sentra:hover { background-color: #fff; color: #45489a; }
        hr { border-top: 1px solid rgba(255,255,255,0.1); }
        p { color: #aaa; font-size: 13px; }
        a { color: #45489a; text-decoration: none; }
        a:hover { color: #fff; }
        @media screen and (max-width: 767px) { body { height: auto; min-height: 100vh; padding: 20px 0; } }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Nóg<em>rád</em></h2>
        <form action="login.php" method="POST" id="loginForm">
            <input type="text" name="uusername" placeholder="Becenév vagy Email" class="form-control" required autocomplete="off">
            <input type="password" name="upw" placeholder="Jelszó" class="form-control" required autocomplete="new-password">
            <button type="submit" class="btn btn-sentra">Belépés</button>
</form>

        <hr>

        <p class="text-center">
            <a href="forgot_password.php" style="color: #ff4d4d; font-weight: 600; text-decoration: none;">
                Elfelejtetted a jelszavad?
            </a>
        </p>

        <p class="text-center">
            Nincs még fiókod? <a href="reg_id.php">Regisztrálj itt!</a>
        </p>
        
        <p class="text-center">
            <small>
                <a href="#" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='index.php'; } return false;">
                    Vissza
                </a>
            </small>
        </p>
    </div>
</body>
