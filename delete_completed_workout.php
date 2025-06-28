<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID.');
}

$workout_id = (int)$_GET['id'];

// Удаляем тренировку
$stmt = $pdo_schedule->prepare("DELETE FROM completed_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $_SESSION['user_id']]);

// Логирование
require 'includes/db_fitness.php';  // подключаем БД логов
$stmt = $pdo_fitness->prepare("
    INSERT INTO logs (user_id, action, details)
    VALUES (?, ?, ?)
");
$stmt->execute([
    $_SESSION['user_id'],
    'Удаление выполненной тренировки',
    'Удалена тренировка ID: ' . $workout_id
]);

// Перенаправление обратно
header('Location: dashboard.php');
exit();
?>