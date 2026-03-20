<?php
require_once __DIR__ . '/init.php';

// Ha nem a formon keresztül érkezett a kérés, visszaküldjük
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['uemail'])) {
    header("Location: register.php");
    exit();
}

// 1. ADATOK ÁTVÉTELE ÉS TISZTÍTÁSA
$uname     = mysqli_real_escape_string($conn, $_POST['uname']);
$uusername = mysqli_real_escape_string($conn, $_POST['uusername']);
$uemail    = mysqli_real_escape_string($conn, $_POST['uemail']);
$pass1     = $_POST['pass1'];
$pass2     = $_POST['pass2'];

// ÚJ: Biztonsági kérdés és válasz átvétele
$usecret_q = mysqli_real_escape_string($conn, $_POST['usecret_q']);
$usecret_a = mysqli_real_escape_string($conn, strtolower(trim($_POST['usecret_a']))); // Kisbetűvel a későbbi könnyű ellenőrzéshez

// 2. ELLENŐRZÉSEK
// Üres mezők?
if (empty($uname) || empty($uusername) || empty($uemail) || empty($pass1) || empty($usecret_q) || empty($usecret_a)) {
    echo "<script>alert('Minden mezőt ki kell tölteni!'); window.location.href='register.php';</script>";
    exit();
}

// Jelszavak egyeznek?
if ($pass1 !== $pass2) {
    echo "<script>alert('A két jelszó nem egyezik!'); window.location.href='register.php';</script>";
    exit();
}

// Jelszó hossza (min 6 karakter)
if (strlen($pass1) < 6) {
    echo "<script>alert('A jelszónak legalább 6 karakter hosszúnak kell lennie!'); window.location.href='register.php';</script>";
    exit();
}

// Létezik-e már az email vagy a becenév?
$checkUser = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE uemail = '$uemail' OR uusername = '$uusername'");
if (mysqli_num_rows($checkUser) > 0) {
    echo "<script>alert('Ez az email cím vagy becenév már foglalt!'); window.location.href='register.php';</script>";
    exit();
}

if (strlen($pass1) > 36) {
    echo "<script>alert('A jelszó maximum 36 karakter lehet!'); window.location.href='register.php';</script>";
    exit();
}

// 3. JELSZÓ TITKOSÍTÁSA ÉS STÁTUSZ
$upw = password_hash($pass1, PASSWORD_DEFAULT);
$ustatus = 'A'; // Aktív

// 4. EGYEDI AZONOSÍTÓ (usess) GENERÁLÁSA
$usess = "";
$talalt_mar_ilyet = true;
while ($talalt_mar_ilyet) {
    $chars = '0123456789abcdefghijklmnopqrstuvwxyz';
    $usess = substr(str_shuffle(str_repeat($chars, 5)), 0, 7);
    $ellenorzes = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE usess = '$usess'");
    if (mysqli_num_rows($ellenorzes) == 0) {
        $talalt_mar_ilyet = false;
    }
}

// 5. BESZÚRÁS AZ ADATBÁZISBA
// Figyelem: Ellenőrizd, hogy az usecret_q és usecret_a oszlopok már léteznek a tábládban!
$sql = "INSERT INTO felhasznalok (uname, uusername, uemail, upw, usess, ustatus, usecret_q, usecret_a, uregdata) 
        VALUES ('$uname', '$uusername', '$uemail', '$upw', '$usess', '$ustatus', '$usecret_q', '$usecret_a', NOW())";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Sikeres regisztráció! Azonosítód: $usess'); 
            window.location.href='login.php';
          </script>";
} else {
    echo "Hiba történt a mentés során: " . mysqli_error($conn);
}
?>
