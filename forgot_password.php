<?php
require_once __DIR__ . '/init.php';

// Aktuális lépés és hiba meghatározása
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = isset($_GET['error']) ? $_GET['error'] : "";

// --- 1. LÉPÉS FELDOLGOZÁSA (Email ellenőrzés) ---
if (isset($_POST['check_user'])) {
    $uemail = mysqli_real_escape_string($conn, trim($_POST['uemail']));
    $res = mysqli_query($conn, "SELECT uid, usecret_q FROM felhasznalok WHERE uemail = '$uemail' LIMIT 1");
    
    if ($row = mysqli_fetch_assoc($res)) {
        $_SESSION['reset_uid'] = $row['uid'];
        $_SESSION['reset_q'] = $row['usecret_q'];
        echo "<script>window.location.replace('forgot_password.php?step=2');</script>";
        exit();
    } else {
        echo "<script>window.location.replace('forgot_password.php?step=1&error=no_user');</script>";
        exit();
    }
}

// --- 2. LÉPÉS FELDOLGOZÁSA (Titkos válasz) ---
if (isset($_POST['check_answer'])) {
    if (!isset($_SESSION['reset_uid'])) { 
        echo "<script>window.location.replace('forgot_password.php?step=1');</script>"; 
        exit(); 
    }
    
    $valasz = mysqli_real_escape_string($conn, strtolower(trim($_POST['uanswer'])));
    $res = mysqli_query($conn, "SELECT usecret_a FROM felhasznalok WHERE uid = '".$_SESSION['reset_uid']."'");
    $row = mysqli_fetch_assoc($res);

    if ($valasz === $row['usecret_a']) {
        echo "<script>window.location.replace('forgot_password.php?step=3');</script>";
        exit();
    } else {
        echo "<script>window.location.replace('forgot_password.php?step=2&error=wrong_ans');</script>";
        exit();
    }
}

// --- 3. LÉPÉS FELDOLGOZÁSA (Új jelszó mentése) ---
if (isset($_POST['save_pw'])) {
    if (!isset($_SESSION['reset_uid'])) { 
        echo "<script>window.location.replace('forgot_password.php?step=1');</script>"; 
        exit(); 
    }

    $p1 = $_POST['p1'];
    $p2 = $_POST['p2'];

    if (strlen($p1) < 6 || $p1 !== $p2) {
        echo "<script>window.location.replace('forgot_password.php?step=3&error=invalid_pw');</script>";
        exit();
    } else {
        $upw = password_hash($p1, PASSWORD_DEFAULT);
        $uid = $_SESSION['reset_uid'];
        mysqli_query($conn, "UPDATE felhasznalok SET upw = '$upw' WHERE uid = '$uid'");
        
        unset($_SESSION['reset_uid'], $_SESSION['reset_q']);
        echo "<script>window.location.replace('login.php?msg=pw_updated');</script>";
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelszó helyreállítás - Nógrád</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background: #121212; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Open Sans', sans-serif; margin: 0; }
        .box { width: 400px; padding: 30px; background: rgba(255,255,255,0.05); border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; margin-bottom: 15px; height: 45px; }
        .form-control:focus { background: rgba(255,255,255,0.15); color: white; border-color: #45489a; box-shadow: none; }
        
        /* Fő kék gomb */
        .btn-sentra { background: #45489a; color: white; width: 100%; height: 50px; border: none; font-weight: bold; border-radius: 8px; transition: 0.3s; margin-bottom: 10px; text-transform: uppercase; }
        .btn-sentra:hover { background: #5659b1; }

        /* PIROS VISSZA GOMB */
        .btn-back-red { background-color: #ff4d4d; color: #fff; border: none; width: 100%; height: 45px; font-weight: 600; border-radius: 8px; transition: 0.3s; text-decoration: none; display: flex; align-items: center; justify-content: center; text-transform: uppercase; }
        .btn-back-red:hover { background-color: #ff3333; color: #fff; }

        .err { background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #ff4d4d; padding: 10px; border-radius: 8px; text-align: center; margin-bottom: 15px; font-size: 13px; }
        label { font-size: 12px; color: #45489a; font-weight: bold; text-transform: uppercase; margin-bottom: 5px; display: block; }
        
        /* Emeltebb kék link stílus a Bejelentkezéshez */
        .link-login { color: #45489a !important; text-decoration: none; font-weight: 800; transition: 0.3s; }
        .link-login:hover { color: #6165d7 !important; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box">
        <h2 class="text-center mb-4">ÚJ <em>JELSZÓ</em></h2>

        <?php if($error): ?>
            <div class="err">
                <?php 
                    if($error == 'no_user') echo "❌ Nincs ilyen email regisztrálva!";
                    if($error == 'wrong_ans') echo "❌ Hibás biztonsági válasz!";
                    if($error == 'invalid_pw') echo "❌ A jelszavak nem egyeznek vagy túl rövidek!";
                ?>
            </div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <?php if($step == 1): ?>
                <label>1. Lépés: Azonosítás</label>
                <input type="email" name="uemail" placeholder="Regisztrált email címed" class="form-control" required>
                <button type="submit" name="check_user" class="btn btn-sentra">Folytatás</button>

            <?php elseif($step == 2): ?>
                <label>2. Lépés: Biztonsági kérdés</label>
                <p class="text-center my-3"><b><?php echo $_SESSION['reset_q']; ?></b></p>
                <input type="text" name="uanswer" placeholder="A válaszod" class="form-control" required>
                <button type="submit" name="check_answer" class="btn btn-sentra">Ellenőrzés</button>

            <?php elseif($step == 3): ?>
                <label>3. Lépés: Új jelszó</label>
                <input type="password" name="p1" placeholder="Új jelszó (min. 6 karakter)" class="form-control" required>
                <input type="password" name="p2" placeholder="Új jelszó újra" class="form-control" required>
                <button type="submit" name="save_pw" class="btn btn-sentra">Mentés</button>
            <?php endif; ?>
            
            <a href="login.php" class="btn-back-red">Vissza</a>
        </form>
        
        <hr style="opacity: 0.1;">
        <p class="text-center small text-white-50">
            Eszébe jutott? <a href="login.php" class="link-login">Bejelentkezés</a>
        </p>
    </div>
</body>
</html>