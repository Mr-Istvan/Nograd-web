<?php
/**
 * VALUTA MODUL - FULL AUTOMATA PROFESSZIONÁLIS VERZIÓ
 * Verzió: 2.7 (60s frissítés, végtelenített görgetés, fix eltolás)
 */

// 1. MOTOR RÉSZ - Adatbázis és Frissítés
if (!isset($conn)) {
    // Az init.php betöltése (egy mappával feljebb)
    require_once __DIR__ . '/../init.php'; 
}

// --- KONFIGURÁCIÓ ---
$api_key = "0371648dd0ea39ce59e22996";
$frissites_gyakorisaga = 86400; // 24 óra (86400s)

// Utolsó frissítés ellenőrzése
$check_sql = mysqli_query($conn, "SELECT MAX(lup_date) as utolso FROM api_valuta");
$check_row = mysqli_fetch_assoc($check_sql);
$utolso_idopont = strtotime($check_row['utolso'] ?? '2026-04-06');
$most = time();


if (($most - $utolso_idopont) >= $frissites_gyakorisaga) {
    $url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/HUF";
    $raw = @file_get_contents($url);
    $data = json_decode($raw, true);

    if ($data && $data['result'] === 'success') {
        $rates = $data['conversion_rates'];
        $valutak = ['EUR', 'USD', 'GBP', 'AUD', 'CZK', 'DKK', 'JPY', 'CAD', 'PLN', 'NOK', 'RUB', 'RON', 'CHF', 'SEK', 'RSD', 'TRY'];

        foreach ($valutak as $kod) {
            if (isset($rates[$kod])) {
                $v = (float)$rates[$kod];
                // UPSERT: elmentjük az újat, a régit átmozgatjuk az elozo_ertek oszlopba
                $sql = "INSERT INTO api_valuta (valuta_kod, elozo_ertek, valto_ertek, lup_date) 
                        VALUES ('$kod', $v, $v, NOW()) 
                        ON DUPLICATE KEY UPDATE 
                        elozo_ertek = valto_ertek, 
                        valto_ertek = $v, 
                        lup_date = NOW()";
                mysqli_query($conn, $sql);
            }
        }
        $utolso_idopont = time(); // Frissítjük a változót a kijelzéshez
    }
}

// 2. ADATOK LEKÉRÉSE A KIJELZÉSHEZ
$valuta_lekerdezes = mysqli_query($conn, "SELECT * FROM api_valuta ORDER BY valuta_kod ASC");
$van_adat = ($valuta_lekerdezes && mysqli_num_rows($valuta_lekerdezes) > 0);
?>

<style>
    /* A KONTÉNER - Fixen alul, 250px-es eltolással */
    .led-fix-footer {
        position: fixed; 
        bottom: 0; 
        left: 250px; 
        width: calc(100% - 250px); 
        height: 24px; 
        background: #000;
        z-index: 9999;
        border-top: 1px solid #fff;
        display: flex;
        align-items: center;
        overflow: hidden;
        box-sizing: border-box;
        cursor: pointer;
    }

    .led-track {
        display: inline-block;
        white-space: nowrap;
        padding-left: 100%; /* Kívülről indul */
        animation: led-scroll 95s linear infinite;
        font-family: 'Courier New', Courier, monospace;
        font-size: 21px;
        line-height: 0; 
        font-weight: bold;
        color: #FFB300;
        text-shadow: 0 0 5px rgba(255, 179, 0, 0.5);
    }

    /* TREND SZÍNEK */
    .up { color: #00D2FF !important; }   /* Kék emelkedés */
    .down { color: #FF3131 !important; } /* Piros csökkenés */
    .sep { color: #fdfdfdd1; margin: 0 24px; }
    .valuta-nev { color: #5e63ebed; }
    
    /* UTOLSÓ FRISSÍTÉS IDŐPONTJA (Debug) */
    .last-refresh-time {
        position: absolute;
        right: 10px;
        bottom: 2px;
        font-size: 11px;
        color: #666666e7;
        z-index: 10001;
        font-family: Arial, sans-serif;
    }

    @keyframes led-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }

    .led-fix-footer:hover .led-track {
        animation-play-state: paused;
    }

    /* Mobilnézet: eltolás megszüntetése */
    @media (max-width: 768px) {
        .led-fix-footer {
            left: 0;
            width: 100%;
        }
    }
</style>

<div class="led-fix-footer" id="tickerBox">
    <div class="last-refresh-time">L.U.P: <?php echo date("H:i:s", $utolso_idopont); ?></div>
    <div class="led-track" id="tickerTrack">
        <?php
        if ($van_adat) {
            $output = "";
            while ($row = mysqli_fetch_assoc($valuta_lekerdezes)) {
                $kod = $row['valuta_kod'];
                if ($row['valto_ertek'] > 0) {
                    // Mivel HUF alapú az API, átváltjuk (1 / érték)
                    $kozep = 1 / $row['valto_ertek'];
                    $elozo = ($row['elozo_ertek'] > 0) ? 1 / $row['elozo_ertek'] : $kozep;
                    
                    // Vételi és eladási ár képzése
                    $veszek = number_format($kozep * 0.985, 1, ',', ' ');
                    $adok = number_format($kozep * 1.015, 1, ',', ' ');
                    
                    // Nyíl és szín meghatározása
                    $trend = ($kozep >= $elozo) ? 'up' : 'down';
                    $nyil = ($trend == 'up') ? '▲' : '▼';

                    $output .= " <span class='valuta-nev'>$kod</span> ";
                    $output .= "$veszek <span class='$trend'>$nyil</span> ";
                    $output .= "$adok <span class='$trend'>$nyil</span> ";
                    $output .= "<span class='sep'>//</span>";
                }
            }
            // DUPLÁZÁS: Így nem lesz üres rész a csíkban, amikor az eleje már kiment
            echo $output . $output;
        } else {
            echo "<span style='color:#fff;'>ADATOK FRISSÍTÉSE...</span>";
        }
        ?>
    </div>
</div>

<script>
    (function() {
        const tBox = document.getElementById('tickerBox');
        const tTrack = document.getElementById('tickerTrack');
        if (tBox && tTrack) {
            // Mobil érintés védelem
            tBox.addEventListener('touchstart', () => { tTrack.style.animationPlayState = 'paused'; }, {passive: true});
            tBox.addEventListener('touchend', () => { tTrack.style.animationPlayState = 'running'; }, {passive: true});
        }
    })();
</script>