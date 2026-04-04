<?php
/**
 * logout.php
 * Kijelentkeztetés, kilépési idő mentése és session törlés.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Adatbázis kapcsolat (fontos az ulogoutdata mentéséhez!)
require_once __DIR__ . '/db.php';

// 2. VISSZATÉRÉSI CÍM ÉS TIMEOUT KEZELÉS
$redirect_to = 'index.php';

if (isset($_GET['reason']) && $_GET['reason'] == 'timeout') {
    // Ha inaktivitás miatt dobtuk ki, a loginra megyünk a hibaüzenettel
    $redirect_to = 'login.php?error=timeout';
}

// 3. KILÉPÉSI IDŐ MENTÉSE (Még mielőtt törölnénk a session-t!)
if (isset($_SESSION['uid'])) {
    $uid = (int)$_SESSION['uid'];
    
    // Itt küldjük el a jelet az adatbázisnak:
    $sql = "UPDATE felhasznalok SET ulogoutdata = NOW() WHERE uid = $uid";
    mysqli_query($conn, $sql);
}

// 4. MUNKAMENET (SESSION) ÉS SÜTIK (COOKIE) TELJES TÖRLÉSE
$_SESSION = array(); // Kiürítjük a változókat

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Itt semmisítjük meg a böngészőben tárolt sütit:
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy(); // Leállítjuk a szerver oldali munkamenetet

// 5. ÁTIRÁNYÍTÁS
header("Location: " . $redirect_to);
exit();
?>