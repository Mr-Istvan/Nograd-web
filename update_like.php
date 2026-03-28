<?php
require_once 'db.php';
header('Content-Type: application/json');

// Feltételezzük, hogy az 'ertekelesek' táblában van egy sor (id=1), ami a statisztikát tárolja
$sql = "UPDATE ertekelesek SET likes = likes + 1 WHERE id = 1";

if (mysqli_query($conn, $sql)) {
    $res = mysqli_query($conn, "SELECT likes FROM ertekelesek WHERE id = 1");
    $row = mysqli_fetch_assoc($res);
    echo json_encode(['success' => true, 'new_likes' => $row['likes']]);
} else {
    echo json_encode(['success' => false]);
}
?>