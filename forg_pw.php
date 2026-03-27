<?php
require_once __DIR__ . '/init.php';

// Ha már van folyamatban lévő reset, töröljük a biztonság kedvéért az elején
if (!isset($_GET['error'])) {
    unset($_SESSION['reset_uid']);
    unset($_SESSION['reset_q']);
}

$error = "";
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'no_user') $error = "Helytelen adatok! Nincs ilyen felhasználó.";
}

// FELDOLGOZÁS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uemail = mysqli_real_escape_string($conn, trim($_POST['uemail']));
    $unev = mysqli_real_escape_string($conn, trim($_POST['unev']));

    $sql = "SELECT uid, usecret_q FROM felhasznalok WHERE uemail = '$uemail' AND uname = '$unev' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $_SESSION['reset_uid'] = $row['uid'];
        $_SESSION['reset_q'] = $row['usecret_q'];
        
        // JS átirányítás a 2. lépésre (Vissza-gomb védelem)
        echo "<script>window.location.replace('forgot_password.php?step=2');</script>";
        exit();
    } else {
        // JS átirányítás hiba esetén is
        echo "<script>window.location.replace('forg_pw.php?error=no_user');</script>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #121212; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Open Sans', sans-serif; }
        .card { width: 400px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 15px; padding: 25px; backdrop-filter: blur(10px); }
        .form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; margin-bottom: 15px; }
        .form-control:focus { background: rgba(255,255,255,0.15); color: white; border-color: #45489a; box-shadow: none; }
        .btn-sentra { background: #45489a; color: white; width: 100%; border-radius: 8px; font-weight: bold; border: none; padding: 10px; }
        .btn-sentra:hover { background: #5659b1; color: white; }
        .error-msg { color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px; font-size: 14px; border: 1px solid rgba(255, 77, 77, 0.2); }
        label { font-size: 13px; color: #aaa; margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="card">
        <h2 class="text-center mb-4">JELSZÓ <em>PÓTLÁSA</em> 🐸</h2>
        
        <?php if ($error): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label><i class="fa fa-user me-1"></i> Teljes név (amit megadtál)</label>
                <input type="text" name="unev" class="form-control" placeholder="pl. Kovács János" required>
            </div>
            <div class="mb-3">
                <label><i class="fa fa-envelope me-1"></i> Regisztrált e-mail</label>
                <input type="email" name="uemail" class="form-control" placeholder="pelda@email.hu" required>
            </div>
            <button type="submit" class="btn btn-sentra">FOLYTATÁS A KÉRDÉSHEZ</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="login.php" class="text-white-50 small" style="text-decoration: none;">Vissza a bejelentkezéshez</a>
        </div>
    </div>
</body>
</html>