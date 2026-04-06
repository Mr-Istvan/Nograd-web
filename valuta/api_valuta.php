<?php
/**
 * VALUTA MODUL - MOTOR ÉS KIJELZŐ EGYBEN
 * Verzió: 2.1 (Színjavított nyilakkal és fix 250px eltolással)
 */

// 1. MOTOR RÉSZ: Adatfrissítés logika
if (isset($_GET['update'])) {
    require_once '../init.php'; // Adatbázis kapcsolat ($conn)

    // --- KVÓTA VÉDELEM ---
    $check_sql = mysqli_query($conn, "SELECT MAX(lup_date) as utolso FROM api_valuta");
    $check_row = mysqli_fetch_assoc($check_sql);
    $utolso_idopont = strtotime($check_row['utolso'] ?? '');
    $mostani_idopont = time();

    // Napi 1 limit (86400 mp)
    if ($utolso_idopont && ($mostani_idopont - $utolso_idopont) < 86400) {
        die("STOP: Az árfolyamok ma már frissítve lettek! (Napi 1 limit aktív)");
    }

    // --- API LEKÉRÉS ---
    $api_key = "0371648dd0ea39ce59e22996";
    $url = "https://v6.exchangerate-api.com/v6/{$api_key}/latest/HUF";

    $raw = @file_get_contents($url);
    $data = json_decode($raw, true);

    if ($data && $data['result'] === 'success') {
        $rates = $data['conversion_rates'];
        $valutak_listaja = ['EUR', 'USD', 'GBP', 'AUD', 'CZK', 'DKK', 'JPY', 'CAD', 'PLN', 'NOK', 'RUB', 'RON', 'CHF', 'SEK', 'RSD', 'TRY'];
        $json_puffer = [];

        foreach ($valutak_listaja as $kod) {
            if (isset($rates[$kod])) {
                $v = $rates[$kod];

                // SQL: UPSERT logika
                $sql = "INSERT INTO api_valuta (valuta_kod, elozo_ertek, valto_ertek, lup_date) 
                        VALUES ('$kod', $v, $v, NOW()) 
                        ON DUPLICATE KEY UPDATE 
                        elozo_ertek = valto_ertek, 
                        valto_ertek = $v, 
                        lup_date = NOW()";
                mysqli_query($conn, $sql);

                $res = mysqli_query($conn, "SELECT elozo_ertek FROM api_valuta WHERE valuta_kod = '$kod'");
                $row = mysqli_fetch_assoc($res);
                $json_puffer[$kod] = [
                    'v' => (float)$v,
                    'e' => (float)$row['elozo_ertek']
                ];
            }
        }
        file_put_contents(__DIR__ . '/valuta.json', json_encode($json_puffer));
        die("SIKER: Az árfolyamok frissítve!");
    } else {
        die("HIBA: Az API nem érhető el.");
    }
}

// 2. KIJELZŐ RÉSZ
$valuta_lekerdezes = mysqli_query($conn, "SELECT * FROM api_valuta ORDER BY valuta_kod ASC");
$van_adat = ($valuta_lekerdezes && mysqli_num_rows($valuta_lekerdezes) > 0);
?>

<style>
    /* A KONTÉNER - Fixen alul, helyet hagyva a menünek */
    .led-fix-footer {
        position: fixed; 
        bottom: 0; 
        left: 250px; /* 250px eltolás a sidebar miatt */
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
        padding-left: 100%;
        animation: led-scroll 75s linear infinite;
        font-family: 'Courier New', Courier, monospace;
        font-size: 21px;
        line-height: 0; 
        font-weight: bold;
        color: #FFB300;
        text-shadow: 0 0 5px rgba(255, 179, 0, 0.5);
    }

    /* SZÍNEK - A nyilakhoz */
    .up { color: #00D2FF !important; }   /* Kék emelkedés */
    .down { color: #FF3131 !important; } /* Piros csökkenés */
    .sep { color: #fdfdfdd1; margin: 0 24px; }
    .valuta-nev { color: #5e63ebed;
; }

    @keyframes led-scroll {
        0% { transform: translateX(0); }
        100% { transform: translateX(-100%); }
    }

    .led-fix-footer:hover .led-track {
        animation-play-state: paused;
    }

    @media (max-width: 768px) {
        .led-fix-footer {
            left: 0;
            width: 100%;
        }
    }

</style>

<div class="led-fix-footer" id="tickerBox">
    <div class="led-track" id="tickerTrack">
        <?php
        if ($van_adat) {
            while ($row = mysqli_fetch_assoc($valuta_lekerdezes)) {
                $kod = $row['valuta_kod'];
                if ($row['valto_ertek'] > 0) {
                    $kozep = 1 / $row['valto_ertek'];
                    $elozo = ($row['elozo_ertek'] > 0) ? 1 / $row['elozo_ertek'] : $kozep;
                    
                    $veszek = number_format($kozep * 0.985, 1, ',', ' ');
                    $adok = number_format($kozep * 1.015, 1, ',', ' ');
                    
                    // Meghatározzuk a színt (osztályt) és a nyilat
                    $trend = ($kozep >= $elozo) ? 'up' : 'down';
                    $nyil = ($trend == 'up') ? '▲' : '▼';

                    // A nyilakat belerakjuk a span-ba, hogy színesek legyenek
                    echo " <span class='valuta-nev'>$kod</span> ";
                    echo "$veszek <span class='$trend'>$nyil</span> ";
                    echo "$adok <span class='$trend'>$nyil</span> ";
                    echo "<span class='sep'>//</span>";
                }
            }
        } else {
            echo "<span style='color:#fff;'>FRISSÍTÉS SZÜKSÉGES... (/?update=1)</span>";
        }
        ?>
    </div>
</div>

<script>
    (function() {
        const tBox = document.getElementById('tickerBox');
        const tTrack = document.getElementById('tickerTrack');
        if (tBox && tTrack) {
            tBox.addEventListener('touchstart', () => { tTrack.style.animationPlayState = 'paused'; }, {passive: true});
            tBox.addEventListener('touchend', () => { tTrack.style.animationPlayState = 'running'; }, {passive: true});
        }
    })();
</script>