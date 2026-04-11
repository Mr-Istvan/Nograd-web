<?php
/**
 * init.php - Nógrád Csodák Rendszermag
 */

// 1. Hibakeresés bekapcsolása (Csak amíg tart a javítás!)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 2. Munkamenet indítása
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Adatbázis betöltése
require_once __DIR__ . '/db.php';
if (!$conn) {
    die("ADATBÁZIS KAPCSOLATI HIBA: " . mysqli_connect_error());
}

require_once __DIR__ . '/session_timeout.php';

// 4. OPTIMALIZÁLT LÁTOGATOTTSÁG SZÁMLÁLÓ
if (isset($conn)) {
    $v_ip   = $_SERVER['REMOTE_ADDR'];
    $v_ua   = $_SERVER['HTTP_USER_AGENT'];
    $v_salt = "NogradCsoda2026_Unique_Like";
    $v_hash = hash('sha256', $v_ip . $v_ua . $v_salt);

    $v_check = mysqli_query($conn, "SELECT id FROM latogatok WHERE fingerprint = '$v_hash' AND datum = CURDATE() LIMIT 1");

    if ($v_check && mysqli_num_rows($v_check) == 0) {
        mysqli_query($conn, "INSERT INTO latogatok (fingerprint, datum) VALUES ('$v_hash', CURDATE())");
    }
}

// 5. AKTIVITÁS ÉS BIZTONSÁG
if (isset($_SESSION['uid'])) {
    $current_uid = (int)$_SESSION['uid'];
    $_SESSION['last_activity'] = time();

    $kick_res = mysqli_query($conn, "SELECT ukick FROM felhasznalok WHERE uid = $current_uid LIMIT 1");
    if ($kick_res && $kick_data = mysqli_fetch_assoc($kick_res)) {
        if ((int)$kick_data['ukick'] === 1) {
            mysqli_query($conn, "UPDATE felhasznalok SET ukick = 0 WHERE uid = $current_uid");
            session_destroy();
            header("Location: login.php?reason=kicked");
            exit();
        }
    }
}
