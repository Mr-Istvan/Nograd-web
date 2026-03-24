<?php
/**
 * SZALLASOK.PHP - VÉGLEGES, SZÍNES VÁLTOZAT (Emoji alapú kategóriák)
 */

require_once __DIR__ . '/../init.php';

// 1. ADATOK LEKÉRDEZÉSE
$sql = "SELECT sz.*, t.telepules, t.tiranyitoszam, szt.sztipus, 
                szolg.szolgreggeli, szolg.szolgfelpanzio, szolg.szolgsport, szolg.szolgwellness
        FROM szallasok AS sz
        LEFT JOIN telepulesek AS t ON sz.tid = t.tid
        LEFT JOIN szallas_tipus AS szt ON sz.sztid = szt.sztid
        LEFT JOIN szolgaltatasok AS szolg ON sz.szszid = szolg.szszid
        ORDER BY sz.szid ASC";

$result = $conn->query($sql);

// 2. STATISZTIKA SZÁMÍTÁSA
$count_sql = "SELECT 
                AVG(REPLACE(REPLACE(szejar, ' Ft', ''), ' ', '')) as atlag_ar, 
                COUNT(szid) as ossz_db 
              FROM szallasok";

$stats_result = $conn->query($count_sql);
$stats = $stats_result->fetch_assoc();

