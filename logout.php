<?php
// Logout során NE fusson le session timeout redirect logika.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// 1. MEGHATÁROZZUK A VISSZATÉRÉSI CÍMET
$redirect_to = 'index.php';

if (isset($_SERVER['HTTP_REFERER'])) {
    // Ha nem a profilról lépünk ki, visszamehetünk az előző oldalra
    if (strpos($_SERVER['HTTP_REFERER'], 'profile.php') === false) {
        $redirect_to = $_SERVER['HTTP_REFERER'];
    }
}

if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    // Kilépés idejének mentése az adatbázisba
    mysqli_query($conn, "UPDATE felhasznalok SET ulogoutdata = NOW() WHERE uid = '$uid'");
}

// Munkamenet teljes törlése
session_unset();
session_destroy();

// Inaktivitás időbélyeg törlése
if (isset($_SESSION['last_activity'])) {
    unset($_SESSION['last_activity']);
}

// 2. VISSZAIRÁNYÍTÁS
header("Location: " . $redirect_to);
exit();
?>