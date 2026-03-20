<?php
require_once __DIR__ . '/init.php';

$step = 1; 
$error = "";

// 1. LÉPÉS: Felhasználó azonosítása csak EMAIL alapján
if (isset($_POST['check_user'])) {
    $uemail = mysqli_real_escape_string($conn, $_POST['uemail']);

    // Megnézzük, van-e ilyen email az adatbázisban
    $sql = "SELECT uid, usecret_q FROM felhasznalok WHERE uemail = '$uemail' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        // Ha van ilyen email, elmentjük a sessionbe az ID-t és a kérdést
        $_SESSION['reset_uid'] = $row['uid'];
        $_SESSION['reset_q'] = $row['usecret_q'];
        $step = 2; 
    } else {
        $error = "Ezzel az email címmel nincs regisztrált felhasználó!";
    }
}

// 2. LÉPÉS: Titkos válasz ellenőrzése
if (isset($_POST['check_answer'])) {
    $uid = $_SESSION['reset_uid'];
    $valasz = mysqli_real_escape_string($conn, $_POST['uanswer']);
    
    // Itt ellenőrizzük, hogy a válasz (usecret_a) egyezik-e
    $res = mysqli_query($conn, "SELECT * FROM felhasznalok WHERE uid = '$uid' AND usecret_a = '$valasz' LIMIT 1");
    if (mysqli_num_rows($res) > 0) {
        $step = 3; 
    } else {
        $error = "Hibás válasz a titkos kérdésre!";
        $step = 2;
    }
}

// 3. LÉPÉS: Új jelszó mentése
if (isset($_POST['save_pw'])) {
    $uid = $_SESSION['reset_uid'];
    $p1 = $_POST['p1'];
    $p2 = $_POST['p2'];

    if ($p1 === $p2 && !empty($p1)) {
        $hash = password_hash($p1, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE felhasznalok SET upw = '$hash' WHERE uid = '$uid'");
        
        // Takarítás: töröljük a reset-hez használt session adatokat
        unset($_SESSION['reset_uid']);
        unset($_SESSION['reset_q']);
        
        echo "<script>alert('Sikeres jelszómódosítás!'); window.location.href='login.php';</script>";
        exit();
    } else {
        $error = "A két jelszó nem egyezik meg!";
        $step = 3;
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Jelszó visszaállítása - Nógrád</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { background: #121212; color: white; display: flex; align-items: center; justify-content: center; height: 100vh; font-family: 'Open Sans', sans-serif; margin: 0; }
        .reset-card { width: 400px; background: rgba(255,255,255,0.05); padding: 30px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .form-control { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; margin-bottom: 15px; border-radius: 8px; }
        .form-control:focus { background: rgba(255,255,255,0.15); color: white; border-color: #45489a; box-shadow: none; }
        .btn-sentra { background: #45489a; color: white; font-weight: bold; width: 100%; border: none; padding: 12px; border-radius: 8px; text-transform: uppercase; transition: 0.3s; }
        .btn-sentra:hover { background: white; color: #45489a; }
        h2 em { color: #45489a; font-style: normal; }
        .alert-danger { background: rgba(255, 77, 77, 0.2); border: 1px solid #ff4d4d; color: #ff4d4d; }
    </style>
</head>
<body>
    <div class="reset-card">
        <h2 class="text-center mb-4">Jelszó <em>Vissza</em></h2>
        
        <?php if($error): ?> <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div> <?php endif; ?>

        <?php if($step == 1): ?>
            <form method="POST">
                <p class="text-center small text-white-50">Add meg az email címedet a kezdéshez!</p>
                <input type="email" name="uemail" placeholder="Email címed" class="form-control" required>
                <button type="submit" name="check_user" class="btn btn-sentra">Folytatás</button>
            </form>

        <?php elseif($step == 2): ?>
            <form method="POST">
                <p class="text-center small text-white-50">Biztonsági kérdésed:</p>
                <h5 class="text-center mb-4" style="color: #45489a;"><?php echo $_SESSION['reset_q']; ?></h5>
                <input type="text" name="uanswer" placeholder="A válaszod" class="form-control" required autocomplete="off">
                <button type="submit" name="check_answer" class="btn btn-sentra">Ellenőrzés</button>
            </form>

        <?php elseif($step == 3): ?>
            <form method="POST">
                <p class="text-center small text-white-50">Add meg az új jelszavadat!</p>
                <input type="password" name="p1" placeholder="Új jelszó" class="form-control" required>
                <input type="password" name="p2" placeholder="Új jelszó újra" class="form-control" required>
                <button type="submit" name="save_pw" class="btn btn-sentra">Jelszó frissítése</button>
            </form>
        <?php endif; ?>

        <div class="text-center mt-3">
            <a href="#" class="small text-white-50 text-decoration-none hover-white" onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='login.php'; } return false;">Vissza</a>
        </div>
    </div>
</body>
</html>
