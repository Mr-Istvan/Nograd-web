<?php
require_once __DIR__ . '/init.php'; 

// 1. Értékelés indítása (AJAX kérésnél)
if (isset($_GET['action']) && $_GET['action'] == 'start') {
    $cookie_nev = "mar_szavazott_24h";
    $cooldownHours = 24;
    $cooldownSeconds = $cooldownHours * 3600;

    if (isset($_COOKIE[$cookie_nev])) {
        $remaining = $cooldownSeconds;
        $hours = floor($remaining / 3600);
        $minutes = floor(($remaining % 3600) / 60);
        echo "WAIT|" . $hours . "|" . $minutes;
        exit;
    }

    $ip_hash = hash('sha256', $_SERVER['REMOTE_ADDR']);
    $sql = "INSERT INTO ertekelo (ip_hash) VALUES ('$ip_hash')";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['current_eval_id'] = mysqli_insert_id($conn);
        echo "OK";
    }
    exit;
}

// 2. Beküldés feldolgozása
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answers'])) {
    if (!isset($_SESSION['current_eval_id'])) {
        header("Location: ertekeles.php?error=send_failed");
        exit;
    }

    $eid = intval($_SESSION['current_eval_id']);
    $answers = $_POST['answers'];
    $total_points = 0;

    // Pontok mentése
    foreach ($answers as $kid => $pont) {
        $kid = intval($kid); $pont = intval($pont);
        $total_points += $pont;
        mysqli_query($conn, "INSERT INTO Valaszok (eid, kid, pont) VALUES ($eid, $kid, $pont)");
    }

    // Összesítés
    $questionCount = count($answers);
    if ($questionCount <= 0) {
        header("Location: ertekeles.php?error=send_failed");
        exit;
    }
    $eatlag = $total_points / $questionCount;
    mysqli_query($conn, "UPDATE ertekelo SET evege = NOW(), osszp = $total_points, eatlag = $eatlag WHERE eid = $eid");

    unset($_SESSION['current_eval_id']);

    setcookie("mar_szavazott_24h", "igen", time() + 86400, "/");
    
    // Átirányítás sikerüzenettel
    header("Location: ertekeles.php?msg=send");
    exit;
} else {
    header("Location: ertekeles.php");
    exit;
}
?>
