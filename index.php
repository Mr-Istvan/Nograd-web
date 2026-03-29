<?php
require_once __DIR__ . '/init.php';
// A képed alapján a nézet neve: ErtekelesekSzama
// Ebben már benne van az eszam, atlag és likes oszlop
//$sql = "SELECT eszam, atlag, likes FROM ErtekelesekSzama LIMIT 1"; // ezt akkor kell használjuk ha a like és értékesitést is futtatjuk!!
//$res = mysqli_query($conn, $sql);

//if (!$res) {
 //   die("Hiba a lekérdezésben: " . mysqli_error($conn));
//}

//$row = mysqli_fetch_assoc($res);

// Biztonsági tartalék, ha üres lenne a nézet
//if (!$row) {
 //   $row = ['eszam' => 0, 'atlag' => 0, 'likes' => 0];
//}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>NÓGRÁD Csodák</title>
        
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/light-box.css">
        <link rel="stylesheet" href="css/owl-carousel.css">
        <link rel="stylesheet" href="css/templatemo-style.css">
        <link rel="stylesheet" href="index/mobile_style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    </head>

<body>
   
    <header class="nav-down responsive-nav">
    

    <div class="logo" style="float: left; padding: 15px 20px;">
       <a href="index.php" class="logo-text" style="font-size: 22px; text-transform: uppercase; font-weight: 800; text-decoration: none;">NÓG<span style="color: #d4a373;">RÁD</span></a>
    </div>

    <button type="button" id="nav-toggle" class="navbar-toggle" style="margin-top: 8px;">
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
                        <li style="padding: 0 15px 10px 15px;">
                            <a href="logout.php" style="color: #ff4d4d !important; font-size: 12px; text-decoration: none;">[ Kilépés ]</a>
                        </li>
                    <?php else: ?>
                        <li style="padding: 5px 15px;"><a href="login.php" style="color: #fff; text-decoration: none;"><i class="fa fa-sign-in"></i> Bejelentkezés</a></li>
                        <li style="padding: 5px 15px;"><a href="reg_id.php" style="color: #fff; text-decoration: none;"><i class="fa fa-user-plus"></i> Regisztráció</a></li>
                    <?php endif; ?>

                    <li style="height: 1px; background: rgba(255,255,255,0.2); margin: 1px 1px;"></li>

                    <li style="padding: 5px 10px;"><a href="#top" style="color: #fff; text-decoration: none;">Kezdőlap</a></li>
                    <li style="padding: 5px 10px;"><a href="#featured" style="color: #fff; text-decoration: none;">Kiemelt</a></li>
                    <li style="padding: 5px 10px;"><a href="#projects" style="color: #fff; text-decoration: none;">Galéria</a></li>
                    <li style="padding: 5px 10px;"><a href="blog.php" style="color: #fff; text-decoration: none;">Blog</a></li>
                    <li style="padding: 5px 10px;"><a href="#video" style="color: #fff; text-decoration: none;">Bemutató</a></li>
                    <li style="padding: 5px 10px;"><a href="#map" style="color: #fff; text-decoration: none;">Térképek</a></li>
                     <li style="padding: 5px 10px;"><a href="#contact" style="color: #fff; text-decoration: none;">Kapcsolat</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <div class="sidebar-navigation">
        <div class="logo">
            <a href="index.php"><em>Nóg</em>rád </a>
        </div>
        <nav>
            <ul>
                <?php if(isset($_SESSION['user_name'])): ?>
                    <li>
                        <a href="profile.php" style="color: #fec107;">
                            <span class="rect"></span>
                            <span class="circle"></span>
                            <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?>
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

                <li style="height: 1px; background: rgba(255,255,255,0.1); margin: 1px 1px;"></li>

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
        <?php include "weather.php"; ?> 
        <style>
    .sidebar-stats {
        padding: 20px;
        color: white;
        border-top: 1px solid rgba(255,255,255,0.1);
        text-align: left;
    }
    .btn-like-sidebar {
        background: transparent;
        color: white;
        border: 1px solid #7fd0ff;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 14px;
        cursor: pointer;
        transition: 0.3s;
        width: 100%;
        margin-top: 10px;
    }
    .btn-like-sidebar:hover {
        background: #7fd0ff;
        color: #000;
        box-shadow: 0 0 15px rgba(127, 208, 255, 0.5);
   }

