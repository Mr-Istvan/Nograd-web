<?php
require_once __DIR__ . '/init.php';
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - Nógrád</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <style>
        body { background: #121212; font-family: 'Open Sans', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .login-box { width: calc(100% - 30px); max-width: 400px; padding: 30px; background: rgba(255, 255, 255, 0.05); border-radius: 15px; backdrop-filter: blur(10px); box-shadow: 0 15px 35px rgba(0,0,0,0.5); border: 1px solid rgba(250, 250, 250, 0.1); }
        .login-box h2 { color: #fff; text-align: center; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 25px; }
        .login-box h2 em { font-style: normal; color: #45489a; }

        .form-control { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15); color: #ffffff !important; height: 45px; margin-bottom: 12px; }
        .form-control::placeholder { color: rgba(255,255,255,0.6) !important; }
        .form-control:focus { background: rgba(255,255,255,0.2); border-color: #45489a; box-shadow: none; }
        
        /* Gombok stílusa */
        .btn-sentra { background-color: #45489a; color: #fff; border: none; width: 100%; height: 50px; font-weight: 600; text-transform: uppercase; border-radius: 8px; transition: 0.3s; margin-bottom: 10px; }
        .btn-sentra:hover { background-color: #5558b0; }

        /* PIROS VISSZA GOMB - Mint a login.php-ban */
        .btn-back-red { background-color: #ff4d4d; color: #fff; border: none; width: 100%; height: 45px; font-weight: 600; border-radius: 8px; transition: 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center; text-transform: uppercase; }
        .btn-back-red:hover { background-color: #ff3333; color: #fff; }

        /* Állapot üzenetek */
        .status-msg { padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .msg-error { background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff4d4d; }
        
        .secret-divider { border-top: 1px dashed rgba(69, 72, 154, 0.5); margin: 25px 0 15px 0; position: relative; text-align: center; }
        .secret-label { position: absolute; top: -12px; left: 50%; transform: translateX(-50%); background: #1a1a1a; padding: 0 10px; color: #45489a; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        
        /* Emeltebb kék link stílus */
        .link-login { color: #45489a !important; text-decoration: none; font-weight: 800; transition: 0.3s; }
        .link-login:hover { color: #6165d7 !important; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Regiszt<em>ráció</em></h2>

        <div id="msg-box">
            <?php if (isset($_GET['error'])): ?>
                <div class="status-msg msg-error">
                    <?php 
                        if($_GET['error'] == 'empty') echo "❌ Minden mezőt ki kell tölteni!";
                        elseif($_GET['error'] == 'match') echo "❌ A két jelszó nem egyezik!";
                        elseif($_GET['error'] == 'short') echo "❌ Túl rövid jelszó (min 6)!";
                        elseif($_GET['error'] == 'taken') echo "❌ Ez a név vagy email már foglalt!";
                        else echo "❌ Hiba történt!";
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <form action="reg_process.php" method="POST" id="regForm" autocomplete="off">
            <input type="text" name="uname" placeholder="Teljes név" class="form-control" required>
            <input type="text" name="uusername" placeholder="Becenév" class="form-control" required>
            <input type="email" name="uemail" placeholder="Email cím" class="form-control" required>
            <input type="password" name="pass1" id="pass1" placeholder="Jelszó (min 6)" class="form-control" maxlength="36" required>
            <input type="password" name="pass2" id="pass2" placeholder="Jelszó megerősítése" class="form-control" required>

            <div class="secret-divider"><span class="secret-label">Jelszó emlékeztetőhöz</span></div>
            
            <input type="text" name="usecret_q" placeholder="Saját biztonsági kérdésed" class="form-control" required>
            <input type="text" name="usecret_a" placeholder="A válaszod" class="form-control" required>

            <button type="submit" class="btn btn-sentra">Fiók létrehozása</button>
            <a href="index.php" class="btn-back-red">Vissza</a>
        </form>
        
        <hr style="opacity: 0.1;">
        <p class="text-center small text-white-50">
            Már van fiókod? <a href="login.php" class="link-login">Jelentkezz be!</a>
        </p>
    </div>

    <script>
        document.getElementById('regForm').onsubmit = function(e) {
            if(document.getElementById('pass1').value !== document.getElementById('pass2').value) {
                e.preventDefault();
                document.getElementById('msg-box').innerHTML = '<div class="status-msg msg-error">❌ A két jelszó nem egyezik!</div>';
            }
        };
    </script>
</body>
</html>