<?php
require_once __DIR__ . '/init.php';

// 1. Gyorsítótár (cache) teljes tiltása
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
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

if ($user && !isset($_SESSION['uid'])) {
    $_SESSION['uid'] = $user['uid'];
}
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
        body { background: #121212; font-family: 'Open Sans'; color: white; display: flex; flex-direction: column; align-items: center; padding: 20px 10px; min-height: 100vh; }
        .profile-container { width: 100%; max-width: 420px; }
        .glass-card { background: rgba(255,255,255,0.05); padding: 25px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .profile-img-wrap { width: 100px; height: 100px; margin: 0 auto 15px; border: 3px solid #0dcaf0; border-radius: 18px; overflow: hidden; background: #222; }
        .profile-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
        .form-control { background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.2); color: white !important; border-radius: 10px; font-size: 14px; padding: 10px 15px; }
        .form-control:focus { background: rgba(255,255,255,0.12); border-color: #0dcaf0; box-shadow: none; }
        .form-control[readonly] { background: rgba(0,0,0,0.3); color: #6c757d !important; cursor: not-allowed; }
        .btn-update { background: #0dcaf0; color: black; font-weight: 700; border: none; border-radius: 0 10px 10px 0; padding: 0 15px; font-size: 11px; text-transform: uppercase; transition: 0.3s; }
        .btn-update:hover { background: #0bb5d9; transform: scale(1.02); }
        .btn-pw { background: #0dcaf0; color: black; font-weight: 700; border: none; width: 100%; border-radius: 10px; height: 45px; font-size: 13px; text-transform: uppercase; margin-top: 10px; transition: 0.3s; }
        .btn-pw:hover { background: #0bb5d9; box-shadow: 0 0 15px rgba(13, 202, 240, 0.4); }
        h2 em { font-style: normal; color: #0dcaf0; }
        hr { opacity: 0.1; margin: 25px 0; }
        label { margin-bottom: 5px; font-weight: 600; }
        /* Rejtett csapda a böngészőnek */
        .hidden-trap { position: absolute; visibility: hidden; width: 0; height: 0; overflow: hidden; }
    </style>
</head>
<body>

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
        
        <div class="mb-4 position-relative">
            <div class="hidden-trap">
                <input type="text" name="fake_user_name_to_avoid_autofill">
            </div>
            <label class="small text-warning">Becenév</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['uusername']); ?>" readonly tabindex="-1">
        </div>

        <form action="update_profile.php" method="POST" class="mb-4" autocomplete="off">
            <div class="hidden-trap">
                <input type="email" name="fake_email_to_avoid_autofill">
            </div>
            <label class="small text-secondary">Email cím frissítése</label>
            <div class="input-group">
                <input type="email" name="uemail_new" class="form-control" 
                       value="<?php echo htmlspecialchars($user['uemail'] ?? ''); ?>" 
                       autocomplete="one-time-code" required> 
                <button type="submit" name="save_email" class="btn btn-update">Mentés</button>
            </div>
        </form>

        <form action="update_profile.php" method="POST" enctype="multipart/form-data" class="mb-4">
            <label class="small text-secondary">Új profilkép feltöltése</label>
            <div class="input-group">
                <input type="file" name="uavatar" class="form-control" style="border-radius: 10px 0 0 10px;" required>
                <button type="submit" name="save_avatar" class="btn btn-update">OK</button>
            </div>
        </form>

        <hr>

        <form action="update_profile.php" method="POST" id="pwForm" autocomplete="off">
            <div class="hidden-trap">
                <input type="password" name="fake_password_to_avoid_autofill">
            </div>
            <label class="small text-secondary">Biztonságos jelszóváltás</label>
            <input type="password" name="old_pw" class="form-control mb-2" placeholder="Jelenlegi jelszó" autocomplete="new-password" required>
            <input type="password" name="new_pw" id="p1" class="form-control mb-2" placeholder="Új jelszó" autocomplete="new-password" required>
            <input type="password" name="new_confirm" id="p2" class="form-control mb-3" placeholder="Új jelszó újra" autocomplete="new-password" required>
            <button type="submit" name="save_pw" class="btn btn-pw shadow">Módosítás mentése</button>
        </form>
    </div>

    <div class="text-center small">
        <a href="#" class="text-info text-decoration-none" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='index.php'; } return false;">← Vissza</a>
        <span class="mx-2 text-muted">|</span>
        <a href="logout.php" class="text-danger text-decoration-none">Kijelentkezés</a>
    </div>
</div>

<script>
    document.getElementById('pwForm').onsubmit = function(e) {
        const p1 = document.getElementById('p1').value;
        const p2 = document.getElementById('p2').value;
        if(p1 !== p2) {
            e.preventDefault();
            alert("A két új jelszó nem egyezik meg!");
        }
    };

    const urlParams = new URLSearchParams(window.location.search);
    const msg = urlParams.get('msg');
    if (msg === 'email_kesz') alert("Sikeres email frissítés!");
    if (msg === 'kep_kesz') alert("Profilkép frissítve!");
    if (msg === 'pw_kesz') alert("Jelszó sikeresen módosítva!");
    if (urlParams.has('error')) alert("Hiba történt!");
</script>

</body>
</html>
