<?php
require_once __DIR__ . '/../init.php';
include '../kozos_menu.php';
include '../kozos_mobile.php';
?>
<?php
// 1. ADATOK LEKÉRDEZÉSE (Biztonságos változónevekkel)
$szallas_sql = "SELECT sz.*, t.telepules, t.tiranyitoszam, szt.sztipus, 
                szolg.szolgreggeli, szolg.szolgfelpanzio, szolg.szolgsport, szolg.szolgwellness
        FROM szallasok AS sz
        LEFT JOIN telepulesek AS t ON sz.tid = t.tid
        LEFT JOIN szallas_tipus AS szt ON sz.sztid = szt.sztid
        LEFT JOIN szolgaltatasok AS szolg ON sz.szszid = szolg.szszid
        ORDER BY sz.szid ASC";

$szallas_result = $conn->query($szallas_sql);
if (!$szallas_result) {
    die("Szálláslekérdezési hiba: " . $conn->error);
}

/* 2. ÁR MIN/MAX LEKÉRDEZÉSE AZ ADATBÁZISBÓL */
$priceBoundsQuery = "SELECT MIN(min_ar) AS min_price, MAX(max_ar) AS max_price FROM szallasok";
$priceBoundsResult = $conn->query($priceBoundsQuery);
if (!$priceBoundsResult) {
    die("Árlekérdezési hiba: " . $conn->error);
}
$priceBoundsRow = $priceBoundsResult->fetch_assoc();
$minDbPrice = (int)($priceBoundsRow['min_price'] ?? 0);
$maxDbPrice = 150000;
if ($maxDbPrice <= 0) {
    $maxDbPrice = 150000;
}
if ($minDbPrice < 0) {
    $minDbPrice = 0;
}

// 3. ADATOK FELDOLGOZÁSA ÉS STATISZTIKA (Még a HTML generálás ELŐTT)
$szallasokData = [];
$osszes_ar = 0;
$ossz_db = 0;

while ($sz_row = $szallas_result->fetch_assoc()) {
    $minAr = (int)($sz_row['min_ar'] ?? 0);
    $maxAr = (int)($sz_row['max_ar'] ?? 0);
    $atlagSzallasAr = (int)round(($minAr + $maxAr) / 2);

    $sz_row['atlag_ar'] = $atlagSzallasAr;
    $sz_row['tisztitott_ar'] = $atlagSzallasAr;
    $osszes_ar += $atlagSzallasAr;
    $ossz_db++;
    $szallasokData[] = $sz_row;
}

$atlag_ar = $ossz_db > 0 ? round($osszes_ar / $ossz_db) : 0;

// 3. SZŰRÉSI KATEGÓRIÁK FÜGGVÉNYEK (A Típus már az adatbázisból jön, így az a függvény törölve lett!)
if (!function_exists('getSzallasCsillag')) {
    function getSzallasCsillag($name) {
        $name = trim((string)$name);
        $map = [
            'Castellum Hotel Hollókő' => 4,
            'Tó Wellness Hotel' => 4,
            'Főnix Wellness Resort' => 4,
            'Kastélyhotel Sasvár' => 4,
            'Calimbra Wellness Hotel' => 4,
            'Salgó Hotel' => 3,
            'Cédrus Club Hotel' => 3,
            'Berceli Kastély' => 3,
            'Eresztvényi Látogatóközpont' => 2,
            'Somoskői Kirándulóközpont' => 2,
            'Börzsönyi Turistaház' => 2,
        ];
        return $map[$name] ?? 0;
    }
}

