<?php
/**
 * init.php
 * Közös belépési pont minden olyan oldalnak, ahol session-t használsz.
 *
 * Miért jó?
 * - Nem kell minden PHP fájl tetejére ugyanazt a session + timeout kódot bemásolni.
 * - Elég 1 sort írni: require_once 'init.php';
 */

/**
 * 1) Session indítása (CSAK ha még nincs aktív)
 *
 * session_status():
 * - PHP_SESSION_NONE    => még nincs session, lehet indítani
 * - PHP_SESSION_ACTIVE  => már fut a session, nem indítjuk újra (különben warning lehet)
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * 2) Inaktivitás figyelés / automatikus kijelentkeztetés
 *
 * A session_timeout.php feladata:
 * - ellenőrzi, hogy mennyi ideje volt utoljára aktivitás
 * - ha lejárt az idő, átirányít logout.php-ra
 */
require_once __DIR__ . '/session_timeout.php';

// Adatbázis kapcsolat (opcionális, de ajánlott ide rakni)
require_once __DIR__ . '/db.php';

/**
 * 3) Adatbázis kapcsolat
 *
 * Ha az adott oldal DB-t használ, elég az init.php-t behúzni és a $conn elérhető lesz.
 * (A db.php jelenleg automatikusan csatlakozik és beállítja az utf8mb4-t.)
 */
