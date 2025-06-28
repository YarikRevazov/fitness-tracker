<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';
require 'includes/db_fitness.php';  // ✅ Подключение для логов

$errors = [];
$success = '';

// Проверка, передан ли id плана
if (!isset($_GET['plan_id']) || !is_numeric($_GET['plan_id'])) {
    die('Неверный ID плана.');
}

$plan_id = (int)$_GET['plan_id'];

// Проверка, существует ли план и принадлежит ли пользователю
$stmt = $pdo_schedule->prepare("SELECT * FROM planned_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $_SESSION['user_id']]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    die('План не найден или доступ запрещён.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercise_name = trim($_POST['exercise_name']);
    $sets = (int)$_POST['sets'];
    $reps = (int)$_POST['reps'];
    $weight = (float)$_POST['weight'];

    if (empty($exercise_name)) {
        $errors[] = 'Название упражнения обязательно.';
    }

    if (empty($errors)) {
        // ✅ Добавляем упражнение
        $stmt = $pdo_schedule->prepare("
            INSERT INTO planned_exercises (planned_workout_id, exercise_name, sets, reps, weight)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$plan_id, $exercise_name, $sets, $reps, $weight]);

        // ✅ Логируем действие
        $stmt = $pdo_fitness->prepare("
            INSERT INTO logs (user_id, action, details)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'],
            'Добавлено упражнение к плану',
            'План ID: ' . $plan_id .
            ', Упражнение: ' . $exercise_name .
            ', Подходы: ' . $sets .
            ', Повторения: ' . $reps .
            ', Вес: ' . $weight
        ]);

        header("Location: view_planned_exercises.php?plan_id=" . $plan_id);
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
<h2>Добавить упражнение к плану: <?php echo htmlspecialchars($plan['workout_type']); ?> (<?php echo $plan['date']; ?>)</h2>
<p><a href="view_planned_exercises.php?plan_id=<?php echo $plan_id; ?>">⬅️ Назад к упражнениям плана</a></p>

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
    <label>Название упражнения:</label><br>
    <input type="text" name="exercise_name" required><br><br>

    <label>Подходы:</label><br>
    <input type="number" name="sets" min="0" value="0"><br><br>

    <label>Повторения:</label><br>
    <input type="number" name="reps" min="0" value="0"><br><br>

    <label>Вес (кг):</label><br>
    <input type="number" step="0.1" name="weight" min="0" value="0"><br><br>

    <button type="submit">Добавить упражнение</button>
</form>
</body>
</html>