<?php
/**
 * init.php - Nógrád Csodák Rendszermag
 * Ez a fájl felel a munkamenet, az adatbázis és a biztonsági ellenőrzések futtatásáért.
 */

// 1. Munkamenet indítása (ha még nem fut)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Szükséges alapfájlok betöltése
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/session_timeout.php';

// --- ÚJ: LÁTOGATOTTSÁG SZÁMLÁLÓ (Egyedi látogatók mérése naponta) ---
// Csak akkor fut le, ha van élő adatbázis kapcsolat
if (isset($conn)) {
    $v_ip   = $_SERVER['REMOTE_ADDR'];
    $v_ua   = $_SERVER['HTTP_USER_AGENT'];
    $v_salt = "NogradCsoda2026_Unique_Like"; // Biztonsági kulcs (legyen ugyanaz, mint a like-nál)
    $v_hash = hash('sha256', $v_ip . $v_ua . $v_salt);

    // Ellenőrizzük, hogy az adott ujjnyomat járt-e már ma nálunk
    $v_check = mysqli_query($conn, "SELECT id FROM latogatok WHERE fingerprint = '$v_hash' AND datum = CURDATE() LIMIT 1");

    if ($v_check && mysqli_num_rows($v_check) == 0) {
        // Ha ma még nem láttuk, rögzítjük mint új egyedi látogatót
        mysqli_query($conn, "INSERT INTO latogatok (fingerprint, datum) VALUES ('$v_hash', CURDATE())");
    }
}

// 3. AKTIVITÁS ÉS BIZTONSÁGI ELLENŐRZÉSEK
if (isset($_SESSION['uid'])) {
    $current_uid = (int)$_SESSION['uid'];

    // --- UTOLSÓ AKTIVITÁS FRISSÍTÉSE (Munkamenetben) ---
    $_SESSION['last_activity'] = time();

    // --- KICK ELLENŐRZÉS (Ha az admint kidobták a panelről) ---
    $kick_query = "SELECT ukick FROM felhasznalok WHERE uid = $current_uid LIMIT 1";
    $kick_res = mysqli_query($conn, $kick_query);

    if ($kick_res && mysqli_num_rows($kick_res) > 0) {
        $kick_data = mysqli_fetch_assoc($kick_res);

        if ((int)$kick_data['ukick'] === 1) {
            // Kick flag visszaállítása
            mysqli_query($conn, "UPDATE felhasznalok SET ukick = 0 WHERE uid = $current_uid");
            
            // Munkamenet megsemmisítése
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();

            header("Location: login.php?reason=kicked");
            exit();
        }
    }
}
?>