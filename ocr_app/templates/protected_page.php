<?php
session_start();

// Проверка, авторизован ли пользователь
if (!isset($_SESSION["userid"])) {
    header("Location: login.html");
    exit();
}

echo "Добро пожаловать, " . $_SESSION["username"] . "!";
?>

<!-- Здесь можно добавить содержимое защищенной страницы -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protected Page</title>
</head>
<body>
    <h1>Это защищенная страница</h1>
    <p>Только авторизованные пользователи могут видеть это содержимое.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
