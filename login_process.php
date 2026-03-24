<?php
require_once __DIR__ . '/init.php';

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

            if ($status == 'T') {
                echo "<script>alert('Ez a fiók fel van függesztve!'); window.location.replace('login.php');</script>";
                exit();
            }

            if ($status == 'X') {
                echo "<script>alert('Ez a fiók korábban törlésre került...'); window.location.replace('login.php');</script>";
                exit();
            }
            
            $uid = $row['uid'];
            
            // Utolsó belépés frissítése
            mysqli_query($conn, "UPDATE felhasznalok SET ulogindata = NOW() WHERE uid = '$uid'");

            // Session adatok beállítása
            $_SESSION['user_id']   = $row['uid'];
            $_SESSION['user_name'] = $row['uusername'];
            $_SESSION['full_name'] = $row['uname'];
            $_SESSION['status']    = $status; 
            $_SESSION['last_activity'] = time();

            // SIKER: Irány a főoldal
            header("Location: index.php");
            exit();

        } else {
            // JAVÍTVA: .replace-re cserélve a visszaléptetés miatt
           echo "<script>alert('Hibás jelszó!'); window.location.replace('login.php');</script>";
exit();
        }
    } else {
        // JAVÍTVA: .replace-re cserélve a visszaléptetés miatt
        echo "<script>alert('Nincs ilyen felhasználó regisztrálva!'); window.location.replace('login.php');</script>";
exit();
    }
}