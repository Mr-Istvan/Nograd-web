<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/init.php';

// Ha nem a formon keresztül érkezett a kérés, visszaküldjük a regisztrációhoz
if ($_SERVER["REQUEST_METHOD"] != "POST" || !isset($_POST['uemail'])) {
    header("Location: reg_id.php");
    exit();
}

// 1. ADATOK ÁTVÉTELE ÉS TISZTÍTÁSA
$uname_raw     = trim($_POST['uname'] ?? '');
$uusername_raw = trim($_POST['uusername'] ?? '');
$uemail_raw    = trim($_POST['uemail'] ?? '');
$pass1         = (string)($_POST['pass1'] ?? '');
$pass2         = (string)($_POST['pass2'] ?? '');

// Biztonsági kérdés/válasz
$usecret_q_raw = trim($_POST['usecret_q'] ?? '');
$usecret_a_raw = trim($_POST['usecret_a'] ?? '');

$uname     = mysqli_real_escape_string($conn, $uname_raw);
$uusername = mysqli_real_escape_string($conn, $uusername_raw);
$uemail    = mysqli_real_escape_string($conn, $uemail_raw);

$usecret_q = mysqli_real_escape_string($conn, $usecret_q_raw);
$usecret_a = mysqli_real_escape_string($conn, strtolower($usecret_a_raw));

// 2. ELLENŐRZÉSEK

// Kötelező mezők ellenőrzése
if ($uname === '' || $uusername === '' || $uemail === '' || $pass1 === '') {
    echo "<script>alert('Minden kötelező mezőt ki kell tölteni!'); window.location.replace('reg_id.php');</script>";
    exit();
}

// Jelszavak egyeznek?
if ($pass1 !== $pass2) {
    echo "<script>alert('A két jelszó nem egyezik!'); window.location.replace('reg_id.php');</script>";
    exit();
}

// Jelszó hossza (min 6 karakter)
if (strlen($pass1) < 6) {
    echo "<script>alert('A jelszónak legalább 6 karakter hosszúnak kell lennie!'); window.location.replace('reg_id.php');</script>";
    exit();
}

// Jelszó hossza (max 36 karakter)
if (strlen($pass1) > 36) {
    echo "<script>alert('A jelszó maximum 36 karakter lehet!'); window.location.replace('reg_id.php');</script>";
    exit();
}

// Létezik-e már az email vagy a becenév?
$checkUser = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE uemail = '$uemail' OR uusername = '$uusername'");
if (mysqli_num_rows($checkUser) > 0) {
    echo "<script>alert('Ez az email cím vagy becenév már foglalt!'); window.location.replace('reg_id.php');</script>";
    exit();
}

// 3. JELSZÓ TITKOSÍTÁSA ÉS STÁTUSZ
$upw = password_hash($pass1, PASSWORD_DEFAULT);
$ustatus = 'A'; // Alapértelmezett: Aktív

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
$sql = "INSERT INTO felhasznalok (uname, uusername, uemail, upw, usess, ustatus, usecret_q, usecret_a, uregdata) 
        VALUES ('$uname', '$uusername', '$uemail', '$upw', '$usess', '$ustatus', '$usecret_q', '$usecret_a', NOW())";

if (mysqli_query($conn, $sql)) {
    echo "<script>
            alert('Sikeres regisztráció! Azonosítód: $usess'); 
            window.location.replace('login.php');
          </script>";
} else {
    // Adatbázis hiba kezelése
    echo "<script>
            alert('Hiba történt a mentés során: " . mysqli_real_escape_string($conn, mysqli_error($conn)) . "'); 
            window.location.replace('reg_id.php');
          </script>";
}
?>