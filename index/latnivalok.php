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
    <title>NÓGRÁD - Látnivalók</title>
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
            background: url('../img/latnivalok_back.jpg') no-repeat center center fixed !important;
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

        .page-content .col-md-12,
        .page-content .col-md-6 {
            padding-left: 0 !important;
            padding-right: 0 !important;
            box-sizing: border-box !important;
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

        .turizm-card.latnivalo i {
            color: #aa5209;
            margin-right: 10px;
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
<body class="page-latnivalok">
   <?= $kozos_menu ?>
<?= $kozos_mobile ?>

        <div class="page-content">
            <section class="content-section">
                <div class="row text-center">
                    <div class="col-md-12">
                        <h1 style="color:white; text-shadow: 2px 2px 8px #000; font-size: 50px; margin-top: 20px;">Nógrádi <em>Csodák</em></h1>
                        <div class="humor-box" style="color: #aa5209;">"Nógrádban nem eltévedsz, hanem felfedezel!"</div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-fort-awesome"></i> Hollókő Ófalu</h4>
                                <p>Az UNESCO Világörökség része. Egy élő falu, ahol a hagyományok nem csak a múzeumban léteznek. A 67 védett ház és a középkori vár felejthetetlen látvány.</p>
                                <div class="fun-fact">
                                    <strong>Tipp:</strong> Kóstold meg a helyi lepényt, és ne felejts el felmenni a várba a panorámáért!
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-life-ring"></i> Bánki-tó</h4>
                                <p>Nógrád tengerpartja. A festői környezetben fekvő tó nemcsak strandolásra, hanem vízi színpadi előadásokra és nagy sétákra is tökéletes.</p>
                                <div class="fun-fact">
                                    <strong>Tipp:</strong> Nyáron a Bánkitó Fesztivál idején a legpezsgőbb az élet!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-heart"></i> Szentkúti Nemzeti Kegyhely</h4>
                                <p>Magyarország egyik legjelentősebb búcsújáróhelye. A gyönyörű bazilika és a barlanglakások spirituális élményt nyújtanak minden látogatónak.</p>
                                <div class="fun-fact">
                                    <strong>Érdekesség:</strong> A legenda szerint Szent László lova ugratott itt, és a patkója nyomán fakadt fel a gyógyító víz.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-shield"></i> Drégely vára</h4>
                                <p>Szondi György várkapitány hősiességének emlékműve. A Börzsöny északi részén magasodó romokhoz vezető túra a történelembe repít vissza.</p>
                                <div class="fun-fact">
                                    <strong>Tipp:</strong> A kilátás miatt érdemes megküzdeni a meredek emelkedővel!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-map-marker"></i> Balassagyarmat Óváros</h4>
                                <p>A "legbátrabb város". A Palóc Múzeum és a vármegyeháza épületei között sétálva megismerhetjük a palóc kultúra és a helyi ellenállás történetét.</p>
                                <div class="fun-fact">
                                    <strong>Kihagyhatatlan:</strong> A Pannónia Motormúzeum a technika szerelmeseinek kötelező program!
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-university"></i> Somoskői Vár</h4>
                                <p>Közvetlenül a határon áll. A várhegy oldalában található világhírű bazaltorgonák (ívelt bazaltoszlopok) Európa-szerte ritkaságnak számítanak.</p>
                                <div class="fun-fact">
                                    <strong>Érdekesség:</strong> Itt forgatták az Egri csillagok több jelenetét is.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-sun-o"></i> Tari Buddhista Központ</h4>
                                <p>A Kőrösi Csoma Sándor Emlékpark egy szelet Tibet Nógrádban. A hófehér Sztúpa és a színes imazászlók között sétálva garantált a nyugalom.</p>
                                <div class="fun-fact">
                                    <strong>Szabály:</strong> A Sztúpát mindig az óramutató járásával megegyező irányban kerüld meg!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-globe"></i> Kazári Riolittufa</h4>
                                <p>A "magyar Kappadókia". Egyedülálló fehér sziklaalakzatok, amikhez hasonló összesen hat van az egész világon. Olyan, mintha a Holdon járnál.</p>
                                <div class="fun-fact">
                                    <strong>Figyelem:</strong> Csak gyalogosan megközelíthető, készülj kényelmes túracipővel!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-paw"></i> Ipolytarnóci Ősmaradványok</h4>
                                <p>A "pannon Pompeji". Ősi lábnyomok, cápafogak és megkövesedett óriásfenyők várnak a világhírű természetvédelmi területen.</p>
                                <div class="fun-fact">
                                    <strong>Kihagyhatatlan:</strong> A 4D mozi és a lombkorona-sétány az egész családnak élmény.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-flag"></i> Salgó Vára</h4>
                                <p>A 625 méter magas bazaltkúpon trónoló várrom Petőfi Sándort is megihlette. Innen látni a legszebb naplementét az egész megyében.</p>
                                <div class="fun-fact">
                                    <strong>Tipp:</strong> Tiszta időben akár a Tátra csúcsait is látni a bástyáról!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-building"></i> Szécsényi Forgách-kastély</h4>
                                <p>A barokk kastély és a hozzá tartozó park a Rákóczi-szabadságharc fontos helyszíne volt. Ma múzeumként működik, gyönyörű tárlatokkal.</p>
                                <div class="fun-fact">
                                    <strong>Érdekesség:</strong> Itt van a híres "tűztorony", ami szemmel láthatóan ferde!
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="turizm-card latnivalo">
                                <h4><i class="fa fa-tint"></i> Páris-patak "Palóc Grand Canyon"</h4>
                                <p>Nógrádszakál mellett található ez a vadregényes szurdokvölgy. A meredek falak és a kidőlt fatörzsek között igazi kaland a túrázás.</p>
                                <div class="fun-fact">
                                    <strong>Figyelem:</strong> Nagy esőzés után gumicsizma erősen ajánlott!
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
       <?php include '../ertekeles_statisztika.php'; ?> 
<?php include "../valuta/api_valuta.php"; ?>
</body>
</html>
