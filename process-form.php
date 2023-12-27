<?php
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $to = "plattejason@gmail.com"; 
    $subject = "New Message from Portfolio";
    $message = $_POST["message"];
    $headers = "From: " . $_POST["email"];

    if (mail($to, $subject, $message, $headers)) {
        $success = true;
    }
}
?>