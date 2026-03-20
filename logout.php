<?php
// Logout során NE fusson le session timeout redirect logika.
// Itt csak session + DB kapcsolat kell.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

if (isset($_SESSION['uid'])) {
    $uid = (int) $_SESSION['uid'];
    // Kilépés idejének mentése az adatbázisba
    mysqli_query($conn, "UPDATE felhasznalok SET ulogoutdata = NOW() WHERE uid = $uid");
}

// Inaktivitás időbélyeg is törlődjön (biztonság kedvéért)
if (isset($_SESSION['last_activity'])) {
    unset($_SESSION['last_activity']);
}

// Munkamenet teljes törlése
session_unset();
session_destroy();

// Session cookie törlése is (különben a böngészőben megmaradhat a cookie)
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Visszairányítás a főoldalra
header("Location: index.php");
exit();
?>
