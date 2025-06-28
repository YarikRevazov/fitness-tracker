<?php
require 'includes/auth_check.php';
require 'includes/db_fitness.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $workout_type = trim($_POST['workout_type']);
    $duration = (int) $_POST['duration'];
    $notes = trim($_POST['notes']);
    $photo_name = null;

    // Проверка обязательных полей
    if (empty($date) || empty($workout_type) || empty($duration)) {
        $errors[] = 'Дата, тип тренировки и длительность обязательны.';
    }

    // Обработка фото (если загружено)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileSize = $_FILES['photo']['size'];
        $fileType = $_FILES['photo']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $photo_name = $newFileName;
            } else {
                $errors[] = 'Ошибка при загрузке файла.';
            }
        } else {
            $errors[] = 'Недопустимый тип файла. Разрешены: ' . implode(', ', $allowedfileExtensions);
        }
    }

    // Если всё хорошо - добавляем в БД
    if (empty($errors)) {
        $stmt = $pdo_fitness->prepare("INSERT INTO workouts (user_id, date, workout_type, duration, notes, photo) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $date,
            $workout_type,
            $duration,
            $notes,
            $photo_name
        ]);
         header('Location: dashboard.php');
    exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<h2>Добавить тренировку</h2>

<?php
if ($errors) {
    echo '<ul style="color:red;">';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
}

if ($success) {
    echo '<p style="color:green;">' . htmlspecialchars($success) . '</p>';
}
?>

<form method="post" enctype="multipart/form-data">
    <label>Дата тренировки:</label><br>
    <input type="date" name="date" required max="<?php echo date('Y-m-d'); ?>"><br><br>

    <label>Тип тренировки:</label><br>
    <input type="text" name="workout_type" placeholder="Кардио / Силовая и т.д." required><br><br>

    <label>Длительность (мин):</label><br>
    <input type="number" name="duration" required><br><br>

    <label>Заметки:</label><br>
    <textarea name="notes"></textarea><br><br>

    <label>Фото (необязательно):</label><br>
    <input type="file" name="photo" accept="image/*"><br><br>

    <button type="submit">Добавить тренировку</button>
</form>

<p><a href="dashboard.php" style="text-align: center; margin: 20px 0; font-size: 18px;">Назад в личный кабинет</a></p>

</body>
</html>