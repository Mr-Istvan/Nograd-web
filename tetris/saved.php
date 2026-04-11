<?php
/**
 * saved.php - Minden játékot új rekordként ment el
 */
require_once __DIR__ . '/init.php'; 

header('Content-Type: application/json; charset=utf-8');

// Adatok beolvasása a játéktól
$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

if ($data && isset($data['nickname']) && isset($data['record'])) {
    
    // Adatok előkészítése (Prepared Statementhez nem kell escape_string)
    $nickname = $data['nickname'];
    $record   = (int)$data['record'];
    $time     = $data['time'];
    $rank     = $data['rank'];
    $date     = $data['date'];

    // --- CSAK EZ A RÉSZ VÁLTOZOTT: Avatar lekérése az adatbázisból ---
    $avatar = 'default.png'; // Alapértelmezett, ha esetleg nincs neki
    
    $avatar_stmt = mysqli_prepare($conn, "SELECT uavatar FROM felhasznalok WHERE uusername = ? LIMIT 1");
    mysqli_stmt_bind_param($avatar_stmt, "s", $nickname);
    mysqli_stmt_execute($avatar_stmt);
    $avatar_res = mysqli_stmt_get_result($avatar_stmt);
    
    if ($userRow = mysqli_fetch_assoc($avatar_res)) {
        if (!empty($userRow['uavatar'])) {
            $avatar = $userRow['uavatar'];
        }
    }
    mysqli_stmt_close($avatar_stmt);
    // ------------------------------------------------------------------

    // ÚJ LOGIKA: Nincs ellenőrzés, csak simán beszúrjuk az adatbázisba új sorként
    $insert_stmt = mysqli_prepare($conn, "INSERT INTO tetris_scores (nickname, record, time, rank, avatar, date) VALUES (?, ?, ?, ?, ?, ?)");
    
    // s = string, i = integer
    mysqli_stmt_bind_param($insert_stmt, "sissss", $nickname, $record, $time, $rank, $avatar, $date);
    
    if (mysqli_stmt_execute($insert_stmt)) {
        echo json_encode(["status" => "success", "message" => "A pontszámodat rögzítettük!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Szerver hiba: " . mysqli_error($conn)]);
    }
    
    mysqli_stmt_close($insert_stmt);

} else {
    echo json_encode(["status" => "error", "message" => "Hiányzó adatok!"]);
}
?>