/* VISSZAHÚZZUK A GOMBOT KÖZÉPRE */
.rating-link-container {
    position: relative !important;
    /* A korábbi fix 250px-et leütjük nullára */
    left: 0 !important; 
    /* Vagy ha még mindig kint van, akkor használhatsz negatív margót: */
    /* margin-left: -250px !important; */ 
    
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 20px auto !important;
}

/* A gomb alatti készítők sávját is húzzuk vissza */
.credits-container {
    left: 0 !important;
    width: 100% !important;
    text-align: center !important;
}

.premium-footer {
    /* Biztosítjuk, hogy a footer tudja, hol a széle */
    /* padding-left: 250px !important; */
    box-sizing: border-box !important;
    text-align: center;
}

@media (max-width: 767px) {
    /* Mobilon tiltsd le a visszahúzást, mert ott nincs sidebar! */
    .premium-footer {
        padding-left: 0 !important;
    }
    .rating-link-container {
        margin-left: 0 !important;
    }
}

.btn-sentra-index {
    display: inline-block;
    text-decoration: none;
    background: #1f378c; /* Sötétkék háttér, hogy látszódjon a fehér szöveg */
    color: #ffffff !important;
    border: 2px solid #345af2;
    padding: 14px 40px;
    font-weight: bold;
    text-transform: uppercase;
    border-radius: 50px;
    transition: 0.4s;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.btn-sentra-index:hover {
    background: #00ffff;
    color: #000 !important;
    box-shadow: 0 0 25px #00ffff;
    transform: translateY(-3px);
}
</style>

      <!--  <div class="sidebar-stats">          //ezen ahelyen ez értékelési statisztikák lennének, müködik, de a Like-olási gomb miatt most nem fér el szépen, így egyelőre félretettem. 
            <div style="margin-bottom: 8px;">
                <i class="fa fa-users" style="color: #ffffff; width: 20px;"></i> 
                <span id="eszam"><?= $row['eszam'] ?></span> kitöltő
            </div>
            <div style="margin-bottom: 8px;">                                              // Ez az értékelési átlag, de mivel a Like gomb miatt nem fér el szépen, így egyelőre félretettem.             
                <i class="fa fa-star" style="color: #ffffff; width: 20px;"></i> 
                <span id="atlag"><?= round($row['atlag'], 2) ?></span> / 5
            </div>
            <div style="margin-bottom: 8px;">
                <i class="fa fa-thumbs-up" style="color: #f0f0f0; width: 20px;"></i> // Ez a Like-ok száma, de mivel a Like gomb miatt nem fér el szépen, így egyelőre félretettem.
                <span id="likes"><?= $row['likes'] ?></span> like
            </div>
            <button onclick="like()" class="btn-like-sidebar">👍 LIKE</button>
        </div>  -->
        <ul class="social-icons" style="display: flex !important; list-style: none; padding: 0; gap: 15px; justify-content: center;">
    <li><a href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook-official"></i></a></li>
    <li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a></li>
    <li><a href="https://mail.google.com/" target="_blank"><i class="fa fa-envelope"></i></a></li>
    <li><a href="https://www.youtube.com/" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
    <li><a href="https://wm-iskola.hu/" target="_blank"><i class="fa fa-graduation-cap"></i></a></li>
</ul>
    </div>

    <div class="slider">
        <div class="Modern-Slider content-section" id="top">
            <div class="item item-1">
                <div class="img-fill">
                <div class="image"></div>
                <div class="info">
                    <div>
                      <h1>Csodálatos helyek<br><em>Nóg</em>rád megyében</h1>
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
                                <h1><em>Nóg</em>rád vármegyei fesztiválok<br>programok</h1>
                                <p>Válogass kedvedre <em>Nóg</em>rád vármegyei <br>kirándulóhelyekből,látnivalókból és
                                programjaiból.</p>
                        
                             <div class="white-button button">
                          <a href="#featured">Ajánlj többet!</a>
                      </div>
                </div>
            </div>
            </div>
            </div>
        </div> </div> <div class="page-content">
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
                    <p><em>Nóg</em>rád megye számos élményprogramot kínál. Az aktív kikapcsolódás szerelmeseinek kerékpártúrák, lovastúrák, kalandpark. Fesztiválozóknak falunapok és Palóc Fesztiválok, kézműves foglalkozások, néptáncbemutatók. Családoknak erdei kisvasutak, kalandparkok, valamint horgász- és vízi sportlehetőségek.</p>
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
                    <h4><em>Nóg</em>rád Ízutazás</h4>
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
    </section>
     

    <section id="projects" class="content-section">
        <div class="section-heading">
            <h1>Élménydús<br><em>Nóg</em>rád </h1>
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
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12"> <!-- col-sm-4-re írtam, hogy tableten is 3 legyen -->
                        <div class="item">
                            <a href="<?php echo htmlspecialchars($imgPath); ?>" data-lightbox="home-gallery">
                                <div class="hover-effect">
                                    <div class="hover-content">
                                        <p>Véletlenszerű válogatás</p>
                                    </div>
                                </div>
                                <!-- Itt az új class: fixed-gallery-img -->
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Galéria kép" class="img-responsive fixed-gallery-img">
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
                    <h1><em>Nóg</em>rád <span style="font-size: 0.9em;">csodák</span> <span style="font-size: 0.75em;">bemutató!</span></h1>
                    <p>Engedd, hogy elvarázsoljon Nógrád megye szépsége!</p>
                </div>
            </div>
        </div>
        
        <div class="row"> <div class="col-md-12">
                <div class="fix-mobil-box">
                    <div class="paper-content">
                        <h2 class="paper-title">🌲 Nógrád Csodái – Digitális turisztikai platform</h2>
                        <p class="paper-intro">
                            A „Nógrád Csodái” egy modern, technológia-orientált turisztikai portál, amelynek célja Nógrád vármegye rejtett kincseinek és népszerű látványosságainak bemutatása. A platform a statikus információs oldalakon túlmutatva, valós idejű adatokkal és közösségi funkciókkal támogatja az utazók élményszerzését.
                        </p>
                        
                        <div class="paper-columns">
                            <div class="paper-col">
                                <h3>🌤️ Valós idejű időjárás-szolgáltatás</h3>
                                <p class="paper-intro">A látogatók biztonsága és a túrák tervezhetősége érdekében a rendszer 17 kiemelt nógrádi helyszín tűpontos, élő meteorológiai adatait (hőmérséklet, szélsebesség, páratartalom, légnyomás) közvetíti API-kapcsolaton keresztül:</p>
                                <ul class="paper-list">
                                    <li class="paper-intro"><strong>Városi központok:</strong> Salgótarján, Balassagyarmat, Pásztó, Szécsény, Bátonyterenye, Rétság.</li>
                                    <li class="paper-intro"><strong>Turisztikai és természeti pontok:</strong> Hollókő, Bánk, Nógrádszakál, Tar, Somoskő, Rónabánya, Ipolytarnóc, Kozárd, Cserhátsurány, Kazár, Mátraszőlős.</li>
                                </ul>

                                <h3 style="margin-top: 20px;">🗺️ Tematikus modulok</h3>
                                <ul class="paper-list">
                                    <li class="paper-intro"><strong>🏛️ Látnivalók:</strong> Átfogó adatbázis a vármegye legfontosabb pontjairól, a somoskői bazaltorgonáktól a „Palóc Grand Canyonként” ismert Páris-patak szurdokvölgyéig.</li>
                                    <li class="paper-intro"><strong>🥾 Bakancsos Kalandok:</strong> 10 válogatott túraútvonal interaktív leírással és „Túra-jegyzetek” funkcióval, amely segít elmerülni az útvonalak egyedi hangulatában.</li>
                                    <li class="paper-intro"><strong>🍲 Palóc Gasztro-túra:</strong> A régió ízeinek bemutatása a hagyományos sztrapacskától a különleges görhelepényig, népi bölcsességekkel fűszerezve.</li>
                                    <li class="paper-intro"><strong>🛏️ Intelligens Szálláskereső:</strong> SQL-alapú rendszer, amely lehetővé teszi a szállások közötti szűrést típus, felszereltség (pl. wellness, ellátás) és ár szerint, kiegészítve megyei szintű statisztikákkal.</li>
                                    <li class="paper-intro"><strong>📅 Programajánló:</strong> Naprakész eseménynaptár a Hollókői Húsvéttól a bio-vásárokig.</li>
                                </ul>
                            </div>

                            <div class="paper-col">
                                <h3>💻 Technológiai háttér és innováció</h3>
                                <ul class="paper-list">
                                    <li class="paper-intro"><strong>Felhasználókezelés:</strong> Biztonságos regisztrációs és profilkezelő rendszer, ahol a tagok egyedi avatart választhatnak, és kezelhetik adataikat.</li>
                                    <li class="paper-intro"><strong>Közösségi Blog:</strong> JSON-alapú adatkezeléssel működő felület, ahol a túrázók fényképes élménybeszámolókat oszthatnak meg egymással.</li>
                                    <li class="paper-intro"><strong>Reszponzivitás:</strong> A teljes felület mobilra optimalizált, egyedi navigációs megoldásokkal és Lightbox galériakezeléssel a terepen történő használathoz.</li>
                                    <li class="paper-intro"><strong>Utazási Praktikák:</strong> Biztonsági útmutató a terepviszonyokról (pl. csúszós bazaltömlések) és a környezettudatos „Zero Waste” túrázás alapelveiről.</li>
                                    <li class="paper-intro"><strong>Hibaélmény:</strong> A projekt integritását egyedi, stílusos 404-es hibaoldal őrzi („Rossz ösvényre tévedtél”), segítve a navigáció helyreállítását.</li>
                                </ul>

                                <div style="background: rgba(255,255,255,0.4); padding: 15px; border-radius: 8px; border: 1px solid #ddd; margin-top: 20px;">
                                    <h4 style="margin-top: 0;">🎯 Mit kínálunk a látogatónak?</h4>
                                    <p class="paper-intro" style="color: #333;">Mélyreható felfedezést, professzionális fotógalériákat és egy élő közösségi platformot. Segítünk eligazodni a rejtett ösvények és a népszerű látványosságok között, miközben technológiai szempontból is a legmagasabb színvonalat nyújtjuk.</p>
                                </div>
                            </div>
                        </div>

                        <div class="paper-footer" style="margin-top: 20px;">
                            <p class="paper-intro" style="font-weight: bold; color: #2d5a27; text-align: center;">Nógrád vármegye vár rád. Kezdd el a felfedezést nálunk!</p>
                        </div>
                    </div>
                </div>
            </div> <div class="col-md-12">
                <div class="box-video-fix">
                    <div class="video-container">
                        <video width="70%" height="auto" controls>
                            <source src="img/video.mp4" type="video/mp4">
                            A böngésződ nem támogatja a videó lejátszását.
                        </video>
                    </div>
                </div>
            </div>
        </div> </section>

    <section id="map" class="content-section">
        <div class="row">
            <div class="col-md-12">
                <div id="contact" class="map map-fix-container">
                    <iframe 
                        src="https://maps.google.com/maps?q=N%C3%B3gr%C3%A1d+megye&t=&z=9&ie=UTF8&iwloc=&output=embed"
                        width="100%" 
                        height="450" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
            </div>

             <div id="contact-content">
            <div class="section-heading">
                <h1><em>Kapcsolat</em></h1>
                <p>Kérdésed van vagy információt gyűjtenél? Írj nekünk közvetlenül!</p>
            </div>
            
            <?php if (isset($_GET['status'])): ?>
                <div class="status-wrapper">
                    <?php if ($_GET['status'] == 'success'): ?>
                        <div class="status-box msg-success">
                            <i class="fa fa-check-circle"></i> ✅ Email elküldve!
                        </div>
                    <?php elseif ($_GET['status'] == 'error'): ?>
                        <div class="status-box msg-error">
                            <i class="fa fa-exclamation-triangle"></i> ❌ Sikertelen küldés, próbáld újra!
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="section-content">
                <form action="contact_process.php" method="POST" class="contact-form-fix">
                    
                    <div class="form-input-container">
                        <label for="visitor_email">Email:</label>
                        <input type="email" id="visitor_email" name="visitor_email" placeholder="pelda@email.hu" class="form-control-custom" required>
                    </div>

                    <div class="form-input-container">
                        <label for="message">Üzenet:</label>
                        <textarea id="message" name="message" placeholder="Írd le az üzenetedet..." class="textarea-custom" required></textarea>
                    </div>

                    <div class="button-container">
                        <button type="submit" class="btn btn-sentra">KÜLDÉS</button>
                    </div>
                    
                </form>
            
            </div>
        </div>

    </section>
            <footer class="premium-footer">
                <div class="footer-inner-wrapper">
                    <div class="rating-link-container">
                        <p class="site-footer-fixed__pill">Tetszett a látogatás? Oszd meg velünk a véleményed!</p>
                        <a href="ertekeles.php" class="btn-sentra-index">⭐ ÉRTÉKELÉS MEGKEZDÉSE</a>
                    </div>

                    <div class="credits-container">
                        <a href="<?php echo (isset($base_url) ? $base_url : ''); ?>Proofiles.php" class="credits-link">
                            <p class="site-footer-fixed__pill">Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                        </a>
                    </div>
                </div>
            </footer>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function() {
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
                    $('header').removeClass('nav-down').addClass('nav-up');
                } else {
                    if(st + $(window).height() < $(document).height()) {
                        $('header').removeClass('nav-up').addClass('nav-down');
                    }
                }
                lastScrollTop = st;
            }
        });
    </script>
    <script>
    // 5 másodperc után elhalványul az üzenet
    setTimeout(function() {
        var statusMsg = document.querySelector('[style*="background-color"]');
        if(statusMsg) {
            statusMsg.style.transition = "opacity 1s ease";
            statusMsg.style.opacity = "0";
            setTimeout(function() { statusMsg.style.display = "none"; }, 1000);
        }
    }, 5000);
    </script>
    <style> /*Értékelés link stílusa*/ */
    .rating-link-container {
        text-align: center;
        margin: 40px 0;
        padding: 20px;
    
        background: rgba(0, 0, 0, 0.6);
        border: 1px solid #345af2;
        border-radius: 25px; /* Kanyaros szél */
        display: inline-block;
        color: white;
    }

    .btn-sentra-index {
        display: inline-block;
        text-decoration: none;
        background: transparent;
        color: #273366;
        border: 2px solid #1463d9;
        padding: 12px 30px;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 50px; /* Teljesen kerekített végek */
        transition: 0.4s;
        box-shadow: 0 0 10px rgba(31, 103, 103, 0.71);
        margin-top: 10px;
    }

    .btn-sentra-index:hover {
        background: #00ffff;
        color: #000;
        box-shadow: 0 0 25px #00ffff;
        transform: translateY(-3px); /* Kicsit megemelkedik */
    }
        </style>
           <script>
function like() {
    fetch('update_like.php')
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const likeSpan = document.getElementById('likes');
            likeSpan.innerText = data.new_likes;
            
            // Animáció: megvillan kékkel
            likeSpan.style.transition = "color 0.3s";
            likeSpan.style.color = "#7fd0ff";
            setTimeout(() => {
                likeSpan.style.color = "";
            }, 500);
        }
    })
    .catch(err => console.error("Hiba:", err));
}
</script>
      <?php include "weather_mobile.php"; ?>

</body>
</html>
