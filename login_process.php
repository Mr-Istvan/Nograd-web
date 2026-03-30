<?php
require_once __DIR__ . '/init.php';

// Ellenőrizzük, hogy POST kéréssel érkezett-e az adat
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uusername'])) {
    
    $usernameOrEmail = mysqli_real_escape_string($conn, trim($_POST['uusername']));
    $pass = $_POST['upw']; 

    // Lekérdezzük a felhasználót Becenév vagy Email alapján
    $query = "SELECT * FROM felhasznalok WHERE uusername = '$usernameOrEmail' OR uemail = '$usernameOrEmail' LIMIT 1";
    $result = mysqli_query($conn, $query);

    // Segédfüggvény a hiba esetén történő tiszta visszadobáshoz (replace-el, hogy az X gomb ne ragadjon be)
    function backWithError($err) {
        echo "<script>window.location.replace('login.php?error=$err');</script>";
        exit();
    }

    if ($row = mysqli_fetch_assoc($result)) {
        // Jelszó ellenőrzése
        if (password_verify($pass, $row['upw'])) {
            
            $status = $row['ustatus'];

            // Tiltott vagy törölt státusz ellenőrzése
            if ($status == 'T') backWithError('suspended');
            if ($status == 'X') backWithError('deleted');
            
            // Sikeres belépés: Utolsó belépés dátumának mentése az adatbázisba
            $uid = $row['uid'];
            mysqli_query($conn, "UPDATE felhasznalok SET ulogindata = NOW() WHERE uid = '$uid'");

            // Session adatok beállítása
            $_SESSION['uid']           = $row['uid'];
            $_SESSION['user_name']     = $row['uusername'];
            $_SESSION['full_name']     = $row['uname'];
            $_SESSION['status']        = $status; 
            $_SESSION['last_activity'] = time();

            // DINAMIKUS ÁTIRÁNYÍTÁS:
            // Megnézzük, van-e elmentett visszatérési címünk a login.php-ból
            if (isset($_SESSION['login_back_url']) && !empty($_SESSION['login_back_url'])) {
                $redirect = $_SESSION['login_back_url'];
                unset($_SESSION['login_back_url']); // Használat után töröljük
            } else {
                $redirect = 'index.php'; // Ha nincs mentett cím, megy a főoldalra
            }

            header("Location: " . $redirect);
            exit();

        } else {
            // Hibás jelszó
            backWithError('wrong_pw');
        }
    } else {
        // Nincs ilyen felhasználó
        backWithError('no_user');
    }
}

// Ha illetéktelenül (nem POST-tal) próbálják hívni a fájlt
header("Location: login.php");
exit();
?>