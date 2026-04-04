<?php
require_once __DIR__ . '/../init.php';
include '../kozos_menu.php';
include '../kozos_mobile.php';
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>NÓGRÁD - Programok</title>
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

        body {
            background: url('../img/program_back.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
            overflow-x: hidden;
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

        .page-content .col-md-12 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            box-sizing: border-box !important;
        }

        .program-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
            padding: 16px 0;
        }

        .program-item {
            flex: 1 1 333px;
            max-width: 600px;
            min-width: 300px;
        }

        .turizm-card.program {
            background: #ffffff !important;
            border-left: 10px solid var(--praktika-blue) !important;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
            min-height: 250px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: left;
        }

        .turizm-card.program h4 {
            color: var(--praktika-blue) !important;
            font-weight: 800 !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            display: block !important;
        }

        .turizm-card.program i {
            color: #9c27b0;
            margin-right: 10px;
        }

        .fun-fact strong {
            color: #9c27b0;
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
                padding: 20px !important;
                padding-top: 50px !important;
            }

            .program-item {
                min-width: 100%;
                max-width: 100%;
            }

            footer.premium-footer {
                padding: 20px !important;
            }

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
<body class="page-programok">
     <?= $kozos_menu ?>
<?= $kozos_mobile ?>

        <div class="page-content">
            <section class="content-section">
                <div class="row text-center">
                    <div class="col-md-12">
                        <h1 style="color:white; text-shadow: 2px 2px 8px #000; font-size: 50px;">Események & <em>Fesztiválok</em></h1>
                        <div class="humor-box">"Nógrádban mindig történik valami, csak győzd szusszal!"</div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="program-container">
                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-book"></i> Irodalmi Emléknapok</h4>
                                <p>Madách Imre és Mikszáth Kálmán szellemi örökségét elevenítjük fel rendhagyó irodalmi órákkal, koszorúzással és színházi előadásokkal.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Január 20-25. <br><strong>Helyszín:</strong> Alsósztregova és Horpács</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-microphone"></i> Salgótarjáni Bányásznap</h4>
                                <p>Hagyományos tisztelgés a bányász múlt előtt. Óriási koncertekkel, kézműves vásárral és látványos esti tűzijátékkal várjuk a látogatókat.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Szeptember 4-6. <br><strong>Helyszín:</strong> Fő tér</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-child"></i> Megyei Gyereknap</h4>
                                <p>Ingyenes ugrálóvárak, arcfestés, interaktív bűvészshow és bábszínházi előadások teszik felejthetetlenné a napot a családok számára.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Május 31. <br><strong>Tipp:</strong> Ingyenes belépés!</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-music"></i> Honti Kulturális Napok</h4>
                                <p>Az Ipoly két partján elterülő települések közös fesztiválja népzenével, kortárs kiállításokkal és nemzetközi folklór találkozóval.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Augusztus 14-16.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-fire"></i> Mikszáth-kupa Főzőverseny</h4>
                                <p>A vidék legjobb szakácsai mérik össze tudásukat bográcsos ételekben. A látogatók kóstolójeggyel végigehetik a palóc kínálatot.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Július 11. <br><strong>Gasztró:</strong> Sztrapacska és gulyás!</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-wrench"></i> Kézműves Alkotónap</h4>
                                <p>Workshopokon próbálhatod ki a korongozást, a hímzést és a vesszőfonást a legkiválóbb nógrádi mesteremberek segítségével.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Október 10.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-moon-o"></i> Holdfény Túra</h4>
                                <p>Különleges éjszakai erdőjárás szakvezetőkkel a Börzsönyben. Megismerhetjük az erdő éjszakai hangjait és a csillagos égboltot.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Június 27. <br><strong>Fontos:</strong> Zseblámpa kell!</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-star"></i> Karácsonyi Váró</h4>
                                <p>Ünnepi készülődés a Forgách-kastély kertjében. Forralt bor, kézműves ajándékok és helyi kórusok ünnepi műsora várja az érkezőket.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. December 1-24. <br><strong>Helyszín:</strong> Szécsény</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-trophy"></i> Pünkösdi Vigasságok</h4>
                                <p>Hagyományőrző ügyességi versenyek, lovas bemutatók és a népszerű Pünkösdi királyválasztás néptánccal kísérve.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Május 24-25.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-road"></i> Karancs-Medves Teljesítménytúra</h4>
                                <p>Változatos távok a legszebb vulkáni kúpok között. Bakancsos kaland profiknak és amatőröknek egyaránt, gyönyörű panorámával.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Április 18.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-headphones"></i> Retro Magnó-nap</h4>
                                <p>Technikai időutazás: több száz működő orsós- és kazettás magnó bemutatója, bakelitbörze és analóg zenehallgatás.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Március 14. <br><strong>Helyszín:</strong> Terény</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-fort-awesome"></i> Pásztói Kolostor Napok</h4>
                                <p>Középkori életmód bemutató a romkertben: gyógynövénykert-túra, kódexmásolás és szerzetesi ételek kóstolója.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Június 13.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-diamond"></i> Kastélynapok</h4>
                                <p>Barokk elegancia: kosztümös tárlatvezetések, hintózás és korhű táncok bemutatója a megye legszebb kastélyaiban.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Július 25-26.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-mountain"></i> Béri Geológiai Nap</h4>
                                <p>Szakértő vezetés a világhírű görbe andezitoszlopokhoz. Földtani előadások és természetjárás a vulkáni maradványokon.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Május 9.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-bomb"></i> Romhányi Csatatér</h4>
                                <p>A Rákóczi-szabadságharc utolsó csatájának emléknapja. Haditornászok, ágyúdörgés és lovasroham idézi meg a múltat.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Február 14.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-train"></i> Mikulás-járatok</h4>
                                <p>Varázslatos utazás az erdei vasúton. A feldíszített vagonokban a Mikulás várja a gyerekeket apró ajándékokkal.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. December 5-6. <br><strong>Helyszín:</strong> Kemence</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-cutlery"></i> Vanyarci Haluskafesztivál</h4>
                                <p>Nógrád legnagyobb gasztro-eseménye: több mázsa sztrapacska, főzőverseny és fergeteges palóc mulatság reggelig.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Szeptember 12.</div>
                            </div>
                        </div>

                        <div class="program-item">
                            <div class="turizm-card program">
                                <h4><i class="fa fa-leaf"></i> Cserhát-völgyi Bio-nap</h4>
                                <p>Egészséges életmód a természet lágyán: bio-termelők piaca, vegyszermentes szörpök és öko-gazdálkodási tanácsadás.</p>
                                <div class="fun-fact"><strong>Dátum:</strong> 2026. Szeptember 26.</div>
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
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#nav-toggle').on('click', function (e) {
                e.preventDefault();
                $('#main-nav').slideToggle(300);
            });
        });
    </script>
</body>
</html>
