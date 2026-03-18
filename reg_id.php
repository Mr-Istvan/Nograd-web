<?php
session_start();
require_once 'db.php';
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - Nógrád</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <style>
        body { background: #121212; font-family: 'Open Sans', sans-serif; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        
        .login-box {
            width: calc(100% - 30px);
            max-width: 400px;
            margin: 20px auto;
            padding: clamp(20px, 5vw, 40px); 
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            border: 1px solid rgba(250, 250, 250, 0.1);
        }

        .login-box h2 {
            color: #fff;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: clamp(20px, 6vw, 32px) !important;
            margin-bottom: clamp(15px, 4vw, 30px) !important;
        }

        .login-box h2 em { font-style: normal; color: #45489a; }

        .form-control {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            color: #fff;
            height: clamp(40px, 10vw, 50px) !important;
            font-size: clamp(14px, 4vw, 16px) !important;
            margin-bottom: 15px;
        }

        .form-control:focus { background: rgba(255,255,255,0.2); border-color: #45489a; color: #fff; box-shadow: none; }

        .btn-sentra {
            background-color: #45489a;
            color: #fff;
            border: none;
            width: 100% !important;
            height: clamp(45px, 10vw, 55px) !important;
            font-size: clamp(15px, 4vw, 18px) !important;
            font-weight: 600;
            text-transform: uppercase;
            transition: 0.3s;
        }

        .btn-sentra:hover { background-color: #fff; color: #45489a; }

        .secret-divider {
            border-top: 1px dashed rgba(69, 72, 154, 0.5);
            margin: 20px 0 15px 0;
            position: relative;
            text-align: center;
        }

        .secret-label {
            position: absolute;
            top: -12px;
            left: 50%;
            transform: translateX(-50%);
            background: #1c1c1c; /* Kicsit sötétebb, hogy elváljon a háttértől */
            padding: 0 10px;
            color: #45489a;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        hr { border-top: 1px solid rgba(255,255,255,0.1); }
        p { color: #aaa; font-size: 13px; }
        a { color: #45489a; text-decoration: none; }
        a:hover { color: #fff; }

        @media screen and (max-width: 767px) {
            body { height: auto; min-height: 100vh; display: flex; align-items: center; padding: 20px 0; }
        }
    </style>
</head>
<body>
    <div class="login-box">
    <h2>Regiszt<em>ráció</em></h2>
    <form action="reg_process.php" method="POST" id="regForm" autocomplete="off">
        <input type="text" name="uname" placeholder="Teljes név" class="form-control" required autocomplete="off">
        <input type="text" name="uusername" placeholder="Becenév" class="form-control" required autocomplete="off">
        
        <input type="email" name="uemail" placeholder="Email cím" class="form-control" required autocomplete="new-password">

        <input type="password" name="pass1" id="pass1" placeholder="Jelszó (max 36)" class="form-control" maxlength="36" required autocomplete="new-password">
        <input type="password" name="pass2" id="pass2" placeholder="Jelszó megerősítése" class="form-control" required autocomplete="new-password">

        <div class="secret-divider">
            <span class="secret-label">Jelszó emlékeztetőhöz</span>
        </div>
        
        <input type="text" name="usecret_q" placeholder="Saját biztonsági kérdésed" class="form-control" required>
        <input type="text" name="usecret_a" placeholder="A válaszod" class="form-control" required autocomplete="off">

        <button type="submit" class="btn btn-sentra">Fiók létrehozása</button>
    </form>
    <hr>
    <p class="text-center">Már van fiókod? <a href="login.php">Jelentkezz be!</a></p>
    <p class="text-center">
        <small>
            <a href="#" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='index.php'; } return false;">
                Vissza
            </a>
        </small>
    </p>
</div>

    <script>
        document.getElementById('regForm').onsubmit = function(e) {
            const p1 = document.getElementById('pass1').value;
            const p2 = document.getElementById('pass2').value;
            if(p1 !== p2) {
                e.preventDefault();
                alert("A két jelszó nem egyezik meg!");
            }
        };
    </script>
</body>
</html>
