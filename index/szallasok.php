<?php
session_start();
require_once "../db.php";

// Lekérjük a szállásokat névsorrendben
$sql = "SELECT * FROM szallasok ORDER BY szname ASC";
$result = $conn->query($sql);

// Statisztikához: összes férőhely kiszámítása - Kezeljük, ha üres az adatbázis
$count_sql = "SELECT SUM(szmaxpeople) as ossz_ferohely, COUNT(szid) as ossz_db FROM szallasok";
$stats_result = $conn->query($count_sql);
$stats = $stats_result->fetch_assoc();

$ossz_db = $stats['ossz_db'] ?? 0;
$ossz_ferohely = $stats['ossz_ferohely'] ?? 0;
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
        body {
            background: url('../img/szallas_5.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }

        /* Kártya stílus - Összhangban a gasztróval */
        .turizm-card {
            background: rgba(255, 255, 255, 0.96) !important;
            border-left: 10px solid #28a745 !important;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.4);
            color: #333 !important;
        }

        .humor-box {
            background: rgba(0, 0, 0, 0.7);
            color: #28a745;
            padding: 20px;
            border: 2px dashed #28a745;
            margin-bottom: 30px;
            font-size: 18px;
            text-align: center;
        }

        .stat-card {
            background: rgba(40, 167, 69, 0.9);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .stat-card:hover { transform: translateY(-5px); }

        /* Keresőmező csinosítása */
        #searchInput {
            margin: 15px 0;
            border-radius: 25px;
            padding: 12px 25px;
            border: 2px solid #28a745;
            width: 100%;
            outline: none;
            font-size: 16px;
        }

        /* Táblázat fixek */
        .table-container { 
            background: white; 
            padding: 10px; 
            border-radius: 8px;
            overflow-x: auto; /* Mobilon görgethető legyen a táblázat */
        }
        
        .table thead { background: #28a745; color: white; }
        .label-people { background: #17a2b8; padding: 4px 10px; border-radius: 20px; color: white; font-weight: bold; }

        /* Mobil menü gomb fix */
        .navbar-toggle { background-color: #28a745 !important; }
    </style>
</head>

<body>
    <header class="nav-down responsive-nav">
    <button type="button" id="nav-toggle" class="navbar-toggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <div id="main-nav">
        <nav style="padding: 20px;">
            <ul class="nav navbar-nav">
                <?php include 'mobile_menu.php'; ?>
            </ul>
        </nav>
    </div>
</header>

    <div class="main-wrapper">
        <div class="sidebar-navigation">
            <div class="logo"><a href="../index.php">NÓG<em>RÁD</em></a></div>
            <nav>
                <div class="user-info" style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px;">
                    <?php if(isset($_SESSION['user_name'])): ?>
                        <span style="display: block; color: #fff; margin-bottom: 5px;">Üdv, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</span>
                        <a href="../logout.php" style="color: #28a745; text-decoration: none; font-weight: bold;">[ Kilépés ]</a>
                    <?php else: ?>
                        <a href="../login.php" style="color: #fff; text-decoration: none;">Bejelentkezés</a>
                    <?php endif; ?>
                </div>
                <ul>
                    <li><a href="../index.php"><span class="rect"></span><span class="circle"></span>Kezdőlap</a></li>
                    <li><a href="latnivalok.php"><span class="rect"></span><span class="circle"></span>Látnivalók</a></li>
                    <li><a href="programok.php"><span class="rect"></span><span class="circle"></span>Programok</a></li>
                    <li><a href="szallasok.php"><span class="rect"></span><span class="circle"></span>Szállások</a></li>
                    <li><a href="gasztronomia.php"><span class="rect"></span><span class="circle"></span>Gasztro</a></li>
                    <li><a href="turazas.php"><span class="rect"></span><span class="circle"></span>Túrázás</a></li>
                    <li><a href="utazasi-praktikak.php"><span class="rect"></span><span class="circle"></span>Praktikák</a></li>
                </ul>
            </nav>
        </div>

        <div class="page-content">
            <section class="content-section">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h1 style="color:white; text-shadow: 2px 2px 8px #000; font-size: 50px; margin-top: 20px;">Nógrádi <em>Pihenés</em></h1>
                            <div class="humor-box">"Nógrádban nem wellness van, hanem tiszta levegő meg pálinka – de az legalább működik."</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="stat-card">
                                <h4>Regisztrált szálláshelyek</h4>
                                <h2 style="font-weight: 800;"><?php echo $ossz_db; ?> db</h2>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-card" style="background: #17a2b8;">
                                <h4>Összesített férőhely</h4>
                                <h2 style="font-weight: 800;"><?php echo number_format($ossz_ferohely, 0, ',', ' '); ?> fő</h2>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="turizm-card">
                                <h3><i class="fa fa-search"></i> Szálláskereső</h3>
                                <input type="text" id="searchInput" placeholder="Város, név vagy irányítószám alapján...">
                                
                                <div class="table-container">
                                    <table class="table table-hover" id="accommodationTable">
                                        <thead>
                                            <tr>
                                                <th>Név</th>
                                                <th>Helyszín</th>
                                                <th>Irsz.</th>
                                                <th>Szobák</th>
                                                <th>Kapacitás</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if($result && $result->num_rows > 0): ?>
                                                <?php while($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($row['szname']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($row['szcity']); ?></td>
                                                    <td><?php echo $row['sziszam']; ?></td>
                                                    <td><?php echo $row['szmaxroom']; ?> db</td>
                                                    <td><span class='label-people'><?php echo $row['szmaxpeople']; ?> fő</span></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr><td colspan="5" class="text-center">Nincs találat az adatbázisban.</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <footer style="padding: 20px; text-align: center; color: #ad2020;">
                <p>Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
            </footer>
        </div>
    </div>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    
    <script>
       $(document).ready(function() {
            // MOBIL MENÜ NYITÁSA/ZÁRÁSA
            $('#nav-toggle').on('click', function (e) {
                e.preventDefault();
                $('#main-nav').slideToggle(300);
            });

            // Gáztronomia/Programok kártyák automatikus magassága (opcionális)
            console.log("Oldal betöltve, JS ellenőrizve.");
       });
    </script>
</body>
</html>
