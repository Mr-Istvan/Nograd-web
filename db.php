<?php
// db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// A Nethely központi címe a legbiztosabb
$szerver     = "mysql.nethely.hu"; 
$felhasznalo = "pnograd";
$jelszo      = "0123456789";
$adatbazis   = "pnograd";
 // Ide azt írd, amit a panelen elmentettél!
//ez sosem enged be a weboldalra!
// Kapcsolódás
if (!function_exists('mysqli_connect')) {
    die("A PHP 'mysqli' kiterjesztés nincs betöltve (XAMPP/php.ini).");
}

$conn = mysqli_connect($szerver, $felhasznalo, $jelszo, $adatbazis);

if (!$conn) {
    // Ne írd ki éles környezetben a részletes hibát, mert érzékeny infót szivárogtathat.
    die("Adatbázis kapcsolódási hiba.");
}

mysqli_set_charset($conn, "utf8mb4");
?>
