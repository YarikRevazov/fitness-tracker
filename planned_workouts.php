<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

// Загружаем планы пользователя
$stmt = $pdo_schedule->prepare("SELECT * FROM planned_workouts WHERE user_id = ? ORDER BY date ASC");
$stmt->execute([$_SESSION['user_id']]);
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<h2>Мои планы тренировок</h2>
<p><a href="add_planned_workout.php">➕ Добавить план тренировки</a> | <a href="dashboard.php">⬅️ Назад в личный кабинет</a></p>

<?php if ($plans): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Дата</th>
            <th>Тип тренировки</th>
            <th>Заметки</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($plans as $plan): ?>
            <tr>
                <td><?php echo htmlspecialchars($plan['date']); ?></td>
                <td><?php echo htmlspecialchars($plan['workout_type']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($plan['notes'])); ?></td>
                <td>
                    <a href="view_planned_exercises.php?plan_id=<?php echo $plan['id']; ?>">👀 Упражнения</a> |
                    <a href="edit_planned_workout.php?id=<?php echo $plan['id']; ?>">✏️ Редактировать</a> |
                   <?php if ($plan['date'] <= date('Y-m-d')): ?>
    <a href="mark_as_done.php?id=<?php echo $plan['id']; ?>" onclick="return confirm('Отметить эту тренировку как выполненную?');">✅ Выполнено</a>
<?php endif; ?>
                    <a href="delete_planned_workout.php?id=<?php echo $plan['id']; ?>" onclick="return confirm('Точно удалить этот план?');">🗑 Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Пока нет запланированных тренировок.</p>
<?php endif; ?>
</body>
</html>