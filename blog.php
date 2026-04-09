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
        "likes" => 0,
        "dislikes" => 0
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
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'status' => 'not_found']);
            exit();
        }
        header("Location: blog.php");
        exit();
    }
    if (!isset($_SESSION['user_name'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'status' => 'login_required']);
            exit();
        }
        header("Location: login.php?msg=like_login");
        exit();
    }
    $userKey = $_SESSION['user_name'];
    if (!isset($postsAll[$id]['votes']) || !is_array($postsAll[$id]['votes'])) {
        $postsAll[$id]['votes'] = [];
    }
    $currentVote = $postsAll[$id]['votes'][$userKey] ?? null;
    if ($currentVote === 'liked') {
        $postsAll[$id]['likes'] = max(0, (int)($postsAll[$id]['likes'] ?? 0) - 1);
        unset($postsAll[$id]['votes'][$userKey]);
        file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => true, 'status' => 'unliked', 'likes' => (int)$postsAll[$id]['likes']]);
            exit();
        }
        header("Location: blog.php?msg=unliked");
        exit();
    }
    if ($currentVote === 'disliked') {
        $postsAll[$id]['dislikes'] = max(0, (int)($postsAll[$id]['dislikes'] ?? 0) - 1);
    }
    $postsAll[$id]['likes'] = (int)($postsAll[$id]['likes'] ?? 0) + 1;
    $postsAll[$id]['votes'][$userKey] = 'liked';
    file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    $authorUsername = $postsAll[$id]['user'] ?? '';
    $authorEmail = '';
    $authorName = $postsAll[$id]['name'] ?? $authorUsername;
    $likerName = $userData['uname'] ?? $userKey;
    if ($authorUsername !== '') {
        $safeAuthor = mysqli_real_escape_string($conn, $authorUsername);
        $authorQuery = mysqli_query($conn, "SELECT uname, uemail FROM felhasznalok WHERE uusername = '$safeAuthor' LIMIT 1");
        if ($authorQuery && ($authorRow = mysqli_fetch_assoc($authorQuery))) {
            $authorEmail = $authorRow['uemail'] ?? '';
            $authorName = $authorRow['uname'] ?? $authorName;
        }
    }

    if (!empty($authorEmail) && $authorEmail !== ($userData['uemail'] ?? '')) {
        $subject = "=?UTF-8?B?" . base64_encode("Új like érkezett a bejegyzésedre - Nógrád Csodák") . "?=";
        $safePostText = htmlspecialchars(mb_substr((string)($postsAll[$id]['text'] ?? ''), 0, 140));
        $safeLiker = htmlspecialchars((string)$likerName);
        $safeAuthorName = htmlspecialchars((string)$authorName);
        $message = "
        <html>
        <head>
            <style>
                body { background:#f1f5f9; padding:20px; font-family:Arial,sans-serif; }
                .card { background:#ffffff; color:#0f172a; padding:30px; border-radius:16px; border:1px solid #e2e8f0; max-width:560px; margin:0 auto; }
                .btn { display:inline-block; margin-top:20px; padding:12px 20px; background:#45489a; color:#fff !important; text-decoration:none; border-radius:10px; font-weight:bold; }
                .muted { color:#64748b; font-size:13px; margin-top:18px; }
            </style>
        </head>
        <body>
            <div class='card'>
                <h2 style='margin-top:0; color:#45489a;'>Szia " . $safeAuthorName . "!</h2>
                <p><strong>" . $safeLiker . "</strong> like-olta az egyik bejegyzésedet.</p>
                <p style='line-height:1.6; color:#334155;'>Bejegyzés részlete: " . $safePostText . "</p>
                <a class='btn' href='https://nogradcsodak.szakdoga.net/blog.php'>Ugrás a blogra</a>
                <div class='muted'>Ez egy automatikus értesítés, kérjük ne válaszolj rá.</div>
            </div>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: Nógrád Csodák <project@nogradcsodak.szakdoga.net>\r\n";
        @mail($authorEmail, $subject, $message, $headers);
    }

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => true, 'status' => 'liked', 'likes' => (int)$postsAll[$id]['likes'], 'dislikes' => (int)($postsAll[$id]['dislikes'] ?? 0)]);
        exit();
    }
    header("Location: blog.php?msg=liked");
    exit();
}