if (!function_exists('isPremiumSzallas')) {
    function isPremiumSzallas($name, $type) {
        // null-védelem a sztring konverziónál
        $nameSafe = (string)$name;
        $typeSafe = (string)$type;
        return stripos($nameSafe, 'luxury') !== false || stripos($nameSafe, 'resort') !== false || stripos($typeSafe, 'kastély') !== false;
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>NÓGRÁD - Szállások</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/fontAwesome.css">
    <link rel="stylesheet" href="../css/templatemo-style.css">
    <link rel="stylesheet" href="../css/turizm.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="mobile_style.css">

    <style>
        :root {
            --praktika-blue: #fec107;
            --card-bg: #ffffff;
        }

        html, body {
            overflow-x: hidden;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background: url('../img/szallasok_back.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }

        .humor-box {
            background: rgba(0, 0, 0, 0.75) !important;
            color: #fec107 !important;
            padding: 13px 15px;
            border-radius: 50px;
            border: 2px dashed #fec107;
            display: inline-block;
            margin: 20px auto;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0,0,0,0.4);
        }

        .page-content {
            margin-left: 250px !important;
            padding: 20px !important;
            width: calc(100% - 250px) !important;
            max-width: calc(100% - 250px) !important;
            box-sizing: border-box !important;
        }

        .content-section {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .page-content .container-fluid {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            box-sizing: border-box !important;
        }

        .page-content .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        .page-content .col-md-12,
        .page-content .col-md-6,
        .page-content .col-xs-6 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            box-sizing: border-box !important;
        }

        .turizm-card {
            background: rgba(255, 255, 255, 0.85) !important;
            border-left: 10px solid var(--praktika-blue) !important;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            margin-bottom: 30px;
        }

        .turizm-card h3 {
            color: var(--praktika-blue) !important;
            font-weight: 800 !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            display: block !important;
        }

        .table-container {
            background: white;
            padding: 10px;
            border-radius: 8px;
            width: 100%;
            overflow-x: auto;
        }

        .name-cell {
            position: relative;
            padding-bottom: 40px !important;
            vertical-align: middle !important;
            min-width: 250px;
        }

        .btn-info-custom {
            position: absolute;
            right: 5px;
            bottom: 5px;
            background-color: #45489a !important;
            color: white !important;
            border: none;
            padding: 5px 12px;
            font-weight: bold;
            border-radius: 4px;
            transform: scale(0.8);
            transform-origin: right bottom;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn.text-white,
        .btn.text-white i {
            color: #ffffff !important;
            opacity: 1 !important;
        }

        .details-content {
            background: #f9f9f9;
            padding: 15px;
            border: 2px solid #45489a;
            border-radius: 10px;
            margin: 5px 0 15px 0;
        }

        .info-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .info-item {
            min-width: 200px;
            flex: 1;
            font-size: 14px;
        }

        .info-item i {
            color: #45489a;
            margin-right: 8px;
            width: 18px;
            text-align: center;
        }

        .mobile-only-info { display: none; }

        @media (max-width: 1333px) {
            .desktop-hide { display: none !important; }
            .mobile-only-info { display: block; }
        }

        .services-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            border-top: 1px solid #ddd;
            margin-top: 15px;
            padding-top: 15px;
        }

        .service-badge {
            padding: 6px 12px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .range-container {
            position: relative;
            height: 60px;
            width: 100%;
            margin-top: 10px;
        }

        .range-track {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 8px;
            width: 100%;
            background: #ddd;
            border-radius: 4px;
            z-index: 1;
        }

        #range-highlight {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 8px;
            background: #00bcd4;
            z-index: 2;
            border-radius: 4px;
        }

        .slider-input {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            -webkit-appearance: none;
            background: none;
            pointer-events: none;
            margin: 0;
            z-index: 3;
        }

        .slider-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 22px;
            width: 22px;
            border-radius: 50%;
            background: #2c3e50;
            cursor: pointer;
            pointer-events: auto;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }

        .bg-success-custom { background-color: #28a745; }
        .bg-danger-custom { background-color: #dc3545; opacity: 0.8; }

        .stat-card {
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }

        .price-tag {
            background: #17a2b8;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: bold;
        }

        footer.premium-footer {
            padding: 12px 0 !important;
            text-align: center !important;
            clear: both;
            margin-top: 50px !important;
            background: none !important;
            display: block !important;
        }

        footer.premium-footer .footer-inner-wrapper {
            width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
            display: block !important;
            text-align: center !important;
        }

        footer.premium-footer .credits-container {
            width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
            display: block !important;
        }

        footer.premium-footer .credits-link {
            display: inline-block !important;
            width: auto !important;
            margin: 0 auto !important;
        }

        footer.premium-footer p {
            font-family: 'Georgia', serif !important;
            font-style: italic !important;
            color: #000000 !important;
            font-size: 14px !important;
            display: inline-block;
            padding: 10px 25px !important;
            background: rgba(255, 255, 255, 0.9) !important;
            border: 0 !important;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        @media (max-width: 767px) {
            .page-content {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 8px !important;
                padding-top: 50px !important;
            }
            .turizm-card { padding: 5px; }
            .stat-card { padding: 16px; }
            footer.premium-footer { padding: 20px !important; }
            footer.premium-footer p {
                font-size: 12px !important;
                padding: 8px 14px !important;
                max-width: calc(100vw - 40px);
                white-space: normal;
                overflow-wrap: anywhere;
                word-break: break-word;
                line-height: 1.5;
            }
        }
    </style>
</head>
<body class="page-szallas">
    <?= $kozos_menu ?? '' ?>
    <?= $kozos_mobile ?? '' ?>

    <div class="page-content">
        <section class="content-section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1 style="color:white; text-shadow: 2px 2px 8px #000; font-size: 45px;">Nógrádi <em>Szállások</em></h1>
                        <div class="humor-box">"Nógrádban a pálinka a wellness alapköve."</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-xs-6">
                        <div class="stat-card"><h5>Szálláshelyek</h5><h3><?= $ossz_db; ?> db</h3></div>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <div class="stat-card" style="background: #17a2b8;"><h5>Átlagár / éj</h5><h3><?= number_format($atlag_ar, 0, ',', ' '); ?> Ft</h3></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="turizm-card">
                            <h3><i class="fa fa-bed"></i> Szálláskereső és Szűrő</h3>
                            
                            <div class="szuro-panel" style="background: rgba(255,255,255,0.95); padding: 20px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ddd; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                                
                                <div style="margin-bottom: 20px;">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Keresés név vagy település alapján..." style="width: 100%; border: 2px solid #45489a; padding: 10px; border-radius: 5px;">
                                </div>

                                <div style="display: flex; flex-wrap: wrap; gap: 20px;">
                                    <div style="flex: 1; min-width: 200px;">
                                        <h5 style="margin-top:0; color:#45489a; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Típus</h5>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-type" value="hotel"> 🏨 Hotel</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-type" value="panzió"> 🛏️ Panzió</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-type" value="apartman"> 🏢 Apartman</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-type" value="camping"> ⛺ Camping</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-type" value="vendeghaz"> 🏡 Vendégház</label>
                                        <label style="display:block; cursor:pointer; margin-top:5px; padding-top:5px; border-top:1px dashed #ccc;"><input type="checkbox" class="filter-premium" value="1"> 💎 Prémium / Luxus</label>
                                    </div>

                                    <div style="flex: 1; min-width: 200px;">
                                        <h5 style="margin-top:0; color:#45489a; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Szolgáltatások</h5>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-service" data-service="Reggeli"> <i class="fa fa-coffee text-success"></i> Reggeli</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-service" data-service="Félpanzió"> <i class="fa fa-cutlery text-success"></i> Félpanzió</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-service" data-service="Sport"> <i class="fa fa-bicycle text-success"></i> Sport</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-service" data-service="Wellness"> <i class="fa fa-leaf text-success"></i> Wellness</label>
                                        <label style="display:block; cursor:pointer;"><input type="checkbox" class="filter-service" data-service="Egyik sem"> <i class="fa fa-ban text-danger"></i> Egyik sem</label>
                                        
                                        <h5 style="margin-top:20px; color:#45489a; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Hotel Csillagok</h5>
                                        <select id="starFilter" class="form-control" style="width: 100%;">
                                            <option value="">Mindegy / Nem Hotel</option>
                                            <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                                            <option value="4">⭐⭐⭐⭐ (4)</option>
                                            <option value="3">⭐⭐⭐ (3)</option>
                                            <option value="2">⭐⭐ (2)</option>
                                            <option value="1">⭐ (1)</option>
                                        </select>
                                    </div>

                                    <div style="flex: 1; min-width: 250px;">
                                        <h5 style="margin-top:0; color:#45489a; border-bottom: 1px solid #ccc; padding-bottom: 5px;">Árkategória (Ft / Éj)</h5>
                                        <div class="range-container">
                                            <div class="range-track"></div>
                                            <div id="range-highlight"></div>
                                            <input type="range" id="minPriceRange" min="<?= $minDbPrice ?>" max="<?= $maxDbPrice ?>" value="<?= $minDbPrice ?>" step="500" class="slider-input">
                                            <input type="range" id="maxPriceRange" min="<?= $minDbPrice ?>" max="<?= $maxDbPrice ?>" value="<?= $maxDbPrice ?>" step="500" class="slider-input">
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-weight: bold; color: #45489a;">
                                            <span id="minPriceLabel"><?= number_format($minDbPrice, 0, ',', ' ') ?> Ft</span>
                                            <span id="maxPriceLabel"><?= number_format($maxDbPrice, 0, ',', ' ') ?> Ft</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-container">
                                <table class="table table-hover" id="accommodationTable">
                                    <thead style="background:#28a745; color:white;">
                                        <tr>
                                            <th>Név és Infó</th>
                                            <th>Kategória</th>
                                            <th class="desktop-hide">Település</th>
                                            <th class="desktop-hide">Irányítószám</th>
                                            <th class="desktop-hide">Ár/Éj</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach($szallasokData as $sz_row): 
                                            // 1. Nevek, települések lekérése null-védelemmel
                                            $name = $sz_row['szname'] ?? 'Ismeretlen Név';
                                            $nameLower = mb_strtolower((string)$name, 'UTF-8');
                                            $telepules = $sz_row['telepules'] ?? '';
                                            $city = mb_strtolower(trim((string)$telepules), 'UTF-8');
                                            
                                            // 2. Ár értékek használata
                                            $minPriceValue = (int)($sz_row['min_ar'] ?? 0);
                                            $maxPriceValue = (int)($sz_row['max_ar'] ?? 0);
                                            $priceValue = (int)round(($minPriceValue + $maxPriceValue) / 2);
                                            $priceDisplay = $priceValue > 0 ? number_format($priceValue, 0, ',', ' ') . ' Ft' : 'Nincs ár';

                                            // 3. Típus egyenesen az adatbázisból (sztipus)
                                            $dbType = $sz_row['sztipus'] ?? 'Egyéb';
                                            $detectedType = mb_strtolower(trim((string)$dbType), 'UTF-8');
                                            
                                            // Típus ikon és JS érték szinkronizálása a Checkboxokkal
                                            $typeIcon = '❓';
                                            if ($detectedType === 'hotel') $typeIcon = '🏨';
                                            elseif ($detectedType === 'panzió') $typeIcon = '🛏️';
                                            elseif ($detectedType === 'apartman') $typeIcon = '🏢';
                                            elseif ($detectedType === 'camping') $typeIcon = '⛺';
                                            elseif ($detectedType === 'vendeghaz' || $detectedType === 'vendégház') {
                                                $detectedType = 'vendeghaz'; // JS checkbox miatt fixáljuk
                                                $typeIcon = '🏡';
                                            }

                                            // 4. Csillagok és Prémium
                                            $isPremium = isPremiumSzallas($name, $detectedType) ? 1 : 0;
                                            $stars = getSzallasCsillag($name);
                                            // Csak a hoteleknél jelenítünk meg csillagot vizuálisan
                                            $starHtml = ($stars > 0 && $detectedType === 'hotel') ? str_repeat('⭐', $stars) : '';

                                            // 5. Szolgáltatások
                                            $activeServices = [];
                                            if (isset($sz_row['szolgreggeli']) && $sz_row['szolgreggeli'] == 1) $activeServices[] = 'Reggeli';
                                            if (isset($sz_row['szolgfelpanzio']) && $sz_row['szolgfelpanzio'] == 1) $activeServices[] = 'Félpanzió';
                                            if (isset($sz_row['szolgsport']) && $sz_row['szolgsport'] == 1) $activeServices[] = 'Sport';
                                            if (isset($sz_row['szolgwellness']) && $sz_row['szolgwellness'] == 1) $activeServices[] = 'Wellness';
                                            $servicesString = implode(',', $activeServices);

                                            // 6. Gombok generálása
                                            $baseStyle = "flex: 1; text-align: center; font-weight: bold; padding: 12px; border-radius: 6px; font-size: 1rem; border: none; min-width: 150px;";

                                            $btn1Html = "";
                                            if(!empty($sz_row['szweb1']) && strtolower($sz_row['szweb1']) !== 'null') {
                                                $url1 = mb_strtolower($sz_row['szweb1'], 'UTF-8');
                                                if (strpos($url1, 'szallas.hu') !== false) {
                                                    $btn1Html = '<a href="'.htmlspecialchars($sz_row['szweb1']).'" target="_blank" class="btn text-white" style="background-color: #dc3545; ' . $baseStyle . '"><i class="fa fa-home"></i> Szállás.hu</a>';
                                                } elseif (strpos($url1, 'booking.com') !== false) {
                                                    $btn1Html = '<a href="'.htmlspecialchars($sz_row['szweb1']).'" target="_blank" class="btn text-white" style="background-color: #003580; ' . $baseStyle . '"><i class="fa fa-bed"></i> Booking</a>';
                                                } else {
                                                    $btn1Html = '<a href="'.htmlspecialchars($sz_row['szweb1']).'" target="_blank" class="btn text-white" style="background-color: #6c757d; ' . $baseStyle . '"><i class="fa fa-globe"></i> Saját Weboldal</a>';
                                                }
                                            }

                                            $btn2Html = "";
                                            if(!empty($sz_row['szweb2']) && strtolower($sz_row['szweb2']) !== 'null') {
                                                $url2 = mb_strtolower($sz_row['szweb2'], 'UTF-8');
                                                if (strpos($url2, 'szallas.hu') !== false) {
                                                    $btn2Html = '<a href="'.htmlspecialchars($sz_row['szweb2']).'" target="_blank" class="btn text-white" style="background-color: #dc3545; ' . $baseStyle . '"><i class="fa fa-home"></i> Szállás.hu</a>';
                                                } elseif (strpos($url2, 'booking.com') !== false) {
                                                    $btn2Html = '<a href="'.htmlspecialchars($sz_row['szweb2']).'" target="_blank" class="btn text-white" style="background-color: #003580; ' . $baseStyle . '"><i class="fa fa-bed"></i> Booking</a>';
                                                } else {
                                                    $btn2Html = '<a href="'.htmlspecialchars($sz_row['szweb2']).'" target="_blank" class="btn text-white" style="background-color: #6c757d; ' . $baseStyle . '"><i class="fa fa-globe"></i> Saját Weboldal</a>';
                                                }
                                            }

                                            // Zöld Google Maps gomb
                                            $szcim = $sz_row['szcim'] ?? '';
                                            $mapSearchText = urlencode($telepules . " " . $szcim . " " . $name);
                                            $btn3Html = '<a href="https://maps.google.com/maps?q=' . $mapSearchText . '" target="_blank" class="btn text-white" style="background-color: #28a745; ' . $baseStyle . '"><i class="fa fa-map-marker"></i> Térkép</a>';
                                        ?>

                                            <tr class="main-row" 
                                                data-name="<?php echo htmlspecialchars($nameLower); ?>" 
                                                data-city="<?php echo htmlspecialchars($city); ?>" 
                                                data-price="<?php echo $priceValue; ?>" 
                                                data-type="<?php echo htmlspecialchars($detectedType); ?>"
                                                data-premium="<?php echo $isPremium; ?>"
                                                data-stars="<?php echo $stars; ?>"
                                                data-services="<?php echo htmlspecialchars($servicesString); ?>">
                                                
                                                <td class="name-cell">
                                                    <strong><?php echo htmlspecialchars($name); ?></strong> <span style="font-size: 0.8em;"><?php echo $starHtml; ?> <?php echo $isPremium ? '💎' : ''; ?></span>
                                                    <br>
                                                    <button class="btn btn-info-custom toggle-details mt-2"><i class="fa fa-plus-circle"></i> Infó</button>
                                                </td>
                                                <td><?php echo $typeIcon . ' ' . ucfirst(htmlspecialchars($dbType)); ?></td>
                                                <td class="desktop-hide"><?php echo htmlspecialchars($telepules); ?></td>
                                                <td class="desktop-hide"><?php echo htmlspecialchars($sz_row['tiranyitoszam'] ?? ''); ?></td>
                                                <td class="desktop-hide"><span class="price-tag"><?php echo $priceDisplay; ?></span></td>
                                            </tr>
                                            
                                            <tr class="details-row" style="display:none;">
                                                <td colspan="5">
                                                    <div class="details-content">
                                                        <div class="info-grid">
                                                            <div class="info-item mobile-only-info"><i class="fa fa-map-marker" style="color:#2196f3;"></i> <strong>Város:</strong> <?php echo htmlspecialchars($telepules); ?></div>
                                                            <div class="info-item mobile-only-info"><i class="fa fa-location-arrow" style="color:#2196f3;"></i> <strong>Irányítószám:</strong> <?php echo htmlspecialchars($sz_row['tiranyitoszam'] ?? ''); ?></div>
                                                            <div class="info-item mobile-only-info"><i class="fa fa-tag" style="color:#4caf50;"></i> <strong>Ár:</strong> <?php echo $priceDisplay; ?></div>
                                                            <div class="info-item"><i class="fa fa-home" style="color:#45489a;"></i> <strong>Cím:</strong> <?php echo htmlspecialchars($szcim); ?></div>
                                                            
                                                            <div style="display: flex; gap: 15px; width: 100%; margin-top: 15px; margin-bottom: 15px; flex-wrap: wrap;">
                                                                <?php echo $btn1Html; ?>
                                                                <?php echo $btn2Html; ?>
                                                                <?php echo $btn3Html; ?>
                                                            </div>
                                                        </div>
                                                        <div class="services-flex mt-3">
                                                            <?php 
                                                            $badges = [
                                                                ['szolgreggeli', 'fa-coffee', 'Reggeli'],
                                                                ['szolgfelpanzio', 'fa-cutlery', 'Félpanzió'],
                                                                ['szolgsport', 'fa-bicycle', 'Sport'],
                                                                ['szolgwellness', 'fa-leaf', 'Wellness']
                                                            ];
                                                            foreach($badges as $b):
                                                                $isActive = (isset($sz_row[$b[0]]) && $sz_row[$b[0]] == 1);
                                                            ?>
                                                                    <span class="service-badge <?php echo $isActive ? 'bg-success-custom' : 'bg-danger-custom'; ?>">
                                                                        <i class="fa <?php echo $isActive ? $b[1] : 'fa-times'; ?>"></i> <?php echo $b[2]; ?>
                                                                    </span>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <footer class="premium-footer">
            <div class="footer-inner-wrapper">
                <div class="credits-container">
                    <a href="../Proofiles.php" class="credits-link">
                        <p class="site-footer-fixed__pill">Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                    </a>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        // 1. Mobil menü lenyitása
        $('#nav-toggle').on('click', function (e) {
            e.preventDefault();
            $('#main-nav').slideToggle(300);
        });

        // 2. Infó gomb lenyílása
        $('.toggle-details').on('click', function() {
            let currentRow = $(this).closest('tr');
            let detailsRow = currentRow.next('.details-row');
            let icon = $(this).find('i');
            detailsRow.fadeToggle(300);
            if(icon.hasClass('fa-plus-circle')) {
                icon.removeClass('fa-plus-circle').addClass('fa-minus-circle');
                $(this).css('background-color', '#dc3545'); 
            } else {
                icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                $(this).css('background-color', '#45489a'); 
            }
        });

        // 3. KOMPLEX SZŰRŐ MOTOR
        function applyFilters() {
            let searchValue = $('#searchInput').val().toLowerCase();
            
            let minPrice = parseInt($('#minPriceRange').val()) || 0;
            let maxPrice = parseInt($('#maxPriceRange').val()) || 9999999;
            
            let selectedStar = $('#starFilter').val(); 

            let checkedTypes = [];
            $('.filter-type:checked').each(function() { checkedTypes.push($(this).val()); });

            let isPremiumChecked = $('.filter-premium').is(':checked');

            let checkedServices = [];
            $('.filter-service:checked').each(function() { checkedServices.push($(this).data('service')); });

            $("#accommodationTable tbody tr.main-row").each(function() {
                let row = $(this);
                let detailsRow = row.next('.details-row');
                
                let name = row.data('name') || "";
                let city = row.data('city') || "";
                let price = parseInt(row.data('price')) || 0;
                let type = row.data('type') || "";
                let premium = parseInt(row.data('premium')) || 0;
                let stars = parseInt(row.data('stars')) || 0;
                let services = (row.data('services') || "").split(',');

                let matchSearch = name.indexOf(searchValue) > -1 || city.indexOf(searchValue) > -1;
                let matchPrice = price >= minPrice && price <= maxPrice;
                let matchType = checkedTypes.length === 0 || checkedTypes.includes(type);
                let matchPremium = !isPremiumChecked || premium === 1;
                
                // Csillag szűrés: Csak a hotelnél releváns
                let matchStars = true;
                if (selectedStar !== "") {
                    // Ha a user csillagot választott, de ez nem hotel, akkor rejtse el
                    if (type !== 'hotel') {
                        matchStars = false;
                    } else {
                        // Ha hotel, akkor a csillagoknak egyeznie kell
                        matchStars = (stars === parseInt(selectedStar));
                    }
                }
                
                let matchService = true;
                if (checkedServices.length > 0) {
                    const wantsNone = checkedServices.includes('Egyik sem');
                    const otherServices = checkedServices.filter(service => service !== 'Egyik sem');
                    const hasAnyRealService = services.some(service => service !== '');
                    
                    if (wantsNone && otherServices.length === 0) {
                        matchService = !hasAnyRealService;
                    } else if (wantsNone && otherServices.length > 0) {
                        matchService = false;
                    } else {
                        for (let i = 0; i < checkedServices.length; i++) {
                            if (!services.includes(checkedServices[i])) { matchService = false; break; }
                        }
                    }
                }

                if (matchSearch && matchPrice && matchType && matchPremium && matchStars && matchService) {
                    row.show();
                } else {
                    row.hide();
                    detailsRow.hide();
                }
            });
        }

        // 4. ÁR CSÚSZKA SZINKRONIZÁLÁS (Elcsúszás elleni védelemmel)
        function syncPriceRange() {
            const minInput = document.getElementById('minPriceRange');
            const maxInput = document.getElementById('maxPriceRange');
            const highlight = document.getElementById('range-highlight');
            
            let minLimit = parseInt(minInput.min);
            let maxLimit = parseInt(minInput.max);
            if (isNaN(minLimit) || minLimit < 0) minLimit = 0;
            if (isNaN(maxLimit) || maxLimit <= minLimit) maxLimit = 150000;

            let v1 = parseInt(minInput.value) || minLimit;
            let v2 = parseInt(maxInput.value) || maxLimit;

            if (v1 > v2) {
                let temp = v1;
                v1 = v2;
                v2 = temp;
                minInput.value = v1;
                maxInput.value = v2;
            }

            let p1 = ((v1 - minLimit) / (maxLimit - minLimit)) * 100;
            let p2 = ((v2 - minLimit) / (maxLimit - minLimit)) * 100;

            highlight.style.left = p1 + "%";
            highlight.style.width = (p2 - p1) + "%";

            document.getElementById('minPriceLabel').innerText = v1.toLocaleString('hu-HU') + " Ft";
            document.getElementById('maxPriceLabel').innerText = v2.toLocaleString('hu-HU') + " Ft";

            applyFilters();
        }

        // Kezdő futtatások
        const maxDbPrice = 150000;
        const minDbPrice = <?php echo (int)$minDbPrice; ?>;
        $('#minPriceRange').attr('min', minDbPrice).attr('max', maxDbPrice).val(minDbPrice);
        $('#maxPriceRange').attr('min', minDbPrice).attr('max', maxDbPrice).val(maxDbPrice);
        $('#minPriceLabel').text(minDbPrice.toLocaleString('hu-HU') + ' Ft');
        $('#maxPriceLabel').text(maxDbPrice.toLocaleString('hu-HU') + ' Ft');
        syncPriceRange();

        // Eseményfigyelők kötése
        document.getElementById('minPriceRange').addEventListener('input', syncPriceRange);
        document.getElementById('maxPriceRange').addEventListener('input', syncPriceRange);
        $('#searchInput').on('keyup', applyFilters);
        $('.filter-type, .filter-premium, .filter-service, #starFilter').on('change', applyFilters);
    });
    </script>
    
    <?php include '../ertekeles_statisztika.php'; ?> 
    <?php include "../valuta/api_valuta.php"; ?>
</body>
</html>
