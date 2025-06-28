<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Прогноз длительности тренировки</title>
   <link rel="stylesheet" href="/fitness_tracker/assets/css/styles.css">
</head>
<body>

<h2 class="fade-in">🧠 Прогноз длительности тренировки</h2>

<div class="center-box fade-in" style="max-width: 600px; margin: 0 auto;">
    <p>
        На основе ваших прошлых тренировок, модель машинного обучения предскажет
        <strong>примерную длительность</strong> следующей тренировки.
    </p>
    <p style="font-size: 14px; color: #aaa;">
        Это поможет лучше планировать время и избегать перетренированности.
    </p>
</div>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'] ?? '';
    $days_diff = $_POST['days_diff'] ?? '';

    if ($type && is_numeric($days_diff)) {
        $command = escapeshellcmd("python ../ml_analysis/predict.py \"$type\" $days_diff");
        $output = shell_exec($command);
        echo "<p class='success fade-in'><strong>⏱️ Ожидаемая длительность:</strong> $output минут</p>";
    } else {
        echo "<ul class='fade-in'><li>❗️ Введите тип тренировки и количество дней задержки</li></ul>";
    }
}
?>

<form method="POST" class="fade-in">
    <label for="type">🏋️ Тип тренировки:</label>
    <select name="type" id="type">
        <option value="Кардио">Кардио</option>
        <option value="Функциональная">Функциональная</option>
        <option value="Силовая">Силовая</option>
        <option value="Йога">Йога</option>
        <option value="HIIT">HIIT</option>
    </select>

    <label for="days_diff">📅 Задержка между планом и выполнением (в днях):</label>
    <input type="number" name="days_diff" id="days_diff" value="1" min="0">

    <button type="submit" class="btn">🔍 Предсказать</button>
</form>

<div class="center-box fade-in" style="margin-top: 30px;">
    <a href="../dashboard.php" class="btn">← Назад к тренировкам</a>
</div>

<footer>Fitness Tracker ML-модуль © 2025</footer>

</body>
</html>