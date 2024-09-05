<?php
session_start();

if (!isset($_SESSION['userid'])) {
    // Если пользователь не авторизован, перенаправляем его на страницу входа
    header("Location: login.php");
    exit();
}

// Параметры подключения к базе данных
$servername = "localhost";
$username = "root";
$password = ""; // укажите ваш пароль, если он есть
$dbname = "регистрация в нейронке"; // замените на имя вашей базы данных

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Обработка данных формы
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $price = $_POST["price"];
    $userid = $_SESSION["userid"];
    
    // Обработка файла
    $target_dir = "templates/uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Создание директории, если не существует
    }
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $sql = "INSERT INTO товары (title, description, price, image, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $title, $description, $price, $target_file, $userid);
        if ($stmt->execute()) {
            // Перенаправление на главную страницу после успешной загрузки
            header("Location: index.php");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

$conn->close();
?>
