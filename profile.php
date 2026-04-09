<?php
require_once __DIR__ . '/init.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['user_name'])) { 
    header("Location: login.php"); 
    exit(); 
}
if (!isset($_GET['msg']) && !isset($_GET['error'])) {
    if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile.php') === false) {
        $_SESSION['back_to_original'] = $_SERVER['HTTP_REFERER'];
    }
}
$final_back_url = $_SESSION['back_to_original'] ?? 'index.php';

$session_user = $_SESSION['user_name'];
$stmt = mysqli_prepare($conn, "SELECT * FROM felhasznalok WHERE uusername = ?");
mysqli_stmt_bind_param($stmt, "s", $session_user);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();

$avatar_file = !empty($user['uavatar']) ? $user['uavatar'] : '';
$avatar_path = (!empty($avatar_file) && file_exists(__DIR__ . '/img/profiles/' . $avatar_file)) ? 'img/profiles/' . $avatar_file : 'img/auto_profile.png';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($user['uusername'] ?? 'Profil'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        .profile-container {
            position: relative;
            z-index: 10;
            width: calc(100% - 30px);
            max-width: 400px;
        }

        .glass-card {
            position: relative;
            background: rgba(10, 15, 30, 0.7);
            border-radius: 25px;
            border: 1px solid rgba(14, 165, 233, 0.3);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.8);
            padding: 40px;
            margin-bottom: 20px;
        }

        .profile-img-wrap {
            width: 100px;
            height: 100px;
            margin: 0 auto 15px;
            border: 3px solid #0ea5e9;
            border-radius: 18px;
            overflow: hidden;
            background: #222;
        }

        .profile-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
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
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
            height: 50px;
            border-radius: 12px;
            font-size: 14px;
            padding: 0 15px;
        }

        .form-control::placeholder {
            color: rgba(234, 234, 234, 0.65) !important;
            opacity: 1;
        }

        .form-control:focus {
            background: rgba(215, 215, 215, 0.1) !important;
            border-color: #0ea5e9 !important;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.3) !important;
            color: #fff !important;
        }

        .form-control[readonly] {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: rgba(255, 255, 255, 0.55) !important;
            cursor: not-allowed;
        }

        .btn-update,
        .btn-pw {
            background: #0ea5e9;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            text-transform: uppercase;
            transition: 0.3s;
            height: 50px;
        }

        .btn-update {
            border-radius: 0 12px 12px 0;
            padding: 0 15px;
            font-size: 11px;
        }

        .btn-pw {
            width: 100%;
            font-size: 13px;
            margin-top: 10px;
        }

        .btn-update:hover,
        .btn-pw:hover {
            background: #0284c7;
            transform: translateY(-2px);
        }

        .status-msg {
            padding: 12px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 14px;
            background: rgba(10, 15, 30, 0.75);
            backdrop-filter: blur(15px);
            color: #fff;
        }

        .msg-success { border: 1px solid rgba(74, 222, 128, 0.5); }
        .msg-error { border: 1px solid rgba(239, 68, 68, 0.5); }

        h2 {
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #fff;
        }

        h2 em {
            font-style: normal;
            color: #0ea5e9;
        }

        .close-icon {
            position: absolute;
            top: 20px;
            right: 25px;
            font-size: 30px;
            color: #f43f5e;
            text-decoration: none !important;
            line-height: 1;
            transition: 0.3s;
        }

        .close-icon:hover {
            transform: rotate(90deg) scale(1.1);
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="glass-card text-center">
            <a href="<?php echo htmlspecialchars($final_back_url); ?>" class="close-icon" title="Bezárás">&times;</a>
            <div class="profile-img-wrap">
                <img src="<?php echo htmlspecialchars($avatar_path . '?t=' . time()); ?>" alt="Avatar">
            </div>
            <h2 class="m-0"><?php echo htmlspecialchars($user['uname'] ?? 'Névtelen'); ?></h2>
            <small class="text-info">@<?php echo htmlspecialchars($user['uusername'] ?? ''); ?></small>
        </div>

        <div class="glass-card">
            <h5 class="mb-4 text-center">Beállí<em>tások</em></h5>

            <div id="msg-box">
                <?php if (isset($_GET['msg'])): ?>
                    <div class="status-msg msg-success">
                        <?php 
                            if($_GET['msg'] == 'email_kesz') echo "✅ Email cím frissítve!";
                            if($_GET['msg'] == 'kep_kesz') echo "✅ Profilkép frissítve!";
                            if($_GET['msg'] == 'pw_kesz') echo "✅ Jelszó módosítva!";
                            if($_GET['msg'] == 'secret_ok') echo "✅ Biztonsági kérdés és válasz mentve!";
                        ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="status-msg msg-error">
                        <?php
                            if($_GET['error'] == 'wrong_pw') echo "❌ Hibás jelenlegi jelszó!";
                            elseif($_GET['error'] == 'match_or_empty') echo "❌ A jelszavak nem egyeznek!";
                            elseif($_GET['error'] == 'avatar_fail') echo "❌ A profilkép feltöltése sikertelen!";
                            elseif($_GET['error'] == 'wrong_secret') echo "❌ Hibás biztonsági kérdés vagy válasz!";
                            else echo "❌ Hiba történt!";
                        ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="small text-warning">Felhasználónév</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['uusername']); ?>" readonly tabindex="-1">
            </div>

            <form action="update_profile.php" method="POST" class="mb-4">
                <label class="small text-secondary">Email cím</label>
                <div class="input-group">
                    <input type="email" name="uemail_new" class="form-control" value="<?php echo htmlspecialchars($user['uemail'] ?? ''); ?>" required> 
                    <button type="submit" name="save_email" class="btn btn-update">Mentés</button>
                </div>
            </form>

            <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="mb-4">
                <label class="small text-secondary">Új profilkép</label>
                <div class="input-group">
                    <input type="file" name="uavatar" class="form-control" style="border-radius: 10px 0 0 10px;">
                    <button type="submit" name="save_avatar" class="btn btn-update">OK</button>
                </div>
            </form>

            <hr style="opacity:0.1">

            <form action="update_profile.php" method="POST" id="pwForm">
                <label class="small text-secondary">Jelszóváltás</label>
                <input type="password" name="old_pw" class="form-control mb-2" placeholder="Jelenlegi jelszó" required>
                <input type="password" name="new_pw" id="p1" class="form-control mb-2" placeholder="Új jelszó" required>
                <input type="password" name="new_confirm" id="p2" class="form-control mb-3" placeholder="Új jelszó újra" required>
                <button type="submit" name="save_pw" class="btn btn-pw shadow">Módosítás mentése</button>
            </form>

            <hr style="opacity:0.1">

            <form action="update_profile.php" method="POST" id="secretForm">
                <label class="small text-secondary">Biztonsági kérdés</label>
                <input type="text" name="usecret_q" class="form-control mb-2" placeholder="Add meg a biztonsági kérdést" required>
                <label class="small text-secondary">Biztonsági válasz</label>
                <input type="text" name="usecret_a" class="form-control mb-3" placeholder="Add meg a biztonsági választ" required>
                <button type="submit" name="save_secret" class="btn btn-pw shadow">Biztonsági adatok mentése</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('pwForm').onsubmit = function(e) {
            if (document.getElementById('p1').value !== document.getElementById('p2').value) {
                e.preventDefault();
                document.getElementById('msg-box').innerHTML = '<div class="status-msg msg-error">❌ A jelszavak nem egyeznek!</div>';
            }
        };
        setTimeout(() => { document.querySelectorAll('.status-msg').forEach(el => el.remove()); }, 4000);
    </script>
</body>
</html>
