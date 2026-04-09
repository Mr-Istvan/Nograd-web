<?php
require_once __DIR__ . '/init.php';

// Megakadályozzuk a cache-elést
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// --- 1. OKOS MEMÓRIA KEZELÉS (VÉGTELEN CIKLUS ELLEN) ---
$exclude_pages = ['login.php', 'reg_id.php', 'forgot_password.php', 'forg_pw.php', 'reg_process.php', 'login_process.php'];

if (isset($_SERVER['HTTP_REFERER'])) {
    $from_page = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    if (!in_array($from_page, $exclude_pages)) {
        $_SESSION['user_origin_url'] = $_SERVER['HTTP_REFERER'];
    }
}

$final_x_url = $_SESSION['user_origin_url'] ?? 'index.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regisztráció - Nógrád</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <style>
        body {
            background-color: #000;
            background-image: url('img/nograd_background.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center center;
            background-size: cover;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Open Sans', sans-serif;
            margin: 0;
            padding: 20px 0;
            overflow-x: hidden;
        }

        @media only screen and (max-width: 768px) {
            body {
                background-image: url('img/nograd_background_mobile.png');
                background-attachment: scroll;
            }
        }

        .login-box {
            position: relative;
            z-index: 10;
            width: calc(100% - 30px);
            max-width: 400px;
            padding: 40px;
            background: rgba(10, 15, 30, 0.7);
            border-radius: 25px;
            border: 1px solid rgba(14, 165, 233, 0.3);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8);
        }

        .login-box h2 {
            color: #fff;
            text-align: center;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 25px;
        }

        .login-box h2 em {
            font-style: normal;
            color: #0ea5e9;
        }

        .form-label {
            display: block;
            color: #0ea5e9;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff !important;
            height: 50px;
            margin-bottom: 12px;
            border-radius: 12px;
            padding: 0 15px;
        }

        .form-control::placeholder {
            color: rgba(234, 234, 234, 0.65) !important;
            opacity: 1;
        }

        .form-control:focus {
            background: rgba(215, 215, 215, 0.1);
            border-color: #0ea5e9;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.3);
            color: #fff;
        }

        .email-container {
            margin-bottom: 12px;
        }

        .email-full {
            width: 100%;
            margin-bottom: 0;
        }

        .btn-sentra {
            background: #0ea5e9;
            color: #fff;
            border: none;
            width: 100%;
            height: 50px;
            font-weight: 800;
            border-radius: 12px;
            transition: 0.3s;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .btn-sentra:hover {
            background: #0284c7;
            transform: translateY(-2px);
        }

        .btn-back-red {
            background-color: #ff4d4d;
            color: #fff;
            border: none;
            width: 100%;
            height: 45px;
            font-weight: 600;
            border-radius: 8px;
            transition: 0.3s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            text-transform: uppercase;
        }

        .btn-back-red:hover {
            background-color: #ff3333;
            color: #fff;
        }

        .status-msg {
            padding: 12px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            transition: opacity 0.5s ease;
            background: rgba(10, 15, 30, 0.75);
            backdrop-filter: blur(15px);
            color: #fff;
        }

        .msg-success { border: 1px solid rgba(74, 222, 128, 0.5); }
        .msg-error { border: 1px solid rgba(239, 68, 68, 0.5); }

        .link-login {
            color: #0ea5e9 !important;
            text-decoration: none;
            transition: 0.3s;
            font-weight: 700;
        }

        .link-login:hover {
            color: #38bdf8 !important;
            text-decoration: underline;
        }

        .close-icon {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 30px;
            color: #f43f5e;
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

        .info-btn {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.5);
            background: #001f3f;
            color: white;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.5);
            user-select: none;
            transition: 0.2s;
            position: absolute;
            top: 20px;
            left: 25px;
            text-decoration: none;
        }

        .info-btn:active {
            transform: scale(0.9);
        }

        .info-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.78);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .info-modal.open {
            display: flex;
        }

        .info-modal__box {
            width: 100%;
            max-width: 520px;
            background: rgba(10, 15, 30, 0.92);
            border: 1px solid rgba(14, 165, 233, 0.4);
            border-radius: 22px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.85);
            padding: 28px 24px;
            color: #fff;
            position: relative;
        }

        .info-modal__close {
            position: absolute;
            top: 14px;
            right: 18px;
            color: #f43f5e;
            font-size: 28px;
            cursor: pointer;
            line-height: 1;
        }

        .info-modal__title {
            color: #0ea5e9;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            font-size: 18px;
        }

        .info-modal__text {
            color: #fff;
            font-size: 15px;
            line-height: 1.7;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <button type="button" class="info-btn" id="infoOpenBtn" aria-label="Információ">i</button>
        <a href="<?php echo htmlspecialchars($final_x_url); ?>" class="close-icon" title="Bezárás">&times;</a>
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
            <label class="form-label">Felhasználónév</label>
            <input type="text" name="uusername" placeholder="Felhasználónév" class="form-control" required>

            <label class="form-label">Email cím</label>
            <input type="email" 
                   name="uemail" 
                   class="form-control" 
                   placeholder="nev@pelda.hu" 
                   required 
                   pattern="^[^ ]+@[^ ]+\.[a-z]{2,6}$"
                   title="Kérjük, adj meg egy teljes email címet (pl. .hu vagy .com végződéssel)!">

            <label class="form-label">Jelszó</label>
            <input type="password" name="pass1" id="pass1" placeholder="Jelszó (min 6)" class="form-control" maxlength="36" required>

            <label class="form-label">Jelszó megerősítése</label>
            <input type="password" name="pass2" id="pass2" placeholder="Jelszó megerősítése" class="form-control" required>

            <button type="submit" class="btn btn-sentra">Fiók létrehozása</button>
            <a href="index.php" class="btn-back-red">Vissza</a>
        </form>

        <hr style="opacity: 0.1;">
        <p class="text-center small text-white-50">
            Már van fiókod? <a href="login.php" class="link-login">Jelentkezz be!</a>
        </p>
    </div>

    <div class="info-modal" id="infoModal" aria-hidden="true">
        <div class="info-modal__box" role="dialog" aria-modal="true" aria-labelledby="infoTitle">
            <div class="info-modal__close" id="infoCloseBtn" aria-label="Bezárás">&times;</div>
            <div class="info-modal__title" id="infoTitle">Miért regisztrálj?</div>
            <div class="info-modal__text">„Regisztráció nélkül sajnos nem tudunk azonosítani a rendszerben, így a személyre szabott funkciók és a hozzászólási lehetőség zárva maradnak előtted. Pár kattintás, és megnyílik a kapu!”</div>
        </div>
    </div>

    <script>
        document.getElementById('regForm').onsubmit = function(e) {
            if (document.getElementById('pass1').value !== document.getElementById('pass2').value) {
                e.preventDefault();
                document.getElementById('msg-box').innerHTML = '<div class="status-msg msg-error">❌ A két jelszó nem egyezik!</div>';
            }
        };

        const infoModal = document.getElementById('infoModal');
        const infoOpenBtn = document.getElementById('infoOpenBtn');
        const infoCloseBtn = document.getElementById('infoCloseBtn');
        if (infoOpenBtn && infoModal) infoOpenBtn.addEventListener('click', () => infoModal.classList.add('open'));
        if (infoCloseBtn && infoModal) infoCloseBtn.addEventListener('click', () => infoModal.classList.remove('open'));
        if (infoModal) infoModal.addEventListener('click', (e) => { if (e.target === infoModal) infoModal.classList.remove('open'); });
    </script>
</body>
</html>
