<?php
// db.php (mysqli) — visszaállítva, hogy működjön a meglévő kódbázissal.

if (($_SERVER['REMOTE_ADDR'] ?? '') === '127.0.0.1' || ($_SERVER['REMOTE_ADDR'] ?? '') === '::1') {
    // HELYI BEÁLLÍTÁSOK (XAMPP)
    $szerver = "localhost";
    $felhasznalo = "root";
    $jelszo = "";
    $adatbazis = "nograd_db";
} else {
    // ÉLES BEÁLLÍTÁSOK (Nethely szerver)
    $szerver = "sql.nethely.hu";
    $felhasznalo = "pnograd";
    $jelszo = "KIM202605";
    $adatbazis = "pnograd";
}

$conn = mysqli_connect($szerver, $felhasznalo, $jelszo, $adatbazis);

if (!$conn) {
    die("Sajnos nem sikerült a csatlakozás: " . mysqli_connect_error());
}
?>
