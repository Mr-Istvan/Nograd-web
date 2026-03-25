<?php
require_once __DIR__ . '/init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['uusername'])) {
    
    $usernameOrEmail = mysqli_real_escape_string($conn, $_POST['uusername']);
    $pass = $_POST['upw']; 

    $query = "SELECT * FROM felhasznalok WHERE uusername = '$usernameOrEmail' OR uemail = '$usernameOrEmail' LIMIT 1";
    $result = mysqli_query($conn, $query);

    // Függvény a tiszta visszadobáshoz
    function backWithError($err) {
        echo "<script>window.location.replace('login.php?error=$err');</script>";
        exit();
    }

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($pass, $row['upw'])) {
            
            $status = $row['ustatus'];

            if ($status == 'T') backWithError('suspended');
            if ($status == 'X') backWithError('deleted');
            
            // Sikeres belépés
            mysqli_query($conn, "UPDATE felhasznalok SET ulogindata = NOW() WHERE uid = '{$row['uid']}'");

            $_SESSION['uid']       = $row['uid'];
            $_SESSION['user_name'] = $row['uusername'];
            $_SESSION['full_name'] = $row['uname'];
            $_SESSION['status']    = $status; 
            $_SESSION['last_activity'] = time();

            header("Location: index.php");
            exit();

        } else {
            backWithError('wrong_pw');
        }
    } else {
        backWithError('no_user');
    }
}
header("Location: login.php");
exit();