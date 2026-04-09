<?php
require_once __DIR__ . '/init.php';

// Csak bejelentkezett felhasználók jelenthetnek
if (!isset($_SESSION['user_name'])) {
    http_response_code(403);
    die("Bejelentkezés szükséges!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_idx = isset($_POST['post_idx']) ? (int)$_POST['post_idx'] : -1;
    $reason = isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '';

    if ($post_idx < 0 || empty($reason)) {
        http_response_code(400);
        die("Hiányzó adatok!");
    }

    $reportsFile = 'data/reports.json';
    $reports = [];

    if (file_exists($reportsFile)) {
        $reports = json_decode(file_get_contents($reportsFile), true) ?: [];
    }

    // Új jelentés adatai
    $newReport = [
        'report_id' => uniqid(),
        'post_idx' => $post_idx,
        'reporter' => $_SESSION['user_name'],
        'reason' => $reason,
        'date' => date('Y-m-d H:i:s'),
        'status' => 'pending' // pending, reviewed, dismissed
    ];

    $reports[] = $newReport;

    if (file_put_contents($reportsFile, json_encode($reports, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        echo "Sikeres jelentés!";
    } else {
        http_response_code(500);
        echo "Hiba a mentés során!";
    }
}