<?php
session_start();
require_once 'db.php';

// Felhasználó azonosítása
if (isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
} elseif (isset($_SESSION['user_name'])) {
    $uname = $_SESSION['user_name'];
    $res = mysqli_query($conn, "SELECT uid FROM felhasznalok WHERE uusername = '$uname'");
    $row = mysqli_fetch_assoc($res);
    $uid = $row['uid'] ?? null;
    if($uid) $_SESSION['uid'] = $uid;
}

if (!$uid) { header("Location: login.php"); exit(); }

$user_res = mysqli_query($conn, "SELECT uavatar, upw, uemail FROM felhasznalok WHERE uid = $uid");
$user_data = mysqli_fetch_assoc($user_res);

// --- [ A ] EMAIL MENTÉSE (JAVÍTVA) ---
if (isset($_POST['save_email'])) {
    // Itt a trükk: uemail_new-t nézzük, mert a formban ezt adtuk meg!
    $email_field = isset($_POST['uemail_new']) ? $_POST['uemail_new'] : (isset($_POST['uemail']) ? $_POST['uemail'] : '');
    $new_email = trim(mysqli_real_escape_string($conn, $email_field));
    
    if (!empty($new_email)) {
        mysqli_query($conn, "UPDATE felhasznalok SET uemail = '$new_email' WHERE uid = $uid");
        
        // Cache-Control fejlécek a biztonság kedvéért
        header("Cache-Control: no-cache, must-revalidate");
        // A v=time() paraméter garantálja, hogy a böngésző új kérést küldjön, ne a régit mutassa
        header("Location: profile.php?msg=email_kesz&v=" . time());
    } else {
        header("Location: profile.php?error=ures_email");
    }
    exit();
}

// --- [ B ] PROFILKÉP (Változatlan) ---
if (isset($_POST['save_avatar']) && isset($_FILES['uavatar']) && $_FILES['uavatar']['error'] === 0) {
    $target_dir = "img/profiles/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    $img_name = time() . "_" . $uid . ".jpg";
    $target_path = $target_dir . $img_name;
    $source_path = $_FILES['uavatar']['tmp_name'];
    $info = getimagesize($source_path);
    if ($info) {
        $src = null;
        if ($info[2] == IMAGETYPE_JPEG) $src = imagecreatefromjpeg($source_path);
        elseif ($info[2] == IMAGETYPE_PNG) $src = imagecreatefrompng($source_path);
        elseif ($info[2] == IMAGETYPE_GIF) $src = imagecreatefromgif($source_path);
        if ($src) {
            $new_img = imagecreatetruecolor(100, 100);
            imagealphablending($new_img, false);
            imagesavealpha($new_img, true);
            imagecopyresampled($new_img, $src, 0, 0, 0, 0, 100, 100, $info[0], $info[1]);
            if (imagejpeg($new_img, $target_path, 85)) {
                mysqli_query($conn, "UPDATE felhasznalok SET uavatar = '$img_name' WHERE uid = $uid");
                if (!empty($user_data['uavatar']) && $user_data['uavatar'] != 'default.png' && file_exists($target_dir . $user_data['uavatar'])) {
                    @unlink($target_dir . $user_data['uavatar']);
                }
            }
            imagedestroy($new_img); imagedestroy($src);
        }
    }
    header("Location: profile.php?msg=kep_kesz&v=" . time());
    exit();
}

// --- [ C ] JELSZÓ (Változatlan) ---
if (isset($_POST['save_pw'])) {
    $old_pw = $_POST['old_pw'];
    $new_pw = $_POST['new_pw'];
    $confirm = $_POST['new_confirm'];
    if (!empty($old_pw) && !empty($new_pw) && $new_pw === $confirm) {
        if (password_verify($old_pw, $user_data['upw'])) {
            $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "UPDATE felhasznalok SET upw = ? WHERE uid = ?");
            mysqli_stmt_bind_param($stmt, "si", $hashed, $uid);
            mysqli_stmt_execute($stmt);
            header("Location: profile.php?msg=pw_kesz");
        } else {
            header("Location: profile.php?error=wrong_pw");
        }
    } else {
        header("Location: profile.php?error=match_or_empty");
    }
    exit();
}

header("Location: profile.php");
exit();
?>