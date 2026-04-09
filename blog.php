<?php
require_once __DIR__ . '/init.php';

$current_page = basename($_SERVER['PHP_SELF']);
$kiemelt_pages = ['galeria.php', 'index.php'];
$felfedezes_pages = ['latnivalok.php', 'programok.php', 'szallasok.php', 'gasztronomia.php', 'turazas.php', 'utazasi-praktikak.php'];
$blog_pages = [ 'profile.php', 'login.php', 'reg_id.php'];
$is_authenticated = isset($_SESSION['user_name']);

$is_active = function (array $pages) use ($current_page) {
    return in_array($current_page, $pages, true);
};

$is_open = function (array $pages) use ($current_page) {
    return in_array($current_page, $pages, true);
};

include 'kozos_menu.php';
include 'kozos_mobile.php';

$postsFile = 'data/posts.json';

// USER ADATOK BETÖLTÉSE
$userData = null;
if (isset($_SESSION['user_name'])) {
    $stmt = mysqli_prepare($conn, "SELECT uusername, uname, uavatar FROM felhasznalok WHERE uusername = ?");
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_name']);
    mysqli_stmt_execute($stmt);
    $userData = mysqli_stmt_get_result($stmt)->fetch_assoc();
}

// POSZT TÖRLÉSE (Szerző vagy Admin/VIP)
if (isset($_GET['delete']) && $userData) {
    $id = (int)$_GET['delete'];
    $postsAll = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

    $isAuthor = (isset($postsAll[$id]) && $postsAll[$id]['user'] === $userData['uusername']);
    $isModerator = (isset($_SESSION['status']) && ($_SESSION['status'] === 'C' || $_SESSION['status'] === 'B'));

    if (isset($postsAll[$id]) && ($isAuthor || $isModerator)) {
        if (!empty($postsAll[$id]['image']) && file_exists("img/posts/" . $postsAll[$id]['image'])) {
            unlink("img/posts/" . $postsAll[$id]['image']);
        }
        unset($postsAll[$id]);
        file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    header("Location: blog.php?msg=torolve");
    exit();
}

// POSZT MENTÉS (szöveg + opcionális kép)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userData && isset($_POST['text'])) {
    $currentPosts = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

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

    if (!isset($postsAll[$id])) {
        header("Location: blog.php");
        exit();
    }

    if (!isset($_SESSION['user_name'])) {
        header("Location: login.php?msg=like_login");
        exit();
    }

    $likeKey = 'liked_posts';
    if (!isset($_SESSION[$likeKey]) || !is_array($_SESSION[$likeKey])) {
        $_SESSION[$likeKey] = [];
    }

    if (in_array($id, $_SESSION[$likeKey], true)) {
        header("Location: blog.php?msg=already_liked");
        exit();
    }

    $postsAll[$id]['likes'] = (int)($postsAll[$id]['likes'] ?? 0) + 1;
    $_SESSION[$likeKey][] = $id;

    file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: blog.php?msg=liked");
    exit();
}

