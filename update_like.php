<?php
// update_like.php
require_once __DIR__ . '/init.php';

header('Content-Type: application/json');

// 1. Ujjnyomat generálása (Salted Fingerprint)
// Ugyanazt a sót használjuk, mint a megjelenítőben!
$ip = $_SERVER['REMOTE_ADDR'];
$ua = $_SERVER['HTTP_USER_AGENT'];
$salt = "NogradCsoda2026_Unique_Like"; 
$fingerprint = hash('sha256', $ip . $ua . $salt);

$success = false;

// 2. Ellenőrizzük az adatbázisban
$check = mysqli_query($conn, "SELECT lid FROM web_like WHERE ip_hash = '$fingerprint' LIMIT 1");

if (mysqli_num_rows($check) == 0) {
    // Ha még nem lájkolt: Mentés
    $insert = mysqli_query($conn, "INSERT INTO web_like (ip_hash) VALUES ('$fingerprint')");
    $success = (bool) $insert;
}

// 3. Lekérjük a friss darabszámot a VIEW-ból (amit a képeken mutattál)
$res = mysqli_query($conn, "SELECT likes FROM ErtekelesekSzama LIMIT 1");
$row = mysqli_fetch_assoc($res);
$newLikes = $row ? (int) $row['likes'] : 0;

// Válasz küldése a JavaScriptnek
echo json_encode([
    'success' => $success,
    'new_likes' => $newLikes
]);
exit;
