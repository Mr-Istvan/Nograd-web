<?php
/**
 * load.php - Toplista lekérése az adatbázisból
 */
require_once __DIR__ . '/init.php'; 

// JSON válasz fejléc
header('Content-Type: application/json; charset=utf-8');

// A legjobb 100 eredmény lekérése pontszám szerint csökkenő sorrendben
$query = "SELECT nickname, record, time, rank, avatar, date 
          FROM tetris_scores 
          ORDER BY record DESC 
          LIMIT 100";

$result = mysqli_query($conn, $query);

$scores = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Típuskonverzió, hogy a JavaScript számként kapja meg a pontot
        $row['record'] = (int)$row['record'];
        $scores[] = $row;
    }
    
    // Siker esetén kiküldjük a listát (üres lista is érvényes válasz)
    echo json_encode($scores);
} else {
    // Adatbázis hiba esetén hibaüzenetet küldünk, hogy a JS ne omoljon össze
    http_response_code(500);
    echo json_encode([
        "error" => "Nem sikerült betölteni a toplistát: " . mysqli_error($conn)
    ]);
}
?>