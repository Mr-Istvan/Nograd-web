<?php
session_start();
require_once("db.php");

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
    

        <style>
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

            /* A feed a "rugalmas közép": kitölti a maradék helyet és görgethető */
            body.blog-page .feed{
                flex: 1 1 auto;
                min-height: 0; /* fontos a scrollhoz flex környezetben */
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
                padding-right: 6px; /* hogy a scrollbar ne takarjon */
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
                border-radius:10px;
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
            body.blog-page .ad-train { position: absolute; width: 100%; animation: adRun 15s linear infinite; }
            @keyframes adRun { 0% { top: 600px; } 100% { top: -100%; } }

            /* MOBIL REKLÁM (Vízszintes úszás alul) */
            body.blog-page .mobile-ad-bar {
                display: none; position: fixed; bottom: 0; left: 0; width: 100%;
                background: #45489a; color: white; padding: 10px 0; z-index: 9999; overflow: hidden;
            }
            body.blog-page .mobile-ad-train { display: flex; white-space: nowrap; animation: adRunSide 15s linear infinite; }

            /* Mobil ad-box: férjen ki a teljes szöveg (legyen szélesebb, ne vágjon) */
            @media (max-width: 1001px){
                body.blog-page .ad-box{
                    width: max-content;
                    max-width: calc(100vw - 40px);
                    white-space: normal;
                }
            }
            @keyframes adRunSide { 0% { transform: translateX(100%); } 100% { transform: translateX(-100%); } }
            
            body.blog-page .ad-box{
                background: rgba(0,0,0,0.22);
                color: rgba(255,255,255,0.92);
                margin: 6px;
                padding: 8px 12px;
                text-align: left;
                font-size: 12px;
                font-weight: 800;
                border: 1px solid rgba(255,255,255,0.22);
                border-radius: 12px;
                box-shadow: 0 6px 16px rgba(0,0,0,0.22);

                /* ne vágja le: férjen ki a teljes szó/szöveg */
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }
            body.blog-page .ad-box i{
                width: 15px;
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
                    transform: translateX(-50%);
                    bottom: calc(50px + 12px + env(safe-area-inset-bottom)); /* a 50px magas alsó reklámsáv fölé */
                    z-index: 10000;
                    margin: 0;
                    width: max-content;
                }
            }
            @media (max-width: 767px){
                body.blog-page .site-footer-fixed{
                    bottom: calc(50px + 18px + env(safe-area-inset-bottom)); /* mobilon egy kicsit még feljebb */
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

            /* 1001px alatt: a mobil alsó reklám sáv legyen 50px magas */
            @media (max-width: 1001px) {
                body.blog-page .mobile-ad-bar {
                    display: block;
                    height: 50px;
                    padding: 0;
                }
                body.blog-page .mobile-ad-train {
                    height: 50px;
                    align-items: center;
                }
                body.blog-page .ad-box {
                    font-size: 12px;
                    padding: 1px 31px; /* szélesebb +5px (26->31) és alacsonyabb -5px (6->1) */
                    margin: 0 6px;
                }

                /* legyen hely a fix alsó sávnak */
                body.blog-page .page-content { padding-bottom: 110px; }
            }

            /* MOBIL (<=767): csak itt tűnjön el a bal menü, és itt legyen mobil reklám */
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
<body class="blog-page">

    <header class="nav-down responsive-nav hidden-lg hidden-md">
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
                    <?php include("index/mobile_menu.php"); ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="sidebar-navigation hidden-sm hidden-xs">
        <div class="logo">
            <a href="index.php">NÓG<em>RÁD</em></a>
        </div>
        <nav>
            <ul>
                <?php if($userLoggedIn): ?>
                    <li><a href="profile.php"><span class="circle"></span>Profil (<?php echo $_SESSION['user_name']; ?>)</a></li>
                    <li><a href="logout.php"><span class="circle"></span>Kilépés</a></li>
                <?php else: ?>
                    <li><a href="login.php"><span class="circle"></span>Bejelentkezés</a></li>
                    <li><a href="reg_id.php"><span class="circle"></span>Regisztráció</a></li>
                <?php endif; ?>
                
                <hr style="border-top: 1px solid rgba(255,255,255,0.1); width: 80%; margin: 15px auto;">
                
                <li><a href="index.php"><span class="circle"></span>Kezdőlap</a></li>
                <li><a href="blog.php"><span class="circle"></span>Blog-fal</a></li>
                <li><a href="contact.php"><span class="circle"></span>Kapcsolat</a></li>
            </ul>
        </nav>
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
                            <textarea name="text" class="form-control" rows="3" placeholder="Írj valamit a falra..." required style="background: #fff; color: #000;"></textarea>
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
                ["icon" => "fa-home", "color" => "#1dcdd0", "text" => "Hollókő Ófalu"],
                ["icon" => "fa-flag", "color" => "#d2dde1", "text" => "Salgó vára"],
                ["icon" => "fa-university", "color" => "#9bb7ff", "text" => "Somoskői Vár"],
                ["icon" => "fa-life-ring", "color" => "#4dd0e1", "text" => "Bánki-tó"],
                ["icon" => "fa-globe", "color" => "#a7c7ff", "text" => "Kazári Riolittufa"],
                ["icon" => "fa-paw", "color" => "#042931", "text" => "Ipolytarnóci Ősmaradványok"],
                ["icon" => "fa-shield", "color" => "#129fca", "text" => "Drégely vára"],
                ["icon" => "fa-map-marker", "color" => "#70cfff", "text" => "Balassagyarmat Óváros"],
                ["icon" => "fa-sun-o", "color" => "#b3e5ff", "text" => "Tari Buddhista Központ"],
                ["icon" => "fa-tint", "color" => "#2e0c92", "text" => "Páris-patak (Palóc Grand Canyon)"],
                ["icon" => "fa-building", "color" => "#9fdcff", "text" => "Szécsényi Forgách-kastély"],
                ["icon" => "fa-cutlery", "color" => "#dee1e7", "text" => "Juhtúrós Sztrapacska"],
                ["icon" => "fa-cutlery", "color" => "#5cc8ff", "text" => "Palócleves"],
                ["icon" => "fa-cutlery", "color" => "#000000", "text" => "Vargánya Étterem (Mátraszentimre)"],
                ["icon" => "fa-cutlery", "color" => "#72b8ff", "text" => "Bársony Vendéglő (Szécsény)"],
                ["icon" => "fa-cutlery", "color" => "#6fd6ff", "text" => "Svejk Vendéglő (Balassagyarmat)"],
                ["icon" => "fa-cutlery", "color" => "#9ac9ff", "text" => "Tóparti Vendéglő (Bánk)"],
                ["icon" => "fa-cutlery", "color" => "#3b4244", "text" => "Castellum Étterem (Hollókő)"],
                ["icon" => "fa-music", "color" => "#7fd0ff", "text" => "Honti Kulturális Napok"],
                ["icon" => "fa-road", "color" => "#0dabe9", "text" => "Karancs-Medves Teljesítménytúra"],
                ["icon" => "fa-moon-o", "color" => "#0242b1", "text" => "Holdfény Túra"],
                ["icon" => "fa-train", "color" => "#8be9ff", "text" => "Mikulás-járatok (Kemence)"],
                ["icon" => "fa-book", "color" => "#082774", "text" => "Irodalmi Emléknapok"],
                ["icon" => "fa-wrench", "color" => "#b3c0ff", "text" => "Kézműves Alkotónap"],
            ];
        ?>
        <div class="ads-container hidden-sm hidden-xs">
            <div class="ad-train">
                <?php foreach($adItems as $ad): ?>
                    <div class="ad-box">
                        <i class="fa <?php echo htmlspecialchars($ad['icon']); ?>" aria-hidden="true" style="color: <?php echo htmlspecialchars($ad['color']); ?>; margin-right: 8px;"></i>
                        <?php echo htmlspecialchars($ad['text']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <footer class="site-footer-fixed" aria-label="Oldal lábléc">
            <p class="site-footer-fixed__pill">
                Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István
            </p>
        </footer>
    </div>

    <div class="mobile-ad-bar">
        <div class="mobile-ad-train">
            <?php foreach($adItems as $ad): ?>
                <div class="ad-box">
                    <i class="fa <?php echo htmlspecialchars($ad['icon']); ?>" aria-hidden="true" style="color: <?php echo htmlspecialchars($ad['color']); ?>; margin-right: 8px;"></i>
                    <?php echo htmlspecialchars($ad['text']); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script>
        // Mobil felső menü: ha a main.js nem fut / más JS hiba van, ettől még működjön
        (function () {
            function ready(fn){ if(document.readyState!=='loading'){ fn(); } else { document.addEventListener('DOMContentLoaded', fn); } }
            ready(function(){
                var btn = document.getElementById('nav-toggle');
                var nav = document.getElementById('main-nav');
                if(!btn || !nav) return;

                btn.addEventListener('click', function(e){
                    e.preventDefault();
                    if (nav.style.display === 'none' || nav.style.display === '') nav.style.display = 'block';
                    else nav.style.display = 'none';
                });

                // linkre katt -> csukódjon (mobilon)
                nav.addEventListener('click', function(e){
                    var a = e.target && e.target.closest ? e.target.closest('a') : null;
                    if(!a) return;
                    if (window.innerWidth < 768) nav.style.display = 'none';
                });
            });
        })();
    </script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

</body>
</html>
