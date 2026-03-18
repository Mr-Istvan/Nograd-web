<?php
if ($_SERVER['SERVER_NAME'] == 'localhost') {
    // XAMPP adatok (otthoni fejlesztéshez)
    $szerver = "localhost";
    $felhasznalo = "root";
    $jelszo = "";
    $adatbazis = "admins";
} else {
    // Nethely adatok (az éles szerverhez)
    // A képeid alapján: pnograd felhasználó és mysql.nethely.hu szerver
    $szerver = "mysql.nethely.hu";
    $felhasznalo = "pnograd";
    $jelszo = "KIM202605"; 
    $adatbazis = "pnograd";
}

// Kapcsolat létrehozása
$conn = mysqli_connect($szerver, $felhasznalo, $jelszo, $adatbazis);

// Kapcsolat ellenőrzése
if (!$conn) {
    die("Hiba a csatlakozáskor: " . mysqli_connect_error());
}

// Karakterkódolás beállítása (hogy az ékezetek szépek legyenek)
mysqli_set_charset($conn, "utf8mb4");
?>