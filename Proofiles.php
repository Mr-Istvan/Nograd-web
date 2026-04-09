<?php
$team = [
    [
        "id"    => "melinda",
        "name"  => "Melinda",
        "role"  => "Frontend & Tartalomkezelő",
        "desc"  => "A látványvilág, a reszponzivitás, az adatbázis (JSON) feltöltése, valamint a multimédiás tartalmak (képek, videók) optimalizálása volt a feladatom.",
        "img"   => "img/Mel!nd@.png"
    ],
    [
        "id"    => "istvan",
        "name"  => "István",
        "role"  => "Backend & Rendszerfejlesztő",
        "desc"  => "A motorháztető alatti részért feleltem: a PHP alapok, a biztonságos session-kezelés és a dinamikus tartalomkiszolgálás az én munkám.",
        "img"   => "img/!STv@n.png"
    ]
];

echo "<style>
    /* MÓDOSÍTVA: overflow-y: auto és overscroll-behavior a frissítéshez */
    body, html { 
        margin: 0; padding: 0; 
        overflow-x: hidden; 
        overflow-y: auto; 
        overscroll-behavior-y: contain; 
        font-family: 'Segoe UI', sans-serif; 
        background: #000; 
        height: 100vh; 
    }
    
    .proof-stage {
        position: relative; width: 100%; height: 100vh;
        background: url('img/background.jpg') no-repeat center center;
        background-size: cover; display: flex; flex-direction: column; justify-content: flex-end;
    }

    .info-overlay-container {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        pointer-events: none;
    }

    .dev-data {
        background: rgba(15, 23, 42, 0.98);
        backdrop-filter: blur(10px);
        border-left: 5px solid #38bdf8;
        padding: 40px !important;
        color: #fff;
        border-radius: 10px;
        display: none;
        pointer-events: auto;
        box-shadow: 0 10px 40px rgba(0,0,0,0.8);
        box-sizing: border-box;
        width: 800px !important; 
        min-width: 800px !important;
        height: auto;
        min-height: 250px;
    }

    .dev-data.active { display: block; animation: slideIn 0.3s ease-out; }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dev-data h3 { font-size: 32px !important; margin: 0; color: #38bdf8; display: block; }
    .dev-data span { font-size: 20px !important; color: #888; font-weight: bold; display: block; margin-bottom: 10px; }
    .dev-data p { 
        font-size: 22px !important; 
        line-height: 1.5 !important; 
        color: #eee; 
        text-align: left;
        margin: 0;
    }

    .dev-wrapper {
        display: flex;
        width: 100%;
        justify-content: space-between;
        padding: 0 50px;
        box-sizing: border-box;
        z-index: 5;
        height: 55vh; 
    }

    .dev-box {
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 45%;
        justify-content: flex-end;
    }

    .dev-img {
        height: 100%;
        width: auto;
        max-width: 100%;
        object-fit: contain;
        filter: brightness(0.7);
        transition: all 0.4s ease;
        transform-origin: bottom center;
    }

    .dev-box.clicked .dev-img {
        filter: brightness(1.1) drop-shadow(0 0 25px rgba(56, 189, 248, 0.6));
        transform: scale(1.05);
    }

    @media (max-width: 820px) {
        .dev-data {
            width: 95vw !important; 
            min-width: 350px !important;
            padding: 20px !important;
        }
        .dev-wrapper {
            padding: 0 10px;
        }
    }

    .close-proof {
        position: fixed; top: 15px; right: 15px; z-index: 1100;
        width: 50px; height: 50px; border: none; border-radius: 50%;
        background: rgba(15, 23, 42, 0.9); color: #fff; font-size: 30px;
        cursor: pointer; display: flex; align-items: center; justify-content: center;
    }
</style>";

echo "<div class='proof-stage'>";
    echo "<button class='close-proof' type='button' onclick=\"window.history.back()\">×</button>";
    
    echo "<div class='info-overlay-container'>";
    foreach ($team as $d) {
        echo "
        <div class='dev-data' id='data-{$d['id']}'>
            <h3>{$d['name']}</h3>
            <span>{$d['role']}</span>
            <p>{$d['desc']}</p>
        </div>";
    }
    echo "</div>";

    echo "<div class='dev-wrapper'>";
    foreach ($team as $d) {
        echo "
        <div class='dev-box' onclick=\"showInfo('{$d['id']}', this, event)\">
            <img src='{$d['img']}' class='dev-img' alt='{$d['name']}'>
        </div>";
    }
    echo "</div>";
echo "</div>";

echo "<script>
    function showInfo(id, element, event) {
        event.stopPropagation();
        document.querySelectorAll('.dev-data').forEach(info => info.classList.remove('active'));
        document.querySelectorAll('.dev-box').forEach(box => box.classList.remove('clicked'));

        const selectedData = document.getElementById('data-' + id);
        if(selectedData) {
            selectedData.classList.add('active');
            element.classList.add('clicked');
        }
    }

    document.querySelector('.proof-stage').addEventListener('click', function() {
        document.querySelectorAll('.dev-data').forEach(info => info.classList.remove('active'));
        document.querySelectorAll('.dev-box').forEach(box => box.classList.remove('clicked'));
    });

    // --- LEHÚZÁSRA FRISSÍTÉS LOGIKA ---
    let startY = 0;
    document.addEventListener('touchstart', (e) => {
        startY = e.touches[0].pageY;
    }, {passive: true});

    document.addEventListener('touchmove', (e) => {
        const currentY = e.touches[0].pageY;
        const diff = currentY - startY;

        // Ha a lap tetején vagyunk és legalább 180px-et húztuk lefelé
        if (window.scrollY === 0 && diff > 180) {
            location.reload();
        }
    }, {passive: true});
</script>";
?>