// DISLIKE
if (isset($_GET['dislike'])) {
    $id = (int)$_GET['dislike'];
    $postsAll = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];
    if (!isset($postsAll[$id])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'status' => 'not_found']);
            exit();
        }
        header("Location: blog.php");
        exit();
    }
    if (!isset($_SESSION['user_name'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'status' => 'login_required']);
            exit();
        }
        header("Location: login.php?msg=like_login");
        exit();
    }
    $userKey = $_SESSION['user_name'];
    if (!isset($postsAll[$id]['votes']) || !is_array($postsAll[$id]['votes'])) {
        $postsAll[$id]['votes'] = [];
    }
    $currentVote = $postsAll[$id]['votes'][$userKey] ?? null;
    if ($currentVote === 'disliked') {
        $postsAll[$id]['dislikes'] = max(0, (int)($postsAll[$id]['dislikes'] ?? 0) - 1);
        unset($postsAll[$id]['votes'][$userKey]);
        file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => true, 'status' => 'undisliked', 'dislikes' => (int)$postsAll[$id]['dislikes']]);
            exit();
        }
        header("Location: blog.php?msg=undisliked");
        exit();
    }
    if ($currentVote === 'liked') {
        $postsAll[$id]['likes'] = max(0, (int)($postsAll[$id]['likes'] ?? 0) - 1);
    }
    $postsAll[$id]['dislikes'] = (int)($postsAll[$id]['dislikes'] ?? 0) + 1;
    $postsAll[$id]['votes'][$userKey] = 'disliked';
    file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => true, 'status' => 'disliked', 'likes' => (int)($postsAll[$id]['likes'] ?? 0), 'dislikes' => (int)$postsAll[$id]['dislikes']]);
        exit();
    }
    header("Location: blog.php?msg=disliked");
    exit();
}

