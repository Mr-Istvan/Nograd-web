<?php
session_start();
// Csak akkor hívjuk meg az adatbázist, ha tényleg kell valamihez
// include "db.php"; 
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>NÓGRÁD</title>
        
<meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/fontAwesome.css">
        <link rel="stylesheet" href="css/light-box.css">
        <link rel="stylesheet" href="css/owl-carousel.css">
        <link rel="stylesheet" href="css/templatemo-style.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    </head>

<body>

     <header class="nav-down responsive-nav">
    <div class="logo" style="float: left; padding: 15px 20px;">
       <a href="index.php" class="logo-text" style="font-size: 22px; text-transform: uppercase; font-weight: 800; text-decoration: none;">NÓG<span style="color: #d4a373;">RÁD</span></a>
    </div>

    <button type="button" id="nav-toggle" class="navbar-toggle" style="margin-top: 12px;">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    
    <div id="main-nav" style="display: none; clear: both; background: rgba(0,0,0,0.95); width: 100%;">
        <nav style="padding: 10px 0;"> 
            <ul class="nav navbar-nav" style="margin: 0; list-style: none; padding: 0;">
                <?php if(isset($_SESSION['user_name'])): ?>
                    <li class="profile-link" style="padding: 5px 20px;">
                        <a href="profile.php" style="font-weight: bold; color: #fec107 !important; text-decoration: none;">
                            <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                    </li>
                    <li style="padding: 0 20px 10px 20px;">
                        <a href="logout.php" style="color: #ff4d4d !important; font-size: 12px; text-decoration: none;">[ Kilépés ]</a>
                    </li>
                <?php else: ?>
                    <li style="padding: 5px 20px;"><a href="login.php" style="color: #fff; text-decoration: none;"><i class="fa fa-sign-in"></i> Bejelentkezés</a></li>
                    <li style="padding: 5px 20px;"><a href="reg_id.php" style="color: #fff; text-decoration: none;"><i class="fa fa-user-plus"></i> Regisztráció</a></li>
                <?php endif; ?>

                <li style="height: 1px; background: rgba(255,255,255,0.2); margin: 5px 20px;"></li>

                <li style="padding: 5px 20px;"><a href="#top" style="color: #fff; text-decoration: none;">Kezdőlap</a></li>
                <li style="padding: 5px 20px;"><a href="#featured" style="color: #fff; text-decoration: none;">Kiemelt</a></li>
                <li style="padding: 5px 20px;"><a href="#projects" style="color: #fff; text-decoration: none;">Galéria</a></li>
                <li style="padding: 5px 20px;"><a href="blog.php" style="color: #fff; text-decoration: none;">Blog</a></li>
                <li style="padding: 5px 20px;"><a href="#video" style="color: #fff; text-decoration: none;">Bemutató</a></li>
                <li style="padding: 5px 20px;"><a href="#map" style="color: #fff; text-decoration: none;">Térképek</a></li>
                 <li style="padding: 5px 20px;"><a href="#contact" style="color: #fff; text-decoration: none;">Kapcsolat</a></li>
            </ul>
        </nav>
    </div>
</header>

<div class="sidebar-navigation">
    <div class="logo">
        <a href="index.php">Nóg<em>rád</em></a>
    </div>
    <nav>
        <ul>
            <?php if(isset($_SESSION['user_name'])): ?>
                <li>
                    <a href="profile.php" style="color: #fec107;">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        <i class="fa fa-user"></i> <?php echo $_SESSION['user_name']; ?>
                    </a>
                </li>
                <li>
                    <a href="logout.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Kilépés
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="login.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Bejelentkezés
                    </a>
                </li>
                <li>
                    <a href="reg_id.php">
                        <span class="rect"></span>
                        <span class="circle"></span>
                        Regisztráció
                    </a>
                </li>
            <?php endif; ?>

            <li style="height: 1px; background: rgba(255,255,255,0.1); margin: 10px 0;"></li>

            <li>
                <a href="#top">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Kezdőlap
                </a>
            </li>
            <li>
                <a href="#featured">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Kiemelt
                </a>
            </li>
            <li>
                <a href="#projects">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Galéria
                </a>
            </li>
            <li>
                <a href="blog.php">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Blog
                </a>
            </li>
            <li>
                <a href="#video">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Bemutató
                </a>
            </li>

            <li>
                <a href="#map">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Térképek
                </a>
            </li>
            <li>
                <a href="#contact">
                    <span class="rect"></span>
                    <span class="circle"></span>
                    Kapcsolat
                </a>
            </li>
        </ul>
    </nav>
    <ul class="social-icons">
        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
        <li><a href="#"><i class="fa fa-rss"></i></a></li>
        <li><a href="#"><i class="fa fa-behance"></i></a></li>
    </ul>
