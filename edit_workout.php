<?php
require 'includes/auth_check.php';
require 'includes/db_fitness.php';

$errors = [];
$success = '';
$workout = null;

// Проверяем, передан ли id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID тренировки.');
}

$workout_id = (int)$_GET['id'];

// Загружаем тренировку
$stmt = $pdo_fitness->prepare("SELECT * FROM workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $_SESSION['user_id']]);
$workout = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$workout) {
    die('Тренировка не найдена или доступ запрещён.');
}

// Обработка обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $workout_type = trim($_POST['workout_type']);
    $duration = (int) $_POST['duration'];
    $notes = trim($_POST['notes']);
    $photo_name = $workout['photo']; // по умолчанию оставляем старое фото

    // Проверка обязательных полей
    if (empty($date) || empty($workout_type) || empty($duration)) {
        $errors[] = 'Дата, тип тренировки и длительность обязательны.';
    }

    // Обработка нового фото (если загружено)
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['photo']['tmp_name'];
        $fileName = $_FILES['photo']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = uniqid() . '.' . $fileExtension;
            $uploadFileDir = 'uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Удаляем старое фото (если есть)
                if ($photo_name && file_exists('uploads/' . $photo_name)) {
                    unlink('uploads/' . $photo_name);
                }
                $photo_name = $newFileName;
            } else {
                $errors[] = 'Ошибка при загрузке файла.';
            }
        } else {
            $errors[] = 'Недопустимый тип файла.';
        }
    }

    // Обновляем запись
    if (empty($errors)) {
        $stmt = $pdo_fitness->prepare("UPDATE workouts SET date = ?, workout_type = ?, duration = ?, notes = ?, photo = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([
            $date,
            $workout_type,
            $duration,
            $notes,
            $photo_name,
            $workout_id,
            $_SESSION['user_id']
        ]);

        header('Location: dashboard.php');
        exit();
    }
}
?>

<h2>Редактировать тренировку</h2>

<?php
if ($errors) {
    echo '<ul style="color:red;">';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<form method="post" enctype="multipart/form-data">
    <label>Дата тренировки:</label><br>
    <input type="date" name="date" value="<?php echo htmlspecialchars($workout['date']); ?>" required max="<?php echo date('Y-m-d'); ?>"><br><br>

    <label>Тип тренировки:</label><br>
    <input type="text" name="workout_type" value="<?php echo htmlspecialchars($workout['workout_type']); ?>" required><br><br>

    <label>Длительность (мин):</label><br>
    <input type="number" name="duration" value="<?php echo htmlspecialchars($workout['duration']); ?>" required><br><br>

    <label>Заметки:</label><br>
    <textarea name="notes"><?php echo htmlspecialchars($workout['notes']); ?></textarea><br><br>

    <label>Текущее фото:</label><br>
    <?php if ($workout['photo']): ?>
        <img src="uploads/<?php echo htmlspecialchars($workout['photo']); ?>" alt="Фото" style="max-width:150px;"><br>
    <?php else: ?>
        Нет фото
    <?php endif; ?><br><br>

    <label>Новое фото (заменить):</label><br>
    <input type="file" name="photo" accept="image/*"><br><br>

    <button type="submit">Сохранить изменения</button>
</form>

<p><a href="dashboard.php">Назад в личный кабинет</a></p>
</body>
</html>