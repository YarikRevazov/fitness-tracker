<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

// Загружаем тренировки пользователя
$stmt = $pdo_schedule->prepare("SELECT * FROM completed_workouts WHERE user_id = ? ORDER BY date_completed DESC");
$stmt->execute([$_SESSION['user_id']]);
$workouts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Получаем частоту и среднюю длительность по типам
$stmtTypes = $pdo_schedule->prepare("
    SELECT workout_type, COUNT(*) AS count, AVG(duration) AS avg_duration
    FROM completed_workouts
    WHERE user_id = ?
    GROUP BY workout_type
    ORDER BY count DESC
");
$stmtTypes->execute([$_SESSION['user_id']]);
$typeStats = $stmtTypes->fetchAll(PDO::FETCH_ASSOC);

$recommendations = [];

$most_common = $typeStats[0]['workout_type'] ?? null;
$least_common = $typeStats[count($typeStats) - 1]['workout_type'] ?? null;

foreach ($typeStats as $row) {
    if ($row['workout_type'] === 'Кардио' && $row['avg_duration'] > 35) {
        $recommendations[] = "🚀 Кардио у вас длится в среднем " . round($row['avg_duration']) . " мин. Хотите сократить? Попробуйте HIIT!";
    }

    if ($row['workout_type'] === 'Функциональная' && $row['count'] < 2) {
        $recommendations[] = "💪 Вы редко выполняете функциональные тренировки — добавьте их для развития координации и силы!";
    }
}

if ($least_common && $least_common !== $most_common) {
    $recommendations[] = "⚖️ Самый редкий тип у вас: <strong>$least_common</strong>. Возможно, стоит попробовать?";
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
<h2>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>
<div class="center-box" style="margin: 20px;">
    <a href="ml_analysis/predict_duration.php" class="btn">📊 Получить прогноз длительности</a>
</div>
<p style="text-align: center; margin: 20px 0; font-size: 18px;">
   <!-- <a href="add_workout.php" style="margin: 0 15px; text-decoration: none; color: #aaff00;">➕ Добавить тренировку</a> | -->
    <a href="add_planned_workout.php" style="margin: 0 15px; text-decoration: none; color: #aaff00;">Добавить план</a> |
    <a href="planned_workouts.php" style="margin: 0 15px; text-decoration: none; color: #aaff00;">Планы тренировок</a> |
    <a href="stats.php" style="margin: 0 15px; text-decoration: none; color: #aaff00;">Статистика</a>
    
</p>
<p>

<h3>Ваши выполненные тренировки:</h3>

<?php if ($workouts): ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>Дата по плану</th>
            <th>Дата выполнения</th>
            <th>Тип</th>
            <th>Длительность (мин)</th>
            <th>Заметки</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($workouts as $workout): ?>
            <tr>
                <td><?php echo htmlspecialchars($workout['date_planned']); ?></td>
                <td><?php echo htmlspecialchars($workout['date_completed']); ?></td>
                <td><?php echo htmlspecialchars($workout['workout_type']); ?></td>
                <td><?php echo htmlspecialchars($workout['duration']); ?> мин</td>
                <td><?php echo nl2br(htmlspecialchars($workout['notes'])); ?></td>
                <td>
                    <!-- Можно добавить редактирование/удаление если нужно -->
                    <a href="edit_completed_workout.php?id=<?php echo $workout['id']; ?>">✏️ Редактировать</a> |
                    <a href="delete_completed_workout.php?id=<?php echo $workout['id']; ?>" onclick="return confirm('Точно удалить эту тренировку?');">🗑 Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Пока нет выполненных тренировок.</p>
    
<?php endif; ?>
<p style="text-align: center; margin: 30px 0;">
    <a href="logout.php" style="
        display: inline-block;
        padding: 10px 20px;
        background-color: #ff4d4d;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    ">🚪 Выйти из профиля</a>
</p>
<?php if (!empty($recommendations)): ?>
    <div class="center-box fade-in" style="margin: 40px auto; max-width: 700px;">
        <h3>🧠 Персональные рекомендации</h3>
        <ul>
            <?php foreach ($recommendations as $rec): ?>
                <li><?= $rec ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
</body>
</html>