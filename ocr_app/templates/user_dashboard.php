<?php
session_start();

// Параметры подключения к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "регистрация в нейронке";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Получение данных пользователя
if (isset($_SESSION["userid"])) {
    $user_id = $_SESSION["userid"];
    
    $sql = "SELECT username, email FROM пользователи WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    $sql_profile = "SELECT full_name, birthdate, profile_description, avatar FROM user_profiles WHERE user_id=?";
    $stmt_profile = $conn->prepare($sql_profile);
    $stmt_profile->bind_param("i", $user_id);
    $stmt_profile->execute();
    $result_profile = $stmt_profile->get_result();
    $profile = $result_profile->fetch_assoc();
} else {
    // Перенаправление на страницу входа, если пользователь не аутентифицирован
    header("Location: login.php");
    exit();
}

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $birthdate = $_POST['birthdate'];
    $profile_description = $_POST['profile_description'];

    // Проверка, существует ли запись в профиле
    if ($profile) {
        // Обновление данных профиля пользователя
        $sql_update = "UPDATE user_profiles SET full_name=?, birthdate=?, profile_description=? WHERE user_id=?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssi", $full_name, $birthdate, $profile_description, $user_id);
        $stmt_update->execute();
    } else {
        // Вставка новых данных профиля пользователя
        $sql_insert = "INSERT INTO user_profiles (user_id, full_name, birthdate, profile_description) VALUES (?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("isss", $user_id, $full_name, $birthdate, $profile_description);
        $stmt_insert->execute();
    }

    // Обработка загрузки аватара
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        $avatar_path = "uploads/" . basename($_FILES['avatar']['name']);
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_path)) {
            $sql_avatar = "UPDATE user_profiles SET avatar=? WHERE user_id=?";
            $stmt_avatar = $conn->prepare($sql_avatar);
            $stmt_avatar->bind_param("si", $avatar_path, $user_id);
            $stmt_avatar->execute();
        }
    }

    // Обработка сброса пароля
    if (isset($_POST['reset_password'])) {
        // Здесь должна быть логика для сброса пароля и отправки нового пароля пользователю
    }

    // Перезагрузка страницы для обновления данных
    header("Location: user_dashboard.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f6f8;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        label {
            flex: 1;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"],
        textarea {
            flex: 2;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        input[type="file"] {
            padding: 0;
        }
        button {
            padding: 12px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        .avatar {
            max-width: 120px;
            border-radius: 6px;
            margin-bottom: 10px;
        }
        .edit-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .edit-button img {
            width: 24px;
            height: 24px;
        }
        .preview {
            margin-top: 10px;
            max-width: 150px;
            height: auto;
            border-radius: 6px;
        }
        .form-actions {
            text-align: center;
        }
        .form-actions button {
            margin: 0 10px;
        }
    </style>
    <script>
        function toggleEditMode(fieldId, buttonId) {
            var field = document.getElementById(fieldId);
            var button = document.getElementById(buttonId);
            var buttonImage = button.getElementsByTagName('img')[0];
            if (field.disabled) {
                field.disabled = false;
                field.classList.remove('non-editable');
                buttonImage.src = "save-icon.png";
            } else {
                field.disabled = true;
                field.classList.add('non-editable');
                buttonImage.src = "pencil.png";
            }
        }

        function previewImage(event) {
            var file = event.target.files[0];
            var reader = new FileReader();

            reader.onload = function(e) {
                var preview = document.getElementById('image-preview');
                preview.src = e.target.result;
                preview.style.display = 'block';
            };

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>User Dashboard</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled class="non-editable">
                <button type="button" id="edit-username" class="edit-button" onclick="toggleEditMode('username', 'edit-username')">
                    <img src="pencil.png" alt="Edit">
                </button>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="non-editable">
                <button type="button" id="edit-email" class="edit-button" onclick="toggleEditMode('email', 'edit-email')">
                    <img src="pencil.png" alt="Edit">
                </button>
            </div>
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" value="<?php echo htmlspecialchars($profile['full_name'] ?? ''); ?>" disabled class="non-editable">
                <button type="button" id="edit-full_name" class="edit-button" onclick="toggleEditMode('full_name', 'edit-full_name')">
                    <img src="pencil.png" alt="Edit">
                </button>
            </div>
            <div class="form-group">
                <label for="birthdate">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" value="<?php echo htmlspecialchars($profile['birthdate'] ?? ''); ?>" disabled class="non-editable">
                <button type="button" id="edit-birthdate" class="edit-button" onclick="toggleEditMode('birthdate', 'edit-birthdate')">
                    <img src="pencil.png" alt="Edit">
                </button>
            </div>
            <div class="form-group">
                <label for="profile_description">Profile Description</label>
                <textarea name="profile_description" id="profile_description" disabled class="non-editable"><?php echo htmlspecialchars($profile['profile_description'] ?? ''); ?></textarea>
                <button type="button" id="edit-profile_description" class="edit-button" onclick="toggleEditMode('profile_description', 'edit-profile_description')">
                    <img src="pencil.png" alt="Edit">
                </button>
            </div>
            <div class="form-group">
                <label for="avatar">Avatar</label>
                <?php if (!empty($profile['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($profile['avatar']); ?>" alt="Avatar" class="avatar">
                <?php endif; ?>
                <input type="file" name="avatar" id="avatar" onchange="previewImage(event)">
                <img id="image-preview" class="preview" style="display:none;">
            </div>
            <div class="form-actions">
                <button type="submit">Save Changes</button>
                <button type="submit" name="reset_password">Reset Password</button>
            </div>
        </form>
    </div>
</body>
</html>

