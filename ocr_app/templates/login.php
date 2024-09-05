<?php
session_start();

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
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // SQL-запрос для проверки данных
    $sql = "SELECT id, password FROM пользователи WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Установка сессионных переменных
            $_SESSION["userid"] = $id;
            $_SESSION["username"] = $username;  

            // Перенаправление на index.php с параметрами для приветственного сообщения
            header("Location: index.php?welcome=1&username=" . urlencode($username));
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that username.";
    }

    $stmt->close();
}

$conn->close();
?>
