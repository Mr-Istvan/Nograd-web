<?php
require_once __DIR__ . '/init.php';

/* Jogosultság ellenőrzése: Csak 'C' (Rendszergazda) és 'B' (Kiemelt) férhet hozzá */
if (!isset($_SESSION['status']) || ($_SESSION['status'] !== 'C' && $_SESSION['status'] !== 'B')) {
    exit("Hiba: Nincs jogosultságod a művelethez!");
}

$my_status = $_SESSION['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // --- 1. BLOG POSZT TÖRLÉSE (Ezt C és B is megteheti) ---
    if ($action === 'delete_post' && isset($_POST['index'])) {
        $postsFile = 'data/posts.json';
        if (file_exists($postsFile)) {
            $posts = json_decode(file_get_contents($postsFile), true);
            $idx = (int)$_POST['index'];
            
            $originalIndex = count($posts) - 1 - $idx;

            if (isset($posts[$originalIndex])) {
                unset($posts[$originalIndex]);
                file_put_contents($postsFile, json_encode(array_values($posts), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
                echo "Poszt sikeresen törölve a szerverről!";
            } else {
                echo "Hiba: A poszt nem található!";
            }
        } else {
            echo "Hiba: A JSON fájl nem létezik!";
        }
        exit();
    }

    // --- 2. FELHASZNÁLÓI MŰVELETEK ---
    if (isset($_POST['uid'])) {
        $uid = (int)$_POST['uid'];

        // CÉLPONT ELLENŐRZÉSE - Kibe akarunk belenyúlni?
        $res = mysqli_query($conn, "SELECT ustatus FROM felhasznalok WHERE uid = $uid");
        $target = mysqli_fetch_assoc($res);
        
        if(!$target) exit("Hiba: Felhasználó nem található.");
        
        $target_status = $target['ustatus'];

        if ($my_status === 'B' && $target_status !== 'A') {
            exit("Hiba: Nincs rá jogosultságod! Csak 'A' rangúakat módosíthatsz.");
        }

        // reset_pw továbbra is csak C rangnak engedélyezett
        if ($action === 'reset_pw' && $my_status !== 'C') {
            exit("Hiba: Ezek a műveletek kizárólag C rang számára elérhetők!");
        }

        switch ($action) {
            case 'ban':
                $sql = "UPDATE felhasznalok SET ustatus = 'T' WHERE uid = $uid";
                if (mysqli_query($conn, $sql)) echo "Felhasználó kitiltva.";
                break;

            case 'activate':
                $sql = "UPDATE felhasznalok SET ustatus = 'A' WHERE uid = $uid";
                if (mysqli_query($conn, $sql)) echo "Felhasználó státusza aktívra állítva.";
                break;

            case 'kick':
                $sql = "UPDATE felhasznalok SET ulogoutdata = CURRENT_TIMESTAMP WHERE uid = $uid";
                if (mysqli_query($conn, $sql)) echo "Sikeres kick.";
                break;

            case 'change_rank':
                if ($my_status !== 'C') exit("Hiba: Rang módosításhoz 'C' (Rendszergazda) szint szükséges.");

                $new_status = isset($_POST['new_status']) ? $_POST['new_status'] : '';
                $allowed_statuses = ['A', 'B', 'C'];

                if (!in_array($new_status, $allowed_statuses, true)) {
                    exit("Hiba: Érvénytelen rang!");
                }

                if ($target_status === 'C' && $new_status !== 'C') {
                    exit("Hiba: A C rang külön védelem alatt áll.");
                }

                $sql = "UPDATE felhasznalok SET ustatus = '$new_status' WHERE uid = $uid";
                if (mysqli_query($conn, $sql)) {
                    echo "Felhasználó rangja sikeresen módosítva: $new_status";
                } else {
                    echo "Hiba: Rang módosítása sikertelen.";
                }
                break;

            // KIZÁRÓLAG 'C' ADMINOS MŰVELETEK (Végleges Törlés és Jelszó Reset)
            case 'delete':
                if ($my_status !== 'C') exit("Hiba: Profil törléséhez 'C' (Rendszergazda) szint szükséges.");
                if ($target_status === 'C' && $uid != $_SESSION['uid']) exit("Hiba: Másik C rangú felhasználó nem törölhető.");
                $sql = "UPDATE felhasznalok SET ustatus = 'X' WHERE uid = $uid";
                if (mysqli_query($conn, $sql)) echo "Felhasználó töröltnek jelölve (X).";
                break;

            case 'reset_pw':
                if ($my_status !== 'C') exit("Hiba: Jelszó generáláshoz 'C' (Rendszergazda) szint szükséges.");
                $chars = "abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789";
                $raw_pw = "NC_" . substr(str_shuffle($chars), 0, 8);
                $hashed_pw = password_hash($raw_pw, PASSWORD_DEFAULT);
                $sql = "UPDATE felhasznalok SET upw = '$hashed_pw' WHERE uid = $uid";
                if (mysqli_query($conn, $sql)) echo "ÚJ IDEIGLENES JELSZÓ: $raw_pw\n\nKérlek, másold ki és küldd el a felhasználónak!";
                break;

            default:
                echo "Hiba: Ismeretlen felhasználói művelet!";
                break;
        }
    }
} else {
    echo "Hiba: Érvénytelen kérés!";
}
?>

1
Castellum Hotel Hollókő


2
Tó Wellness Hotel


3
Főnix Wellness Resort


4
Cédrus Club Hotel


5
Salgó Hotel


6
Kastélyhotel Sasvár


7
Boróka Vendégház


8
Bánki-tó Vendégház


9
Prónay-kastély


10
Mátra Mona Luxury Apartment


11
Nádas fogadó Teresztenye


12
Teleki-Degenfeld Kastély


13
Piros Csizma Vendégház


14
Galagonya Vendégház térképe



15
Hétlámpás Vendégház Hollókő


16
Felső Fogadó


17
FeteKert Apartmanok


18
Napfénydomb Vendégház


19
Nádas Camping


20
Bárna Vadász- és Pihenőház


21
Cédrus Club Hotel


22
Somoskői Kirándulóközpont


23
Kaláris Vendégház


24
Templomvölgy Resort


25
Tó Wellness Hotel


26
Mátra Kemping


27
Tóparti Apartman


28
Zagyva-völgyi Vendégház


29
Várhegy Panzió


30
Cserhát Kapuja


31
Hollóköves Vendégház


32
Eresztvényi Turistaház


33
Rétsági Panzió


34
Tereskei Vendégház


35
Berceli Kastély


36
Kutasó Apartman


37
Palotási Tóparti Ház


38
Mátraverebélyi Zarándokház


39
Szentkúti Kegyhely Szálló


40
Legéndi Vendégház


41
Nógrádsipeki Pihenő


42
Felsőpetényi Vendégház


43
Karancssági Fogadó


44
Ipolyvecei Pihenőház


45
Diósjenői Kemping


46
Börzsönyi Turistaház


47
Somlyó-hegyi Apartman


48
Cserhátsurányi Kastélyszálló


49
Endrefalvai Vendégház


50
Garábi Élményszálló