<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $workout_type = trim($_POST['workout_type']);
    $notes = trim($_POST['notes']);

    if (empty($date) || empty($workout_type)) {
        $errors[] = 'Дата и тип тренировки обязательны.';
    }

    if (empty($errors)) {
        $stmt = $pdo_schedule->prepare("INSERT INTO planned_workouts (user_id, date, workout_type, notes) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $date, $workout_type, $notes]);

        header('Location: planned_workouts.php');
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

<h2>Добавить план тренировки</h2>
<p><a href="planned_workouts.php">Назад к планам</a></p>

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
    <label>Дата тренировки:</label><br>
    <input type="date" name="date" required><br><br>

    <label>Тип тренировки:</label><br>
    <input type="text" name="workout_type" placeholder="Кардио / Силовая и т.д." required><br><br>

    <label>Заметки:</label><br>
    <textarea name="notes"></textarea><br><br>

    <button type="submit">Добавить план</button>
</form>
</body>
</html>