<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

$errors = [];
$success = '';

// Проверка ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID.');
}

$workout_id = (int)$_GET['id'];

// Загружаем данные тренировки
$stmt = $pdo_schedule->prepare("SELECT * FROM completed_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $_SESSION['user_id']]);
$workout = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$workout) {
    die('Тренировка не найдена или доступ запрещён.');
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_completed = $_POST['date_completed'];
    $workout_type = trim($_POST['workout_type']);
    $duration = (int)$_POST['duration'];
    $notes = trim($_POST['notes']);

    if (empty($date_completed) || empty($workout_type)) {
        $errors[] = 'Дата и тип тренировки обязательны.';
    }

    if (empty($errors)) {
        $stmt = $pdo_schedule->prepare("
            UPDATE completed_workouts
            SET date_completed = ?, workout_type = ?, duration = ?, notes = ?
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([
            $date_completed,
            $workout_type,
            $duration,
            $notes,
            $workout_id,
            $_SESSION['user_id']
        ]);

        // ✅ Логируем
        require 'includes/db_fitness.php';
        $log = $pdo_fitness->prepare("
            INSERT INTO logs (user_id, action, details)
            VALUES (?, ?, ?)
        ");
        $log->execute([
            $_SESSION['user_id'],
            'Редактирование выполненной тренировки',
            'ID тренировки: ' . $workout_id . ', Тип: ' . $workout_type
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
    <title>Редактировать выполненную тренировку</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<h2>Редактировать выполненную тренировку</h2>

<?php
if ($errors) {
    echo '<ul style="color:red;">';
    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error) . '</li>';
    }
    echo '</ul>';
}
?>

<form method="post">
    <label>Дата выполнения:</label><br>
    <input type="date" name="date_completed" value="<?php echo htmlspecialchars($workout['date_completed']); ?>" required><br><br>

    <label>Тип тренировки:</label><br>
    <input type="text" name="workout_type" value="<?php echo htmlspecialchars($workout['workout_type']); ?>" required><br><br>

    <label>Длительность (мин):</label><br>
    <input type="number" name="duration" value="<?php echo htmlspecialchars($workout['duration']); ?>"><br><br>

    <label>Заметки:</label><br>
    <textarea name="notes"><?php echo htmlspecialchars($workout['notes']); ?></textarea><br><br>

    <button type="submit">Сохранить изменения</button>
</form>

<p><a href="dashboard.php">⬅️ Назад в личный кабинет</a></p>

</body>
</html>