<?php
require_once __DIR__ . '/init.php';

$postsFile = 'data/posts.json';

// USER ADATOK BETÖLTÉSE
$userData = null;
if (isset($_SESSION['user_name'])) {
    $stmt = mysqli_prepare($conn, "SELECT uusername, uname, uavatar FROM felhasznalok WHERE uusername = ?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_name']);
    mysqli_stmt_execute($stmt);
    $userData = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// POSZT MENTÉS (szöveg + opcionális kép) + név/avatar mentése a posztba
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userData && isset($_POST['text'])) {
    $currentPosts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

    // KÉP FELTÖLTÉS
    $img = "";
    if (!empty($_FILES['image']['name'])) {
        $img = time() . "_" . basename($_FILES['image']['name']);
        @mkdir("img/posts/", 0777, true);
        move_uploaded_file($_FILES['image']['tmp_name'], "img/posts/" . $img);
    }

    $currentPosts[] = [
        "user" => $userData['uusername'],
        "name" => $userData['uname'],
        "avatar" => $userData['uavatar'] ?? "default.png",
        "text" => $_POST['text'],
        "image" => $img,
        "time" => date("H:i"),
        "likes" => 0
    ];

    file_put_contents($postsFile, json_encode($currentPosts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: blog.php");
    exit();
}

// LIKE
if (isset($_GET['like'])) {
    $id = (int)$_GET['like'];
    $postsAll = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];
    if (isset($postsAll[$id])) {
        $postsAll[$id]['likes'] = (int)($postsAll[$id]['likes'] ?? 0) + 1;
        file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
    header("Location: blog.php");
    exit();
}

// Beolvasás a megjelenítéshez
$posts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];
$userLoggedIn = ($userData !== null);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title>NóGRÁD-Blog</title>
        
<meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.png">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="css/fontAwesome.css">
        <link rel="stylesheet" href="css/light-box.css">
        <link rel="stylesheet" href="css/owl-carousel.css">
        <link rel="stylesheet" href="css/templatemo-style.css">
        <link rel="stylesheet" href="index/mobile_style.css">

        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">

        <script src="js/vendor/modernizr-2.8.3-respond-1.4.2.min.js"></script>
    

        <style> 
        
        /* BLOG MOBIL MENÜGOMB: HAJSZÁLPONTOSAN KÖZÉPEN */
@media (max-width: 767px) {
    #blog-mobile-toggle {
        display: block !important;
        position: absolute !important;
        left: 50% !important;   /* Vízszintesen középre tolja */
        top: 50% !important;    /* Függőlegesen középre tolja */
        transform: translate(-50%, -50%) !important; /* Mértani középpont korrekció */
        margin: 0 !important;
        
        /* Megjelenés: kényelmes, nagyobb gomb */
        padding: 12px 20px !important; 
        border-radius: 12px !important;
        border: none !important;
        z-index: 20001 !important; /* Fontos, hogy a header felett legyen */
        transition: all 0.3s ease;
    }

    /* A gomb színe a blog oldal stílusához igazodva (prémium sötét) */
    body.blog-page #blog-mobile-toggle { 
        background-color: #444 !important; 
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important; 
    }

    /* A hamburger csíkok fehér színe */
    #blog-mobile-toggle .icon-bar {
        background-color: #ffffff !important;
        width: 22px !important;
        height: 2px !important;
        display: block !important;
        margin: 4px auto !important;
    }
}
                        /* BLOG - PRÉMIUM SÖTÉT/SZÜRKE */
            body.page-blog header.blog-mobile-nav { border-bottom-color: #444 !important; }
            body.page-blog #blog-mobile-toggle { 
                background-color: #444 !important; 
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4) !important; 
            }
            body { 
                background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('img/blog_back.jpg') no-repeat center center fixed; 
                background-size: cover;
            }

            /* blog specifikus scope: nem hat más oldalakra */
            body.blog-page .page-content { padding: 40px; }

            /* A blog cím (section-heading) kerüljön feljebb: a nagy felső üresség innen jön.
               Desktopon feljebb húzzuk, mobilon (<=767) maradjon annyi hely, hogy ne ütközzön a fix mobil menüvel. */
            body.blog-page .content-section { padding-top: 20px !important; }
            body.blog-page .section-heading { margin-bottom: 30px; }

            /* Blog-fal cím: “diamond” + fehér/kék/fekete/világoskék/sötétkék + aranykék (nem sima sárga) */
            body.blog-page .section-heading h1{
                font-size: clamp(34px, 4vw, 50px);
                font-weight: 900;
                letter-spacing: 0.6px;
                text-transform: uppercase;
                position: relative;
                display: inline-block;
                padding: 12px 18px 12px 58px;
                border-radius: 16px;

                /* 2px fehér körvonal a felirat körül */
                -webkit-text-stroke: 2px #ffffff;
                paint-order: stroke fill;
            }
            /* Firefox fallback: text-shadow körvonallal */
            @supports not (-webkit-text-stroke: 2px #fff){
                body.blog-page .section-heading h1{
                    text-shadow:
                        -1px -1px 0 #fff,
                        1px -1px 0 #fff,
                        -1px  1px 0 #fff,
                        1px  1px 0 #fff,
                        0 10px 22px rgba(0,0,0,0.65);
                }
            }
            body.blog-page .section-heading h1{

                /* fehér-kék-fekete-világoskék-sötétkék hangulat */
                background:
                    linear-gradient(135deg, rgba(255,255,255,0.10), rgba(180,220,255,0.14) 35%, rgba(0,0,0,0.12)),
                    radial-gradient(120% 140% at 0% 0%, rgba(90,160,255,0.22) 0%, rgba(0,0,0,0) 60%);
                border: 1px solid rgba(120,190,255,0.55);
                box-shadow:
                    0 14px 30px rgba(0,0,0,0.35),
                    inset 0 1px 0 rgba(255,255,255,0.15);
                text-shadow: 0 10px 22px rgba(0,0,0,0.65);

                /* “betűtípusos dizájn”: kicsit elegánsabb display feel */
                font-family: "Trebuchet MS","Segoe UI",system-ui,-apple-system,Arial,sans-serif;
            }
            body.blog-page .section-heading h1::before{
                content: "◆";
                position: absolute;
                left: 20px;
                top: 50%;
                transform: translateY(-50%);
                /* aranykék (nem sima sárga) */
                color: #7fd0ff;
                text-shadow: 0 8px 18px rgba(0,0,0,0.55);
                font-size: 28px;
                line-height: 1;
            }
            body.blog-page .section-heading h1 em{
                /* aranykék árnyalat */
                color: #7fd0ff;
                font-style: normal;
                filter: drop-shadow(0 6px 12px rgba(0,0,0,0.35));
            }

            /* Alcím: nagyobb + világosabb */
            body.blog-page .section-heading p{
                font-size: clamp(18px, 2.2vw, 24px);
                color: rgba(235,245,255,0.96) !important;
                text-shadow: 0 8px 18px rgba(0,0,0,0.65);
                margin-top: 16px;
                font-family: "Segoe UI",system-ui,-apple-system,Arial,sans-serif;
                letter-spacing: 0.2px;
            }

            @media (max-width: 767px) {
                body.blog-page .content-section { padding-top: 105px !important; }
            }
            
            @media (max-width: 767px) {
                /* --- 1. A FEJLÉCEK (Eltűnnek görgetéskor) --- */
               /* --- 1. A FEJLÉCEK (Okos görgetés) --- */
                header.responsive-nav, 
                header.blog-mobile-nav { 
                    display: flex !important;
                    align-items: center !important;
                    position: fixed !important; /* Fixen tartjuk */
                    top: 0; 
                    left: 0; 
                    width: 100%;
                    height: 90px !important;
                    background: #ffffff !important; 
                    z-index: 9999 !important;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    border-bottom: 3px solid #333;
                    transition: top 0.3s ease-in-out !important; /* Sima animáció fel/le */
                }

                /* Ez az az osztály, amit a gép ráad, ha lefele görgetsz */
                header.blog-mobile-nav.nav-up {
                    top: -100px !important; /* Felhúzza a képernyőn kívülre */
                }
                /* --- 2. A GOMBOK (Hajszálpontosan középen, modern forma) --- */
                .navbar-toggle, 
                #blog-mobile-toggle {
                    display: block !important;
                    position: absolute !important;
                    left: 50% !important;
                    top: 50% !important;
                    transform: translate(-50%, -50%) !important;
                    margin: 0 !important;
                    padding: 12px 20px !important;
                    border-radius: 12px !important;
                    border: none !important;
                    z-index: 1001 !important;
                    transition: all 0.3s ease;
                }

                /* --- 3. A HAMBURGER CSÍKOK (Hogy a blogon is ugyanaz legyen) --- */
                /* Ez kezeli a .icon-bar-t és a blog span-jeit is */
                .navbar-toggle .icon-bar, 
                #blog-mobile-toggle span {
                    background-color: #ffffff !important;
                    width: 26px !important; /* Megnövelt szélesség */
                    height: 2px !important;
                    display: block !important;
                    margin: 3px 0 !important; /* Egyforma távolság a csíkok között */
                    border-radius: 1px !important;
                    transition: all 0.3s ease;
                }

                /* --- 4. SZŐNYEG MENÜK --- */
                #main-nav, #blog-mobile-menu { 
                    background: rgba(26, 26, 26, 0.98) !important; 
                    display: none; 
                    position: absolute !important;
                    top: 90px !important; 
                    left: 0 !important;
                    width: 100% !important;
                    z-index: 9998 !important;
                    box-shadow: 0 10px 20px rgba(0,0,0,0.5);
                }

                #main-nav ul li a, #blog-mobile-menu a { 
                    color: #ffffff !important; 
                    display: block !important;
                    padding: 12px 0 !important; /* Szép szellős sorköz */
                    text-align: center !important;
                    font-size: 16px !important;
                    text-decoration: none !important;
                    border-bottom: 1px solid rgba(255,255,255,0.05) !important;
                }
            }
            
            /* ===========================
               RESZPONZÍV ÜZENŐFAL + TIPOGRÁFIA (blog-page scope)
               - PC alap: 16px
               - Mobil alap: 16–18px (fluid)
               - Chat/üzenetszöveg: ~16px
               - Kis szöveg (időbélyeg): 12–14px
               =========================== */

            /* PC alap betűméret: 20–22px (kényelmesebb chat) */
            body.blog-page{
                font-size: 20px;
            }
            @media (min-width: 1200px){
                body.blog-page{
                    font-size: 22px;
                }
            }

            /* Mobilon: 16–18px (folyékony skálázás) */
            @media (max-width: 767px){
                body.blog-page{
                    font-size: clamp(16px, 4vw, 18px);
                }
            }

            /* ===========================
               Rugalmas magasság: csak a FEED görgethető
               Szerkezet:
               - section-heading (fix tartalom)
               - composer (fix tartalom)
               - feed (flex:1 => kitölti a maradék helyet, és scrolloz)
               =========================== */

            /* A page-content legyen "oszlop" és érjen le a viewport aljáig,
               hogy a footer alulra kerülhessen, miközben a feed a köztes teret tölti ki. */
            body.blog-page .page-content{
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            /* A tartalmi szekció töltse ki a footer előtti teret */
            body.blog-page .content-section{
                display: flex;
                flex-direction: column;
                flex: 1 1 auto;
                min-height: 0; /* fontos: különben a flex-gyerek (feed) nem tud rendesen összemenni */
            }

                body.blog-page .feed {
                    flex: none !important;      /* Kikapcsoljuk a rugalmas méretezést */
                    height: auto !important;    /* Hagyjuk, hogy olyan hosszú legyen, amilyen az üzenetek listája */
                    overflow-y: visible !important; /* Ne legyen belső görgetősáv */
                    padding-right: 0;           /* Mobilon nincs szükség a scrollbar eltolásra */
                }

            /* Mobilon a fix footer + mobil reklámsáv miatt a FEED aljára kell hely,
               különben az utolsó üzenet alá/fölé belóg. */
            @media (max-width: 767px){
                body.blog-page .feed{
                    padding-bottom: 140px;
                }
            }

            /* ha az alsó fix mobil reklámsáv látszik, legyen neki hely */
            @media (max-width: 1001px){
                body.blog-page .feed{
                    padding-bottom: 60px;
                }
            }
            /* Mobilon (<=767) legyen nagyobb hely, hogy a fix footer biztosan ne lógjon rá az utolsó üzenetre */
            @media (max-width: 767px){
                body.blog-page .feed{
                    padding-bottom: 160px;
                }
            }

            /* Üzenet kártyák - Fehér, fekete keret */
            /* ====== MINTA-SZERŰ "CHAT BUBBLE" KÁRTYA ======
               - szélesebb, egységes max szélesség
               - lágyabb árnyék, kerekebb sarkok
               - a saját üzenet jobbra, a többi balra
            */
            /* Üzenet megjelenítés (a kérésed alapján):
               - Mások üzenete: bal oldalt, SZÜRKE háttér, FEKETE betű
               - Saját üzenet: jobb oldalt, KÉK háttér, FEHÉR betű */
            body.blog-page .post-card {
                padding: 14px 16px;
                margin-bottom: 14px;
                border-radius: 14px;
                width: 92%;
                max-width: 720px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.18);
                box-sizing: border-box;
                font-size: 1rem; /* chat ~16px */
                line-height: 1.45;
                border: 1px solid rgba(0,0,0,0.15) !important;
            }

            body.blog-page .other-post{
                margin-left: 0;
                background: #e6e6e6 !important;
                color: #000 !important;
            }

            body.blog-page .my-post{
                margin-left: auto;
                margin-right: 25px; /* alap: jobbra */
                background: #45489a !important;
                color: #fff !important;
            }

            /* Extra keskeny nézetben (pl. 400px körül) is maradjon jobbra tolva, de ne lógjon ki */
            @media (max-width: 420px){
                body.blog-page .my-post{
                    margin-right: 20px; /* kérés: ~20px */
                }
            }

            /* meta sor (név/idő) is igazodjon a buborék színéhez */
            body.blog-page .my-post .post-meta strong,
            body.blog-page .my-post .post-meta small,
            body.blog-page .my-post .post-text{
                color: #fff !important;
            }
            body.blog-page .other-post .post-meta strong,
            body.blog-page .other-post .post-text{
                color: #000 !important;
            }
            body.blog-page .other-post .post-meta small{
                color: #444 !important;
            }

            /* Név (profilnév): + ~1px még */
            body.blog-page .post-meta strong{
                font-size: calc(1.35em + 1px);
                font-weight: 800;
                letter-spacing: 0.2px;
            }
            body.blog-page .post-meta small{
                font-size: clamp(12px, 0.85rem, 14px); /* kis szöveg: 12–14px */
                color:#888;
            }
            /* Üzenet szöveg: + ~1px + még ~0.5px (kicsit jobban olvasható) */
            body.blog-page .post-text{
                margin: 0;
                font-size: calc(1.25em + 1.5px);
                line-height: 1.5;
                text-align: left;
            }

            /* Első betű legyen nagybetűs (vizuális, nem kövér) */
            body.blog-page .post-text::first-letter{
                font-size: 1.25em;
                font-weight: normal;
                text-transform: uppercase;
            }
            /* Új sorok (nl2br -> <br>) utáni első betű nagybetűsítése */
            body.blog-page .post-text br + *::first-letter,
            body.blog-page .post-text br + span::first-letter,
            body.blog-page .post-text br + br + *::first-letter{
                text-transform: uppercase;
            }

            /* Feltöltött képek: max 140x140, balra, szöveg körbefolyik */
            body.blog-page .post-card img[alt="post image"]{
                max-width: 150px;
                max-height: 150px;
                width: auto;
                height: auto;
                object-fit: cover;
                display: block;
                margin: 10px 0 0 0;
                border-radius: 10px;
            }

            body.blog-page .composer{
                background:rgba(255,255,255,0.05);
                padding:20px;
                border-radius:20px;
                margin-bottom:20px;
                border:1px solid #45489a;
            }
            body.blog-page .composer textarea{
                font-size: 1rem;
            }
            body.blog-page .composer button{
                font-size: 1rem;
            }

            /* ASZTALI REKLÁM (Függőleges) */
            body.blog-page .ads-container {
                width: 125px; height: 600px; position: fixed; right: 20px; top: 100px;
                background: rgba(0,0,0,0.5); border: 1px solid #45489a; overflow: hidden; z-index: 10;
            }
                    /* ASZTALI FÜGGŐLEGES (Felfelé úszó) */
            body.blog-page .ad-train {
                position: absolute;
                width: 100%!important;
                min-height: 35px !important;
                /* A 60s a sebesség, növeld ha túl gyors */
                animation: infiniteVertical 5s linear infinite; 
            }

            @keyframes infiniteVertical {
                0% { top: 0; }
                100% { top: -50%; } /* Csak a feléig megyünk, mert ott kezdődik a másolat */
            }
            /* MOBIL REKLÁM (Vízszintes úszás alul) */
            body.blog-page .mobile-ad-bar {
                display: none; position: fixed; bottom: 0; left: 0; width: 100%;
                background: #2b2d51; color: white; padding: 0 0; z-index: 9999; overflow: hidden;
            }
           
            /* Mobil ad-box: férjen ki a teljes szöveg (legyen szélesebb, ne vágjon) */
            @media (max-width: 1001px){
                body.blog-page .ad-box{
                    width: max-content;
                    max-width: calc(100vw - 40px);
                    white-space: normal;
                }
            }
            @keyframes adRunSide { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
            
                    /* A szöveges dobozok (kapszulák) végleges, szűkített kódja */
            body.blog-page .ad-box {
                background: rgba(0,0,0,0.22);
                color: rgba(255,255,255,0.92);
                margin: 0 5px !important;    
                padding: 2px 12px !important; 
                text-align: center;
                font-size: 11px !important;  
                font-weight: 800;
                border: 1px solid rgba(255,255,255,0.22);
                border-radius: 12px;
                display: flex;
                align-items: center;
                height: 31px; /* Ez tartja kordában a magasságot */
                box-shadow: 0 4px 8px rgba(0,0,0,0.1); /* Kicsit finomabb árnyék */
                white-space: nowrap; /* Hogy a név egy sorban maradjon */
            }
            body.blog-page .ad-box i{
                width: 28px;
                text-align: center;
            }

            /* ===== FOOTER: desktopon csak a lap legalján, mobilon fixen látszódjon ===== */
            body.blog-page .site-footer-fixed{
                position: static; /* desktop/tablet: nem fixed */
                left: auto;
                transform: none;
                bottom: auto;
                z-index: auto;
                margin: 16px 0 0 0;
                padding: 0;
                pointer-events: none;
                display: flex;
                justify-content: center;
            }

            /* 1001px alatt legyen fix és mindig látszódjon.
               768-1001 között már nincs jobb oldali reklám (ads-container), ezért lehet fentebb. */
            @media (max-width: 1001px){
                body.blog-page .site-footer-fixed{
                    position: fixed;
                    left: 50%;
                    bottom: 5px;;
                    transform: translateX(-50%);
                    bottom: calc(50px + 18px + env(safe-area-inset-bottom)); /* mindig a kék reklámsáv fölött */
                    z-index: 10000;
                    margin: 0;
                    width: max-content;
                }
            }
            @media (max-width: 767px){
                body.blog-page .site-footer-fixed{ 
                    bottom: calc(50px + 18px + env(safe-area-inset-bottom)); /* mobilon is ugyanúgy */
                }
            }
            body.blog-page .site-footer-fixed__pill{
                font-family: 'Georgia', serif;
                font-style: italic;
                background: #fff;
                color: #000;
                padding: 6px 20px;
                border: 2px solid #d4af37;
                border-radius: 25px;
                display: inline-block;
                margin: 0;
                line-height: 1.35;
                pointer-events: auto;
                text-align: center;
                white-space: nowrap;
            }
                                /* --- MOBIL REKLÁM VONAT ÖSSZEVONT ÉS JAVÍTOTT --- */
                body.blog-page .mobile-ad-train {
                    display: flex !important;          /* Elemek egymás mellett */
                    white-space: nowrap !important;    /* Ne törje meg a sorokat */
                    height:35px;                      /* Fix magasság */
                    align-items: center;               /* Függőlegesen középre teszi az ikonokat/szöveget */
                    width: max-content;                /* Engedi, hogy a tartalom túllógjon a képernyőn */
                    
                    /* VÉGTELENÍTETT ANIMÁCIÓ: 30 másodperc (állítsd nagyobbra, ha túl gyors) */
                    animation: adRunSideInfinite 60s linear infinite;
                }

                /* Hoverre megáll, hogy el lehessen olvasni a hotelt */
                body.blog-page .mobile-ad-train:hover {
                    animation-play-state: paused;
                }

                /* A JAVÍTOTT KEYFRAME: Csak 50%-ig toljuk el! */
                @keyframes adRunSideInfinite {
                    0% {
                        transform: translateX(0);
                    }
                    100% {
                        /* Mivel PHP-ban dupláztuk a listát, a -50%-nál pont 
                        ugyanúgy néz ki, mint az elején, így észrevétlen az ugrás */
                        transform: translateX(-50%);
                    }
                }
            /* FIX bottom szabályok már nem kellenek, mert nem lebeg a footer */
            @media (max-width: 1001px){
                body.blog-page .site-footer-fixed__pill{
                    font-size: 12px;
                }
            }
            @media (max-width: 767px){
                body.blog-page .site-footer-fixed__pill{
                    padding: 6px 14px;
                    font-size: 12px;
                }
            }

            /* DESKTOP/TABLET (>=768): üzenőfal ne ütközzön a fixed reklámmal + ne legyen túl széles */
            @media (min-width: 768px) {
                body.blog-page .page-content {
                    /* sidebar + content megvan globál css-ben; a fixed reklám miatt kell jobb oldali hely */
                    padding-right: 185px; /* 125px reklám + 20px right + ráhagyás */
                }

                body.blog-page .page-content .col-md-11 {
                    width: 100% !important;
                    float: none !important;
                    max-width: 980px; /* alap nézetben fixebb, nem óriási */
                    margin-left: auto;
                    margin-right: auto;
                }

                body.blog-page .post-card {
                    width: 100%;
                    max-width: 900px;
                }
            }

            /* 1001px alatt a jobb oldali reklám tűnjön el (ahogy kérted) */
            @media (max-width: 1001px) and (min-width: 768px) {
                body.blog-page .ads-container { display: none !important; }
                body.blog-page .page-content { padding-right: 40px; } /* vissza az alap paddingre */
            }

            /* 1001px alatt: Stabilizálás és a reklámsáv javítása */
            @media (max-width: 1001px) {
                /* 1. Megszüntetjük a rángatást: az oldal magassága legyen rugalmas */
                body.blog-page .page-content {
                    display: block !important;
                    height: auto !important;
                    min-height: 100vh;
                    padding-bottom: 180px !important; /* Elég hely a reklámnak és a footernek */
                }
               
                body.blog-page .ad-box {
                    font-size: 12px;
                    padding: 1px 31px; /* szélesebb +5px (26->31) és alacsonyabb -5px (6->1) */
                    margin: 0 6px;
                }

                /* legyen hely a fix alsó sávnak */
                body.blog-page .page-content { padding-bottom: 110px; }
            }

            /* MOBIL    (<=767): csak itt tűnjön el a bal menü, és itt legyen mobil reklám */
            @media (max-width: 767px) {
                body.blog-page .ads-container {
                    display: none;
                }
                body.blog-page .mobile-ad-bar {
                    display: block;
                }
                body.blog-page .page-content {
                    margin-left: 0 !important;
                    width: 100% !important;
                    padding: 20px;
                    /* legyen hely: footer (fix) + mobil reklámsáv alatt */
                    padding-bottom: 140px;
                }
                body.blog-page .col-md-11 {
                    width: 100% !important;
                    float: none !important;
                }
                body.blog-page .post-card {
                    width: 100%;
                    margin-left: 0 !important;
                    max-width: none;
                }
                body.blog-page .section-heading h1 {
                    font-size: clamp(26px, 7vw, 32px);
                }

                /* Mobilon NE kényszerítsük 14px-re a teljes chatet,
                   mert a cél 16–18px alap. Maradjon az alap (1rem). */
                body.blog-page .section-heading p{ font-size: 1rem !important; }
                body.blog-page .post-card p,
                body.blog-page .post-card strong,
                body.blog-page textarea,
                body.blog-page button { font-size: 1rem !important; }

                /* kis szöveg maradjon 12–14px tartományban */
                body.blog-page .post-card small{ font-size: clamp(12px, 0.85rem, 14px) !important; }
            }
        </style>
    </head>
<body class="blog-page index-subpage">
     <div class="logo" style="float: left; padding: 15px 20px;">
       
    </div>

<style>
/* BLOG mobil hamburger menü - index.php mobil nav stílus (fehér header, középen gomb) */
@media (max-width: 767px){
  body.blog-page .blog-mobile-nav{
    display:block !important;
    position:fixed !important;
    top:0; left:0; width:100%;
    height: 80px;
    background-color: rgba(250,250,250,.95);
    box-shadow: 0px 5px 15px rgba(0,0,0,0.2);
    z-index: 20000 !important;
    transition: top 0.3s ease-in-out !important;
  }

  body.blog-page .blog-mobile-nav.nav-up {
    top: -100px !important;
  }

  body.blog-page .blog-mobile-nav__brand{
    position:absolute;
    left: 18px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 22px;
    text-transform: uppercase;
    font-weight: 800;
    text-decoration: none;
    color: #333;
  }
  body.blog-page .blog-mobile-nav__toggle{
    position:absolute;
    top: 40%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
    display:inline-block;
    background: transparent;
    border: none;
    padding: 10px 12px;
    z-index: 20001;
  }
  /* iconbar: kéK */
  body.blog-page .blog-mobile-nav__toggle .icon-bar{
    display:block;
    width:22px;
    height:2px;
    background-color:#3498db;
    margin:4px 0;
  }

  body.blog-page #blog-mobile-menu{
    display:none;
    position: fixed;
    z-index: 19999;
    top: 80px;
    left: 0;
    width: 100%;
    background-color: rgba(0,0,0,0.9);
  }
  body.blog-page #blog-mobile-menu a{
    font-size: 15px;
    text-transform: capitalize;
    color: #fff !important;
    box-shadow: none;
    border: none;
    display:block;
    padding: 12px 0;
    text-align:center;
    text-decoration:none !important;
  }
  body.blog-page #blog-mobile-menu a:hover{ opacity: .85; background-color: transparent; }

  /* kiemelések */
  body.blog-page #blog-mobile-menu a.menu-profile{ color: #fec107 !important; font-weight: 800; }
  body.blog-page #blog-mobile-menu a.menu-logout{ color: #ff4d4d !important; font-weight: 800; }

  /* content ne csússzon a fix header alá */
  body.blog-page .page-content{ padding-top: 105px !important; }
}
@media (min-width: 768px){
  body.blog-page .blog-mobile-nav{ display:none !important; }
  body.blog-page #blog-mobile-menu{ display:none !important; }
 
}
 textarea.form-control:focus {
    border: 3px solid #2d5a27; /* A te zölded */
    box-shadow: 0 0 10px rgba(45, 90, 39, 0.2);
    outline: none;
}
textarea.form-control {
    font-size: 18px !important; /* Itt állítod a betűméretet */
    line-height: 1.5;           /* A sorköz, hogy ne érjenek össze a betűk */
    padding: 12px;              /* Hogy legyen hely a szöveg körül */
}

