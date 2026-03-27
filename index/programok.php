<?php
require_once __DIR__ . '/../init.php';
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
        body {
            background: url('../img/program_back.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        /* Egyedi lila stílus a programokhoz */
        .turizm-card.program {
            border-left: 10px solid #9c27b0 !important;
        }
        .turizm-card.program i {
            color: #9c27b0;
            margin-right: 10px;
        }
        .humor-box {
            background: rgba(0, 0, 0, 0.7) !important;
            color: #9c27b0 !important;
            padding: 13px 15px;
            border-radius: 50px;
            border: 2px dashed #9c27b0;
            display: inline-block;
            margin: 20px auto;
            font-weight: bold;
            text-shadow: none;
        }

        /* A kártyák tárolója flexibilis lesz */
            
                .program-container {
                display: flex;
                flex-wrap: wrap;
                justify-content: flex-start; /* Balról tölti fel a sort */
                gap: 20px;
                padding: 16px;
            }

            .program-item {
                flex: 1 1 333px; /* Az '1' miatt automatikusan kitölti a maradék helyet */
                max-width: 600px; /* Ne engedjük túl szélesre nyúlni egy sorban */
                min-width: 300px; /* Mobilon se legyen túl kicsi */
            }

            .turizm-card.program {
                height: 100%;
                display: flex;
                flex-direction: column;
                justify-content: space-between; /* Szépen elosztja a szöveget a kártyán belül */
            }
                .fun-fact strong {
                color: #9c27b0; /* A dátumok is kapják meg a programok lila színét */
            }
    </style>
</head>
<body>
    <header class="nav-down responsive-nav">

    <div class="logo-mobile-left">
       <a href="../index.php">NÓG<span>RÁD</span></a>
    </div>
    <button type="button" id="nav-toggle" class="navbar-toggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <div id="main-nav">
        <nav style="padding: 12px;">
            <ul class="nav navbar-nav">
                <?php include 'mobile_menu.php'; ?>
            </ul>
        </nav>
    </div>
</header>

    <div class="main-wrapper">
        <div class="sidebar-navigation">
        
            <nav>
                <div class="user-info" style="padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 15px;">
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
            <?php include "../weather.php"; ?>
        </div>

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

            <footer class="premium-footer" style="padding: 10px; text-align: center; color: #0a1f98;">
                <a href="../Proofiles.php" style="display:inline-block; color: inherit; text-decoration: none; cursor: pointer;">
                    <p>Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                </a>
            </footer>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
        // Kattintás a hamburger ikonra
        $('#nav-toggle').on('click', function (e) {
            e.preventDefault();
            $('#main-nav').slideToggle(300); // 300ms alatt gördül le/fel
        });
    });
    </script>
    <?php include "../weather_mobile.php"; ?>
</body>
</html>
