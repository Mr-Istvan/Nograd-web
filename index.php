<?php
require_once __DIR__ . '/init.php';

// 1. ELŐBB DEFINIÁLJUK A VÁLTOZÓKAT
$current_page = basename($_SERVER['PHP_SELF']);

$kiemelt_pages = ['galeria.php'];
$felfedezes_pages = ['latnivalok.php', 'programok.php', 'szallasok.php', 'gasztronomia.php', 'turazas.php', 'utazasi-praktikak.php'];
$blog_pages = ['blog.php', 'profile.php', 'login.php', 'reg_id.php'];
$is_authenticated = isset($_SESSION['user_name']);

$is_active = function (array $pages) use ($current_page) {
    return in_array($current_page, $pages, true);
};

$is_open = function (array $pages) use ($current_page) {
    return in_array($current_page, $pages, true);
};

// 2. CSAK EZUTÁN HÍVJUK BE A MENÜT (így már látja a fenti változókat)
include 'kozos_menu.php';   // Létrehozza a $kozos_menu-t
include 'kozos_mobile.php'; // Létrehozza a $kozos_mobile-t
$v_hash = hash('sha256', $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . "NogradCsoda2026_Unique_Like");
$check_like = mysqli_query($conn, "SELECT lid FROM web_like WHERE ip_hash = '$v_hash' LIMIT 1");
$already_liked = (mysqli_num_rows($check_like) > 0);
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
      
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
        
    </head>

    <body>
        <div class="e">
            
            <?= $kozos_menu ?>
            
            <?= $kozos_mobile ?>
            

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

                /* --- ASZTALI NÉZETRE KORLÁTOZZUK AZ ELTOLÁST --- */
                @media (min-width: 767px) {
                    .rating-link-container {
                        position: relative !important;
                        margin-left: 0px !important;
                        width: calc(100% - 100px) !important;  /*-Ez igazítja lent azz értékelési részt--*/
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        justify-content: center !important;
                        margin-top: 20px !important;
                        margin-bottom: 20px !important;
                    }

                    .credits-container {
                        margin-left: 0px !important;            /*-Ez igazítja lent a Bemutatók részét--*/
                        width: calc(100% - 100px) !important;
                        text-align: center !important;
                    }

                    #map .row {
                        margin-left: 250px !important;
                        width: calc(100% - 250px) !important;
                    }

                    #map .col-md-12 {
                        padding-left: 0 !important;
                        padding-right: 0 !important;
                    }

                    #contact.map-fix-container {
                        width: 100% !important;
                    }
                }

                /* --- MOBIL NÉZET (Marad az eredeti) --- */
                @media (max-width: 767px) {
                    .rating-link-container,
                    .credits-container,
                    .premium-footer {
                        margin-left: 0 !important;
                        width: 100% !important;
                        left: 0 !important;
                    }
                    .premium-footer {
                        bottom: 30px !important;
                    }
                    .PrevArrow,
                    .NextArrow {
                        display: none !important;
                    }
                }

                .btn-sentra-index {
                    display: inline-block;
                    text-decoration: none;
                    background: #1f378c;
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
                                <p>Keresd a legszebb úticélokat!<br>
                                    Oszd meg barátaiddal!</p>
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
            </div>
        </div>

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
        $galleryPool = [];
        $blogPool = glob(__DIR__ . '/img/blog_*.jpg') ?: [];
        foreach ($blogPool as $p) {
            if (basename($p) === 'blog_back.jpg') continue;
            $galleryPool[] = 'img/' . basename($p);
        }

        $portfolioPool = glob(__DIR__ . '/img/portfolio_*.jpg') ?: [];
        foreach ($portfolioPool as $p) {
            $galleryPool[] = 'img/' . basename($p);
        }

        $galleryPool = array_values(array_unique($galleryPool));
        shuffle($galleryPool);
        $random3 = array_slice($galleryPool, 0, 3);
        ?>

          <div class="section-content">
            <div class="row">
                <?php foreach ($random3 as $imgPath): ?>
                    <!-- A Bootstrap osztályok mellé a CSS-ben kényszerítettük az 1/3-ot -->
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                        <div class="item">
                            <a href="<?php echo htmlspecialchars($imgPath); ?>" data-lightbox="home-gallery">
                                <img src="<?php echo htmlspecialchars($imgPath); ?>" alt="Galéria kép" class="img-responsive fixed-gallery-img">
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>


            <div class="index-main-wrap" style="margin-top: 18px;">
                <div class="accent-button button">
                    <a href="galeria.php">Ugrás a teljes galériára</a>
                </div>
            </div>

            <div class="index-hero" style="margin-top: 28px; padding: 18px; background: #f4f4f4; border-radius: 10px; text-align: left;">
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
        
            <div class="row">
                <div class="col-md-12">
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

                                    <div class="index-main-wrap" style="background: rgba(255,255,255,0.4); padding: 15px; border-radius: 8px; border: 1px solid #ddd; margin-top: 20px;">
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
                </div>
                <div class="col-md-12">
                    <div class="box-video-fix">
                        <div class="video-container">
                            <video width="70%" height="auto" controls>
                                <source src="img/video.mp4" type="video/mp4">
                                A böngésződ nem támogatja a videó lejátszását.
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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
        </section>

        <section id="contact-content" class="content-section">
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

                    <div class="contact-action-buttons">
                        <button type="submit" class="btn btn-sentra contact-action-btn">KÜLDÉS</button>
                        
                        <button type="button" onclick="handleLike()" id="mainLikeBtn" class="btn btn-sentra contact-action-btn <?= $already_liked ? 'btn-liked' : '' ?>" <?= $already_liked ? 'disabled' : '' ?>>
                            👍 <span id="btn-like-text"><?= $already_liked ? 'KÖSZÖNJÜK!' : 'LIKE' ?></span>
                        </button>
                    </div>
                </form>
            </div> <footer class="premium-footer">
                <div class="footer-inner-wrapper">
                    <div class="rating-link-container">
                        <p class="site-footer-fixed__pill">Tetszett a látogatás? Oszd meg velünk a véleményed!</p>
                        <a href="ertekeles.php" class="btn-sentra-index">⭐ ÉRTÉKELÉS MEGKEZDÉSE</a>
                    </div>

                    <div class="credits-container" style="margin-bottom: 5px;">
                        <a href="<?php echo (isset($base_url) ? $base_url : ''); ?>Editors.php" class="credits-link">
                            <p class="site-footer-fixed__pill" style="margin-bottom: 5px;">Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
                        </a>
                    </div>
                </div>
            </footer>
        </section>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.11.2.min.js"><\/script>')</script>

    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

    <script>
        $(document).ready(function() {
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
    setTimeout(function() {
        var statusMsg = document.querySelector('[style*="background-color"]');
        if(statusMsg) {
            statusMsg.style.transition = "opacity 1s ease";
            statusMsg.style.opacity = "0";
            setTimeout(function() { statusMsg.style.display = "none"; }, 1000);
        }
    }, 5000);
    </script>
    <style>
    /* ÉRTÉKELÉS DOBOZ KONTÉNER */
    .rating-link-container {
        text-align: center;
        margin: 40px auto 5px;
        padding: 30px;
        background: rgba(0, 0, 0, 0.7);
        border: 2px solid #345af2;
        border-radius: 25px;
        display: block;
        max-width: 500px;
        color: white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    .premium-footer {
        position: relative;
        bottom: 30px;
    }

    /* AZ ÉRTÉKELÉS GOMB */
    .btn-sentra-index {
        display: inline-block;
        text-decoration: none;
        background: #1f378c;
        color: #ffffff !important;
        border: 2px solid #345af2;
        padding: 14px 40px;
        font-weight: bold;
        text-transform: uppercase;
        border-radius: 50px;
        transition: 0.4s;
        margin-top: 15px;
        margin-bottom: 5px;
        box-shadow: 0 4px 15px rgba(52, 90, 242, 0.3);
    }

    /* HOVER EFFEKT */
    .btn-sentra-index:hover {
        background: #00ffff;
        color: #000000 !important;
        box-shadow: 0 0 25px #00ffff;
        transform: translateY(-3px);
    }

    .contact-action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 15px;
        width: 100%;
        flex-wrap: nowrap;
    }

    .contact-action-btn {
        flex: 1 1 0;
        min-width: 0;
        margin: 0 !important;
        white-space: nowrap;
    }

    .contact-action-btn.btn-liked {
        background: #2ecc71 !important;
        border-color: #2ecc71 !important;
    }

    @media (max-width: 767px) {
        .contact-action-buttons {
            flex-direction: column;
            align-items: stretch;
        }

        .contact-action-btn {
            width: 100%;
        }
    }
        </style>

    <script>
function handleLike() {
    const likeBtn = document.getElementById('mainLikeBtn');
    const likeText = document.getElementById('btn-like-text');

    if (!likeBtn || !likeText) return;

    likeBtn.disabled = true;

    fetch('update_like.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            likeBtn.style.background = '#2ecc71';
            likeBtn.style.borderColor = '#2ecc71';
            likeText.innerText = 'KÖSZÖNJÜK!';
        } else {
            likeBtn.disabled = false;
            alert(data.message || 'Nem sikerült rögzíteni a like-ot.');
        }
    })
    .catch(err => {
        console.error('Hiba:', err);
        likeBtn.disabled = false;
        alert('Hiba történt a like küldése közben.');
    });
}
</script>
      
<?php include 'ertekeles_statisztika.php'; ?> 
<?php include "valuta/api_valuta.php"; ?>
</body>
</html>
