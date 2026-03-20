<?php
require_once __DIR__ . '/../init.php';
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
        body {
            background: url('../img/latnivalok_back.jpg') no-repeat center center fixed !important;
            background-size: cover !important;
        }
        /* --- HUMOR BOX (Most már egységes a többivel) --- */
    .humor-box {
        background: rgba(0, 0, 0, 0.7) !important;
        color: #b76c09 !important;
        padding: 15px 25px;
        border-radius: 50px;
        border: 2px dashed #d6790fea;
        display: inline-block;
        margin: 20px auto;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0,0,0,0.4);
    }
        /* Egyedi ikon szín a látnivalókhoz */
        .turizm-card.latnivalo i {
            color: #aa5209;
            margin-right: 10px;
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
            </section>

            <footer style="padding: 20px; text-align: center;">
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
    </script>
</body>
</html>
