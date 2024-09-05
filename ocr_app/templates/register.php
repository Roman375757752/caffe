<?php
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
    $email = $_POST["email"];
    $password = $_POST["password"];
    $repeat_password = $_POST["repeat_password"];

    // Проверка совпадения паролей
    if ($password !== $repeat_password) {
        die("Passwords do not match!");
    }

    // Хэширование пароля
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // SQL-запрос для вставки данных
    $sql = "INSERT INTO пользователи (username, email, password) VALUES (?, ?, ?)";

    // Подготовка и выполнение запроса
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        // Перенаправление на index.html после успешной регистрации
        header("Location: index.php");
        exit(); // Завершение выполнения скрипта
    } else {
        echo "Ошибка: " . $stmt->error;
    }

    // Закрытие соединения
    $stmt->close();
}

$conn->close();
?>
