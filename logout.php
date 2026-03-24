<?php
// Logout során NE fusson le session timeout redirect logika.
// Itt csak session + DB kapcsolat kell.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    // Kilépés idejének mentése az adatbázisba
    mysqli_query($conn, "UPDATE felhasznalok SET ulogoutdata = NOW() WHERE uid = '$uid'");
}

// Munkamenet teljes törlése
session_unset();
session_destroy();

// Inaktivitás időbélyeg is törlődjön (biztonság kedvéért)
if (isset($_SESSION['last_activity'])) {
    unset($_SESSION['last_activity']);
}

// Visszairányítás a főoldalra
header("Location: index.php");
exit();
?>
