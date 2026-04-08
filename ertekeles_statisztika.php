<?php
// ertekeles_statisztika.php
require_once __DIR__ . '/init.php';

// Adatok lekérése a statisztikához
$stats_sql = "SELECT (SELECT COUNT(id) FROM latogatok) as osszes_latogato, eszam, atlag, likes FROM ErtekelesekSzama LIMIT 1";
$stats_res = mysqli_query($conn, $stats_sql);
$stats = mysqli_fetch_assoc($stats_res) ?: ['osszes_latogato' => 0, 'eszam' => 0, 'atlag' => 0, 'likes' => 0];
?>

<div class="stat-fixed-wrapper">
    <div class="emoji-stat-box">
        <span>👁️ <?= number_format($stats['osszes_latogato'], 0, ',', ' ') ?></span>
        <span>👥 <?= $stats['eszam'] ?></span>
        <span>⭐ <?= number_format($stats['atlag'], 1, ',', '.') ?></span>
        <span>👍 <span id="likes-count-display"><?= $stats['likes'] ?></span></span>
    </div>
</div>

<style>
/* KÜLSŐ LÁTHATATLAN KERET TULAJDONSÁGAI */
.stat-fixed-wrapper {
    position: fixed;
    display: flex;
    z-index: 9998; /* A valuta (9999) alá/mellé, hogy ne takarjon rá semmire */
    pointer-events: none; /* Hogy a mellette lévő üres, átlátszó részen át tudj kattintani az oldalra */
}

/* A BELSŐ LILA/KÉK DOBOZ (#4c50c0) */
.emoji-stat-box {
    pointer-events: auto; /* A dobozra újra lehessen kattintani, ha kell */
    display: inline-flex;
    align-items: center;
    gap: 12px; /* Kicsit kisebb rés, hogy jól mutasson a sarokban */
    background-color: #4c50c0; /* A kért egyedi szín */
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 5px; /* Enyhe kerekítés */
    padding: 6px 12px;
    color: #fff;
    font-size: 13px; /* Kicsit kisebb betű, hogy kényelmesen elférjen */
    font-weight: bold;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.3); /* Finom árnyék felfelé */
}

/* PC NÉZET: Fixen a bal oldali sávba (sidebar) tolva! */
@media (min-width: 768px) {
    .stat-fixed-wrapper {
        left: 250px; /* Balról 15px margó, hogy ne érjen teljesen a széléhez */
        bottom: 20px; /* A valuta sáv PC-s magassága felett (ezt növelheted, ha feljebb akarod) */
        justify-content: flex-start; /* BALRA IGAZÍTÁS */
    }
}

/* MOBIL NÉZET: Középen, fixen 41px-re az aljától (a valuta felett) */
@media (max-width: 767px) {
    .stat-fixed-wrapper {
        left: 1px;
        width: 100%;
        bottom: 20px; /* A valuta sáv felett mobilon */
        justify-content: flex-start; /* Mobilon középre rakja */
    }
}
</style>