$ossz_db = $stats['ossz_db'] ?? 0;
$atlag_ar = round($stats['atlag_ar'] ?? 0);
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
        html, body { overflow-x: hidden; width: 100%; margin: 0; padding: 0; }
        body { background: url('../img/szallasok_back.jpg') no-repeat center center fixed !important; background-size: cover !important; }

        .page-content { width: 100%; padding: 0; }
        .content-section { padding: 20px 10px; }
        .container-fluid { padding-left: 0; padding-right: 0; }

        .turizm-card {
            background: rgba(255, 255, 255, 0.85) !important;
            border-left: 10px solid #28a745 !important;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            margin-bottom: 30px;
        }

        .table-container { background: white; padding: 10px; border-radius: 8px; width: 100%; overflow-x: auto; }

        .name-cell { position: relative; padding-bottom: 40px !important; vertical-align: middle !important; min-width: 250px; }

        .btn-info-custom {
            position: absolute; right: 5px; bottom: 5px;
            background-color: #45489a !important; color: white !important;
            border: none; padding: 5px 12px; font-weight: bold; border-radius: 4px;
            transform: scale(0.8); transform-origin: right bottom; font-size: 13px;
            cursor: pointer; transition: all 0.2s;
        }

        .details-content { background: #f9f9f9; padding: 15px; border: 2px solid #45489a; border-radius: 10px; margin: 5px 0 15px 0; }
        .info-grid { display: flex; flex-wrap: wrap; gap: 15px; }
        .info-item { min-width: 200px; flex: 1; font-size: 14px; }
        .info-item i { color: #45489a; margin-right: 8px; width: 18px; text-align: center; }

        .mobile-only-info { display: none; }
        @media (max-width: 1333px) {
            .desktop-hide { display: none !important; }
            .mobile-only-info { display: block; }
        }

        .services-flex { display: flex; flex-wrap: wrap; gap: 10px; border-top: 1px solid #ddd; margin-top: 15px; padding-top: 15px; }
        .service-badge { padding: 6px 12px; border-radius: 20px; color: white; font-weight: bold; font-size: 12px; display: inline-flex; align-items: center; gap: 5px; }
        .bg-success-custom { background-color: #28a745; }
        .bg-danger-custom { background-color: #dc3545; opacity: 0.8; }

        .stat-card { background: rgba(40, 167, 69, 0.9); color: white; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 20px; }
        .humor-box { background: rgba(0, 0, 0, 0.7); color: #28a745; padding: 15px; border: 2px dashed #28a745; margin-bottom: 25px; text-align: center; font-weight: bold; }
        .price-tag { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 10px; font-size: 13px; font-weight: bold; }
        
        .cat-icon { font-size: 18px; margin-left: 8px; vertical-align: middle; }
    </style>
</head>

<body>
    <header class="nav-down responsive-nav">
        <button type="button" id="nav-toggle" class="navbar-toggle">
            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
        </button>
        <div id="main-nav">
            <nav style="padding: 20px;"><ul class="nav navbar-nav"><?php include 'mobile_menu.php'; ?></ul></nav>
        </div>
    </header>

    <div class="main-wrapper">
        <div class="sidebar-navigation">
            <div class="logo"><a href="../index.php">NÓG<em>RÁD</em></a></div>
            <nav>
                <div class="user-info" style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px;">
                    <?php if(isset($_SESSION['user_name'])): ?>
                        <span style="display: block; color: rgba(255,255,255,0.8);">Üdv, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</span>
                        <a href="../logout.php" style="color: #28a745; font-weight: bold;">[ Kilépés ]</a>
                    <?php else: ?>
                        <a href="../login.php" style="color: #fff;">Bejelentkezés</a>
                    <?php endif; ?>
                </div>
                <ul>
                    <li><a href="../index.php"><span class="rect"></span><span class="circle"></span>Kezdőlap</a></li>
                    <li><a href="latnivalok.php"><span class="rect"></span><span class="circle"></span>Látnivalók</a></li>
                    <li><a href="programok.php"><span class="rect"></span><span class="circle"></span>Programok</a></li>
                    <li><a href="szallasok.php"><span class="rect"></span><span class="circle"></span>Szállások</a></li>
                    <li><a href="gasztronomia.php"><span class="rect"></span><span class="circle"></span>Gasztro</a></li>
                    <li><a href="turazas.php"><span class="rect"></span><span class="circle"></span>Túrázás</a></li>
                </ul>
            </nav>
            <?php include "../weather.php"; ?>
        </div>

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
                            <div class="stat-card"><h5>Szálláshelyek</h5><h3><?php echo $ossz_db; ?> db</h3></div>
                        </div>
                        <div class="col-md-6 col-xs-6">
                            <div class="stat-card" style="background: #17a2b8;"><h5>Átlagár / éj</h5><h3><?php echo number_format($atlag_ar, 0, ',', ' '); ?> Ft</h3></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="turizm-card">
                                <h3><i class="fa fa-bed"></i> Szálláskereső</h3>
                                <input type="text" id="searchInput" class="form-control" style="margin-bottom:15px;" placeholder="Keresés név vagy város alapján...">
                                
                                <div class="table-container">
                                    <table class="table table-hover" id="accommodationTable">
                                        <thead style="background:#28a745; color:white;">
                                            <tr>
                                                <th>Név és Infó</th>
                                                <th>Típus</th>
                                                <th class="desktop-hide">Település</th>
                                                <th class="desktop-hide">Irányítószám</th>
                                                <th class="desktop-hide">Ár/Éj</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while($row = $result->fetch_assoc()): ?>
                                                <tr class="main-row">
                                                    <td class="name-cell">
                                                        <strong><?php echo htmlspecialchars($row['szname']); ?></strong>
                                                        
                                                        <?php
                                                        $name = $row['szname'];
                                                        $output_icon = "";

                                                        // ⭐⭐⭐⭐ 4 CSILLAG
                                                        if (in_array($name, ['Castellum Hotel Hollókő', 'Tó Wellness Hotel', 'Kastélyhotel Sasvár', 'Teleki-Degenfeld Kastélyszálló', 'Templomvölgy Resort Mátrakeresztes', 'Főnix Wellness Resort', 'Főnix Kastélyszanatórium és Egészséghotel'])) {
                                                            $output_icon = '<span class="cat-icon">⭐⭐⭐⭐</span>';
                                                        } 
                                                        // ⭐⭐⭐ 3 CSILLAG
                                                        elseif (in_array($name, ['Cédrus Club Hotel', 'Salgó Hotel', 'Berceli Kastély', 'Cserhátsurányi Kastélyszálló'])) {
                                                            $output_icon = '<span class="cat-icon">⭐⭐⭐</span>';
                                                        } 
                                                        // ⭐⭐ 2 CSILLAG
                                                        elseif (in_array($name, ['Eresztvényi Turistaház', 'Börzsönyi Turistaház'])) {
                                                            $output_icon = '<span class="cat-icon">⭐⭐</span>';
                                                        }
                                                        // 💎 PRÉMIUM
                                                        elseif (in_array($name, ['Prónay-kastély', 'Mátra Mona Luxury Apartment', 'Napfénydomb Vendégház', 'Galagonya Vendégház'])) {
                                                            $output_icon = '<span class="cat-icon">💎</span>';
                                                        }
                                                        // ⚖️ KÖZEPES
                                                        elseif (in_array($name, ['Boróka Vendégház', 'Bánki-tó Vendégház', 'Kaláris Vendégház', 'Zagyva-völgyi Vendégház', 'Legéndi Vendégház', 'Endrefalvai Vendégház'])) {
                                                            $output_icon = '<span class="cat-icon">⚖️</span>';
                                                        }
                                                        // 💰 EGYSZERŰ
                                                        elseif (in_array($name, ['Piros Csizma Vendégház', 'Nádas fogadó', 'Nógrádsipeki Pihenő', 'Felsőpetényi Vendégház', 'Nádas fogadó Teresztenye'])) {
                                                            $output_icon = '<span class="cat-icon">💰</span>';
                                                        }
                                                        // 🏨 PANZIÓK
                                                        elseif (in_array($name, ['Várhegy Panzió', 'Cserhát Kapuja', 'Rétsági Panzió', 'Karancssági Fogadó', 'Tereskei Vendégház', 'Mátraverebélyi Zarándokház', 'Szentkúti Kegyhely Szálló'])) {
                                                            $output_icon = '<span class="cat-icon">🏨</span>';
                                                        }
                                                        // ⛺ KEMPING
                                                        elseif (in_array($name, ['Nádas Camping', 'Somoskői Kirándulóközpont', 'Mátra Kemping', 'Diósjenői Kemping'])) {
                                                            $output_icon = '<span class="cat-icon">⛺</span>';
                                                        }

                                                        echo $output_icon;
                                                        ?>

                                                        <button class="btn btn-info-custom toggle-details"><i class="fa fa-plus-circle"></i> Infó</button>
                                                    </td>
                                                    <td><?php echo $row['sztipus']; ?></td>
                                                    <td class="desktop-hide"><?php echo $row['telepules']; ?></td>
                                                    <td class="desktop-hide"><?php echo $row['tiranyitoszam']; ?></td>
                                                    <td class="desktop-hide"><span class="price-tag"><?php echo $row['szejar']; ?></span></td>
                                                </tr>
                                                <tr class="details-row" style="display:none;">
                                                    <td colspan="5">
                                                        <div class="details-content">
                                                            <div class="info-grid">
                                                                <div class="info-item mobile-only-info"><i class="fa fa-map-marker" style="color:#2196f3;"></i> <strong>Város:</strong> <?php echo $row['telepules']; ?></div>
                                                                <div class="info-item mobile-only-info"><i class="fa fa-location-arrow" style="color:#2196f3;"></i> <strong>Irányítószám:</strong> <?php echo $row['tiranyitoszam']; ?></div>
                                                                <div class="info-item mobile-only-info"><i class="fa fa-tag" style="color:#4caf50;"></i> <strong>Ár:</strong> <?php echo $row['szejar']; ?></div>
                                                                
                                                                <div class="info-item"><i class="fa fa-home" style="color:#45489a;"></i> <strong>Cím:</strong> <?php echo $row['szcim']; ?></div>
                                                                <div class="info-item"><i class="fa fa-phone" style="color:#28a745;"></i> <strong>Tel:</strong> <?php echo $row['sztel']; ?></div>
                                                                <div class="info-item"><i class="fa fa-envelope" style="color:#dc3545;"></i> <strong>Email:</strong> <?php echo $row['szemail']; ?></div>
                                                            </div>
                                                            <div class="services-flex">
                                                                <?php 
                                                                $badges = [
                                                                    ['szolgreggeli', 'fa-coffee', 'Reggeli'],
                                                                    ['szolgfelpanzio', 'fa-cutlery', 'Félpanzió'],
                                                                    ['szolgsport', 'fa-bicycle', 'Sport'],
                                                                    ['szolgwellness', 'fa-leaf', 'Wellness']
                                                                ];
                                                                foreach($badges as $b): ?>
                                                                    <span class="service-badge <?php echo ($row[$b[0]] == 1) ? 'bg-success-custom' : 'bg-danger-custom'; ?>">
                                                                        <i class="fa <?php echo ($row[$b[0]] == 1) ? $b[1] : 'fa-times'; ?>"></i> <?php echo $b[2]; ?>
                                                                    </span>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
           <footer class="premium-footer" style="padding: 20px; text-align: center; color: #0a1f98;">
                <a href="../Proofiles.php" style="display:inline-block; color: inherit; text-decoration: none; cursor: pointer;">
                    <p>Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                </a>
            </footer>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#nav-toggle').on('click', function (e) {
            e.preventDefault();
            $('#main-nav').slideToggle(300);
        });

        $('#searchInput').on('keyup', function() {
            let value = $(this).val().toLowerCase();
            $("#accommodationTable tbody tr.main-row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
            $(".details-row").hide();
        });

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
    });
    </script>
</body>
</html>