@media (max-width: 1001px) {
    body.blog-page .mobile-ad-bar {
        display: block !important;
        position: fixed !important;
        bottom: 0;
        left: 250px; /* Eltoljuk a menüsáv szélességével */
        width: calc(100% - 250px); /* A szélességből levonjuk a menüt */
        height: 35px;
        background: #45489a;
        z-index: 99999;
    }

    body.blog-page .page-content {
        padding-bottom: 120px !important;
    }
}

/* 767px alatt eltűnik a bal oldali menü, itt a reklámnak újra 100%-osnak kell lennie */
@media (max-width: 767px) {
    body.blog-page .mobile-ad-bar {
        left: 0 !important;
        width: 100% !important;
    }
}

.footer-wrapper {
    clear: both;
    display: block;
    width: 100%;
    text-align: center;
}

body.blog-page .premium-footer {
    background: transparent !important;
    padding: 20px 0 !important;
    margin: 0 auto !important;
    display: block !important;
    width: 100% !important;
}

.footer-inner {
    display: flex;
    justify-content: center;
}

/* Asztali gépen a sidebar és a reklám között középre igazítjuk */
@media (min-width: 1002px) {
    .footer-wrapper {
        padding-right: 145px; /* A jobb oldali reklámsáv (125px + gap) kompenzálása */
    }
}

