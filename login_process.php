<?php
// 1. Biztonságos session indítás
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. ADATBÁZIS KAPCSOLAT
require_once "db.php"; 

// 3. CSAK akkor fut le a logika, ha valóban küldtek adatot (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uusername'])) {
    
    $usernameOrEmail = mysqli_real_escape_string($conn, $_POST['uusername']);
    $pass            = $_POST['upw']; 

    // Lekérdezés
    $query = "SELECT * FROM felhasznalok WHERE uusername = '$usernameOrEmail' OR uemail = '$usernameOrEmail' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Jelszó ellenőrzése
        if (password_verify($pass, $row['upw'])) {
            
            $status = $row['ustatus'];

            // --- STÁTUSZ ELLENŐRZÉSE ---

            // 1. Tiltott (T)
            if ($status == 'T') {
                echo "<script>alert('Ez a fiók fel van függesztve!'); window.location.href='login.php';</script>";
                exit();
            }

            // 2. Kilépett / Törölt profil (X)
            if ($status == 'X') {
                echo "<script>alert('Ez a fiók korábban törlésre került és már nem aktív.'); window.location.href='login.php';</script>";
                exit();
            }

            /* --- HA  A = alap felhasználó

                        B = haladó felhasználó (tilthat ,törölhet képket videokat )

                        C = mestre (lehet tanár és IT programozo)



                        T = tiltva

                        X = akik végleg kilptek /törölt profil ---*/
            
            $uid = $row['uid'];
            
            // Utolsó belépés frissítése
            mysqli_query($conn, "UPDATE felhasznalok SET ulogindata = NOW() WHERE uid = '$uid'");

            // Session adatok beállítása
            $_SESSION['user_id']   = $row['uid'];
            $_SESSION['user_name'] = $row['uusername'];
            $_SESSION['full_name'] = $row['uname'];
            $_SESSION['status']    = $status; // Itt tároljuk le az A, B vagy C értéket
            $_SESSION['last_activity'] = time(); // inaktivitás figyelés kezdete

            // SIKER: Irány a főoldal
            header("Location: index.php");
            exit();

        } else {
            echo "<script>alert('Hibás jelszó!'); window.location.href='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Nincs ilyen felhasználó regisztrálva!'); window.location.href='login.php';</script>";
        exit();
    }
}
