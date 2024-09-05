<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $data = "Имя: $name\nЭлектронная почта: $email\nСообщение: $message\n\n";

    $file = 'feedback.txt';
    file_put_contents($file, $data, FILE_APPEND | LOCK_EX);

    echo "The message has been sent successfully!";
} else {
    echo "An error occurred when sending the message. Please try again..";
}
?>
