<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "project@nogradcsodak.szakdoga.net";
    $subject = "Új üzenet a weboldalról";
    
    // Adatok tisztítása
    $email = filter_var($_POST['visitor_email'], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST['message']);
    
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    if (mail($to, $subject, $message, $headers)) {
        // Siker esetén
        header("Location: index.php?status=success#contact-content");
    } else {
        // Hiba esetén
        header("Location: index.php?status=error#contact-content");
    }
    exit();
}
?>