<?php
require_once __DIR__ . '/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Adatok megtisztítása a biztonság kedvéért
    $uemail = mysqli_real_escape_string($conn, $_POST['uemail']);
    $unev = mysqli_real_escape_string($conn, $_POST['unev']);

    // Lekérdezzük, létezik-e ilyen páros az adatbázisban
    $sql = "SELECT uid FROM felhasznalok WHERE uemail = '$uemail' AND unev = '$unev'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Elmentjük a sessionbe, hogy kinek a jelszavát módosítjuk
        $_SESSION['reset_uid'] = $row['uid'];
        header("Location: uj_jelszo.php"); // Itt fogja megadni az újat
        exit();
    } else {
        $error = "Helytelen adatok! Nincs ilyen felhasználó ezzel az e-maillel.";
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Elfelejtett jelszó</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/fontAwesome.css">
</head>
<body style="background: #222; color: white; display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div class="card bg-dark p-4" style="width: 400px; border: 2px solid #0d6efd; border-radius: 15px;">
        <h2 class="text-center mb-4">Jelszó pótlása 🐸</h2>
        <form method="POST">
            <div class="mb-3">
                <label><i class="fa fa-user"></i> Felhasználónév</label>
                <input type="text" name="unev" class="form-control bg-secondary text-white border-0" required>
            </div>
            <div class="mb-3">
                <label><i class="fa fa-envelope"></i> E-mail cím</label>
                <input type="email" name="uemail" class="form-control bg-secondary text-white border-0" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold">ELLENŐRZÉS</button>
        </form>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger mt-3 py-2 small"><?php echo $error; ?></div>
        <?php endif; ?>
        <a href="login.php" class="text-center d-block mt-3 text-info text-decoration-none">Vissza a belépéshez</a>
    </div>
</body>
</html>
