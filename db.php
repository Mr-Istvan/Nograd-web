<?php
// db.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// A Nethely központi címe a legbiztosabb
$szerver     = "mysql.nethely.hu"; 
$felhasznalo = "pnograd";
$adatbazis   = "pnograd";
$jelszo      = "KIM202605"; // Ide azt írd, amit a panelen elmentettél!
//ez sosem enged be a weboldalra!
// Kapcsolódás
$conn = mysqli_connect($szerver, $felhasznalo, $jelszo, $adatbazis);

if (!$conn) {
    die("Hiba: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");

echo "✅ SIKERÜLT! Az adatbázis kapcsolat él.";
?>