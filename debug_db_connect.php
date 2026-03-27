<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'nograd_db';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset('utf8mb4');
    echo "OK: connected to {$host}/{$db} as {$user}\n";
} catch (mysqli_sql_exception $e) {
    echo "ERR: " . $e->getMessage() . "\n";
}
