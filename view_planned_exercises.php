<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

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

// Загружаем упражнения плана
$stmt = $pdo_schedule->prepare("SELECT * FROM planned_exercises WHERE planned_workout_id = ?");
$stmt->execute([$plan_id]);
$exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<h2>Упражнения к плану: <?php echo htmlspecialchars($plan['workout_type']); ?> (<?php echo $plan['date']; ?>)</h2>
<p><a href="add_planned_exercise.php?plan_id=<?php echo $plan_id; ?>">➕ Добавить упражнение</a> | <a href="planned_workouts.php">⬅️ Назад к планам</a></p>

<?php if ($exercises): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Название</th>
            <th>Подходы</th>
            <th>Повторения</th>
            <th>Вес (кг)</th>
        </tr>
        <?php foreach ($exercises as $ex): ?>
            <tr>
                <td><?php echo htmlspecialchars($ex['exercise_name']); ?></td>
                <td><?php echo $ex['sets']; ?></td>
                <td><?php echo $ex['reps']; ?></td>
                <td><?php echo $ex['weight']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Пока нет упражнений для этого плана.</p>
<?php endif; ?>
</body>
</html>