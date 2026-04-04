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
    <title>NÓGRÁD - Palóc Gasztro-túra</title>
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
            background: url('../img/gastro_1.jpg') no-repeat center center fixed !important;
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
        .page-content .col-md-5,
        .page-content .col-md-7 {
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

        .turizm-card h3 {
            color: var(--praktika-blue) !important;
            font-weight: 800 !important;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            display: block !important;
        }

        .page-content .col-md-7 .turizm-card {
            min-height: 600px;
        }

        .page-content .col-md-7 .scroll-box {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .venue-box-fancy {
            border-left: 5px solid #9c27b0;
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            transition: 0.3s;
        }

        .venue-link {
            text-decoration: none;
            display: block;
        }

        .venue-link strong {
            color: #000;
            font-size: 16px;
        }

        .venue-link p {
            color: #555 !important;
            font-size: 13px;
            margin: 5px 0 0 0;
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

            .turizm-card {
                padding: 20px;
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
<body>
   <?= $kozos_menu ?>
<?= $kozos_mobile ?>

        <div class="page-content">
            <section class="content-section">
                <div class="container-fluid">
                    <div class="row text-center">
                        <div class="col-md-12">
                            <h1 style="color:white; text-shadow: 2px 2px 8px #000; font-size: 50px; margin-top: 20px;">Palóc <em>Abrosz</em></h1>
                            <div class="humor-box">"Nógrádban az a diéta, ha a szalonnát kenyér nélkül eszed."</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-5">
                            <div class="turizm-card">
                                <h3><i class="fa fa-cutlery"></i> Legendás Falatok</h3>
                                <hr>
                                <div class="food-item">
                                    <strong>1. Juhtúrós Sztrapacska</strong>
                                    <p>A palóc beton. Úgy a székhez szegez, hogy a desszertért már daruval kell kiemelni.</p>
                                </div>
                                <div class="food-item">
                                    <strong>2. Palócleves</strong>
                                    <p>Gundel János kreációja. Olyan sűrű, hogy ha beleállítod a kanalat, az másnap is ott áll.</p>
                                </div>
                                <div class="food-item">
                                    <strong>3. Macok / Tócsni</strong>
                                    <p>A krumpli és az olaj szent házassága. Ha nem csöpög a zsír, valamit rosszul csinálsz!</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="turizm-card" style="min-height: 600px;">
                                <h3><i class="fa fa-map-marker"></i> Hol adják a legjobban?</h3>
                                <p style="font-size: 12px; color: #888 !important;">(Kattints a névre a térkép frissítéséhez!)</p>
                                <hr>

                                <div style="height: 333px; width: 100%; border: 2px solid #fec107; border-radius: 10px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                                    <iframe id="map-frame" src="https://maps.google.com/maps?q=Nógrád+megye+gasztronómia&t=&z=10&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                                </div>

                                <div class="scroll-box" style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                                    <div class="venue-box-fancy" style="border-left: 5px solid #9c27b0; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px; transition: 0.3s;">
                                        <a href="javascript:void(0);" onclick="updateMap('Vargánya Étterem Mátraszentimre', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Vargánya Étterem (Mátraszentimre)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">A hegység királya: vadpörkölt és erdei gombák minden mennyiségben.</p>
                                        </a>
                                    </div>

                                    <div class="venue-box-fancy" style="border-left: 5px solid #ff5722; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                                        <a href="javascript:void(0);" onclick="updateMap('Bársony Vendéglő Szécsény', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Bársony Vendéglő (Szécsény)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">Igazi házias ízek, ahol a rántott hús még a tányérról is lelóg.</p>
                                        </a>
                                    </div>

                                    <div class="venue-box-fancy" style="border-left: 5px solid #00bcd4; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                                        <a href="javascript:void(0);" onclick="updateMap('Svejk Vendéglő Balassagyarmat', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Svejk Vendéglő (Balassagyarmat)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">Hatalmas palóc adagok a város szívében. Itt nem maradsz éhes!</p>
                                        </a>
                                    </div>

                                    <div class="venue-box-fancy" style="border-left: 5px solid #795548; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                                        <a href="javascript:void(0);" onclick="updateMap('Halász Fogadó Salgótarján', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Halász Fogadó (Salgótarján)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">Itt az adagokat decibelben mérik: akkor jó, ha reccsen az asztal.</p>
                                        </a>
                                    </div>

                                    <div class="venue-box-fancy" style="border-left: 5px solid #e91e63; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                                        <a href="javascript:void(0);" onclick="updateMap('Tóparti Vendéglő Bánk', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Tóparti Vendéglő (Bánk)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">Kilátás a tóra, de úgyis csak a tányért fogod nézni.</p>
                                        </a>
                                    </div>

                                    <div class="venue-box-fancy" style="border-left: 5px solid #4caf50; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                                        <a href="javascript:void(0);" onclick="updateMap('Castellum Étterem Hollókő', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Castellum Étterem (Hollókő)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">Minden falatba belefőzték a történelmet és a világörökséget.</p>
                                        </a>
                                    </div>

                                    <div class="venue-box-fancy" style="border-left: 5px solid #607d8b; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                                        <a href="javascript:void(0);" onclick="updateMap('Kanyar Tanya Pásztó', this)" class="venue-link" style="text-decoration: none; display: block;">
                                            <strong style="color: #000; font-size: 16px;">Kanyar Tanya (Pásztó)</strong>
                                            <p style="color: #555 !important; font-size: 13px; margin: 5px 0 0 0;">Ha ezt a kanyart kihagyod, az életed egyik legnagyobb hibáját követed el!</p>
                                        </a>
                                    </div>
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

        function updateMap(placeName, element) {
            const url = "https://maps.google.com/maps?q=" + encodeURIComponent(placeName) + "&t=&z=15&ie=UTF8&iwloc=&output=embed";
            document.getElementById('map-frame').src = url;
            $('.venue-box-fancy').css('background', '#f9f9f9');
            $(element).closest('.venue-box-fancy').css('background', '#fff9e6');
        }
    </script>
    <style>
        body {
            background: url('../img/gastro_1.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        .turizm-card {
            border-left: 10px solid #fec107 !important;
            background: rgba(255, 255, 255, 0.95) !important;
            color: #333 !important;
        }
        .food-item p {
            color: #555 !important;
            font-size: 14px;
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 15px;
            display: block !important;
        }
        .food-item strong {
            color: #000 !important;
            font-size: 18px;
        }
        header.responsive-nav { border-bottom: 3px solid #fec107 !important; }
        .navbar-toggle { background-color: #fec107 !important; }
        .humor-box {
            color: #fec107;
            border: 2px dashed #fec107;
            background: rgba(0, 0, 0, 0.7);
            padding: 15px;
            margin: 20px 0;
            font-size: 18px;
        }
        #main-nav li[style*="background: #28a745"] {
            background: #fec107 !important;
        }
    </style>

</body>
</html>
