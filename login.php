<?php 
require_once __DIR__ . '/init.php';

// Megakadályozzuk, hogy a böngésző cache-elje az oldalt
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// --- 1. OKOS MEMÓRIA KEZELÉS (VÉGTELEN CIKLUS ELLEN) ---
// Ezeket az oldalakat SOHA nem mentjük el eredeti kiindulópontnak
$exclude_pages = ['login.php', 'reg_id.php', 'forgot_password.php', 'forg_pw.php', 'reg_process.php', 'login_process.php'];

if (isset($_SERVER['HTTP_REFERER'])) {
    $from_page = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    
    // Csak akkor frissítjük az eredeti címet, ha NEM a tiltólistáról érkezik a felhasználó
    if (!in_array($from_page, $exclude_pages)) {
        $_SESSION['user_origin_url'] = $_SERVER['HTTP_REFERER'];
    }
}

// Az X gomb célpontja: Az elmentett eredeti tartalom, ha nincs, akkor index.php
$final_x_url = $_SESSION['user_origin_url'] ?? 'index.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés - Nógrád</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
       /* 1. MÓDOSÍTÁS: Alapbeállítások */
            body { 
                background: #000; 
                color: white; 
                display: flex; 
                align-items: center; 
                justify-content: center; 
                height: 100vh; 
                font-family: 'Open Sans', sans-serif; 
                margin: 0; 
                overflow: hidden; 
            }
            /* 2. ÚJ: dizájn */
            #matrix {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1; 
            }
            /* 3. MÓDOSÍTÁS: A doboz (az űrlap) */
            .login-box { 
                position: relative; 
                z-index: 10; 
                width: 400px; 
                padding: 30px; 
                background: rgba(0, 0, 0, 0.8); 
                border-radius: 15px; 
                border: 1px solid rgba(0, 255, 255, 0.3); 
                backdrop-filter: blur(5px); 
                -webkit-backdrop-filter: blur(5px); 
                box-shadow: 0 10px 30px rgba(0,0,0,0.8); 
            }.form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: #fff; margin-bottom: 15px; height: 45px; }
             .form-control:focus { background: rgba(255,255,255,0.15); color: #fff; border-color: #45489a; box-shadow: none; }
             .form-control::placeholder { color: rgba(255,255,255,0.6) !important; }
             .login-box h2 em { font-style: normal; color: #45489a; }
        /* Gombok stílusa */
        .btn-sentra { background-color: #45489a; color: #fff; border: none; width: 100%; height: 50px; font-weight: 600; border-radius: 8px; transition: 0.3s; margin-bottom: 10px; }
        .btn-sentra:hover { background-color: #5659b1; }

        /* VISSZA GOMB - Ugyanaz a piros, mint a linknél (#ff4d4d) */
        .btn-back-red { background-color: #ff4d4d; color: #fff; border: none; width: 100%; height: 45px; font-weight: 600; border-radius: 8px; transition: 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center; }
        .btn-back-red:hover { background-color: #ff3333; color: #fff; }

        /* Állapot üzenetek */
        .status-msg { padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 20px; font-weight: 600; font-size: 14px; transition: opacity 0.5s ease; }
        .msg-success { background: rgba(25, 135, 84, 0.2); border: 1px solid #198754; color: #2ecc71; }
        .msg-error { background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff4d4d; }
        
        /* Alsó linkek */
        .link-forgot { color: #ff4d4d !important; text-decoration: none; transition: 0.3s; }
        .link-forgot:hover { color: #ff8080 !important; text-decoration: underline; }
        
        .link-reg { color: #2ecc71 !important; text-decoration: none; font-weight: bold; transition: 0.3s; }
        .link-reg:hover { color: #5efc9d !important; text-decoration: underline; }
        .close-icon { 
            position: absolute; 
            top: 15px; 
            right: 20px; 
            font-size: 35px; 
            color: #ef4444; 
            cursor: pointer; 
            z-index: 100; 
            text-decoration: none !important;
            line-height: 1;
            transition: 0.3s;
        }
        .close-icon:hover { 
            color: #ff0000; 
            transform: scale(1.2); 
        }
    </style>
</head>
<body>
    <?php include 'matrix_bg.php'; ?>
    <div class="login-box">
        <a href="<?php echo htmlspecialchars($final_x_url); ?>" class="close-icon" title="Bezárás">&times;</a>
        <h2 class="text-center text-white mb-4">NÓG<em>RÁD</em></h2>
        
        <div id="info-box">
            <?php if (isset($_GET['msg'])): ?>
                <div id="fade-msg" class="status-msg msg-success">
                    <?php 
                        if($_GET['msg'] == 'reg_kesz') echo "✅ Sikeres regisztráció!";
                        if($_GET['msg'] == 'pw_updated') echo "✅ Jelszó sikeresen frissítve!";
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="status-msg msg-error">❌ Hibás felhasználónév vagy jelszó!</div>
            <?php endif; ?>
        </div>

        <form action="login_process.php" method="POST">
            <input type="text" name="uusername" placeholder="Becenév vagy Email" class="form-control" required>
            <input type="password" name="upw" placeholder="Jelszó" class="form-control" required>
            
            <button type="submit" class="btn btn-sentra">BELÉPÉS</button>
            <a href="index.php" class="btn-back-red">VISSZA</a>
        </form>

        <div class="text-center mt-3">
            <p class="small mb-1">
                <a href="forgot_password.php" class="link-forgot">Elfelejtett jelszó?</a>
            </p>
            <p class="small text-white-50">
                Nincs még fiókod? <a href="reg_id.php" class="link-reg">Regisztráció</a>
            </p>
        </div>
    </div>

    <script>
        const msg = document.getElementById('fade-msg');
        if (msg) {
            setTimeout(() => {
                msg.style.opacity = '0';
                setTimeout(() => { msg.style.display = 'none'; }, 500);
            }, 3000);
        }
    </script>
</body>
</html>