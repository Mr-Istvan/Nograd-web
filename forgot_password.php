<?php
/**
 * Nógrád Csodák - Jelszó Helyreállítási Központ
 * Verzió: 3.8 (Enterprise Glassmorphism Edition)
 * * BIZTONSÁGI ÉS DIZÁJN JELLEMZŐK:
 * - ✅ Sikeres művelet visszajelzés
 * - ❌ Hibaüzenet kezelés ikonokkal
 * - ⏱️ Token lejárati figyelmeztetés
 * - Dual-Auth: Email Token VAGY Biztonsági Kérdés
 */

require_once __DIR__ . '/init.php';

// ==========================================================
// 1. OKOS MEMÓRIA ÉS NAVIGÁCIÓ (X GOMB CÉLPONT)
// ==========================================================
$exclude_pages = ['login.php', 'reg_id.php', 'forgot_password.php', 'forg_pw.php', 'reg_process.php', 'login_process.php', 'reset.php'];

if (isset($_SERVER['HTTP_REFERER'])) {
    $from_path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $from_page = basename($from_path);
    if (!in_array($from_page, $exclude_pages) && !empty($from_page)) {
        $_SESSION['user_origin_url'] = $_SERVER['HTTP_REFERER'];
    }
}
$final_x_url = $_SESSION['user_origin_url'] ?? 'index.php';

// ==========================================================
// 2. FOLYAMAT IRÁNYÍTÁS (STEP CONTROL)
// ==========================================================
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = $_GET['error'] ?? "";
$msg = $_GET['msg'] ?? "";

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
        header("Location: forgot_password.php?step=1&error=no_user");
        exit();
    }
}

if (isset($_POST['method_email'])) {
    if (!isset($_SESSION['reset_uid'])) {
        header("Location: forgot_password.php");
        exit();
    }

    $uid = $_SESSION['reset_uid'];
    $uemail = $_SESSION['reset_email'];
    $uname = $_SESSION['reset_display_name'];

    $token = bin2hex(random_bytes(32));
    $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

    $upd_stmt = mysqli_prepare($conn, "UPDATE felhasznalok SET reset_token = ?, reset_expires = ? WHERE uid = ?");
    mysqli_stmt_bind_param($upd_stmt, "ssi", $token, $expires, $uid);

    if (mysqli_stmt_execute($upd_stmt)) {
        require_once 'send_reset_mail.php';
        if (sendResetEmail($uemail, $uname, $token)) {
            header("Location: forgot_password.php?step=5&msg=mail_sent");
        } else {
            header("Location: forgot_password.php?step=2&error=mail_fail");
        }
    }
    exit();
}

if (isset($_POST['check_answer'])) {
    $user_answer = mysqli_real_escape_string($conn, strtolower(trim($_POST['uanswer'])));
    $uid = $_SESSION['reset_uid'] ?? 0;

    $res = mysqli_query($conn, "SELECT usecret_a FROM felhasznalok WHERE uid = '$uid' LIMIT 1");
    $row = mysqli_fetch_assoc($res);

    if ($row && $user_answer === strtolower($row['usecret_a'])) {
        header("Location: forgot_password.php?step=4");
        exit();
    } else {
        header("Location: forgot_password.php?step=3&error=wrong_ans");
        exit();
    }
}

