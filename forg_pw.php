<?php
require_once __DIR__ . '/init.php';

// Megakadályozzuk a cache-elést
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

$exclude_pages = ['login.php', 'reg_id.php', 'forgot_password.php', 'forg_pw.php', 'reg_process.php', 'login_process.php', 'reset.php'];

if (isset($_SERVER['HTTP_REFERER'])) {
    $from_page = basename(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH));
    if (!in_array($from_page, $exclude_pages)) {
        $_SESSION['user_origin_url'] = $_SERVER['HTTP_REFERER'];
    }
}

$final_x_url = $_SESSION['user_origin_url'] ?? 'index.php';
$error = $_GET['error'] ?? "";

if (!isset($_GET['error'])) {
    unset($_SESSION['reset_uid'], $_SESSION['reset_q'], $_SESSION['reset_email'], $_SESSION['reset_display_name']);
}

if (isset($_POST['identify_user'])) {
    $uemail = mysqli_real_escape_string($conn, trim($_POST['uemail']));
    $query = "SELECT uid, uusername, usecret_q, uemail FROM felhasznalok WHERE uemail = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $uemail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['reset_uid'] = $row['uid'];
        $_SESSION['reset_q'] = $row['usecret_q'];
        $_SESSION['reset_email'] = $row['uemail'];
        $_SESSION['reset_display_name'] = $row['uusername'];
        header("Location: forgot_password.php?step=2");
        exit();
    } else {
        header("Location: forg_pw.php?error=no_user");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó pótlása</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .box {
            position: relative;
            width: 95%;
            max-width: 400px;
            padding: 40px;
            background: rgba(10, 15, 30, 0.7);
            border-radius: 25px;
            border: 1px solid rgba(14, 165, 233, 0.3);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.8);
            z-index: 10;
        }

        h2 {
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
        }

        h2 em {
            color: #0ea5e9;
            font-style: normal;
        }

        label {
            font-size: 11px;
            text-transform: uppercase;
            color: #0ea5e9;
            font-weight: 800;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 1px;
        }

        .form-control {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            height: 50px;
            border-radius: 12px;
            margin-bottom: 20px;
            padding: 0 15px;
        }

        .form-control::placeholder {
            color: rgba(136, 136, 136, 0.65);
            opacity: 1;
        }

        .form-control:focus {
            background: rgba(215, 215, 215, 0.1);
            border-color: #0ea5e9;
            color: #fff;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.3);
        }

        .action-btn-glass {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 18px;
            color: white;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
            margin-bottom: 15px;
            text-align: left;
        }

        .action-btn-glass:hover {
            background: rgba(14, 165, 233, 0.15);
            border-color: #0ea5e9;
            transform: translateY(-2px);
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
            text-transform: uppercase;
        }

        .btn-sentra:hover {
            background: #0284c7;
            transform: translateY(-2px);
        }

        .close-icon {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 30px;
            color: #f43f5e;
            text-decoration: none;
            transition: 0.3s;
        }
        .close-icon:hover { transform: rotate(90deg) scale(1.1); }

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
            max-width: 560px;
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
    <div class="box">
        <button type="button" class="info-btn" id="infoOpenBtn" aria-label="Információ">i</button>
        <a href="<?php echo htmlspecialchars($final_x_url); ?>" class="close-icon" title="Bezárás">&times;</a>
        <h2 class="text-center mb-5">BIZTONSÁGI <em>KÖZPONT</em></h2>

        <form method="POST" autocomplete="off">
            <label>Azonosítás</label>
            <input type="email" name="uemail" placeholder="Add meg az email címed..." class="form-control" required>
            <button type="submit" name="identify_user" class="btn btn-sentra">FOLYTATÁS</button>
        </form>

        <?php if($error === 'no_user'): ?>
            <div class="mt-4 status-msg msg-error">❌ Nincs ilyen email regisztrálva!</div>
        <?php endif; ?>

        <hr style="opacity: 0.1; margin: 30px 0;">
        <p class="text-center small text-white-50 m-0">
            Eszébe jutott? <a href="login.php" style="color:#0ea5e9; text-decoration:none; font-weight:700;">Belépés</a>
        </p>
    </div>

    <div class="info-modal" id="infoModal" aria-hidden="true">
        <div class="info-modal__box" role="dialog" aria-modal="true" aria-labelledby="infoTitle">
            <div class="info-modal__close" id="infoCloseBtn" aria-label="Bezárás">&times;</div>
            <div class="info-modal__title" id="infoTitle">Elfelejtett jelszó</div>
            <div class="info-modal__text">Ne aggódj, segítünk visszajutni! Válaszd a számodra kényelmesebb azonosítást:

1. E-mail alapú visszaállítás
Küldünk egy biztonsági linket az e-mail címedre, amivel azonnal megadhatsz egy új jelszót.

2. Biztonsági kérdés
Emlékszel még a profilodnál megadott titkos kérdésre? Válaszold meg helyesen, és már bent is vagy!</div>
        </div>
    </div>

    <script>
        const infoModal = document.getElementById('infoModal');
        const infoOpenBtn = document.getElementById('infoOpenBtn');
        const infoCloseBtn = document.getElementById('infoCloseBtn');
        if (infoOpenBtn && infoModal) infoOpenBtn.addEventListener('click', () => infoModal.classList.add('open'));
        if (infoCloseBtn && infoModal) infoCloseBtn.addEventListener('click', () => infoModal.classList.remove('open'));
        if (infoModal) infoModal.addEventListener('click', (e) => { if (e.target === infoModal) infoModal.classList.remove('open'); });
    </script>
</body>
</html>
