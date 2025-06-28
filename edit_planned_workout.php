<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

$errors = [];
$success = '';

// Проверка id
if (!isset($_GET['id']) ||  !is_numeric($_GET['id'])) {
    die('Неверный ID плана.');
}

$plan_id = (int)$_GET['id'];

// Загружаем план по ID и user_id
$stmt = $pdo_schedule->prepare("SELECT * FROM planned_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $_SESSION['user_id']]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    die('План тренировки не найден или доступ запрещён.');
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'];
    $workout_type = trim($_POST['workout_type']);
    $notes = trim($_POST['notes']);

    if (empty($date) || empty($workout_type)) {
        $errors[] = 'Дата и тип тренировки обязательны.';
    }

    if (empty($errors)) {
        $stmt = $pdo_schedule->prepare("UPDATE planned_workouts SET date = ?, workout_type = ?, notes = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$date, $workout_type, $notes, $plan_id, $_SESSION['user_id']]);

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
<h2>Редактировать план тренировки</h2>
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
    <input type="date" name="date" value="<?php echo htmlspecialchars($plan['date']); ?>" required><br><br>

    <label>Тип тренировки:</label><br>
    <input type="text" name="workout_type" value="<?php echo htmlspecialchars($plan['workout_type']); ?>" required><br><br>

    <label>Заметки:</label><br>
    <textarea name="notes"><?php echo htmlspecialchars($plan['notes']); ?></textarea><br><br>

    <button type="submit">Сохранить изменения</button>
</form>
</body>
</html>