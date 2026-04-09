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
    <title>NÓGRÁD - Bakancsos Kalandok</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/fontAwesome.css">
    <link rel="stylesheet" href="../css/templatemo-style.css">
    <link rel="stylesheet" href="../css/turizm.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="mobile_style.css">
    <style>
        :root {
            --turan-green: #2d5a27;
            --turan-accent: #fec107;
            --card-bg: #ffffff;
        }
        body {
            background: url('../img/turaz_1.jpg') no-repeat center center fixed !important;
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
        .page-content .col-md-5,
        .page-content .col-md-7 {
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
        }
        .tour-plan-card {
            background: var(--card-bg) !important;
            border-left: 10px solid var(--turan-green) !important;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            cursor: pointer;
            position: relative;
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            padding-right: 120px;
            min-height: 250px;
        }
        .tour-plan-card.active-tour {
            border-left-color: var(--turan-accent) !important;
            transform: scale(1.02);
        }
        .tour-plan-card h5 {
            color: #1a1a1a !important;
            font-weight: 800 !important;
            margin-bottom: 8px !important;
            font-size: 20px !important;
        }
        .tour-plan-card p {
            color: #444444 !important;
            font-weight: 600 !important;
            margin-bottom: 5px !important;
            font-size: 14px !important;
        }
        .tour-note-btn {
            position: absolute;
            right: 14px;
            bottom: 14px;
            border: 1px solid #fec107;
            background: rgba(255, 255, 255, 0.96);
            color: #1a1a1a;
            font-size: 13px;
            line-height: 1.2;
            padding: 10px 12px;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            transition: all 0.25s ease;
            max-width: 110px;
            white-space: nowrap;
        }
        .tour-note-btn:hover { background: #fec107; color: #000; transform: translateY(-1px); }
        .tour-note-modal { display: none; position: fixed; inset: 0; z-index: 10000; background: rgba(0, 0, 0, 0.55); padding: 18px; }
        .tour-note-modal.open { display: flex; align-items: center; justify-content: center; }
        .tour-note-modal__panel { width: min(92vw, 420px); background: #ffffff; border-radius: 16px; border-left: 8px solid #fec107; box-shadow: 0 18px 45px rgba(0,0,0,0.35); padding: 18px 18px 16px 18px; position: relative; }
        .tour-note-modal__close { position: absolute; top: 10px; right: 10px; border: none; background: transparent; font-size: 22px; line-height: 1; color: #333; cursor: pointer; }
        .tour-note-modal__number { font-size: 34px; font-weight: 900; color: rgba(0,0,0,0.08); position: absolute; right: 16px; top: 8px; }
        .tour-note-modal__title { font-size: 20px; font-weight: 800; color: #1a1a1a; margin: 0 34px 10px 0; }
        .tour-note-modal__quote { font-size: 15px; line-height: 1.55; color: #2a2a2a; margin: 0 0 10px 0; font-style: italic; }
        .tour-note-modal__desc { font-size: 13px; line-height: 1.55; color: #555; margin: 0; }
        @media (max-width: 767px) {
            .tour-plan-card { padding: 16px 16px 58px 16px; padding-right: 110px; border-radius: 14px; }
            .tour-plan-card h5 { font-size: 17px !important; padding-right: 10px; }
            .tour-plan-card p { font-size: 12px !important; padding-right: 10px; }
            .tour-number { font-size: 30px; right: 12px; }
            .tour-note-btn { right: 10px; bottom: 10px; font-size: 11px; padding: 7px 8px; max-width: 96px; }
            .tour-note-modal { padding: 12px; }
            .tour-note-modal__panel { width: min(94vw, 380px); padding: 16px 16px 14px 16px; }
            .tour-note-modal__title { font-size: 17px; }
            .tour-note-modal__quote { font-size: 13px; }
            .tour-note-modal__desc { font-size: 12px; }
        }
        .tour-number { position: absolute; right: 15px; top: 5px; font-size: 38px; font-weight: 900; color: rgba(0, 0, 0, 0.1) !important; }
        .tour-plan-card i { color: var(--turan-green) !important; margin-right: 8px; }
        .fun-fact { font-size: 13px; background: #f8f9fa !important; padding: 10px; margin-top: 10px; border-radius: 6px; border-left: 3px solid var(--turan-accent); color: #555 !important; }
        .map-wrapper { position: sticky; top: 20px; border: 10px solid #fff; border-radius: 20px; height: 600px; box-shadow: 0 15px 45px rgba(0,0,0,0.5); background: #222; overflow: hidden; }

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
            border: 2px solid #d4af37 !important;
            border-radius: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        @media (max-width: 767px) {
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
        @media (max-width: 991px) { .map-wrapper { position: relative !important; height: 450px !important; margin-top: 20px; } }
    </style>
</head>
<body class="page-turazas">
   <?= $kozos_menu ?>
<?= $kozos_mobile ?>

        <div class="page-content">
         
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h1 style="color:white; text-shadow: 2px 2px 10px #091169; font-size: 55px; margin-top: 20px;">Bakancsos <em>Kalandok</em></h1>
                        <div class="humor-box">"Nógrádban nem eltévedsz, hanem felfedezel... csak néha tovább tart, mint tervezted."</div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="tour-list-container">
                            <div class="tour-plan-card active-tour" onclick="updateMap('Salgóbánya, Salgó vára', this)">
                                <span class="tour-number">01</span>
                                <h5>Salgó vára & Boszorkány-kő</h5>
                                <p><i class="fa fa-map-marker"></i> Salgóbánya</p>
                                <p><i class="fa fa-clock-o"></i> 1.5 óra | <i class="fa fa-arrows-h"></i> 4.2 km</p>
                                <p><i class="fa fa-signal" style="color: #28a745;"></i> Nehézség: <strong>Könnyű</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(1)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Kazár, Riolittufa', this)">
                                <span class="tour-number">02</span>
                                <h5>Kazári Riolittufa-mező</h5>
                                <p><i class="fa fa-map-marker"></i> Kazár</p>
                                <p><i class="fa fa-clock-o"></i> 2 óra | <i class="fa fa-arrows-h"></i> 6.0 km</p>
                                <p><i class="fa fa-signal" style="color: #ffc107;"></i> Nehézség: <strong>Közepes</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(2)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Nagyoroszi, Drégely vára', this)">
                                <span class="tour-number">03</span>
                                <h5>Drégelyvár történelmi út</h5>
                                <p><i class="fa fa-map-marker"></i> Nagyoroszi / Drégelypalánk</p>
                                <p><i class="fa fa-clock-o"></i> 4 óra | <i class="fa fa-arrows-h"></i> 9.5 km</p>
                                <p><i class="fa fa-signal" style="color: #dc3545;"></i> Nehézség: <strong>Nehéz (meredek)</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(3)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Somoskő, Bazaltorgona', this)">
                                <span class="tour-number">04</span>
                                <h5>Somoskői Bazaltorgonák</h5>
                                <p><i class="fa fa-map-marker"></i> Somoskő</p>
                                <p><i class="fa fa-clock-o"></i> 1 óra | <i class="fa fa-arrows-h"></i> 2.5 km</p>
                                <p><i class="fa fa-signal" style="color: #28a745;"></i> Nehézség: <strong>Nagyon könnyű</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(4)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Salgótarján, Karancs kilátó', this)">
                                <span class="tour-number">05</span>
                                <h5>Karancs, a palóc Olümposz</h5>
                                <p><i class="fa fa-map-marker"></i> Salgótarján</p>
                                <p><i class="fa fa-clock-o"></i> 4.5 óra | <i class="fa fa-arrows-h"></i> 11.0 km</p>
                                <p><i class="fa fa-signal" style="color: #dc3545;"></i> Nehézség: <strong>Kihívást jelentő</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(5)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Hollókő, Vár', this)">
                                <span class="tour-number">06</span>
                                <h5>Hollókői várkör</h5>
                                <p><i class="fa fa-map-marker"></i> Hollókő</p>
                                <p><i class="fa fa-clock-o"></i> 1.5 óra | <i class="fa fa-arrows-h"></i> 3.8 km</p>
                                <p><i class="fa fa-signal" style="color: #28a745;"></i> Nehézség: <strong>Könnyű</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(6)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Mátraverebély, Szentkút', this)">
                                <span class="tour-number">07</span>
                                <h5>Szentkúti Remetebarlangok</h5>
                                <p><i class="fa fa-map-marker"></i> Mátraverebély</p>
                                <p><i class="fa fa-clock-o"></i> 2 óra | <i class="fa fa-arrows-h"></i> 5.2 km</p>
                                <p><i class="fa fa-signal" style="color: #ffc107;"></i> Nehézség: <strong>Közepes</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(7)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Ipolytarnóc, Ősmaradványok', this)">
                                <span class="tour-number">08</span>
                                <h5>Ipolytarnóci Lombkorona-sétány</h5>
                                <p><i class="fa fa-map-marker"></i> Ipolytarnóc</p>
                                <p><i class="fa fa-clock-o"></i> 3 óra | <i class="fa fa-arrows-h"></i> 4.0 km</p>
                                <p><i class="fa fa-signal" style="color: #28a745;"></i> Nehézség: <strong>Könnyű (családi)</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(8)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Alsópetény, Prónay-kilátó', this)">
                                <span class="tour-number">09</span>
                                <h5>Prónay-kilátó Panoráma túra</h5>
                                <p><i class="fa fa-map-marker"></i> Alsópetény / Romhány</p>
                                <p><i class="fa fa-clock-o"></i> 3.5 óra | <i class="fa fa-arrows-h"></i> 8.5 km</p>
                                <p><i class="fa fa-signal" style="color: #ffc107;"></i> Nehézség: <strong>Közepes</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(9)">Népi mondás</button>
                            </div>
                            <div class="tour-plan-card" onclick="updateMap('Bánk, Tópart', this)">
                                <span class="tour-number">10</span>
                                <h5>Bánki-tó kerülő séta</h5>
                                <p><i class="fa fa-map-marker"></i> Bánk</p>
                                <p><i class="fa fa-clock-o"></i> 0.5 óra | <i class="fa fa-arrows-h"></i> 2.1 km</p>
                                <p><i class="fa fa-signal" style="color: #28a745;"></i> Nehézség: <strong>Séta (nagyon könnyű)</strong></p>
                                <button type="button" class="tour-note-btn" onclick="event.stopPropagation(); openTourNote(10)">Népi mondás</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="map-wrapper">
                            <iframe id="map-frame" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2673.743120531!2d19.8458!3d48.1758!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47401662095f9e85%3A0x1d365f573f08680!2sSalg%C3%B3%20v%C3%A1ra!5e1!3m2!1shu!2shu!4v1700000000000" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="premium-footer" style="padding: 12px; text-align: center; color: #0a1f98;">
                <div class="credits-container">
                    <a href="../Editors.php" class="credits-link" style="display:inline-block; color: inherit; text-decoration: none; cursor: pointer;">
                        <p class="site-footer-fixed__pill">Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                    </a>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        function updateMap(destination, element) {
            const fullLocation = destination + ", Nógrád, Hungary";
            const url = "https://maps.google.com/maps?q=" + encodeURIComponent(fullLocation) + "&t=k&z=15&ie=UTF8&iwloc=&output=embed";
            const mapFrame = document.getElementById('map-frame');
            mapFrame.src = url;
            $('.tour-plan-card').removeClass('active-tour');
            $(element).addClass('active-tour');
            if ($(window).width() < 992) {
                $('html, body').animate({ scrollTop: $(".map-wrapper").offset().top - 100 }, 600);
            }
        }

        $(document).ready(function() {
            $('#nav-toggle').on('click', function (e) {
                e.preventDefault();
                $('#main-nav').slideToggle(300);
            });
            console.log("A JavaScript sikeresen betöltve és fut!");
        });
    </script>
    <div id="tour-note-modal" class="tour-note-modal" aria-hidden="true">
        <div class="tour-note-modal__panel" role="dialog" aria-modal="true" aria-labelledby="tour-note-title">
            <button type="button" class="tour-note-modal__close" onclick="closeTourNote()" aria-label="Bezárás">&times;</button>
            <div class="tour-note-modal__number" id="tour-note-number">01</div>
            <h3 class="tour-note-modal__title" id="tour-note-title">Népi mondás</h3>
            <p class="tour-note-modal__quote" id="tour-note-quote"></p>
            <p class="tour-note-modal__desc" id="tour-note-desc"></p>
        </div>
    </div>
    <script>
        const tourNotes = {
            1: { number: "01", title: "Salgó vára & Boszorkány-kő", quote: "„Ahol a sziklából vár nőtt, és a szélben még ma is hallani a boszorkányok táncát.”", desc: "(A legenda szerint a Boszorkány-kőnél az ördög és a boszorkányok mulattak, a szélvihar pedig az ő hahotázásuk.)" },
            2: { number: "02", title: "Kazári Riolittufa-mező", quote: "„A holdbéli táj, amit az anyatermészet fehér csipkéből faragott a palócok földjén.”", desc: "" },
            3: { number: "03", title: "Drégelyvár történelmi út", quote: "„Szent hely ez: Szondi György vitézeinek hűsége még a kövekben is lüktet.”", desc: "(Arany János balladája után: „Él-e még a vár?” – a válasz a hősök emlékezetében van.)" },
            4: { number: "04", title: "Somoskői Bazaltorgonák", quote: "„Kővé vált vízesés, ahol a bazaltorgonák az ég felé zenélnek.”", desc: "" },
            5: { number: "05", title: "Karancs, a palóc Olümposz", quote: "„Aki felér a Karancs tetejére, az egész palóc világot a tenyerén hordozza.”", desc: "(Régi mondás, hogy a Karancs kilátójából tiszta időben még a Tátra csúcsai is integetnek.)" },
            6: { number: "06", title: "Hollókői várkör", quote: "„Vár, amit az ördögfiak építettek, és ahol megállt az idő a kövek között.”", desc: "(A legenda szerint a vár urának elrabolt szép asszonyát holló képében segítettek kiszabadítani az ördögök, innen a név.)" },
            7: { number: "07", title: "Szentkúti Remetebarlangok", quote: "„Szentkút vize gyógyít, remetéinek csendje pedig megnyugtatja a lelket.”", desc: "(A nép szerint Szent László lovának patanyomából fakadt itt fel az első forrás.)" },
            8: { number: "08", title: "Ipolytarnóci Lombkorona-sétány", quote: "„Ahol a múlt megkövült, és az ősidők óriásai hagyták ott lábuk nyomát.”", desc: "(Gyakran nevezik a „magyar Pompejinek” is.)" },
            9: { number: "09", title: "Prónay-kilátó Panoráma túra", quote: "„Ahol a horizont tágul, a gondok pedig eltörpülnek a Börzsöny és a Cserhát ölelésében.”", desc: "" },
            10:{ number: "10", title: "Bánki-tó kerülő séta", quote: "„A tenger, ami a hegyek közé tévedt – a nyugalom kék szigete.”", desc: "(Gyakran hívják a „tenger szemének” is a helyiek.)" }
        };
        function openTourNote(id) {
            const note = tourNotes[id];
            if (!note) return;
            document.getElementById("tour-note-number").textContent = note.number;
            document.getElementById("tour-note-title").textContent = note.title;
            document.getElementById("tour-note-quote").textContent = note.quote;
            document.getElementById("tour-note-desc").textContent = note.desc;
            document.getElementById("tour-note-modal").classList.add("open");
            document.getElementById("tour-note-modal").setAttribute("aria-hidden", "false");
        }
        function closeTourNote() {
            document.getElementById("tour-note-modal").classList.remove("open");
            document.getElementById("tour-note-modal").setAttribute("aria-hidden", "true");
        }
        document.getElementById("tour-note-modal").addEventListener("click", function (e) { if (e.target === this) closeTourNote(); });
    </script>
       <?php include '../ertekeles_statisztika.php'; ?> 
<?php include "../valuta/api_valuta.php"; ?>
</body>
</html>
