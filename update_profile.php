<?php
require_once __DIR__ . '/init.php';

// Felhasználó azonosítása (Session alapján)
$uid = $_SESSION['uid'] ?? null;
if (!$uid && isset($_SESSION['user_name'])) {
    $uname = $_SESSION['user_name'];
    $res = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE uusername = '$uname'");
    $uid = mysqli_fetch_assoc($res)['uid'] ?? null;
    if($uid) $_SESSION['uid'] = $uid;
}
if (!$uid) { header("Location: login.php"); exit(); }

$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT uavatar, upw, uemail FROM felhasznalok WHERE uid = $uid"));

// A "MÁGIA": Ez a függvény kicseréli az aktuális oldalt az előzményekben
function redirect($url) {
    echo "<script>window.location.replace('$url');</script>";
    exit();
}

// --- [ A ] EMAIL MENTÉSE ---
if (isset($_POST['save_email'])) {
    $new_email = trim(mysqli_real_escape_string($conn, $_POST['uemail_new'] ?? $_POST['uemail'] ?? ''));
    if (!empty($new_email)) {
        mysqli_query($conn, "UPDATE felhasznalok SET uemail = '$new_email' WHERE uid = $uid");
        redirect("profile.php?msg=email_kesz&v=".time());
    }
    redirect("profile.php?error=ures_email");
}

// --- [ B ] PROFILKÉP (Átméretezéssel és konvertálással) ---
if (isset($_POST['save_avatar']) && isset($_FILES['uavatar']) && $_FILES['uavatar']['error'] === 0) {
    $target_dir = __DIR__ . "/img/profiles/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $img_name = time() . "_" . $uid . ".jpg";
    $source_path = $_FILES['uavatar']['tmp_name'];
    $info = @getimagesize($source_path);

    if ($info) {
        $src = null;
        if ($info[2] === IMAGETYPE_JPEG) {
            $src = imagecreatefromjpeg($source_path);
        } elseif ($info[2] === IMAGETYPE_PNG) {
            $src = imagecreatefrompng($source_path);
        } elseif ($info[2] === IMAGETYPE_GIF) {
            $src = imagecreatefromgif($source_path);
        }

        if ($src) {
            $new_img = imagecreatetruecolor(100, 100);
            imagealphablending($new_img, false);
            imagesavealpha($new_img, true);
            $bg = imagecolorallocate($new_img, 255, 255, 255);
            imagefilledrectangle($new_img, 0, 0, 100, 100, $bg);
            imagecopyresampled($new_img, $src, 0, 0, 0, 0, 100, 100, $info[0], $info[1]);

            if (imagejpeg($new_img, $target_dir . $img_name, 85)) {
                mysqli_query($conn, "UPDATE felhasznalok SET uavatar = '$img_name' WHERE uid = $uid");
                if (!empty($user_data['uavatar']) && $user_data['uavatar'] !== 'auto_profile.png' && file_exists($target_dir . $user_data['uavatar'])) {
                    @unlink($target_dir . $user_data['uavatar']);
                }
                redirect("profile.php?msg=kep_kesz&v=" . time());
            }

            imagedestroy($new_img);
            imagedestroy($src);
        }
    }

    redirect("profile.php?error=avatar_fail&v=" . time());
}

// --- [ C ] JELSZÓ MÓDOSÍTÁSA ---
if (isset($_POST['save_pw'])) {
    $old_pw = $_POST['old_pw'] ?? '';
    $p1 = $_POST['new_pw'] ?? '';
    $p2 = $_POST['new_confirm'] ?? '';

    if (!empty($old_pw) && $p1 === $p2 && !empty($p1)) {
        if (password_verify($old_pw, $user_data['upw'])) {
            $hashed = password_hash($p1, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE felhasznalok SET upw = '$hashed' WHERE uid = $uid");
            redirect("profile.php?msg=pw_kesz");
        }
        redirect("profile.php?error=wrong_pw");
    }
    redirect("profile.php?error=match_or_empty");
}

// --- [ D ] TITKOS KÉRDÉS MENTÉSE ---
if (isset($_POST['save_secret'])) {
    $q = mysqli_real_escape_string($conn, $_POST['usecret_q'] ?? '');
    $a = mysqli_real_escape_string($conn, strtolower(trim($_POST['usecret_a'] ?? '')));
    if (!empty($q) && !empty($a)) {
        mysqli_query($conn, "UPDATE felhasznalok SET usecret_q = '$q', usecret_a = '$a' WHERE uid = $uid");
        redirect("profile.php?msg=secret_ok");
    }
}

redirect("profile.php");
