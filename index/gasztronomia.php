<?php
session_start();
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
        /* CSAK az oldal-specifikus színek maradjanak itt! */
        .turizm-card { border-left: 10px solid #fec107 !important; } /* Gasztro sárga */
        header.responsive-nav { border-bottom: 3px solid #fec107 !important; }
        .navbar-toggle { background-color: #fec107 !important; }
        .humor-box { color: #fec107; border: 2px dashed #fec107; } /* A vicc doboz is legyen sárga */
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
                        <a href="../logout.php" style="color: #fec107; text-decoration: none; font-weight: bold; font-size: 13px;">[ Kilépés ]</a>
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
                                    <iframe id="map-frame" 
                                            src="https://maps.google.com/maps?q=Nógrád+megye+gasztronómia&t=&z=10&ie=UTF8&iwloc=&output=embed" 
                                            width="100%" 
                                            height="100%" 
                                            style="border:0;" 
                                            allowfullscreen="" 
                                            loading="lazy">
                                    </iframe>
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
                        </div> </div> </div> </section>
            
            <footer style="padding: 20px; text-align: center; color: #0a1f98;">
                <p>Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
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

        // Térkép frissítő függvény (Ide került be)
        function updateMap(placeName, element) {
            // Frissítjük a térkép URL-jét a kiválasztott helyre
            const url = "https://maps.google.com/maps?q=" + encodeURIComponent(placeName) + "&t=&z=15&ie=UTF8&iwloc=&output=embed";
            document.getElementById('map-frame').src = url;

            // Vizuális visszajelzés a kijelölésről
            $('.venue-box-fancy').css('background', '#f9f9f9'); // Összes visszaállítása alapra
            $(element).closest('.venue-box-fancy').css('background', '#fff9e6'); // Aktuális kiemelése
        }
    </script>


<style>
        /* 1. HÁTTÉRKÉP BEÁLLÍTÁSA */
        body {
            background: url('../img/gastro_1.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }

        /* 2. KÁRTYÁK ÉS SZÖVEG SZÍNEK */
        .turizm-card { 
            border-left: 10px solid #fec107 !important; 
            background: rgba(255, 255, 255, 0.95) !important; 
            color: #333 !important; /* Ettől lesz sötét a főszöveg */
        }
        
        /* Ez hozza vissza az ételek alatti kis mondásokat */
        .food-item p {
            color: #555 !important; 
            font-size: 14px;
            font-style: italic;
            line-height: 1.6;
            margin-bottom: 15px;
            display: block !important; /* Biztosítjuk, hogy ne legyen rejtve */
        }

        .food-item strong {
            color: #000 !important;
            font-size: 18px;
        }

        /* 3. EGYÉB DESIGN ELEMEK */
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

        /* Mobil menü elválasztó csíkja sárga legyen */
        #main-nav li[style*="background: #28a745"] { 
            background: #fec107 !important; 
        }
    </style>
</body>
</html>