if (isset($_POST['save_pw'])) {
    $p1 = $_POST['p1'];
    $p2 = $_POST['p2'];

    if (strlen($p1) < 6 || $p1 !== $p2) {
        header("Location: forgot_password.php?step=4&error=invalid_pw");
        exit();
    } else {
        $hashed_pw = password_hash($p1, PASSWORD_DEFAULT);
        $uid = $_SESSION['reset_uid'];
        mysqli_query($conn, "UPDATE felhasznalok SET upw = '$hashed_pw', reset_token = NULL, reset_expires = NULL WHERE uid = $uid");

        unset($_SESSION['reset_uid'], $_SESSION['reset_q'], $_SESSION['reset_email'], $_SESSION['reset_display_name']);
        header("Location: login.php?msg=pw_updated");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó Helyreállítás - Nógrád Csodák</title>
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

        #info-box {
            position: fixed;
            top: 25px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999999;
            width: 90vw;
            max-width: 450px;
            pointer-events: none;
        }

        .status-msg {
            background: rgba(10, 15, 30, 0.75) !important;
            backdrop-filter: blur(15px) !important;
            border-radius: 20px !important;
            padding: 15px 25px !important;
            text-align: center !important;
            color: #fff !important;
            font-size: 15px !important;
            font-weight: 600 !important;
            box-shadow: 0 15px 40px rgba(0,0,0,0.8) !important;
            margin-bottom: 10px;
            pointer-events: auto;
            animation: slideDownFade 0.4s ease-out;
        }

        .msg-success { border: 1px solid rgba(74, 222, 128, 0.5) !important; }
        .msg-error { border: 1px solid rgba(239, 68, 68, 0.5) !important; }

        @keyframes slideDownFade {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
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
            box-shadow: 0 25px 50px rgba(0,0,0,0.8);
            z-index: 10;
        }

        @media only screen and (max-width: 768px) {
            body {
                background-color: #000;
                background-image: url('img/nograd_background_mobile.png');
                background-repeat: no-repeat;
                background-attachment: scroll;
                background-position: center center;
                background-size: cover;
            }
        }

        h2 { font-weight: 800; letter-spacing: 1px; text-transform: uppercase; color: #fff; }
        h2 em { color: #0ea5e9; font-style: normal; }

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

        .btn-sentra:hover { background: #0284c7; transform: translateY(-2px); }

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

        .info-modal.show {
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

    <div id="info-box">
        <?php if($msg == 'mail_sent'): ?>
            <div id="fade-msg" class="status-msg msg-success">
                ✅ E-mail sikeresen elküldve!
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div id="fade-msg" class="status-msg msg-error">
                <?php
                    if($error == 'no_user') echo "❌ Nincs ilyen email regisztrálva!";
                    if($error == 'wrong_ans') echo "❌ Hibás biztonsági válasz!";
                    if($error == 'invalid_pw') echo "❌ A jelszavak nem egyeznek!";
                    if($error == 'mail_fail') echo "❌ Hiba az email küldésekor!";
                ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="box">
        <button type="button" class="info-btn" id="infoOpenBtn" aria-label="Információ">i</button>
        <a href="<?= htmlspecialchars($final_x_url) ?>" class="close-icon">&times;</a>
        <h2 class="text-center mb-5">BIZTONSÁGI <em>KÖZPONT</em></h2>

        <form method="POST" autocomplete="off">
            <?php if($step == 1): ?>
                <label>Azonosítás</label>
                <input type="email" name="uemail" placeholder="Add meg az email címed..." class="form-control" required>
                <button type="submit" name="identify_user" class="btn btn-sentra">FOLYTATÁS</button>

            <?php elseif($step == 2): ?>
                <label class="mb-3 text-center">Hogyan igazolod magad?</label>
                <p class="text-center text-white-50 small mb-4">Ne aggódj, segítünk visszajutni! Válaszd a számodra kényelmesebb azonosítást:</p>

                <button type="submit" name="method_email" class="action-btn-glass">
                    <i class="fa fa-paper-plane" style="color:#0ea5e9"></i>
                    <div>
                        <span>E-mail alapú visszaállítás</span>
                        <small>Küldünk egy biztonsági linket az e-mail címedre, amivel azonnal megadhatsz egy új jelszót.</small>
                    </div>
                </button>

                <a href="forgot_password.php?step=3" style="text-decoration:none">
                    <div class="action-btn-glass">
                        <i class="fa fa-user-shield" style="color:#0ea5e9"></i>
                        <div>
                            <span>Biztonsági kérdés</span>
                            <small>Emlékszel még a profilodnál megadott titkos kérdésre? Válaszold meg helyesen, és már bent is vagy!</small>
                        </div>
                    </div>
                </a>

            <?php elseif($step == 3): ?>
                <label>Ellenőrző kérdés</label>
                <p class="text-center my-4" style="font-size: 18px; font-weight: 700;">
                    "<?= $_SESSION['reset_q'] ?>"
                </p>
                <input type="text" name="uanswer" placeholder="A válaszod..." class="form-control" required>
                <button type="submit" name="check_answer" class="btn btn-sentra">ELLENŐRZÉS</button>

            <?php elseif($step == 4): ?>
                <label>Új jelszó megadása</label>
                <input type="password" name="p1" placeholder="Új jelszó" class="form-control" required>
                <input type="password" name="p2" placeholder="Új jelszó újra" class="form-control" required>
                <button type="submit" name="save_pw" class="btn btn-sentra">MENTÉS</button>

            <?php elseif($step == 5): ?>
                <div class="text-center py-4">
                    <i class="fa fa-envelope-circle-check" style="font-size: 60px; color: #4ade80; margin-bottom: 20px;"></i>
                    <h4>Postáztuk a linket! 📩</h4>
                    <p class="text-white-50 small mt-3">Ellenőrizd a Spam mappát is! A link 1 óráig érvényes.</p>
                    <a href="login.php" class="btn btn-sentra mt-4">VISSZA A BELÉPÉSHEZ</a>
                </div>
            <?php endif; ?>
        </form>

        <?php if($step < 5): ?>
            <hr style="opacity: 0.1; margin: 30px 0;">
            <p class="text-center small text-white-50 m-0">
                Eszébe jutott? <a href="login.php" style="color:#0ea5e9; text-decoration:none; font-weight:700;">Belépés</a>
            </p>
        <?php endif; ?>
    </div>

    <div class="info-modal" id="infoModal" aria-hidden="true">
        <div class="info-modal__box" role="dialog" aria-modal="true" aria-labelledby="infoTitle">
            <div class="info-modal__close" id="infoCloseBtn" role="button" tabindex="0" aria-label="Bezárás">&times;</div>
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
        if (infoOpenBtn && infoModal) infoOpenBtn.addEventListener('click', () => infoModal.classList.add('show'));
        if (infoCloseBtn && infoModal) infoCloseBtn.addEventListener('click', () => infoModal.classList.remove('show'));
        if (infoModal) infoModal.addEventListener('click', (e) => { if (e.target === infoModal) infoModal.classList.remove('show'); });
        if (infoCloseBtn) infoCloseBtn.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') infoCloseBtn.click(); });

        setTimeout(() => {
            const msg = document.getElementById('fade-msg');
            if (msg) {
                msg.style.transition = "opacity 0.8s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 800);
            }
        }, 4000);
    </script>
</body>
</html>
