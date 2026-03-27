<?php
require_once __DIR__ . '/init.php';

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

if (!isset($_SESSION['user_name'])) { 
    header("Location: login.php"); 
    exit(); 
}

$session_user = $_SESSION['user_name'];
$stmt = mysqli_prepare($conn, "SELECT * FROM felhasznalok WHERE uusername = ?");
mysqli_stmt_bind_param($stmt, "s", $session_user);
mysqli_stmt_execute($stmt);
$user = mysqli_stmt_get_result($stmt)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($user['uusername'] ?? 'Profil'); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    <style>
        /* 1. MÓDOSÍTÁS: Alapbeállítások a <style> részben */
        body { 
            background: #000; 
            font-family: 'Open Sans', sans-serif; 
            color: white; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
            padding: 20px 10px; 
            min-height: 100vh; 
            margin: 0;
            overflow-x: hidden; /* A mátrix miatt ne legyen vízszintes görgetés */
        }

        /* 2. ÚJ: A Mátrix háttér fixálása a <style> részben */
        #matrix {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1; 
        }

        /* 3. MÓDOSÍTÁS: A kártyák (Glassmorphism) a <style> részben */
        .glass-card { 
            position: relative;
            z-index: 10;
            background: rgba(0, 0, 0, 0.8) !important; /* Sötétebb alap az olvashatóságért */
            padding: 25px; 
            border-radius: 20px; 
            border: 1px solid rgba(0, 255, 255, 0.3) !important; /* Neon kék keret */
            backdrop-filter: blur(8px); 
            -webkit-backdrop-filter: blur(8px); 
            margin-bottom: 20px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.8); 
        }

        .profile-container { width: 100%; max-width: 420px; }
        .profile-img-wrap { width: 100px; height: 100px; margin: 0 auto 15px; border: 3px solid #0dcaf0; border-radius: 18px; overflow: hidden; background: #222; }
        .profile-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .form-control { 
                background: rgba(255, 255, 255, 0.1) !important; 
                border: 1px solid rgba(0, 255, 255, 0.3) !important; /* Halvány neon kék keret */
                color: white !important; 
                border-radius: 10px; 
                font-size: 14px; 
                padding: 10px 15px; 
        }

            /* EGY EXTRA TRÜKK: A "Jelenlegi jelszó" stb. szövegek (placeholder) színe */
        .form-control::placeholder {
                color: rgba(255, 255, 255, 0.5) !important; 
        }

            /* 2. FÓKUSZ: Amikor beleklikkelsz a mezőbe */
        .form-control:focus { 
                background: rgba(255, 255, 255, 0.15) !important; 
                border-color: #00ffff !important; /* Erős neon kék keret */
                box-shadow: 0 0 10px rgba(0, 255, 255, 0.2) !important; /* Finom ragyogás */
        }

            /* 3. CSAK OLVASHATÓ: A becenév mező, amit nem tudsz átírni */
        .form-control[readonly] { 
                background: rgba(0, 0, 0, 0.5) !important; 
                border: 1px solid rgba(255, 255, 255, 0.1) !important;
                color: #888 !important; /* Szürkébb szöveg, hogy látsszon: ez zárolva van */
                cursor: not-allowed; 
        }
        .btn-update { background: #0dcaf0; color: black; font-weight: 700; border: none; border-radius: 0 10px 10px 0; padding: 0 15px; font-size: 11px; text-transform: uppercase; transition: 0.3s; }
        .btn-pw { background: #0dcaf0; color: black; font-weight: 700; border: none; width: 100%; border-radius: 10px; height: 45px; font-size: 13px; text-transform: uppercase; margin-top: 10px; transition: 0.3s; }
        .status-msg { padding: 12px; border-radius: 12px; text-align: center; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .msg-success { background: rgba(25, 135, 84, 0.2); border: 1px solid #198754; color: #2ecc71; }
        .msg-error { background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff4d4d; }
        h2 em { font-style: normal; color: #0dcaf0; }
        label { margin-bottom: 5px; font-weight: 600; display: block; }
    </style>
</head>
<body>
    <?php include 'matrix_bg.php'; ?>

<div class="profile-container">
    <div class="glass-card text-center">
        <div class="profile-img-wrap">
            <img src="img/profiles/<?php echo !empty($user['uavatar']) ? $user['uavatar'] : 'default.png'; ?>?t=<?php echo time(); ?>" alt="Avatar">
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
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="status-msg msg-error">
                    <?php 
                        if($_GET['error'] == 'wrong_pw') echo "❌ Hibás jelenlegi jelszó!";
                        if($_GET['error'] == 'match_or_empty') echo "❌ A jelszavak nem egyeznek!";
                        else echo "❌ Hiba történt!";
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label class="small text-warning">Becenév</label>
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
                <input type="file" name="uavatar" class="form-control" style="border-radius: 10px 0 0 10px;" required>
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
    </div>

    <div class="text-center small">
        <a href="index.php" class="text-info text-decoration-none" style="font-weight: 700;">← VISSZA</a>
        <span class="mx-2 text-muted">|</span>
        <a href="logout.php" class="text-danger text-decoration-none">Kijelentkezés</a>
    </div>
</div>

<script>
    document.getElementById('pwForm').onsubmit = function(e) {
        if(document.getElementById('p1').value !== document.getElementById('p2').value) {
            e.preventDefault();
            document.getElementById('msg-box').innerHTML = '<div class="status-msg msg-error">❌ A jelszavak nem egyeznek!</div>';
        }
    };
    setTimeout(() => { document.querySelectorAll('.status-msg').forEach(el => el.remove()); }, 4000);
</script>
</body>
</html>