// DISLIKE
if (isset($_GET['dislike'])) {
    $id = (int)$_GET['dislike'];
    $postsAll = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

    if (!isset($postsAll[$id])) {
        header("Location: blog.php");
        exit();
    }

    if (!isset($_SESSION['user_name'])) {
        header("Location: login.php?msg=like_login");
        exit();
    }

    $dislikeKey = 'disliked_posts';
    if (!isset($_SESSION[$dislikeKey]) || !is_array($_SESSION[$dislikeKey])) {
        $_SESSION[$dislikeKey] = [];
    }

    if (in_array($id, $_SESSION[$dislikeKey], true)) {
        header("Location: blog.php?msg=already_disliked");
        exit();
    }

    $postsAll[$id]['dislikes'] = (int)($postsAll[$id]['dislikes'] ?? 0) + 1;
    $_SESSION[$dislikeKey][] = $id;

    file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    header("Location: blog.php?msg=disliked");
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
    <title>NÓGRÁD-Blog</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/templatemo-style.css">

    <style>
        .sidebar-navigation { z-index: 9999 !important; }
        body.blog-page { background: url('img/blog_back.jpg') no-repeat center center fixed, linear-gradient(to bottom, #afa89d, #b0a99f); background-size: cover; background-blend-mode: multiply; }
        body.blog-page .page-content { padding: 40px; }
        body.blog-page .content-section { padding-top: 20px !important; }
        body.blog-page .section-heading { width: 100% !important; text-align: center !important; margin-top: 20px !important; margin-bottom: 30px !important; margin-left: 20px !important; margin-right: 2px !important; padding-left: 0 !important; padding-right: 0 !important; }
        @media (min-width: 767px) { body.blog-page .section-heading { display: block !important; max-width: 100% !important; } }
        body.blog-page .section-heading h1{ font-size: clamp(34px, 4vw, 50px); font-weight: 900; letter-spacing: 0.6px; text-transform: uppercase; position: relative; display: inline-block; padding: 12px 18px 12px 58px; border-radius: 16px; -webkit-text-stroke: 2px #ffffff; paint-order: stroke fill; background: linear-gradient(135deg, rgba(255,255,255,0.10), rgba(180,220,255,0.14) 35%, rgba(0,0,0,0.12)), radial-gradient(120% 140% at 0% 0%, rgba(90,160,255,0.22) 0%, rgba(0,0,0,0) 60%); border: 1px solid rgba(120,190,255,0.55); box-shadow: 0 14px 30px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.15); text-shadow: 0 10px 22px rgba(0,0,0,0.65); font-family: "Trebuchet MS","Segoe UI",system-ui,-apple-system,Arial,sans-serif; }
        @supports not (-webkit-text-stroke: 2px #fff){ body.blog-page .section-heading h1{ text-shadow: -1px -1px 0 #fff, 1px -1px 0 #fff, -1px  1px 0 #fff, 1px  1px 0 #fff, 0 10px 22px rgba(0,0,0,0.65); } }
        body.blog-page .section-heading h1::before{ content: "◆"; position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: #7fd0ff; text-shadow: 0 8px 18px rgba(0,0,0,0.55); font-size: 28px; line-height: 1; }
        body.blog-page .section-heading h1 em{ color: #7fd0ff; font-style: normal; filter: drop-shadow(0 6px 12px rgba(0,0,0,0.35)); }
        body.blog-page .section-heading p{ font-size: clamp(18px, 2.2vw, 24px); color: rgba(235,245,255,0.96) !important; text-shadow: 0 8px 18px rgba(0,0,0,0.65); margin-top: 16px; font-family: "Segoe UI",system-ui,-apple-system,Arial,sans-serif; letter-spacing: 0.2px; }
        body.blog-page{ font-size: 24px; }
        @media (min-width: 1200px){ body.blog-page{ font-size: 23px; } }
        @media (max-width: 767px){ body.blog-page{ font-size: clamp(21px, 4vw, 18px); } body.blog-page .content-section { padding-top: 50px !important; } }
        @media (max-width: 767px) { body.blog-page .page-content { padding-top: 10px !important; margin-top: 0 !important; } body.blog-page .content-section { padding-top: 0 !important; margin-top: 0 !important; } body.blog-page .section-heading { margin-top: -20px !important; padding-top: 0 !important; } body.blog-page .section-heading h1 { margin-top: 0 !important; padding-top: 0 !important; } }
        body.blog-page .page-content{ min-height: 100vh; display: flex; flex-direction: column; padding: 0 20px 110px 0 !important; }
        .page-content, .content-section, .content-section .row, .content-section .col-md-12 { max-width: 100% !important; width: 100% !important; padding-left: 0 !important; padding-right: 5px !important; margin-left: 0 !important; margin-right: 0 !important; }
        .content-section .section-heading { text-align: left !important; margin-left: 15px !important; }
        .feed { max-width: none !important; width: auto !important; margin-left: 15px !important; margin-right: 15px !important; }
        /* --- 1. ALAPHELYZET (767px alatt: Mobil) --- */
        @media (max-width: 766px) {
            body.blog-page .composer,
            body.blog-page .feed {
                margin-left: 5px !important;
                margin-right: 10px !important;
                width: 100% !important;
            }
        }

        /* --- 2. KÖZTES ÁLLAPOT (767px - 1000px között) --- 
           Itt már ott a 250px-es menü balra, de jobb oldalon MÉG NINCS reklám.
        */
        @media (min-width: 767px) and (max-width: 1000px) {
            body.blog-page .composer,
            body.blog-page .feed {
                margin-left: 250px !important; 
                margin-right: 5px !important;
                width: calc(100% - 250px) !important;
                padding: 0 15px !important;
                box-sizing: border-box;
            }
        }

        /* --- 3. ASZTALI NÉZET (1001px felett) --- 
           Itt ott a 250px menü ÉS a jobb oldali reklám (kb. 150px sáv) is.
        */
        @media (min-width: 1001px) {
            body.blog-page .composer,
            body.blog-page .feed {
                margin-left: 250px !important;
                margin-right: 150px !important;
                width: calc(100% - 400px) !important;
                box-sizing: border-box;
                max-width: none !important;
            }
        }

        /* --- ÍRÁSI FELÜLET JAVÍTÁSA (A textarea kényelme) --- */
        body.blog-page .composer textarea {
            width: 100% !important;
            min-height: 120px;
            padding: 15px;
            font-size: 16px;
            box-sizing: border-box;
            resize: vertical;
        }
        @media (min-width: 767px) and (max-width: 1000px) {
            body.blog-page .composer,
            body.blog-page .feed {
                margin-left: 260px !important;
                margin-right: 20px !important;
                width: auto !important;
                max-width: none !important;
            }
        }
        @media (min-width: 767px) and (max-width: 1001px) {
            body.blog-page .page-content { padding-right: 0 !important; }
            body.blog-page .feed,
            body.blog-page .composer {
                width: calc(100% - 260px) !important;
                max-width: calc(100% - 260px) !important;
                margin-left: 260px !important;
                margin-right: 0 !important;
                box-sizing: border-box !important;
            }
        }
        @media (max-width: 767px) { body.blog-page .page-content { padding-top: 10px !important; } .section-heading { margin-top: -40px !important; padding-top: 0 !important; } }
        @media (min-width: 767px) { body.blog-page .page-content { padding: 0 20px 110px 0 !important; } }
        @media (max-width: 767px) { body.blog-page .page-content{ padding-top:115px !important; } }
        @media (max-width: 767px) { body.blog-page .feed .post-content, body.blog-page .feed p, body.blog-page .post-text { font-size: 25px !important; line-height: 1.7 !important; color: #161616 !important; } body.blog-page .feed h2, body.blog-page .feed h3, body.blog-page .post-title { font-size: 1.5rem !important; margin-bottom: 10px !important; } body.blog-page .post, body.blog-page .feed-item { padding: 15px !important; margin-bottom: 20px !important; width: 100% !important; box-sizing: border-box !important; } body.blog-page .post-meta, body.blog-page .user-info span { font-size: 25px !important; } }
        body.blog-page .content-section{ display: flex; flex-direction: column; flex: 1 1 auto; min-height: 0; }
        body.blog-page .feed { flex: none !important; height: auto !important; overflow-y: visible !important; padding-right: 0; }
        @media (max-width: 1001px){ body.blog-page .feed{ padding-bottom: 60px; } }
        @media (max-width: 767px){ body.blog-page .feed{ padding-bottom: 160px; } }
        body.blog-page .post-card { padding: 14px 16px; margin-bottom: 14px; border-radius: 14px; width: 92%; max-width: 720px; box-shadow: 0 10px 25px rgba(0,0,0,0.18); box-sizing: border-box; font-size: 1rem; line-height: 1.45; border: 1px solid rgba(0,0,0,0.15) !important; position: relative; }
        body.blog-page .other-post{ margin-left: 0; background: #e6e6e6 !important; color: #000 !important; }
        body.blog-page .my-post{ margin-left: auto; margin-right: 25px; background: #45489a !important; color: #fff !important; }
        @media (max-width: 420px){ body.blog-page .my-post{ margin-right: 20px; } }
        body.blog-page .my-post .post-meta strong, body.blog-page .my-post .post-meta small, body.blog-page .my-post .post-text{ color: #fff !important; }
        body.blog-page .other-post .post-meta strong, body.blog-page .other-post .post-text{ color: #000 !important; }
        body.blog-page .other-post .post-meta small{ color: #444 !important; }
        body.blog-page .post-meta strong{ font-size: calc(1.35em + 1px); font-weight: 800; letter-spacing: 0.2px; }
        body.blog-page .post-meta small{ font-size: clamp(12px, 0.85rem, 14px); color:#888; }
        body.blog-page .post-text{ margin: 0; font-size: calc(1.25em + 1.5px); line-height: 1.5; text-align: left; }
        body.blog-page .post-card img[alt="post image"]{ max-width: 150px; max-height: 150px; width: auto; height: auto; object-fit: cover; display: block; margin: 10px 0 0 0; border-radius: 10px; }
        .delete-post-btn { position: absolute; bottom: 12px; right: 12px; background-color: #ff0000 !important; color: #ffffff !important; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 50%; z-index: 1000; box-shadow: 0 2px 8px rgba(0,0,0,0.3); transition: all 0.2s ease-in-out; text-decoration: none !important; }
        .delete-post-btn:hover { background-color: #cc0000 !important; transform: scale(1.15); }
        .delete-post-btn i { font-size: 14px; }
        .status-msg { padding: 12px; border-radius: 12px; text-align: center; margin-bottom: 20px; font-weight: 600; font-size: 20px; backdrop-filter: blur(8px); animation: fadeInDown 0.5s ease; }
        .post-feedback { display: none; margin-top: 10px; padding: 10px 12px; border-radius: 10px; font-size: 14px; font-weight: 700; color: #000 !important; text-shadow: none !important; }
        .msg-success { background: rgba(25, 135, 84, 0.3); border: 1px solid #198754; color: #2ecc71; }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        body.blog-page .composer{ background: rgba(255,255,255,0.05); padding: 20px; border-radius: 20px; margin-bottom: 20px; border: 1px solid #45489a; }
        body.blog-page .composer textarea, body.blog-page .composer button{ font-size: 1rem; }
        .footer-wrapper { clear: both; display: block; width: 100%; text-align: center; }
        body.blog-page .premium-footer { background: transparent !important; padding: 20px 0 !important; margin: 0 auto !important; display: block !important; width: 100% !important; }
        @media (min-width: 767px) { body.blog-page .premium-footer { margin-left: 260px !important; width: calc(100% - 260px) !important; } }
        footer.premium-footer { padding: 30px !important; text-align: center !important; clear: both; margin-top: 50px !important; background: none !important; }
        footer.premium-footer p { font-family: 'Georgia', serif !important; font-style: italic !important; color: #000000 !important; font-size: 14px !important; display: inline-block; padding: 10px 25px !important; background: rgba(255, 255, 255, 0.9) !important; border: 2px solid #d4af37 !important; border-radius: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        @media (max-width: 767px) { footer.premium-footer { padding: 20px !important; } footer.premium-footer p { font-size: 12px !important; padding: 8px 14px !important; max-width: calc(100vw - 40px); white-space: normal; overflow-wrap: anywhere; word-break: break-word; line-height: 1.5; } }
        @media (min-width: 767px) {
            body.blog-page .page-content { padding-right: 185px; }
            body.blog-page .page-content .col-md-12 { width: 100% !important; float: none !important; max-width: 980px; margin-left: auto; margin-right: auto; }
            body.blog-page .post-card { width: 100%; max-width: 900px; }
        }
        @media (max-width: 1001px) and (min-width: 767px) { body.blog-page .ads-container { display: none !important; } body.blog-page .page-content { padding-right: 40px; } }
        @media (max-width: 1001px) { body.blog-page .page-content { display: block !important; height: auto !important; min-height: 100vh; padding-bottom: 110px !important; } body.blog-page .ad-box { font-size: 15px; padding: 1px 31px; margin: 0 6px; } body.blog-page .mobile-ad-bar { display: block !important; position: fixed !important; bottom: 0; left: 250px; width: calc(100% - 250px); height: 35px; background: #45489a; z-index: 99999; } body.blog-page .page-content { padding-bottom: 120px !important; } }
        @media (max-width: 767px) { body.blog-page .ads-container { display: none; } body.blog-page .mobile-ad-bar { left: 0 !important; width: 100% !important; display: block !important; } body.blog-page .page-content { margin-left: 0 !important; width: 100% !important; padding: 20px; padding-bottom: 140px; padding-right: 0 !important; box-sizing: border-box !important; } body.blog-page .col-md-12 { width: 100% !important; float: none !important; } body.blog-page .post-card { width: 100%; margin-left: 0 !important; max-width: none; } body.blog-page .section-heading h1 { font-size: clamp(26px, 7vw, 32px); } body.blog-page .section-heading p{ font-size: 1rem !important; } body.blog-page .post-card p, body.blog-page .post-card strong, body.blog-page textarea, body.blog-page button { font-size: 1rem !important; } body.blog-page .post-card small{ font-size: clamp(12px, 0.85rem, 14px) !important; } }
        @media (max-width: 767px){
            body.blog-page .blog-mobile-nav{ display:block !important; position:fixed !important; top:0; left:0; width:100%; height:90px; background-color:#ffffff; box-shadow:0 2px 10px rgba(0,0,0,0.1); border-bottom:3px solid #333; z-index:9999 !important; transition: top 0.3s ease-in-out !important; }
            body.blog-page .page-content { padding-right: 0 !important; box-sizing: border-box !important; }
        @media (max-width: 767px){
            body.blog-page .blog-mobile-nav{ display:block !important; position:fixed !important; top:0; left:0; width:100%; height:90px; background-color:#ffffff; box-shadow:0 2px 10px rgba(0,0,0,0.1); border-bottom:3px solid #333; z-index:9999 !important; transition: top 0.3s ease-in-out !important; }
            body.blog-page .page-content { padding-right: 0 !important; box-sizing: border-box !important; }
            body.blog-page .blog-mobile-nav.nav-up{ top:-100px !important; }
            body.blog-page .logo-mobile-left{ position:absolute; left:15px; top:50%; transform:translateY(-50%); }
            body.blog-page .logo-mobile-left a{ font-size:22px; font-weight:800; text-transform:uppercase; text-decoration:none; color:#333333; }
            body.blog-page .logo-mobile-left a span{ color:#d4a373; }
            body.blog-page .navbar-toggle, body.blog-page .blog-mobile-nav__toggle{ position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); display:block !important; background:transparent; border:none; padding:10px 12px; z-index:10001; }
            body.blog-page .navbar-toggle .icon-bar, body.blog-page .blog-mobile-nav__toggle .icon-bar{ display:block; width:22px; height:2px; background-color:#3498db; margin:4px 0; }
            body.blog-page #main-nav, body.blog-page #blog-mobile-menu{ display:none; position:fixed; top:90px; left:0; width:100%; background-color:rgba(0,0,0,0.95); z-index:9998; box-shadow:0 10px 20px rgba(0,0,0,0.5); padding:12px; }
            body.blog-page #main-nav nav, body.blog-page #blog-mobile-menu nav{ padding:12px; }
            body.blog-page #main-nav a, body.blog-page #blog-mobile-menu a{ color:#ffffff !important; display:block !important; text-align:center !important; text-decoration:none !important; padding:5px 0 !important; font-size:16px !important; }
            body.blog-page .page-content{ padding-top:115px !important; }
        }
        /* HELPDESK STÍLUSOK A BLOGHOZ (A profil popup miatt) */
        :root {
            --bg-dark: #020617;
            --card-bg: rgba(15, 23, 42, 0.95);
            --accent-color: <?php echo (isset($_SESSION['status']) && $_SESSION['status'] === 'C') ? '#0ea5e9' : '#b4865a'; ?>;
            --border-color: rgba(180, 134, 90, 0.3);
            --text-muted: #94a3b8;
        }

        .glass-card-modal {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.8);
            display: flex;
            flex-direction: column;
        }
        
        .glass-card-modal::-webkit-scrollbar { width: 8px; }
        .glass-card-modal::-webkit-scrollbar-thumb { background: var(--border-color); border-radius: 10px; }
    
        /* --- TÖKÉLETES MOBIL NÉZET AZ ÜVEGKÁRTYÁHOZ (<767px) --- */
    @media (max-width: 767px) {
        .blog-profile-modal-box {
            width: 95% !important;
            min-height: auto !important;
            max-height: 85vh !important;
            padding: 10px !important;
        }
        
        #quickProfileContent div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important; 
            text-align: center !important;
            gap: 10px !important;
        }
        
        #quickProfileContent img {
            margin: 0 auto 15px auto !important; 
            display: block !important;
        }
        
        #quickProfileContent .btn {
            width: 100% !important;
            margin-bottom: 5px !important;
        }
    }
    </style>
</head>
<body class="blog-page">
    
    <div class="e">
            <?= $kozos_menu ?>
            <?= $kozos_mobile ?>
    </di>
    <div class="page-content">
        <section class="content-section">

            <div class="section-heading" style="text-align:center;">
                <h1>Blog-<em>fal</em></h1>
                <p>"Nógrád élmények és top ajánlatok"</p>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div id="msg-box">
                        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'torolve'): ?>
                            <div class="status-msg msg-success">✅ A bejegyzésed sikeresen törölve!</div>
                        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'liked'): ?>
                            <div class="status-msg msg-success">❤️ A like rögzítve!</div>
                        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'already_liked'): ?>
                            <div class="status-msg msg-success">⚠️ Ezt a bejegyzést már like-oltad.</div>
                        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'disliked'): ?>
                            <div class="status-msg msg-success">👎 A "Nem tetszik" rögzítve!</div>
                        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'already_disliked'): ?>
                            <div class="status-msg msg-success">⚠️ Ezt a bejegyzést már jelölted "Nem tetszik"-kel.</div>
                        <?php endif; ?>
                    </div>
<div class="hidden-xs hidden-sm" style="margin-left: 260px; margin-bottom: 20px; text-align: center;">
    <a href="../tetris/tetris.php" 
       style="display: inline-block; cursor: pointer; transition: transform 0.1s ease-in-out;"
       onmouseover="this.style.transform='scale(1.05)';"
       onmouseout="this.style.transform='scale(1)';">
        <img src="../tetris/tetris_button.png" 
             alt="Nógrád Tetris - Játssz!" 
             style="width: 180px; /* PC-n egy picit lehet nagyobb */
                    height: auto;
                    border-radius: 12px; 
                    border: 2px solid #5a5db8; 
                    box-shadow: 0 6px 20px rgba(70, 73, 154, 0.5); 
                    display: block;">
    </a>
</div>

<div class="visible-xs visible-sm" style="margin-top: -50px; margin-bottom: 20px; text-align: center; position: relative; z-index: 10;">
    <a href="../tetris/tetris.php" 
       style="display: inline-block; cursor: pointer; transition: transform 0.1s ease-in-out;"
       onmousedown="this.style.transform='scale(0.95)';"
       onmouseup="this.style.transform='scale(1)';"
       ontouchstart="this.style.transform='scale(0.95)';"
       ontouchend="this.style.transform='scale(1)';">
        <img src="../tetris/tetris_button.png" 
             alt="Nógrád Tetris - Játssz!" 
             style="width: 140px; /* Mobilon marad a kompakt méret */
                    height: auto;
                    border-radius: 12px; 
                    border: 2px solid #5a5db8; 
                    box-shadow: 0 4px 15px rgba(70, 73, 154, 0.4); 
                    display: block;">
    </a>
</div>

                    <?php if($userLoggedIn): ?>
                        <div class="composer" style="
    max-width: 900px !important; 
    width: 100%; 
    margin-left: auto; 
    margin-right: auto; 
    display: block; 
    box-sizing: border-box; 
    padding: 10px;
">
                            <form action="/Blog" method="POST" enctype="multipart/form-data">
                                <textarea name="text" class="form-control" rows="5" placeholder="Írj valamit a falra..." required style="background: #fff; color: #000; font-size: 18px; min-height: 66px;"></textarea>
                                <div style="margin-top:10px; display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
                                    <input type="file" name="image" class="form-control" style="max-width: 320px; background:#fff;">
                                    <button type="submit" class="btn btn-primary" style="background:#45489a; border:none; padding:10px 30px;">Küldés</button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="feed" aria-label="Üzenőfal" role="region">
                        <?php
                        $postsReversed = array_reverse($posts, true);
                        foreach($postsReversed as $idx => $p):
                            $p_uid = $p['uid'] ?? 0;
                            if($p_uid == 0 && isset($p['user'])) {
                                $un = mysqli_real_escape_string($conn, $p['user']);
                                $fRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE uusername = '$un'"));
                                $p_uid = $fRow['uid'] ?? 0;
                            }

                            $isMine = ($userLoggedIn && $p['user'] == $userData['uusername']);
                            $isAdmin = (isset($_SESSION['status']) && $_SESSION['status'] === 'C');
                            $isVIP = (isset($_SESSION['status']) && $_SESSION['status'] === 'B');
                            $canDelete = ($isMine || $isAdmin || $isVIP);
                            
                            $likes = (int)($p['likes'] ?? 0);
                            $name = $p['name'] ?? ('@' . $p['user']);
                            $avatar = $p['avatar'] ?? 'default.png';
                        ?>
                        <div class="post-card <?php echo $isMine ? 'my-post' : 'other-post'; ?>" id="post-<?php echo $idx; ?>" style="background:rgba(0,0,0,0.6); padding:20px; border-radius:15px; margin-bottom:20px; border:1px solid rgba(255,255,255,0.05);">
                            <div class="post-meta" style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:12px; margin-bottom:15px;">
                                <div class="user-trigger" onclick="toggleUserMenu(event, '<?php echo $idx; ?>')" style="display:flex; gap:12px; align-items:center; cursor:pointer; position:relative;">
                                    <img src="img/profiles/<?php echo htmlspecialchars($avatar); ?>" alt="avatar" style="width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid #b4865a;">
                                    <div style="line-height:1.2;">
                                        <strong style="color:#fff; font-size:22px;"><?php echo htmlspecialchars($name); ?> <i class="fa fa-caret-down" style="font-size:12px; color:#b4865a; margin-left:3px;"></i></strong>
                                        <div style="color:#94a3b8; font-size:11px;"><?php echo htmlspecialchars($p['time'] ?? ''); ?></div>
                                    </div>
                                    <div id="dropdown-<?php echo $idx; ?>" class="user-quick-menu" style="display:none; position:absolute; top:45px; left:0; background:#0f172a; border:1px solid #b4865a; border-radius:12px; z-index:100; min-width:180px; box-shadow:0 10px 25px rgba(0,0,0,0.8); overflow:hidden;">
                                        <a href="javascript:void(0)" onclick="openQuickProfile(<?php echo $p_uid; ?>)" style="display:block; padding:12px 15px; color:#fff; text-decoration:none; border-bottom:1px solid #1e293b; font-weight:600;"><i class="fa fa-user" style="width:25px; text-align:center;"></i> Adatlap</a>
                                       <?php  if($isAdmin || $isVIP): ?>
                                            <a href="javascript:void(0)" onclick="adminAction('kick', <?php echo $p_uid; ?>)" style="display:block; padding:12px 15px; color:#f59e0b; text-decoration:none; border-bottom:1px solid #1e293b; font-weight:600;"><i class="fa fa-bolt" style="width:25px; text-align:center;"></i> Kick</a>
                                            <a href="javascript:void(0)" onclick="adminAction('ban', <?php echo $p_uid; ?>)" style="display:block; padding:12px 15px; color:#ef4444; text-decoration:none; <?php echo $isAdmin ? 'border-bottom:1px solid #1e293b;' : ''; ?> font-weight:600;"><i class="fa fa-ban" style="width:25px; text-align:center;"></i> Tiltás</a>
                                        <?php endif; ?>
                                        <?php if($isAdmin): ?>
                                            <a href="javascript:void(0)" onclick="adminAction('reset_pw', <?php echo $p_uid; ?>)" style="display:block; padding:12px 15px; color:#38bdf8; text-decoration:none; font-weight:600;"><i class="fa fa-key" style="width:25px; text-align:center;"></i> PW Reset</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <p class="post-text" style="color:#f1f5f9; font-size:22px; line-height:1.6; margin-bottom:15px;"><?php echo nl2br(htmlspecialchars($p['text'] ?? '')); ?></p>

                            <?php if(!empty($p['image'])): ?>
                                <img src="img/posts/<?php echo htmlspecialchars($p['image']); ?>" alt="post image" style="width:100%; border-radius:12px; margin-bottom:15px; border:1px solid rgba(255,255,255,0.1);">
                            <?php endif; ?>

                            <div style="display:flex; align-items:center; justify-content:space-between; background:rgba(255,255,255,0.03); padding:10px 15px; border-radius:10px; border:1px solid rgba(255,255,255,0.05);">
                                <div style="display:flex; gap:20px; align-items:center;">
                                    <a href="?like=<?php echo (int)$idx; ?>" class="like-btn" data-post-id="<?php echo (int)$idx; ?>" style="text-decoration:none; color:<?php echo $likes > 0 ? '#ef4444' : '#94a3b8'; ?>; font-size:22px; display:flex; align-items:center; gap:6px; font-weight:bold;">
                                        <i class="fa <?php echo $likes > 0 ? 'fa-heart' : 'fa-heart-o'; ?>"></i> <span class="like-count"><?php echo $likes; ?></span>
                                    </a>
                                    <a href="?dislike=<?php echo (int)$idx; ?>" class="dislike-btn" data-post-id="<?php echo (int)$idx; ?>" style="text-decoration:none; color:#94a3b8; font-size:22px; display:flex; align-items:center; gap:6px;" title="Nem tetszik">
                                        <i class="fa fa-thumbs-down"></i> <span class="dislike-count"><?php echo (int)($p['dislikes'] ?? 0); ?></span>
                                    </a>
                                    <a href="javascript:void(0)" onclick="reportPost(<?php echo $idx; ?>)" style="text-decoration:none; color:#64748b; font-size:14px; display:flex; align-items:center; gap:6px;" title="Bejegyzés jelentése">
                                        <i class="fa fa-flag"></i> Jelentés
                                    </a>
                                </div>
                                <?php if ($canDelete): ?>
                                    <a href="?delete=<?php echo (int)$idx; ?>" onclick="return confirm('Biztosan törölni szeretnéd ezt a bejegyzést?');" style="text-decoration:none; color:#ef4444; font-size:16px;" title="Törlés">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="post-feedback" style="display:none; margin-top:10px; padding:10px 12px; border-radius:10px; font-size:14px; font-weight:600;"></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>


        <div class="footer-wrapper">
           
            <footer class="premium-footer">
                <p>
                    <a href="/editors" class="credits-link" style="color: inherit; text-decoration: none; cursor: pointer;">
                        Nógrádi csodák © Vizsgaremek . 2026 // Készítette: #F.Melinda és #M.István
                    </a>
                </p>
            </footer>
        </div>
    </div>

    <style>
        :root {
            --modal-accent: <?php echo (isset($_SESSION['status']) && $_SESSION['status'] === 'C') ? '#0ea5e9' : '#b4865a'; ?>;
            --modal-border: <?php echo (isset($_SESSION['status']) && $_SESSION['status'] === 'C') ? 'rgba(14, 165, 233, 0.5)' : 'rgba(180, 134, 90, 0.5)'; ?>;
        }

        .blog-profile-modal-box {
            background: rgba(10, 15, 30, 0.75) !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid var(--modal-border) !important;
            border-radius: 20px !important;
            position: relative;
            width: 90vw !important; 
            max-width: 500px !important;
            min-height: 300px !important;
            max-height: 85vh !important;
            box-sizing: border-box !important; 
            display: flex;
            flex-direction: column;
            box-shadow: 0 15px 50px rgba(0,0,0,0.9);
        }

        #quickProfileContent {
            padding: 15px;
            overflow-y: auto !important; 
            overflow-x: auto !important; 
            flex: 1;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        #quickProfileContent * {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        #quickProfileContent::-webkit-scrollbar { width: 12px; }
        #quickProfileContent::-webkit-scrollbar-thumb { background: var(--modal-accent); border-radius: 10px; }

        @media (max-width: 767px) {
            body.blog-page .section-heading h1 {
                font-size: 46px !important;
                margin-top: -22px !important;
                padding: 10px 16px 10px 48px !important;
            }

            body.blog-page .section-heading h1::before {
                left: 12px !important;
                font-size: 26px !important;
            }

            body.blog-page .section-heading p{
                font-size: 1.35rem !important;
                margin-top: 8px !important;
            }

            body.blog-page .post-card p,
            body.blog-page .post-card strong,
            body.blog-page textarea,
            body.blog-page button,
            body.blog-page .post-meta small,
            body.blog-page .post-meta span,
            body.blog-page .ad-box {
                font-size: 1.22rem !important;
            }

            body.blog-page .post-text {
                font-size: 1.35rem !important;
                line-height: 1.75 !important;
            }

            body.blog-page .post-card small{ font-size: 1.08rem !important; }
        }
        }
    </style>

    <div id="quickProfileModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:transparent; z-index:99999; justify-content:center; align-items:center;">
        <div class="blog-profile-modal-box">
            <button onclick="closeQuickProfile()" style="position:absolute; top:15px; right:15px; background:rgba(255,255,255,0.1); color:white; border:none; width:32px; height:32px; border-radius:50%; cursor:pointer; z-index:100; transition: 0.3s; display:flex; align-items:center; justify-content:center;" onmouseover="this.style.background='#ef4444'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                <i class="fa fa-times"></i>
            </button>
            <div id="quickProfileContent"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quickProfileModal = document.getElementById('quickProfileModal');
            if (quickProfileModal) {
                quickProfileModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeQuickProfile();
                    }
                });
            }

            if (window.jQuery) {
                $('#mobile-panel').hide();
                $('#mobile-hamburger').removeClass('active');
                $('.m-has-submenu').removeClass('open').find('.m-submenu').hide();
                $('body').css('overflow', 'auto');
            }
        });
    </script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/vendor/bootstrap.min.js"></script>
    <script src="js/plugins.js"></script>
    <script src="js/main.js"></script>

    <script>
        function toggleUserMenu(event, id) {
            event.stopPropagation();
            document.querySelectorAll('.user-quick-menu').forEach(menu => {
                if(menu.id !== 'dropdown-' + id) menu.style.display = 'none';
            });
            const menu = document.getElementById('dropdown-' + id);
            if(menu) menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
        }

        window.addEventListener('click', function(event) {
            if (!event.target.closest('.user-trigger')) {
                document.querySelectorAll('.user-quick-menu').forEach(menu => menu.style.display = 'none');
            }
        });

        function resetBlogMenus() {
            if (window.jQuery) {
                $('#mobile-hamburger').removeClass('active');
                $('#mobile-panel').hide();
                $('body').css('overflow', 'auto');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            resetBlogMenus();
        });

        function openQuickProfile(uid) {
            if(!uid || uid == 0) return;
            const modal = document.getElementById('quickProfileModal');
            const content = document.getElementById('quickProfileContent');

            modal.style.display = 'flex';
            content.innerHTML = `
                <div style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:100%; color:#b4865a;">
                    <i class="fa fa-refresh fa-spin fa-4x" style="margin-bottom:20px;"></i>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 15px; font-weight:bold; letter-spacing:1px;">
                        DECRYPTING USER DATA [UID: ${uid}]...
                    </div>
                </div>
            `;

            $.get('admin_fetch_user.php', { id: uid }, function(data) {
                content.innerHTML = data;
            }).fail(function() {
                content.innerHTML = '<div style="color:#ef4444; text-align:center; padding:20px; margin-top:50px;">Hiba történt az adatok lekérésekor!</div>';
            });
        }

        function closeQuickProfile() {
            document.getElementById('quickProfileModal').style.display = 'none';
        }

        window.isCAdmin = <?php echo (isset($_SESSION['status']) && $_SESSION['status'] === 'C') ? 'true' : 'false'; ?>;

        function adminAction(action, uid) {
            if(!uid || uid == 0) return;
            if (!window.isCAdmin && (action === 'kick' || action === 'ban')) {
                Swal.fire({
                    title: 'Nincs jogosultság',
                    text: 'Ezeket a műveleteket csak a C rang használhatja.',
                    icon: 'error',
                    background: '#0f172a',
                    color: '#f8fafc'
                });
                return;
            }

            let titleText = "Biztonsági megerősítés";
            let messageText = "Valóban végrehajtja a kért műveletet ezen a felhasználón?";
            let btnText = "Igen";
            let btnColor = "#b4865a";

            if(action === 'ban') { titleText = "Felhasználó Tiltása"; messageText = "A felhasználó nem fog tudni belépni a rendszerbe."; btnText = "Tiltás"; btnColor = "#ef4444"; }
            if(action === 'kick') { titleText = "Kényszerített Kiléptetés"; messageText = "A felhasználó azonnal ki lesz dobva a rendszerből."; btnText = "Kirúgás"; btnColor = "#f59e0b"; }
            if(action === 'reset_pw') { titleText = "Jelszó Reset"; messageText = "Új ideiglenes jelszó generálása. Ezt el kell juttatnod a felhasználónak."; btnText = "Reset"; btnColor = "#38bdf8"; }

            Swal.fire({
                title: titleText,
                text: messageText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: btnColor,
                cancelButtonColor: '#334155',
                confirmButtonText: btnText,
                cancelButtonText: 'Mégse',
                background: '#0f172a',
                color: '#f8fafc'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post('admin_process_action.php', { action: action, uid: uid }, function(res) {
                        const isError = res.toLowerCase().includes("hiba") || res.toLowerCase().includes("jogosultság");
                        Swal.fire({
                            title: isError ? 'Hiba történt!' : 'Sikeres művelet!',
                            text: res,
                            icon: isError ? 'error' : 'success',
                            background: '#0f172a',
                            color: '#f8fafc',
                            timer: isError ? 4000 : 2500,
                            showConfirmButton: false
                        }).then(() => {
                            if(!isError) location.reload();
                        });
                    });
                }
            });
        }

        function reportPost(idx) {
            Swal.fire({
                title: 'Bejegyzés jelentése 🏴',
                text: 'Kérjük, válaszd ki az okot:',
                input: 'select',
                inputOptions: {
                    'spam': 'Spam / Kéretlen hirdetés',
                    'hate': 'Gyűlöletbeszéd / Bántó tartalom',
                    'harassment': 'Zaklatás',
                    'other': 'Egyéb probléma'
                },
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Jelentés küldése',
                cancelButtonText: 'Mégse',
                background: '#0f172a',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed && result.value) {
                    fetch('report_process.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
                        body: `post_idx=${encodeURIComponent(idx)}&reason=${encodeURIComponent(result.value)}`
                    })
                    .then(response => response.text().then(text => ({ ok: response.ok, text: text.trim() })))
                    .then(result => {
                        if (!result.ok) {
                            throw new Error(result.text || 'Hiba');
                        }

                        Swal.fire({
                            title: 'Rögzítve!',
                            text: 'A moderátoraink hamarosan átnézik...',
                            icon: 'success',
                            background: '#0f172a',
                            color: '#fff'
                        });
                    })
                    .catch(() => {
                        Swal.fire({
                            title: 'Hiba!',
                            text: 'Nem sikerült elküldeni a jelentést.',
                            icon: 'error',
                            background: '#0f172a',
                            color: '#fff'
                        });
                    });
                }
            });
        }
    </script>

    <script>
        setTimeout(() => {
            const msg = document.querySelector('.status-msg');
            if (msg) {
                msg.style.transition = "opacity 0.6s ease";
                msg.style.opacity = "0";
                setTimeout(() => msg.remove(), 600);
            }
        }, 4000);
    </script>

    <script>
        function showPostFeedback(btn, message, isError) {
            const postCard = btn.closest('.post-card');
            if (!postCard) return;

            const box = postCard.querySelector('.post-feedback');
            if (!box) return;

            box.style.display = 'block';
            box.style.background = isError ? 'rgba(239, 68, 68, 0.18)' : 'rgba(34, 197, 94, 0.18)';
            box.style.border = isError ? '1px solid rgba(239, 68, 68, 0.45)' : '1px solid rgba(34, 197, 94, 0.45)';
            box.style.color = '#000000';
            box.style.textShadow = 'none';
            box.style.fontWeight = '700';
            box.textContent = message;

            clearTimeout(box._hideTimer);
            box._hideTimer = setTimeout(function() {
                box.style.display = 'none';
                box.textContent = '';
            }, 2500);
        }

        document.querySelectorAll('.like-btn').forEach(function(btn) {
            btn.addEventListener('click', function(event) {
                event.preventDefault();
                const likeUrl = this.getAttribute('href');
                const likeCountEl = this.querySelector('.like-count');
                const iconEl = this.querySelector('i');

                fetch(likeUrl, { credentials: 'same-origin' })
                    .then(function(response) {
                        return response.text().then(function(text) {
                            return { ok: response.ok, text: text.trim() };
                        });
                    })
                    .then(function(result) {
                        if (result.text.indexOf('already_liked') !== -1) {
                            showPostFeedback(btn, 'Ezt már like-oltad.', true);
                            return;
                        }
                        if (result.text.indexOf('liked') !== -1) {
                            const currentCount = parseInt(likeCountEl.textContent, 10) || 0;
                            if (currentCount === 0) {
                                likeCountEl.textContent = '1';
                                iconEl.classList.remove('fa-heart-o');
                                iconEl.classList.add('fa-heart');
                                btn.style.color = '#ef4444';
                            }
                            showPostFeedback(btn, 'Like rögzítve.', false);
                            return;
                        }
                        showPostFeedback(btn, 'Nem sikerült a like.', true);
                    })
                    .catch(function() {
                        showPostFeedback(btn, 'Hiba történt a like mentésekor.', true);
                    });
            });
        });

        document.querySelectorAll('.dislike-btn').forEach(function(btn) {
            btn.addEventListener('click', function(event) {
                event.preventDefault();
                const dislikeUrl = this.getAttribute('href');
                const countEl = this.querySelector('.dislike-count');
                const iconEl = this.querySelector('i');

                fetch(dislikeUrl, { credentials: 'same-origin' })
                    .then(function(response) {
                        return response.text().then(function(text) {
                            return { ok: response.ok, text: text.trim() };
                        });
                    })
                    .then(function(result) {
                        if (result.text.indexOf('already_disliked') !== -1) {
                            showPostFeedback(btn, 'Ezt már dislike-oltad.', true);
                            return;
                        }
                        if (result.text.indexOf('disliked') !== -1) {
                            const currentCount = parseInt(countEl.textContent, 10) || 0;
                            if (currentCount === 0) {
                                countEl.textContent = '1';
                                iconEl.style.color = '#ef4444';
                                btn.style.color = '#ef4444';
                            }
                            showPostFeedback(btn, 'Dislike rögzítve.', false);
                            return;
                        }
                        showPostFeedback(btn, 'Nem sikerült a dislike mentése.', true);
                    })
                    .catch(function() {
                        showPostFeedback(btn, 'Hiba történt a dislike mentésekor.', true);
                    });
            });
        });
    </script>
       <?php include 'ertekeles_statisztika.php'; ?> 
    <?php include "valuta/api_valuta.php"; ?>
</body>
</html>
