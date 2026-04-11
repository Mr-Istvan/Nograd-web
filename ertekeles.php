<?php
// 1. Munkamenet és adatbázis kapcsolat inicializálása
require_once __DIR__ . '/init.php'; 

// 2. Állapot ellenőrzése: Befejezte-e már a felhasználó?
$isFinished = (isset($_GET['msg']) && $_GET['msg'] == 'send');

$kerdesek = [];
// Csak akkor kérjük le a kérdéseket, ha még nincs kész az értékelés
if (!$isFinished) {
    $sql = "SELECT kid, kerdesek FROM Kerdesek ORDER BY kid ASC";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) { $kerdesek[] = $row; }
    }

    // Biztonsági ellenőrzés
    if (count($kerdesek) < 44) {
        die("<h1 style='color:white; text-align:center; margin-top:50px;'>Hiba: Az adatbázis nem tartalmazza mind a 44 kérdést!</h1>");
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nógrádi Csodák - Értékelés</title>
    <style>
        /* --- ALAPOK: Flexbox középre igazítás --- */
        body, html {
            margin: 0; padding: 0; height: 100vh; width: 100%;
            background-color: #000; display: flex; justify-content: center;
            align-items: center; overflow: hidden; font-family: 'Open Sans', sans-serif;
        }

        /* 1. RÉTEG: Háttérkép */
        .matrix-layer { position: fixed; inset: 0; z-index: 1; background: url('img/nograd_background.png') no-repeat center center fixed; background-size: cover; }
        
        @media (max-width: 767px) {
            .matrix-layer { background-image: url('img/nograd_background_mobile.png'); }
        }

        /* 2. RÉTEG: Sötétítő overlay */
        .overlay-layer { 
            position: fixed; inset: 25px; background: rgba(0, 0, 0, 0.5); 
            z-index: 2; pointer-events: none; border-radius: 45px; 
        }

        /* --- 3. RÉTEG: FŐ KONTÉNER (Dizájn beállítások) --- */
        .eval-box {
            position: relative; z-index: 10; 
            background: rgba(214, 225, 226, 0.98); 
            color: #222; 
            border: 2px solid #008080; 
            box-shadow: 0 0 42px rgba(63, 24, 86, 0.91);
            border-radius: 45px; box-sizing: border-box; text-align: center;
            display: flex; flex-direction: column; justify-content: space-between;
            transition: all 0.5s ease;
        }

        /* MÉRETEK */
        @media (min-width: 801px) { .eval-box { width: 800px; height: 450px; padding: 40px; } }
        @media (max-width: 800px) { .eval-box { width: 600px; height: 370px; padding: 30px; } }
        
        /* MOBIL: 500px MAGASSÁG */
        @media (max-width: 767px) { 
            .eval-box { 
                width: 330px; 
                min-height: 500px; 
                padding: 25px; 
                margin: 15px; 
            } 
            .step-content { max-height: 330px !important; }
        }

        /* A PIROS X - (10px fel, 10px jobbra eltolva) */
        .close-exit {
            position: absolute; top: 10px; right: 15px;
            background: rgba(180, 0, 0, 0.2); border: 4px solid #b32424;
            color: #b32424; width: 38px; height: 38px; line-height: 34px;
            text-align: center; text-decoration: none; font-weight: bold;
            font-size: 24px; border-radius: 10px; cursor: pointer; z-index: 100;
        }
        .close-exit:hover { background: #b32424; color: #fff; box-shadow: 0 0 15px #b32424; }

        /* BETŰK ÉS GOMBOK */
        h1 { color: #113862; text-shadow: 0 0 5px rgba(0, 92, 92, 0.2); font-size: 30px; margin: 0; }
        
        .btn-sentra {
            display: inline-block; text-decoration: none;
            background: transparent; color: #005c5c; border: 2px solid #005c5c;
            padding: 12px 30px; font-weight: bold; text-transform: uppercase;
            border-radius: 35px; cursor: pointer; transition: 0.3s; margin-top: 15px;
        }
        .btn-sentra:hover:not(:disabled) { background: #005c5c; color: #fff; box-shadow: 0 0 15px #005c5c; }
        .btn-sentra:disabled { border-color: #999; color: #999; cursor: not-allowed; }

        /* LÉPTETÉS ÉS KÉRDÉSEK */
        .step { display: none; width: 100%; height: 100%; }
        .step.active { display: flex; flex-direction: column; justify-content: center; }
        .step-content { max-height: 250px; overflow-y: auto; padding-right: 10px; margin: 15px 0; text-align: left; }
        .step-content::-webkit-scrollbar { width: 6px; }
        .step-content::-webkit-scrollbar-thumb { background: #005c5c; border-radius: 10px; }

        .question-item { text-align: left; margin-bottom: 15px; border-bottom: 1px solid rgba(0, 0, 0, 0.1); padding-bottom: 10px; }
        
        /* SÖTÉTEBB TÜRKIZ/KÉK BETŰK AZ OLVASHATÓSÁGÉRT */
        .question-text { font-size: 15px; color: #005c5c; font-weight: 700; line-height: 1.4; }
        
        .rating-options { display: flex; gap: 12px; font-size: 14px; color: #333; margin-top: 8px; }
        input[type="radio"] { cursor: pointer; accent-color: #005c5c; transform: scale(1.2); }

        /* KÖSZÖNJÜK KÉPERNYŐ STÍLUSA */
        .thanks-screen {
            display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100%;
        }
        .thanks-screen p { font-size: 18px; color: #333; margin-bottom: 30px; font-weight: 600; }

        .footer-text { font-size: 11px; color: rgba(0, 0, 0, 0.4); padding: 10px; }
    </style>
</head>
<body>

    <!-- NÓGRÁD HÁTTÉR ÉS OVERLAY -->
    <div class="matrix-layer"></div>
    <div class="overlay-layer"></div>

    <div class="eval-box">
        <!-- DINAMIKUS X GOMB: Ha kész, simán visszavisz, ha nincs kész, kérdez -->
        <a href="index.php" class="close-exit" title="Kilépés" 
           onclick="<?php echo $isFinished ? '' : "return confirm('Biztosan kilépsz? A válaszok elvesznek!')"; ?>">X</a>

        <?php if ($isFinished): ?>
            <!-- --- KÖSZÖNJÜK / KIJELENTKEZÉS ÁLLAPOT --- -->
            <div class="thanks-screen">
                <h1>Sikeres beküldés!</h1>
                <p>Köszönjük, hogy időt szántál az értékelésre.</p>
                <a href="index.php" class="btn-sentra">Kilépés a főoldalra</a>
            </div>
        <?php else: ?>
            <!-- --- AKTÍV ÉRTÉKELÉSI FOLYAMAT --- -->
            <h1>Értékelési oldal</h1>

            <form id="evalForm" method="POST" action="process_eval.php" style="height: 100%; display: flex; flex-direction: column;">
                
                <!-- START OLDAL -->
                <div class="step active" id="step-start">
                    <p style="margin-top: 50px; color: #333; font-weight: 600;">Köszönjük, hogy megosztod velünk a véleményed!<br>(Összesen 44 rövid kérdés vár rád)</p>
                    <button type="button" class="btn btn-sentra" onclick="startEvaluation()">Értékelés Megkezdése</button>
                </div>

                <!-- KÉRDÉS BLOKKOK (11x4 kérdés) -->
                <?php for($b = 0; $b < 11; $b++): ?>
                    <div class="step" id="step-block-<?php echo $b; ?>">
                        <div class="step-content">
                            <?php for($i = 0; $i < 4; $i++): 
                                $idx = ($b * 4) + $i; $q = $kerdesek[$idx]; ?>
                                <div class="question-item">
                                    <div class="question-text"><?php echo ($idx+1) . ". " . htmlspecialchars($q['kerdesek']); ?></div>
                                    <div class="rating-options">
                                        <?php for($v = 1; $v <= 5; $v++): ?>
                                            <label><input type="radio" name="answers[<?php echo $q['kid']; ?>]" value="<?php echo $v; ?>" onchange="validateBlock(<?php echo $b; ?>)"> <?php echo $v; ?></label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                        <button type="button" id="btn-next-<?php echo $b; ?>" class="btn btn-sentra" disabled 
                                onclick="<?php echo ($b < 10) ? "changeStep('block-".($b+1)."')" : "this.form.submit()"; ?>">
                            <?php echo ($b < 10) ? "Tovább" : "Értékelés Beküldése"; ?>
                        </button>
                    </div>
                <?php endfor; ?>
            </form>
        <?php endif; ?>

        <div class="footer-text">Nógrádi csodák © 2026</div>
    </div>

    <script>
        // Értékelés elindítása (AJAX)
        function startEvaluation() {
            fetch('process_eval.php?action=start')
                .then(r => r.text())
                .then(d => {
                    const response = d.trim();
                    if (response === "OK") {
                        changeStep('block-0');
                        return;
                    }

                    if (response.startsWith("WAIT|")) {
                        const parts = response.split("|");
                        const hours = parseInt(parts[1], 10) || 0;
                        const minutes = parseInt(parts[2], 10) || 0;
                        alert(`Már értékeltél. Új értékelés ${hours} óra ${minutes} perc múlva lehetséges.`);
                        return;
                    }

                    alert('Nem sikerült elindítani az értékelést.');
                })
                .catch(() => alert('Hiba történt az értékelés indításakor.'));
        }

        // Lépés váltás logikája
        function changeStep(id) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            let target = document.getElementById('step-' + id);
            if(target) target.classList.add('active');
        }

        // 4 válasz kötelező blokkonként
        function validateBlock(b) {
            const block = document.getElementById('step-block-' + b);
            if(!block) return;
            const checked = block.querySelectorAll('input[type="radio"]:checked').length;
            document.getElementById('btn-next-' + b).disabled = (checked < 4);
        }
    </script>
</body>
</html>