/* Mobilon a fix reklámsáv fölé emeljük, hogy ne takarja ki */
@media (max-width: 1001px) {
    .footer-wrapper {
        margin-bottom: 80px !important; /* Hely a kék mozgó reklámnak */
    }
    
    .credits-link p {
        font-size: 11px !important;
        padding: 8px 15px !important;
    }
}
</style>

    <!-- BLOG mobil hamburger menü (767px alatt) - index stílus: fehér header, középen gomb -->
    <header class="blog-mobile-nav hidden-lg hidden-md nav-down" aria-label="Mobil navigáció">
        <a class="blog-mobile-nav__brand" href="index.php">NÓG<span style="color:#d4a373;">RÁD</span></a>

        <button type="button" id="blog-mobile-toggle" class="blog-mobile-nav__toggle" aria-controls="blog-mobile-menu" aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </header>

    <nav id="blog-mobile-menu" aria-label="Mobil menü">
        <?php if(isset($_SESSION['user_name'])): ?>
            <a class="menu-profile" href="profile.php"><i class="fa fa-user"></i> Üdv, (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a>
            <a class="menu-logout" href="logout.php">Kilépés</a>
        <?php else: ?>
            <a href="login.php"><i class="fa fa-sign-in"></i> Belépés</a>
            <a href="reg_id.php"><i class="fa fa-user-plus"></i> Regisztráció</a>
        <?php endif; ?>

        <a href="index.php">Kezdőlap</a>
        <a href="blog.php">Blog</a>
        <a href="index.php#featured">Kiemelt</a>
    </nav>

    <div class="sidebar-navigation hidden-sm hidden-xs">
        <div class="logo">
            <a href="index.php"><em>NÓG</em>RÁD</a>
        </div>
        <nav>
            <ul>
                <?php if(isset($_SESSION['user_name'])): ?>
                    <li>
                        <a href="profile.php" style="color: #fec107;">
                            <span class="rect"></span>
                            <span class="circle"></span>
                            <i class="fa fa-user"></i>Üdv, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
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
                
                <hr style="border-top: 1px solid rgba(255,255,255,0.1); width: 80%; margin: 15px auto;">
                
                <li><a href="index.php"><span class="circle"></span>Kezdőlap</a></li>
                <li><a href="blog.php"><span class="circle"></span>Blog-fal</a></li>
                <li><a href="contact.php"><span class="circle"></span>Kapcsolat</a></li>
            </ul>
                    
        </nav>
        <?php include "weather.php"; ?>
    </div>

    <div class="page-content">
        <section class="content-section">
            <div class="section-heading">
                <h1>Blog-<em>fal</em></h1>
                <p>"Nógrád élmények és top ajánlatok"</p>
            </div>

            <div class="row">
                <div class="col-md-11">
                    <?php if($userLoggedIn): ?>
                    <div class="composer">
                        <form action="blog.php" method="POST" enctype="multipart/form-data">
                            <textarea name="text" class="form-control" rows="5" 
          placeholder="Írj valamit a falra..." 
          required="" 
          style="background: #fff; color: #000; font-size: 18px; min-height: 66px;"></textarea>
                            <div style="margin-top:10px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                <input type="file" name="image" class="form-control" style="max-width: 320px; background:#fff;">
                                <button type="submit" class="btn btn-primary" style="background:#45489a; border:none; padding:10px 30px;">Küldés</button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                    <div class="feed" aria-label="Üzenőfal" role="region">
                        <?php
                            $postsReversed = array_reverse($posts, true); // kulcsok megmaradnak (like-hoz kell)
                            foreach($postsReversed as $idx => $p):
                                $isMine = ($userLoggedIn && $p['user'] == $userData['uusername']);
                                $likes = (int)($p['likes'] ?? 0);
                                $name = $p['name'] ?? ('@' . $p['user']);
                                $avatar = $p['avatar'] ?? 'default.png';
                        ?>
                        <div class="post-card <?php echo $isMine ? 'my-post' : 'other-post'; ?>">
                            <div class="post-meta" style="display:flex; justify-content:space-between; border-bottom:1px solid #eee; margin-bottom:10px;">
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <img src="img/profiles/<?php echo htmlspecialchars($avatar); ?>" alt="avatar" style="width:28px; height:28px; border-radius:50%; object-fit:cover;">
                                    <strong><?php echo htmlspecialchars($name); ?></strong>
                                </div>
                                <small><?php echo htmlspecialchars($p['time'] ?? ''); ?></small>
                            </div>

                            <p class="post-text"><?php echo nl2br(htmlspecialchars($p['text'] ?? '')); ?></p>

                            <?php if(!empty($p['image'])): ?>
                                <img src="img/posts/<?php echo htmlspecialchars($p['image']); ?>" alt="post image" style="max-width:100%; border-radius:10px; margin-top:10px;">
                            <?php endif; ?>

                            <div style="margin-top:10px; display:flex; align-items:center; gap:10px; font-size:0.95rem;">
                                <span>❤️ <?php echo $likes; ?></span>
                                <a href="?like=<?php echo (int)$idx; ?>" style="text-decoration:underline; color: inherit;">Like</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>
                                    
        <?php
            // Reklám-rotátor elemek (név + ikon + "nem egyforma" szín)
            // Ezeket a nevek/helyek listáit az index aloldalak tartalmából szedtem össze.
            $adItems = [
              ["icon" => "fa-star", "color" => "#ffcc00", "text" => "Castellum Hotel Hollókő"],
                ["icon" => "fa-tint", "color" => "#00d2ff", "text" => "Tó Wellness Hotel Bánk"],
                ["icon" => "fa-leaf", "color" => "#a2d043", "text" => "Főnix Wellness Resort"],
                ["icon" => "fa-bed", "color" => "#90a4ae", "text" => "Cédrus Club Hotel"],
                ["icon" => "fa-building-o", "color" => "#78909c", "text" => "Salgó Hotel"],
                ["icon" => "fa-fort-awesome", "color" => "#e1bee7", "text" => "Kastélyhotel Sasvár"],
                ["icon" => "fa-home", "color" => "#8d6e63", "text" => "Boróka Vendégház"],
                ["icon" => "fa-water", "color" => "#4fc3f7", "text" => "Bánki-tó Vendégház"],
                ["icon" => "fa-university", "color" => "#cfd8dc", "text" => "Prónay-kastély"],
                ["icon" => "fa-diamond", "color" => "#b3e5ff", "text" => "Mátra Mona Luxury"],
                ["icon" => "fa-coffee", "color" => "#a1887f", "text" => "Nádas fogadó Teresztenye"],
                ["icon" => "fa-header", "color" => "#546e7a", "text" => "Teleki-Degenfeld Kastély"],
                ["icon" => "fa-home", "color" => "#ffab91", "text" => "Piros Csizma Vendégház"],
                ["icon" => "fa-map", "color" => "#81c784", "text" => "Galagonya Vendégház"],
                ["icon" => "fa-lightbulb-o", "color" => "#fff176", "text" => "Hétlámpás Vendégház"],
                ["icon" => "fa-cutlery", "color" => "#ff8a65", "text" => "Felső Fogadó Felsőtold"],
                ["icon" => "fa-building", "color" => "#ce93d8", "text" => "FeteKert Apartmanok"],
                ["icon" => "fa-sun-o", "color" => "#ffd54f", "text" => "Napfénydomb Vendégház"],
                ["icon" => "fa-tent", "color" => "#4db6ac", "text" => "Nádas Camping Bánk"],
                ["icon" => "fa-tree", "color" => "#66bb6a", "text" => "Bárna Vadász- és Pihenőház"],
                ["icon" => "fa-fire", "color" => "#ff7043", "text" => "Somoskői Kirándulóközpont"],
                ["icon" => "fa-tag", "color" => "#ba68c8", "text" => "Kaláris Vendégház"],
                ["icon" => "fa-university", "color" => "#90caf9", "text" => "Templomvölgy Resort"],
                ["icon" => "fa-compass", "color" => "#4caf50", "text" => "Mátra Kemping Sástó"],
                ["icon" => "fa-home", "color" => "#9575cd", "text" => "Tóparti Apartman"],
                ["icon" => "fa-bed", "color" => "#4fc3f7", "text" => "Zagyva-völgyi Vendégház"],
                ["icon" => "fa-fort-awesome", "color" => "#8d6e63", "text" => "Várhegy Panzió Nógrád"],
                ["icon" => "fa-key", "color" => "#f48fb1", "text" => "Cserhát Kapuja Nézsa"],
                ["icon" => "fa-home", "color" => "#a5d6a7", "text" => "Hollóköves Vendégház"],
                ["icon" => "fa-university", "color" => "#b0bec5", "text" => "Eresztvényi Turistaház"],
                ["icon" => "fa-building-o", "color" => "#90a4ae", "text" => "Rétsági Panzió"],
                ["icon" => "fa-bed", "color" => "#81c784", "text" => "Tereskei Vendégház"],
                ["icon" => "fa-header", "color" => "#5c6bc0", "text" => "Berceli Kastély"],
                ["icon" => "fa-home", "color" => "#ffcc80", "text" => "Kutasó Apartman"],
                ["icon" => "fa-tint", "color" => "#81d4fa", "text" => "Palotási Tóparti Ház"],
                ["icon" => "fa-users", "color" => "#ce93d8", "text" => "Mátraverebélyi Zarándokház"],
                ["icon" => "fa-university", "color" => "#d1c4e9", "text" => "Szentkúti Kegyhely Szálló"],
                ["icon" => "fa-bed", "color" => "#aed581", "text" => "Legéndi Vendégház"],
                ["icon" => "fa-money", "color" => "#66bb6a", "text" => "Nógrádsipeki Pihenő"],
                ["icon" => "fa-money", "color" => "#9ccc65", "text" => "Felsőpetényi Vendégház"],
                ["icon" => "fa-cutlery", "color" => "#ffb74d", "text" => "Karancssági Fogadó"],
                ["icon" => "fa-home", "color" => "#4fc3f7", "text" => "Ipolyvecei Pihenőház"],
                ["icon" => "fa-tent", "color" => "#26a69a", "text" => "Diósjenői Kemping"],
                ["icon" => "fa-tree", "color" => "#8d6e63", "text" => "Börzsönyi Turistaház"],
                ["icon" => "fa-mountain", "color" => "#78909c", "text" => "Somlyó-hegyi Apartman"],
                ["icon" => "fa-university", "color" => "#9575cd", "text" => "Cserhátsurányi Kastélyszálló"],
                ["icon" => "fa-bed", "color" => "#dce775", "text" => "Endrefalvai Vendégház"],
                ["icon" => "fa-star", "color" => "#f06292", "text" => "Garábi Élményszálló"],
                ["icon" => "fa-star", "color" => "#ffcc00", "text" => "Castellum Hotel Hollókő 4⭐"],
                ["icon" => "fa-tint", "color" => "#00d2ff", "text" => "Tó Wellness Hotel Bánk 4⭐"],
                ["icon" => "fa-leaf", "color" => "#a2d043", "text" => "Főnix Wellness Resort 4 ⭐"],
                ["icon" => "fa-bed", "color" => "#90a4ae", "text" => "Cédrus Club Hotel 4⭐"],
                ["icon" => "fa-building-o", "color" => "#78909c", "text" => "Salgó Hotel 3⭐"],
                ["icon" => "fa-fort-awesome", "color" => "#e1bee7", "text" => "Kastélyhotel Sasvár 4⭐"],
                ["icon" => "fa-home", "color" => "#8d6e63", "text" => "Boróka Vendégház 3⭐"],
                ["icon" => "fa-water", "color" => "#4fc3f7", "text" => "Bánki-tó Vendégház 3⭐"],
                ["icon" => "fa-university", "color" => "#cfd8dc", "text" => "Prónay-kastély Alsópetény 💎"],
                ["icon" => "fa-diamond", "color" => "#b3e5ff", "text" => "Mátra Mona Luxury Apartment 💎"],
                ["icon" => "fa-header", "color" => "#546e7a", "text" => "Teleki-Degenfeld Kastélyszálló"],
                ["icon" => "fa-tent", "color" => "#4db6ac", "text" => "Nádas Camping Bánk ⛺"],
                ["icon" => "fa-tree", "color" => "#66bb6a", "text" => "Börzsönyi Turistaház Diósjenő"],
                ["icon" => "fa-sun-o", "color" => "#ffd54f", "text" => "Napfénydomb Vendégház Mátraszele"],
                ["icon" => "fa-users", "color" => "#ce93d8", "text" => "Mátraverebélyi Zarándokház 🏨"],
            ];
        ?>
            <!-- ASZTALI REKLÁM -->
        <div class="ads-container hidden-sm hidden-xs">
            <div class="ad-train">
                <?php 
                // Kétszer íratjuk ki a listát a folyamatosságért
                for ($i = 0; $i < 2; $i++): 
                    foreach($adItems as $ad): ?>
                        <div class="ad-box">
                            <i class="fa <?php echo htmlspecialchars($ad['icon']); ?>" style="color: <?php echo htmlspecialchars($ad['color']); ?>;"></i>
                            <?php echo htmlspecialchars($ad['text']); ?>
                        </div>
                    <?php endforeach; 
                endfor; ?>
            </div>
        </div>
                          
           <div class="footer-wrapper" style="width: 100%; margin-top: 50px; padding-bottom: 20px;">
    <ul class="social-icons" style="display: flex !important; list-style: none; padding: 0; gap: 15px; justify-content: center; margin-bottom: 20px;">
        <li><a href="https://www.facebook.com/" target="_blank"><i class="fa fa-facebook-official"></i></a></li>
        <li><a href="https://twitter.com/" target="_blank"><i class="fa fa-twitter"></i></a></li>
        <li><a href="https://mail.google.com/" target="_blank"><i class="fa fa-envelope"></i></a></li>
        <li><a href="https://www.youtube.com/" target="_blank"><i class="fa fa-youtube-play"></i></a></li>
        <li><a href="https://wm-iskola.hu/" target="_blank"><i class="fa fa-graduation-cap"></i></a></li>
    </ul>

    <footer class="premium-footer">
        <div class="credits-container">
            <a href="<?php echo (isset($base_url) ? $base_url : ''); ?>Proofiles.php" class="credits-link">
                <p class="site-footer-fixed__pill">Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István</p>
            </a>
        </div>
    </footer>
</div>

        <style>
            @media (max-width: 767px) {
                footer[style*="color: #cd7e0f"] { margin-bottom: 45px !important; }
            }
        </style>
    </div>

        <!-- MOBIL REKLÁM -->
    <div class="mobile-ad-bar">
        <div class="mobile-ad-train">
            <?php 
            // Itt is duplázunk a végtelenítéshez
            for ($i = 0; $i < 2; $i++):
                foreach($adItems as $ad): ?>
                    <div class="ad-box">
                        <i class="fa <?php echo htmlspecialchars($ad['icon']); ?>" style="color: <?php echo htmlspecialchars($ad['color']); ?>;"></i>
                        <?php echo htmlspecialchars($ad['text']); ?>
                    </div>
                <?php endforeach;
            endfor; ?>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        // Blog mobil menü: független a template main.js-től
        (function () {
            function ready(fn){ if(document.readyState!=='loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
            ready(function(){
                var btn = document.getElementById('blog-mobile-toggle');
                var nav = document.getElementById('blog-mobile-menu');
                if(!btn || !nav) return;

                function closeMenu(){
                    nav.style.display = 'none';
                    btn.setAttribute('aria-expanded', 'false');
                }
                function toggleMenu(){
                    var open = (nav.style.display !== 'none' && nav.style.display !== '');
                    if(open) closeMenu();
                    else {
                        nav.style.display = 'block';
                        btn.setAttribute('aria-expanded', 'true');
                    }
                }

                btn.addEventListener('click', function(e){ e.preventDefault(); toggleMenu(); });

                // linkre katt -> csukódjon mobilon
                nav.addEventListener('click', function(e){
                    var a = e.target && e.target.closest ? e.target.closest('a') : null;
                    if(!a) return;
                    if (window.innerWidth < 768) closeMenu();
                });

                // resize -> csuk (ha átlépünk desktopra)
                window.addEventListener('resize', function(){
                    if(window.innerWidth >= 768) closeMenu();
                });
            });
        })();
    </script>
    <script>
        $(document).ready(function() {
            if (!$('header.blog-mobile-nav').length) return;

            var didScroll = false;
            var lastScrollTop = 0;
            var delta = 5;

            $(window).on('scroll', function() {
                didScroll = true;
            });

            setInterval(function() {
                if (!didScroll) return;
                didScroll = false;

                var st = $(window).scrollTop();
                var navbar = $('header.blog-mobile-nav');
                var navbarHeight = navbar.outerHeight() || 0;

                if (Math.abs(lastScrollTop - st) <= delta) return;

                if (st > lastScrollTop && st > navbarHeight) {
                    navbar.addClass('nav-up').removeClass('nav-down');
                    var nav = document.getElementById('blog-mobile-menu');
                    var btn = document.getElementById('blog-mobile-toggle');
                    if (nav && nav.style.display === 'block') {
                        nav.style.display = 'none';
                        if (btn) btn.setAttribute('aria-expanded', 'false');
                    }
                } else if (st + $(window).height() < $(document).height()) {
                    navbar.removeClass('nav-up').addClass('nav-down');
                }

                lastScrollTop = st;
            }, 250);
        });
    </script>
    <style>
    @media (max-width: 767px) {
        body.blog-page header.blog-mobile-nav {
            transition: top 0.3s ease-in-out !important;
        }

        body.blog-page header.blog-mobile-nav.nav-up {
            top: -100px !important;
        }
    }
</style>
                        
    <script src="js/main.js"></script>
    <?php include "weather_mobile.php"; ?>
</body>
</html>
