<?php
require_once __DIR__ . '/../init.php';
include '../kozos_menu.php';
include '../kozos_mobile.php';
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
        :root {
            --praktika-blue: #3f51b5;
            --turan-accent: #fec107;
        }
        body {
            background: url('../img/travel_1.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
            overflow-x: hidden;
        }
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
        .turizm-card {
            background: #ffffff !important;
            border-left: 10px solid var(--praktika-blue) !important;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            min-height: 250px;
            text-align: left;
        }
        .turizm-card h4 {
            color: var(--praktika-blue) !important;
            font-weight: 800 !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            display: block !important;
        }
        .turizm-list { list-style: none; padding: 0; }
        .turizm-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-start;
            color: #1a1a1a !important;
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

        .main-wrapper {
            margin-top: 15px;
        }

        .page-content {
            margin-left: 260px !important;
            padding: 20px !important;
            width: calc(100% - 260px) !important;
            max-width: calc(100% - 260px) !important;
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
        .page-content .col-md-6 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            box-sizing: border-box !important;
        }
        @media (max-width: 767px) {
            .page-content {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 20px !important;
                padding-top: 50px !important;
            }

            .responsive-nav {
                background: #232323 !important;
                border-bottom: 3px solid var(--praktika-blue) !important;
            }
            .navbar-toggle {
                background: var(--praktika-blue) !important;
                border: none !important;
            }
            .navbar-toggle .icon-bar { background-color: #fff !important; }
        }

        @media (max-width: 767px) {
            .page-content h1 {
                position: relative;
                top: -50px;
            }
        }
    </style>
</head>
<body>
<?= $kozos_menu ?>
<?= $kozos_mobile ?>
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
                                    <li><i class="fa fa-star"></i> <span><strong>Parkolás:</strong> Hollókőn a külső parkoló fizetős (kb. 1200 Ft/nap).</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>MÁV:</strong> A Vác–Balassagyarmat vonal az ország egyik legszebb vasútvonala.</span></li>
                                    <li><i class="fa fa-warning"></i> <span><strong>Térerő:</strong> A mélyebb völgyekben a GPS eltévedhet, legyen offline térképed!</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card">
                                <h4><i class="fa fa-money"></i> Pénzügyek és Belépők</h4>
                                <ul class="turizm-list">
                                    <li><i class="fa fa-star"></i> <span><strong>ATM pontok:</strong> Csak a városokban találsz automatát.</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>Kártyás fizetés:</strong> A múzeumokban megy, de falun vigyél készpénzt!</span></li>
                                    <li><i class="fa fa-star"></i> <span><strong>Árak:</strong> Hollókő belépő kb. 2500-3500 Ft, a várak 1000-2000 Ft.</span></li>
                                    <li><i class="fa fa-clock-o"></i> <span><strong>Boltok:</strong> A falusi "ABC"-k hétvégén gyakran csak délig vannak nyitva.</span></li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="turizm-card praktika">
                                <h4><i class="fa fa-tasks"></i> Digitális Pakolólista (Interaktív)</h4>
                                <div class="np-checklist-container">
                                    <label class="np-check-item">
                                        <input type="checkbox">
                                        <span class="np-checkmark"></span>
                                        <span class="np-text">Kényelmes túrabakancs</span>
                                    </label>
                                    <label class="np-check-item">
                                        <input type="checkbox">
                                        <span class="np-checkmark"></span>
                                        <span class="np-text">Esőkabát (zápor esetére)</span>
                                    </label>
                                    <label class="np-check-item">
                                        <input type="checkbox">
                                        <span class="np-checkmark"></span>
                                        <span class="np-text">Powerbank (a GPS-nek)</span>
                                    </label>
                                    <label class="np-check-item">
                                        <input type="checkbox">
                                        <span class="np-checkmark"></span>
                                        <span class="np-text">Készpénz az erdei büfékbe</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="turizm-card">
                                <h4><i class="fa fa-leaf"></i> Bakancsos tippek</h4>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="turizm-list">
                                            <li><i class="fa fa-check"></i> <span><strong>Kéktúra:</strong> Az Országos Kéktúra legszebb szakaszai Nógrádon haladnak.</span></li>
                                            <li><i class="fa fa-check"></i> <span><strong>Vízvétel:</strong> Kevés a kiépített forrás, vigyél magaddal eleget!</span></li>
                                            <li><i class="fa fa-check"></i> <span><strong>Vadvilág:</strong> Gyakori a szarvas és vaddisznó, ne térj le az ösvényről!</span></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="turizm-list">
                                            <li><i class="fa fa-check"></i> <span><strong>Felszerelés:</strong> Esőben a bazaltömlések csúsznak, kell a túrabakancs.</span></li>
                                            <li><i class="fa fa-check"></i> <span><strong>Kullancsok:</strong> Magas fűben fokozott veszély, használj riasztót.</span></li>
                                            <li><i class="fa fa-trash"></i> <span><strong>Környezetvédelem:</strong> Amit bevittél az erdőbe, hozd is ki!</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <footer class="premium-footer" style="padding: 17px; text-align: center; color: #0a1f98;">
                <a href="../Editors.php" class="credits-link" style="display:block; color: inherit; text-decoration: none; cursor: pointer; text-align:center; width:100%;">
                    <p class="site-footer-fixed__pill" style="margin:0 auto; text-align:center; display:inline-block;">Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                </a>
            </footer>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#nav-toggle').on('click', function (e) {
                e.preventDefault();
                $('#main-nav').slideToggle(300);
            });
        });
    </script>
    <?php include '../ertekeles_statisztika.php'; ?> 
    <?php include "../valuta/api_valuta.php"; ?>
</body>
</html>