// Hozzászólás mentése
if (isset($_GET['reply']) && $_SERVER['REQUEST_METHOD'] === 'POST' && $userData) {
    while (ob_get_level()) {
        ob_end_clean();
    }

    $id = (int)$_GET['reply'];
    $postsAll = file_exists($postsFile) ? json_decode(file_get_contents($postsFile), true) : [];

    if (!isset($postsAll[$id])) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'status' => 'not_found']);
        exit();
    }

    $replyText = trim($_POST['reply_text'] ?? $_POST['text'] ?? '');
    if ($replyText === '') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'status' => 'empty']);
        exit();
    }

    if (!isset($postsAll[$id]['replies']) || !is_array($postsAll[$id]['replies'])) {
        $postsAll[$id]['replies'] = [];
    }

    $replyData = [
        'user' => $userData['uusername'],
        'name' => '@' . ($userData['uusername'] ?? 'user'),
        'avatar' => $userData['uavatar'] ?? 'default.png',
        'text' => $replyText,
        'time' => date('H:i')
    ];

    $postsAll[$id]['replies'][] = $replyData;
    $saved = file_put_contents($postsFile, json_encode($postsAll, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    if ($saved === false) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['success' => false, 'status' => 'save_failed']);
        exit();
    }

    $authorUsername = $postsAll[$id]['user'] ?? '';
    $authorEmail = '';
    $authorName = $postsAll[$id]['name'] ?? $authorUsername;
    if ($authorUsername !== '' && $authorUsername !== ($userData['uusername'] ?? '')) {
        $safeAuthor = mysqli_real_escape_string($conn, $authorUsername);
        $authorQuery = mysqli_query($conn, "SELECT uname, uemail FROM felhasznalok WHERE uusername = '$safeAuthor' LIMIT 1");
        if ($authorQuery && ($authorRow = mysqli_fetch_assoc($authorQuery))) {
            $authorEmail = $authorRow['uemail'] ?? '';
            $authorName = $authorRow['uname'] ?? $authorName;
        }
    }

    if (!empty($authorEmail)) {
        $subject = "=?UTF-8?B?" . base64_encode("Új hozzászólás érkezett a bejegyzésedhez - Nógrád Csodák") . "?=";
        $safeReply = htmlspecialchars(mb_substr($replyText, 0, 220));
        $safeReplier = htmlspecialchars($userData['uusername'] ?? 'user');
        $safeAuthorName = htmlspecialchars($authorName);
        $message = "
        <html>
        <head>
            <style>
                body { margin:0; padding:0; background:#f4f4f5; font-family:Arial,sans-serif; }
                .wrap { max-width:640px; margin:0 auto; padding:24px; }
                .card { background:#ffffff; border:1px solid #dbe4f0; border-radius:18px; overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,0.08); }
                .head { padding:18px 22px; background:#ececff; border-bottom:1px solid #d8dcff; display:flex; align-items:center; gap:14px; }
                .avatar { width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid #45489a; }
                .name { font-size:20px; font-weight:800; color:#111827; margin:0; line-height:1.1; }
                .time { font-size:12px; color:#64748b; margin-top:4px; }
                .body { padding:22px; color:#111827; font-size:15px; line-height:1.65; }
                .quote { margin:18px 0; padding:14px 16px; background:#f8fafc; border-left:4px solid #45489a; border-radius:12px; color:#334155; }
                .btn { display:inline-block; margin-top:18px; background:#45489a; color:#fff !important; text-decoration:none; padding:12px 18px; border-radius:12px; font-weight:700; }
                .foot { padding:16px 22px 22px; color:#64748b; font-size:12px; }
            </style>
        </head>
        <body>
            <div class='wrap'>
                <div class='card'>
                    <div class='head'>
                        <img class='avatar' src='https://nogradcsodak.szakdoga.net/img/profiles/" . htmlspecialchars($userData['uavatar'] ?? 'default.png') . "' alt='avatar'>
                        <div>
                            <p class='name'>" . $safeReplier . "</p>
                            <div class='time'>" . date('H:i') . "</div>
                        </div>
                    </div>
                    <div class='body'>
                        <div>Kaptál egy új hozzászólást a bejegyzésedhez:</div>
                        <div class='quote'>" . $safeReply . "</div>
                        <a class='btn' href='https://nogradcsodak.szakdoga.net/blog.php'>Megnézem</a>
                    </div>
                    <div class='foot'>Ez egy automatikus értesítés, kérjük ne válaszolj rá.</div>
                </div>
            </div>
        </body>
        </html>";

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8\r\n";
        $headers .= "From: Nógrád Csodák <project@nogradcsodak.szakdoga.net>\r\n";
        @mail($authorEmail, $subject, $message, $headers);
    }

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => true, 'status' => 'replied', 'name' => $replyData['name'], 'avatar' => $replyData['avatar'], 'text' => $replyData['text'], 'time' => $replyData['time']]);
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
        .content-section .section-heading { text-align: center !important; margin-left: 0 !important; margin-right: 0 !important; }
        .tetris-promo { width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; margin: 0 auto 20px auto; padding: 0 10px; box-sizing: border-box; }
        .tetris-promo__link { display: inline-block; cursor: pointer; transition: transform 0.1s ease-in-out; text-decoration: none; }
        .tetris-promo__img { display: block; width: 180px; max-width: 100%; height: auto; border-radius: 12px; border: 2px solid #5a5db8; box-shadow: 0 6px 20px rgba(70, 73, 154, 0.5); margin: 0 auto; }
        .tetris-promo__text { margin-top: 8px; font-weight: 700; color: #fff; text-shadow: 1px 1px 4px rgba(0,0,0,0.85); font-size: 14px; text-align: center; line-height: 1.35; }
        @media (max-width: 766px) {
            .tetris-promo { margin-top: -18px !important; margin-bottom: 16px !important; padding: 0 12px !important; }
            .tetris-promo__img { width: 140px !important; }
            .tetris-promo__text { font-size: 13px !important; margin-top: 6px !important; }
        }
        @media (min-width: 767px) and (max-width: 1000px) {
            .tetris-promo { margin-top: -8px !important; margin-bottom: 18px !important; }
            .tetris-promo__img { width: 160px !important; }
            .tetris-promo__text { font-size: 14px !important; margin-left: 0 !important; }
        }

        @media (min-width: 767px) and (max-width: 850px) {
            .tetris-promo__text { margin-left: 50px !important; }
        }
        @media (min-width: 1001px) {
            .tetris-promo { margin-top: 0 !important; margin-bottom: 20px !important; }
        }
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
                margin-left: 270px !important; 
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
    </div>
    <div class="page-content">
        <section class="content-section">

            <div class="section-heading" style="text-align:center; width:100%; margin-left:auto; margin-right:auto;">
                <h1 style="display:inline-block; margin:0 auto;">Blog-<em>fal</em></h1>
                <p style="margin-left:auto; margin-right:auto;">"Nógrád élmények és top ajánlatok"</p>
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
<div class="tetris-promo">
    <a href="../tetris/tetris.php" class="tetris-promo__link">
        <img src="../tetris/tetris_button.png" alt="Nógrád Tetris - Játssz!" class="tetris-promo__img">
    </a>
    <div class="tetris-promo__text">👉 „Készen állsz? Döntsd meg a rekordot! 👑”</div>
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
                            $name = '@' . ($p['user'] ?? 'user');
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
                                    <a href="javascript:void(0)" onclick="toggleReplyBox(<?php echo $idx; ?>)" style="text-decoration:none; color:#45489a; font-size:14px; display:flex; align-items:center; gap:6px; font-weight:700;" title="Válasz / hozzászólás">
                                        <i class="fa fa-commenting-o"></i> 💬
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

                            <div class="reply-box" id="reply-box-<?php echo $idx; ?>" style="display:none; margin-top:12px; padding:12px; border-radius:12px; background:rgba(255,255,255,0.06); border:1px solid rgba(69,72,154,0.45);">
                                <textarea class="form-control reply-text" id="reply-text-<?php echo $idx; ?>" rows="3" placeholder="Írj hozzászólást..." style="width:100%; resize:vertical; background:#fff; color:#000; margin-bottom:10px;"></textarea>
                                <div style="display:flex; gap:10px; justify-content:flex-end; flex-wrap:wrap;">
                                    <button type="button" class="btn btn-primary" style="background:#45489a; border:none;" onclick="submitReply(<?php echo $idx; ?>)">Küldés</button>
                                    <button type="button" class="btn btn-default" style="background:#cbd5e1; border:none;" onclick="toggleReplyBox(<?php echo $idx; ?>)">Mégse</button>
                                </div>
                            </div>

                            <div class="reply-thread" id="reply-thread-<?php echo $idx; ?>" style="margin-top:12px;">
                                <?php if (!empty($p['replies']) && is_array($p['replies'])): ?>
                                    <?php foreach ($p['replies'] as $reply): ?>
                                        <div style="margin-top:10px; margin-left:35px; padding:12px 14px; background:rgba(255,255,255,0.92); border-left:4px solid #45489a; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); color:#111;">
                                            <div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;">
                                                <img src="img/profiles/<?php echo htmlspecialchars($reply['avatar'] ?? 'default.png'); ?>" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid #b4865a;">
                                                <strong style="font-size:14px;"><?php echo htmlspecialchars($reply['name'] ?? ($reply['user'] ?? 'Felhasználó')); ?></strong>
                                                <small style="color:#64748b;"><?php echo htmlspecialchars($reply['time'] ?? ''); ?></small>
                                            </div>
                                            <div style="font-size:15px; line-height:1.5;"><?php echo nl2br(htmlspecialchars($reply['text'] ?? '')); ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>


        <div class="footer-wrapper">
           
            <footer class="premium-footer">
                <p>
                    <a href="/Proofiles.php" class="credits-link" style="color: inherit; text-decoration: none; cursor: pointer;">
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
        window.__blogUserLoggedIn = <?php echo $userLoggedIn ? 'true' : 'false'; ?>;

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

        function showLoginRequired() {
            Swal.fire({
                title: 'Figyelmeztetés',
                text: 'Ehhez be kell jelentkezned!',
                icon: 'error',
                confirmButtonText: 'Rendben',
                confirmButtonColor: '#ef4444',
                background: '#0f172a',
                color: '#fff'
            });
        }

        function reportPost(idx) {
            if (!window.__blogUserLoggedIn) {
                showLoginRequired();
                return;
            }
        }

        function toggleReplyBox(idx) {
            if (!window.__blogUserLoggedIn) {
                showLoginRequired();
                return;
            }

            const box = document.getElementById('reply-box-' + idx);
            if (!box) return;
            box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
        }

        function submitReply(idx) {
            if (!window.__blogUserLoggedIn) {
                showLoginRequired();
                return;
            }

            const textarea = document.getElementById('reply-text-' + idx);
            const box = document.getElementById('reply-box-' + idx);
            const thread = document.getElementById('reply-thread-' + idx);
            const text = textarea ? textarea.value.trim() : '';
            const feedbackTarget = document.getElementById('post-' + idx);

            if (!text) {
                showPostFeedback(feedbackTarget, 'Kérlek írj hozzászólást.', true);
                return;
            }

            const payload = new URLSearchParams();
            payload.append('reply_text', text);
            payload.append('text', text);
            payload.append('post_idx', String(idx));

            fetch('blog.php?reply=' + encodeURIComponent(idx), {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload.toString()
            })
            .then(function(response) {
                return response.text().then(function(rawText) {
                    let data = null;
                    try {
                        data = JSON.parse(rawText);
                    } catch (e) {
                        data = { success: false, status: 'invalid_json', raw: rawText };
                    }
                    return { ok: response.ok, data: data, raw: rawText };
                });
            })
            .then(function(result) {
                if (!result.data || result.data.success !== true) {
                    console.error('Reply save failed:', result.raw || result.data);
                    showPostFeedback(feedbackTarget, 'Nem sikerült elküldeni a hozzászólást.', true);
                    return;
                }

                const replyItem = document.createElement('div');
                replyItem.style.cssText = 'margin-top:10px; margin-left:35px; padding:12px 14px; background:rgba(255,255,255,0.92); border-left:4px solid #45489a; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.08); color:#111;';
                replyItem.innerHTML = '<div style="display:flex; align-items:center; gap:10px; margin-bottom:8px;"><img src="img/profiles/' + (result.data.avatar || 'default.png') + '" style="width:28px; height:28px; border-radius:50%; object-fit:cover; border:1px solid #b4865a;"><strong style="font-size:14px;">' + (result.data.name || 'Felhasználó') + '</strong><small style="color:#64748b;">' + (result.data.time || '') + '</small></div><div style="font-size:15px; line-height:1.5;">' + (result.data.text || '') + '</div>';

                if (thread) {
                    thread.appendChild(replyItem);
                } else {
                    console.error('Reply thread container missing for post', idx);
                }

                if (textarea) textarea.value = '';
                if (box) box.style.display = 'none';
                showPostFeedback(feedbackTarget, 'Hozzászólás elküldve.', false);
            })
            .catch(function(err) {
                console.error('Reply submit exception:', err);
                showPostFeedback(feedbackTarget, 'Hiba történt a hozzászólás mentésekor.', true);
            });
        }

        function reportPost(idx) {
            if (!window.__blogUserLoggedIn) {
                showLoginRequired();
                return;
            }

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
                didOpen: () => {
                    const select = Swal.getPopup()?.querySelector('select');
                    if (select) {
                        select.style.color = '#000';
                        select.style.backgroundColor = '#fff';
                    }
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
                if (!window.__blogUserLoggedIn) {
                    showLoginRequired();
                    return;
                }
                const likeUrl = this.getAttribute('href');
                const likeCountEl = this.querySelector('.like-count');
                const iconEl = this.querySelector('i');

                fetch(likeUrl, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function(result) {
                        if (!result.data || result.data.success !== true) {
                            if (result.data && result.data.status === 'login_required') {
                                showLoginRequired();
                                return;
                            }
                            showPostFeedback(btn, 'Nem sikerült a like.', true);
                            return;
                        }

                        if (result.data.status === 'liked') {
                            const currentCount = parseInt(likeCountEl.textContent, 10) || 0;
                            likeCountEl.textContent = String((result.data.likes !== undefined) ? result.data.likes : currentCount + 1);
                            iconEl.classList.remove('fa-heart-o');
                            iconEl.classList.add('fa-heart');
                            btn.style.color = '#ef4444';
                            showPostFeedback(btn, 'Like rögzítve.', false);
                            return;
                        }

                        if (result.data.status === 'unliked') {
                            const currentCount = parseInt(likeCountEl.textContent, 10) || 0;
                            likeCountEl.textContent = String((result.data.likes !== undefined) ? result.data.likes : Math.max(0, currentCount - 1));
                            iconEl.classList.remove('fa-heart');
                            iconEl.classList.add('fa-heart-o');
                            btn.style.color = '#94a3b8';
                            showPostFeedback(btn, 'Like visszavonva.', false);
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
                if (!window.__blogUserLoggedIn) {
                    showLoginRequired();
                    return;
                }
                const dislikeUrl = this.getAttribute('href');
                const countEl = this.querySelector('.dislike-count');
                const iconEl = this.querySelector('i');

                fetch(dislikeUrl, {
                    credentials: 'same-origin',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(function(response) {
                        return response.json().then(function(data) {
                            return { ok: response.ok, data: data };
                        });
                    })
                    .then(function(result) {
                        if (!result.data || result.data.success !== true) {
                            if (result.data && result.data.status === 'login_required') {
                                showLoginRequired();
                                return;
                            }
                            showPostFeedback(btn, 'Nem sikerült a dislike.', true);
                            return;
                        }

                        if (result.data.status === 'disliked') {
                            const currentCount = parseInt(countEl.textContent, 10) || 0;
                            countEl.textContent = String((result.data.dislikes !== undefined) ? result.data.dislikes : currentCount + 1);
                            iconEl.style.color = '#ef4444';
                            btn.style.color = '#ef4444';
                            showPostFeedback(btn, 'Dislike rögzítve.', false);
                            return;
                        }

                        if (result.data.status === 'undisliked') {
                            const currentCount = parseInt(countEl.textContent, 10) || 0;
                            countEl.textContent = String((result.data.dislikes !== undefined) ? result.data.dislikes : Math.max(0, currentCount - 1));
                            iconEl.style.color = '#94a3b8';
                            btn.style.color = '#94a3b8';
                            showPostFeedback(btn, 'Dislike visszavonva.', false);
                            return;
                        }

                        showPostFeedback(btn, 'Nem sikerült a dislike.', true);
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
