<?php
// session_timeout.php
// 1 perc (60s) inaktivitás után automatikus kijelentkeztetés (szerver oldali)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeoutSeconds = 0; // 0 = kikapcsolva (nincs automatikus kijelentkeztetés inaktivitás miatt)

// Ha belépett felhasználó, frissítjük az aktivitás időpontját minden kérésnél
if (isset($_SESSION['user_id'])) {
    // Csak akkor léptetünk ki inaktivitás miatt, ha a timeout engedélyezve van (> 0)
    if ($timeoutSeconds > 0 && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutSeconds) {
        // Inaktivitás miatt lejárt: irány logout
        // (Ne itt session_destroy-t csináljunk, a logout.php kezeli a DB frissítést is)
        header("Location: logout.php?reason=timeout");
        exit();
    }

    $_SESSION['last_activity'] = time();
}
