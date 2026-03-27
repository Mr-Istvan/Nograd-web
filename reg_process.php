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

// Adatok tisztítása
$uname     = mysqli_real_escape_string($conn, trim($_POST['uname'] ?? ''));
$uusername = mysqli_real_escape_string($conn, trim($_POST['uusername'] ?? ''));
$uemail    = mysqli_real_escape_string($conn, trim($_POST['uemail'] ?? ''));
$pass1     = $_POST['pass1'] ?? '';
$pass2     = $_POST['pass2'] ?? '';
$usecret_q = mysqli_real_escape_string($conn, $_POST['usecret_q'] ?? '');
$usecret_a = mysqli_real_escape_string($conn, strtolower(trim($_POST['usecret_a'] ?? '')));

// 1. Üres mezők ellenőrzése
if (empty($uname) || empty($uusername) || empty($uemail) || empty($pass1)) {
    backWithError('empty');
}

// 2. Jelszó ellenőrzések
if ($pass1 !== $pass2) {
    backWithError('match');
}
if (strlen($pass1) < 6) {
    backWithError('short');
}

// 3. Foglaltság ellenőrzése
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
$sql = "INSERT INTO felhasznalok (uname, uusername, uemail, upw, usess, ustatus, usecret_q, usecret_a, uregdata) 
        VALUES ('$uname', '$uusername', '$uemail', '$upw', '$usess', 'A', '$usecret_q', '$usecret_a', NOW())";

if (mysqli_query($conn, $sql)) {
    // SIKER: Átirányítás a loginra a sikerüzenet kódjával
    echo "<script>window.location.replace('login.php?msg=reg_kesz');</script>";
    exit();
} else {
    backWithError('db_error');
}
?>