</div>

        <div class="slider">
            <div class="Modern-Slider content-section" id="top">
                <div class="item item-1">
                    <div class="img-fill">
                    <div class="image"></div>
                    <div class="info">
                        <div>
                          <h1>Csodálatos helyek<br>Nógrád megyében</h1>
                          <p>Nógrád legszebb látnivalói, kirándulóhelyek,<br>
                                városnézések és programajánlók.</p>
                          <div class="white-button button">
                              <a href="#featured">Fedezz fel többet!</a>
                          </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="item item-2">
                    <div class="img-fill">
                        <div class="image"></div>
                        <div class="info">
                        <div>
                          <h1>Kérlek, mondd el <br>barátaidnak</h1>
                          <p>Keresd a legszebb úticélokat!
                            <br>Oszd meg barátaiddal!</p>
                          
                          <div class="white-button button">
                              <a href="#featured">Ossz meg többet!</a>
                          </div>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="item item-3">
                    <div class="img-fill">
                        <div class="image"></div>
                        <div class="info">
                        <div>
                          <h1>Nógrád vármegyei fesztiválok<br>programok</h1>
                          <p>Válogass kedvedre Nógrád vármegyei <br>kirándulóhelyekből,látnivalókból és
                                programjaiból.</p>
                          
                          <div class="white-button button">
                              <a href="#featured">Ajánlj többet!</a>
                          </div>
                        </div>
                        </div>
                    </div>
                </div>
                </div>
        </div>


    <div class="page-content">
         <section id="featured" class="content-section">
        <div class="section-heading">
            <h1>Nógrád megye<br><em>Csodái</em></h1>
            <p>“Egy csodálatos világban élünk, amely tele van szépséggel és kalanddal.
            <br>ha nyitott szemmel keressük őket.” – Jawaharlal Nehru</p>
        </div>
        <div class="section-content">
            <div class="owl-carousel owl-theme">
                
                <div class="item">
                    <div class="image">
                        <img src="img/latnivalok.jpg" alt="Látnivalók">
                        <div class="featured-button button">
                            <a href="index/latnivalok.php">Ugrás a galériára</a>
                        </div>
                    </div>
                    <div class="text-content">
                        <h4>Látnivalók, kirándulóhelyek</h4>
                        <span>Irány a természet</span>
                        <p>Ha Magyarország felfedezetlen kincseire vágysz, ahol a természet, a történelem és a nyugalom kéz a kézben jár, akkor Nógrád megye a te helyed. Ez a kis megye az ország északi részén található, és bár területileg kicsi, látnivalókban és élményekben annál gazdagabb.</p>
                    </div>
                </div>

                <div class="item">
                    <div class="image">
                        <img src="img/programok.jpg" alt="Programajánló">
                        <div class="featured-button button">
                            <a href="index/programok.php">Ugrás a galériára</a>
                        </div>
                    </div>
                    <div class="text-content">
                        <h4>Programajánló</h4>
                        <span>Élmények, kalandok</span>
                        <p>Nógrád megye számos élményprogramot kínál. Az aktív kikapcsolódás szerelmeseinek kerékpártúrák, lovastúrák, kalandpark. Fesztiválozóknak falunapok és Palóc Fesztiválok, kézműves foglalkozások, néptáncbemutatók. Családoknak erdei kisvasutak, kalandparkok, valamint horgász- és vízi sportlehetőségek.</p>
                    </div>
                </div>

                <div class="item">
                    <div class="image">
                        <img src="img/szallasok.jpg" alt="Szálláshelyek">
                        <div class="featured-button button">
                            <a href="index/szallasok.php">Szálláshelyekért kattints ide!</a>
                        </div>
                    </div>
                    <div class="text-content">
                        <h4>Szálláshelyek</h4>
                        <span>Összes szálláshely Nógrádban</span>
                        <p>Nógrád megye szállásai között mindenki megtalálja a számára ideális pihenőhelyet: a kényelmes wellnesshotelektől és hangulatos falusi vendégházaktól kezdve a romantikus kastélyszállókig. Akár természetközeli kikapcsolódásra, akár aktív túrázásra vágysz, a megye változatos szállástípusai tökéletes kiindulópontot kínálnak a felfedezéshez.</p>
                    </div>
                </div>

                <div class="item">
                    <div class="image">
                        <img src="img/utazasi-praktikak.jpg" alt="Utazási praktikák Nógrádban">
                        <div class="featured-button button">
                            <a href="index/utazasi-praktikak.php">Részletek</a>
                        </div>
                    </div>
                    <div class="text-content">
                        <h4>Utazási Praktikák</h4>
                        <span>Hogyan közlekedj és spórolj</span>
                        <p>Nógrádban a várakhoz gyakran meredek út vezet. Hasznos infók: Parkolási díj Somoskőnél kb. 500-1000 Ft. Buszjáratok Salgótarjánból óránként indulnak a főbb látványosságokhoz. Érdemes készpénzt hozni, mert a kis falvakban (pl. Kazár) kevés az ATM!</p>
                    </div>
                </div>

                <div class="item">
                    <div class="image">
                        <img src="img/gasztronomia.jpg" alt="Nógrádi ételek">
                        <div class="featured-button button">
                            <a href="index/gasztronomia.php">Étterem ajánló</a>
                        </div>
                    </div>
                    <div class="text-content">
                        <h4>Nógrádi Ízutazás</h4>
                        <span>Palóc konyha és helyi piacok</span>
                        <p>Keresse a helyi védjegyes termékeket! A palócleves és a juhtúrós sztrapacska mellett érdemes megkóstolni a nógrádi nemesnyár lekvárt. Salgótarjánban szombatonként a helyi kistermelői piacon friss kecskesajtot és füstölt vadhúst is vásárolhat.</p>
                    </div>
                </div>

                <div class="item">
                    <div class="image">
                        <img src="img/bakancsos-kalandok.jpg" alt="Bakancsos Kalandok">
                        <div class="featured-button button">
                            <a href="index/turazas.php">Térkép megnyitása</a>
                        </div>
                    </div>
                    <div class="text-content">
                        <h4>Bakancsos Kalandok</h4>
                        <span>Kilátók és tanösvények</span>
                        <p>Látogasson el az Ipolytarnóci Ősmaradványokhoz, ahol 17 millió éves leletek várják. A Prónay-kilátóból (Alsópetény) tiszta időben a Tátra csúcsai is látszanak. A Medves-fennsík bazaltkúpjai pedig Európában is egyedülálló látványvonalat nyújtanak.</p>
                    </div>
                </div>

            </div> 
        </div> 
        </section> ```



            <section id="projects" class="content-section">
    <div class="section-heading">
        <h1>Élménydús<br><em>Nógrád</em></h1>
        <p>
            <strong>Miért válaszd Nógrád vármegyét?</strong>
            Várak, panorámák, erdők és palóc hangulat – rövid kiruccanásra is tökéletes, élményekből pedig sosem fogy ki.
        </p>
    </div>

    <?php
    // Főoldali "Galéria" rész: 3 random kocka a blog_* és portfolio_* képekből (blog_back.jpg kizárva)
    $galleryPool = [];

    // blog_* képek (kizárjuk a blog_back.jpg-t)
    $blogPool = glob(__DIR__ . '/img/blog_*.jpg') ?: [];
    foreach ($blogPool as $p) {
        if (basename($p) === 'blog_back.jpg') continue;
        $galleryPool[] = 'img/' . basename($p);
    }

    // portfolio_* thumb képek
    $portfolioPool = glob(__DIR__ . '/img/portfolio_*.jpg') ?: [];
    foreach ($portfolioPool as $p) {
        $galleryPool[] = 'img/' . basename($p);
    }

    // Biztonság: duplikációk kiszedése + keverés + 3 elem
    $galleryPool = array_values(array_unique($galleryPool));
    shuffle($galleryPool);
    $random3 = array_slice($galleryPool, 0, 3);
    ?>

    <div class="section-content">
        <div class="row">
            <?php foreach ($random3 as $imgPath): ?>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <div class="item">
                        <a href="<?php echo htmlspecialchars($imgPath); ?>" data-lightbox="home-gallery">
                            <div class="hover-effect">
                                <div class="hover-content">
                                    
                                    <p>Véletlenszerű válogatás</p>
                                </div>
                            </div>
                            <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Galéria kép" class="img-responsive">
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="margin-top: 18px;">
            <div class="accent-button button">
                <a href="galeria.php">Ugrás a teljes galériára</a>
            </div>
        </div>

        <div style="margin-top: 28px; padding: 18px; background: #f4f4f4; border-radius: 10px; text-align: left;">
            <h3 style="margin-top: 0; color: #232323;">Blog – Üzenőfal</h3>
            <p style="color: #4a4a4a; margin-bottom: 12px;">
                Van kedved megosztani egy élményt, tippet vagy kedvenc kirándulóhelyet Nógrádból?
                A Blog oldalon üzenhetsz, olvashatsz és csatlakozhatsz a beszélgetéshez.
            </p>
            <div class="accent-button button" style="margin-top: 10px;">
                <a href="blog.php" style="font-size: 19.5px; height: 66px; line-height: 66px; padding: 0 38px;">Ugrás a Blogra</a>
            </div>
        </div>
    </div>
</section>
            <section id="video" class="content-section">
                <div class="row">
                    <div class="col-md-12">
                        <div class="section-heading">
                            <h1>Nógrád <em>bemutatkozó</em> kisfilmje.</h1>
                            <p>Engedd, hogy elvarázsoljon Nógrád megye szépsége!</p>
                        </div>
                        <div class="text-content">
                            <p>Nógrád lankáin szellő szalad,
                            erdők ölén bújik a vad.
                            Várak őrzik múlt idők meséjét,
                            patak csillan, tükrözi az ég kékjét.

                            Barangolj itt, hol béke vár,
                            hol minden ösvény új csodát kínál.
                            Nógrád megye, szíved dalol,
                            mert itt a természet maga szól!</p>
                        </div>
                        <div class="accent-button button">
                            <a href="blog.php">Ugrás a hozzászólásokra</a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="box-video">
                            <div class="video-container">
                                <video width="100%" height="520" controls>
                                    <source src="img/video.mp4" type="video/mp4">
                                    A böngésződ nem támogatja a videó lejátszását.
                                </video>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
                 
               <section id="contact" class="content-section">
                <div id="map">
                
                	
                    <iframe src="https://maps.google.com/maps?q=N%C3%B3gr%C3%A1d+megye&t=&z=9&ie=UTF8&iwloc=&output=embed"
                    width="80%" height="400px" frameborder="0" style="border:0" allowfullscreen></iframe>
                </div>
                <div id="contact-content">
                    <div class="section-heading">
                        <h1>Hozzászólás<br><em>Vélemény</em></h1>
                        <p>
                            A hozzászólásokat átköltöztettük a <strong>Blog</strong> oldalra.
                            <br>
                            Menj a <a href="blog.php">Blog</a> menüpontra, és írj egy üzenetet – kíváncsiak vagyunk a véleményedre!
                        </p>
                    </div>
                    <div class="section-content">
                        <div class="accent-button button">
                            <a href="blog.php">Ugrás a Blogra</a>
                        </div>
                    </div>
                </div>
            </section>
            <section class="footer">
                <p>Nógrádi csodák &copy; Vizsgaremek
                
                . 2026  //Készítette : #F.Melinda és #M.István</p>
            </section>
        </div>

   
   

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function() {
            // A mobil menü toggle-t és a kattintást TÖRÖLTÜK innen, 
            // mert a main.js már kezeli őket a helyes (767px) mérettel.

            // FEJLÉC ELREJTÉSE GÖRDÍTÉSKOR
            var didScroll;
            var lastScrollTop = 0;
            var delta = 5;
            var navbarHeight = $('header').outerHeight();

            $(window).scroll(function(event){
                didScroll = true;
            });

            setInterval(function() {
                if (didScroll) {
                    hasScrolled();
                    didScroll = false;
                }
            }, 250);

            function hasScrolled() {
                var st = $(this).scrollTop();
                if(Math.abs(lastScrollTop - st) <= delta) return;
                
                if (st > lastScrollTop && st > navbarHeight){
                    // Lefelé görgetésnél elrejti
                    $('header').removeClass('nav-down').addClass('nav-up');
                } else {
                    // Felfelé görgetésnél megjeleníti
                    if(st + $(window).height() < $(document).height()) {
                        $('header').removeClass('nav-up').addClass('nav-down');
                    }
                }
                lastScrollTop = st;
            }
        }); // Itt zárul a document.ready
    </script>
</body>
</html>
