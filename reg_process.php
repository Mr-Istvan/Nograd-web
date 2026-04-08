<?php
require_once __DIR__ . '/init.php';

// Csak POST kérést fogadunk a biztonság érdekében
if ($_SERVER["REQUEST_METHOD"] != "POST") { 
    header("Location: reg_id.php"); 
    exit(); 
}

// Függvény a hibaüzenetek stabil átadásához
function backWithError($err) {
    echo "<script>window.location.replace('reg_id.php?error=$err');</script>";
    exit();
}

// Kötelező mezők beolvasása
$uusername = mysqli_real_escape_string($conn, trim($_POST['uusername'] ?? ''));
$uemail_user = trim($_POST['uemail_user'] ?? '');
$uemail_domain = trim($_POST['uemail_domain'] ?? '');
$uemail_tld = trim($_POST['uemail_tld'] ?? '');
$pass1 = $_POST['pass1'] ?? '';
$pass2 = $_POST['pass2'] ?? '';

$uemail_user = preg_replace('/\s+/', '', $uemail_user);
$uemail_domain = preg_replace('/\s+/', '', $uemail_domain);
$uemail_tld = preg_replace('/\s+/', '', $uemail_tld);
$uemail = strtolower($uemail_user . '@' . $uemail_domain . '.' . $uemail_tld);

// 1. Kötelező mezők ellenőrzése
if (empty($uusername) || empty($uemail_user) || empty($uemail_domain) || empty($uemail_tld) || empty($pass1) || empty($pass2)) {
    backWithError('empty');
}

if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
    backWithError('empty');
}

// 2. Jelszó ellenőrzések
if ($pass1 !== $pass2) {
    backWithError('match');
}
if (strlen($pass1) < 6) {
    backWithError('short');
}

/**
 * 3. Foglaltság ellenőrzése
 */
$check = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE uemail = '$uemail' OR uusername = '$uusername'");
if (mysqli_num_rows($check) > 0) {
    backWithError('taken');
}

// 4. Egyedi usess azonosító generálása
$usess = "";
$talalt = true;
while ($talalt) {
    $usess = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyz'), 0, 7);
    $check_ss = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE usess = '$usess'");
    if (mysqli_num_rows($check_ss) == 0) {
        $talalt = false;
    }
}

// 5. Mentés az adatbázisba
$upw = password_hash($pass1, PASSWORD_DEFAULT);
$sql = "INSERT INTO felhasznalok (uusername, uemail, upw, usess, ustatus, uregdata) 
        VALUES ('$uusername', '$uemail', '$upw', '$usess', 'A', NOW())";

if (mysqli_query($conn, $sql)) {
    echo "<script>window.location.replace('login.php?msg=reg_kesz');</script>";
    exit();
} else {
    backWithError('db_error');
}
?>
