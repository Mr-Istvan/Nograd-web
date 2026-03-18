<?php
session_start();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>NÓGRÁD - Utazási Praktikák</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/fontAwesome.css">
    <link rel="stylesheet" href="../css/templatemo-style.css"> 
    <link rel="stylesheet" href="../css/turizm.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="mobile_style.css">

    <style>
    /* --- ALAP ÉS SZÍNEK --- */
    :root {
        --praktika-blue: #3f51b5;
        --turan-accent: #fec107;
    }

    body {
        background: url('../img/travel_1.jpg') no-repeat center center fixed !important;
        background-size: cover !important;
        overflow-x: hidden;
    }

    /* --- HUMOR BOX (Most már egységes a többivel) --- */
    .humor-box {
        background: rgba(0, 0, 0, 0.7) !important;
        color: #fec107 !important;
        padding: 15px 25px;
        border-radius: 50px;
        border: 2px dashed #fec107;
        display: inline-block;
        margin: 20px auto;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4);
    }

    /* --- PRAKTIKÁK KÁRTYA (Brutális kontraszt) --- */
    .turizm-card {
        background: #ffffff !important; /* Tiszta fehér háttér */
        border-left: 10px solid var(--praktika-blue) !important;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        min-height: 250px;
        text-align: left;
    }

    /* Címek javítása */
    .turizm-card h4 {
        color: var(--praktika-blue) !important;
        font-weight: 800 !important;
        margin-bottom: 20px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 10px;
        display: block !important;
    }

    /* Lista elemek - hogy mobilon is látszódjanak! */
    .turizm-list {
        list-style: none;
        padding: 0;
    }

    .turizm-list li {
        margin-bottom: 15px;
        display: flex;
        align-items: flex-start;
        color: #1a1a1a !important; /* Mélyfekete szöveg */
        line-height: 1.6;
        font-weight: 500;
    }

    .turizm-list li i {
        margin-right: 12px;
        color: var(--praktika-blue) !important;
        margin-top: 5px;
        min-width: 20px;
        font-size: 18px;
    }

    /* --- MOBIL NAVIGÁCIÓ FIXÁLÁSA --- */
    @media (max-width: 767px) {
        .page-content { 
            padding-top: 100px !important; 
        }
        
        .responsive-nav {
            background: #232323 !important;
            border-bottom: 3px solid var(--praktika-blue) !important;
        }

        .navbar-toggle {
            background: var(--praktika-blue) !important;
            border: none !important;
        }
        
        .navbar-toggle .icon-bar {
            background-color: #fff !important;
        }
    }
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
            <div class="logo">
                <a href="../index.php">NÓG<em>RÁD</em></a>
            </div>
            <nav>
                <div class="user-info" style="padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px;">
                    <?php if(isset($_SESSION['user_name'])): ?>
                        <span style="display: block; color: #fff; margin-bottom: 5px;">Üdv, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong>!</span>
                        <a href="../logout.php" style="color: #45489a; text-decoration: none; font-weight: bold; font-size: 13px;">[ Kilépés ]</a>
                    <?php else: ?>
                        <a href="../login.php" style="color: #fff; text-decoration: none; font-weight: bold;">Bejelentkezés</a>
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
                <div class="row text-center">
                    <div class="col-md-12">
                        <h1 style="color:white; text-shadow: 2px 2px 8px #000; font-size: 50px; margin-top: 20px;">Minden, ami <em>Praktikus</em></h1>
                        <div class="humor-box">"A legjobb praktika: ha megkínálnak, ne kérdezd mi az, csak idd meg!"</div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="turizm-card">
                                <h4><i class="fa fa-car"></i> Közlekedés és Navigáció</h4>
                                <ul class="turizm-list">
                                    <li><i class="fa fa-star"></i> <span><strong>21-es főút:</strong> Budapest felől a leggyorsabb elérés. 2x2 sávos, kiváló minőségű és teljesen ingyenes.</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>Parkolás:</strong> Hollókőn a külső parkoló fizetős (kb. 1200 Ft/nap), a vár alatti részeken is hasonló díjakra kell számítani.</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>MÁV:</strong> A Vác–Balassagyarmat vonal az ország egyik legszebb vasútvonala, érdemes kipróbálni a látvány miatt.</span></li>
                                    <li><i class="fa fa-warning"></i> <span><strong>Térerő:</strong> A Karancs-Medves és a Cserhát mélyebb völgyeiben (pl. Bedepuszta környéke) a GPS eltévedhet, legyen offline térképed!</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card">
                                <h4><i class="fa fa-money"></i> Pénzügyek és Belépők</h4>
                                <ul class="turizm-list">
                                    <li><i class="fa fa-star"></i> <span><strong>ATM pontok:</strong> Csak a városokban (Salgótarján, Balassagyarmat, Pásztó, Rétság, Bátonyterenye) találsz automatát.</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>Kártyás fizetés:</strong> A nagyobb múzeumokban működik, de a falusi kisboltokban és erdei büfékben csak készpénzt fogadnak el.</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>Árak:</strong> Hollókő falubelépő kb. 2500-3500 Ft, a várak (Salgó, Somoskő) 1000-2000 Ft között látogathatóak.</span></li>
                                    <li><i class="fa fa-clock-o"></i> <span><strong>Boltok:</strong> A falusi "ABC"-k hétvégén gyakran csak délig vannak nyitva, érdemes előre készülni élelemmel.</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="turizm-card">
                                <h4><i class="fa fa-leaf"></i> Bakancsos tippek és Túraútvonalak</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="turizm-list">
                                            <li><i class="fa fa-check"></i> <span><strong>Kéktúra:</strong> Az Országos Kéktúra Nógrádon halad át az egyik legszebb szakaszán (Cserhát).</span></li>
                                            <li><i class="fa fa-check"></i> <span><strong>Vízvétel:</strong> Az erdőkben kevés a kiépített forrás, a várak meredek kaptatói előtt tankolj fel vízzel!</span></li>
                                            <li><i class="fa fa-check"></i> <span><strong>Vadvilág:</strong> A megye erdőiben gyakori a gímszarvas és a vaddisznó, a túraösvényekről ne térj le!</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="turizm-list">
                                            <li><i class="fa fa-check"></i> <span><strong>Felszerelés:</strong> A bazaltömlések esőben rendkívül csúszósak, csak bordázott talpú túracipőben indulj el.</span></li>
                                            <li><i class="fa fa-check"></i> <span><strong>Kullancsok:</strong> A Medves-fennsík magas füves részein fokozott a veszély, használj riasztót.</span></li>
                                            <li><i class="fa fa-trash"></i> <span><strong>Környezetvédelem:</strong> Nógrád érintetlen természeti kincs, minden szemetet vigyél haza magaddal!</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer style="padding: 20px; text-align: center; color: #cd7e0f;">
                <p>Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
            </footer>
        </div>
    </div>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function() {
                // Kattintás a hamburger ikonra
                $('#nav-toggle').on('click', function (e) {
                    e.preventDefault();
                    $('#main-nav').slideToggle(300); // 300ms alatt gördül le/fel
     });});
</script>
</body>
</html>
