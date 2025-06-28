<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';  // Здесь только schedule_db нужно для статистики

// 1️⃣ Общее количество выполненных тренировок (до сегодняшней даты)
$stmt = $pdo_schedule->prepare("SELECT COUNT(*) FROM completed_workouts WHERE user_id = ? AND date_completed <= CURDATE()");
$stmt->execute([$_SESSION['user_id']]);
$total_workouts = $stmt->fetchColumn();

// 2️⃣ Суммарная длительность выполненных тренировок (до сегодняшней даты)
$stmt = $pdo_schedule->prepare("SELECT SUM(duration) FROM completed_workouts WHERE user_id = ? AND date_completed <= CURDATE()");
$stmt->execute([$_SESSION['user_id']]);
$total_duration = $stmt->fetchColumn();
if (!$total_duration) {
    $total_duration = 0;
}

// 3️⃣ Количество выполненных тренировок по типам (до сегодняшней даты)
$stmt = $pdo_schedule->prepare("SELECT workout_type, COUNT(*) as count FROM completed_workouts WHERE user_id = ? AND date_completed <= CURDATE() GROUP BY workout_type");
$stmt->execute([$_SESSION['user_id']]);
$type_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4️⃣ Общее количество запланированных тренировок
$stmt = $pdo_schedule->prepare("SELECT COUNT(*) FROM planned_workouts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_plans = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<h2>📊 Статистика</h2>
<a href="dashboard.php" style="display: inline-block; margin: 20px; text-decoration: none; color: #aaff00; font-size: 18px;">
    ⬅️ Назад в личный кабинет
</a>

<div style="
    max-width: 600px;
    margin: 30px auto;
    background-color: #2c2c2c;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 0 20px rgba(0,0,0,0.5);
    color: #e0e0e0;
    text-align: center;
    font-family: Arial, sans-serif;
">

    <h2 style="color: #c3ff00; margin-bottom: 30px; font-size: 28px;">
        📊 Статистика
    </h2>

    <div style="margin-bottom: 25px;">
        <p style="color: #aaff00; font-size: 20px; margin: 10px 0;">
            Выполненных тренировок: <span style="color: #ffffff;"><?php echo $total_workouts; ?></span>
        </p>
        <p style="color: #aaff00; font-size: 20px; margin: 10px 0;">
            Суммарная длительность: <span style="color: #ffffff;"><?php echo $total_duration; ?> минут</span>
        </p>
        <p style="color: #aaff00; font-size: 20px; margin: 10px 0;">
            Запланированных тренировок: <span style="color: #ffffff;"><?php echo $total_plans; ?></span>
        </p>
    </div>

    <h3 style="color: #c3ff00; font-size: 22px; margin-top: 30px; margin-bottom: 15px;">Тренировки по типам:</h3>

    <div style="
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: center;
    ">
        <?php foreach ($type_counts as $type): ?>
            <div style="
                background-color: #3a3a3a;
                padding: 10px 20px;
                border-radius: 5px;
                width: 80%;
                max-width: 400px;
                box-shadow: 0 0 10px rgba(0,0,0,0.3);
                display: flex;
                justify-content: space-between;
                font-size: 18px;
            ">
                <span><?php echo htmlspecialchars($type['workout_type']); ?></span>
                <span><?php echo $type['count']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>

</div>
</body>
</html>