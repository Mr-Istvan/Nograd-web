<?php
// session_timeout.php
// 1 perc (60s) inaktivitás után automatikus kijelentkeztetés (szerver oldali)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$timeoutSeconds = 1800; // 30 perc (30 * 60 másodperc)

// Ha belépett felhasználó, frissítjük az aktivitás időpontját minden kérésnél
if (isset($_SESSION['user_name'])) { 
    if ($timeoutSeconds > 0 && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutSeconds) {
        header("Location: logout.php?reason=timeout");
        exit();
    }
    $_SESSION['last_activity'] = time();
}
