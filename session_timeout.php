<?php
// session_timeout.php
// 1 perc (60s) inaktivitás után automatikus kijelentkeztetés (szerver oldali)

if (session_status() === PHP_SESSION_NONE) {
    // Ezt a fájlt az init.php hívja, az indítja a session-t.
    // Ha ezt közvetlenül include-olnád init nélkül, inkább include-old az init.php-t.
    return;
}

$timeoutSeconds = 60 * 30; // 30 perc inaktivitás után automatikus kijelentkeztetés

// Ha belépett felhasználó, frissítjük az aktivitás időpontját minden kérésnél
if (isset($_SESSION['uid'])) { // user_id helyett uid, mert ezt használod a profilnál
    if ($timeoutSeconds > 0 && isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeoutSeconds) {
        header("Location: logout.php?reason=timeout");
        exit();
    }
    $_SESSION['last_activity'] = time();
}
