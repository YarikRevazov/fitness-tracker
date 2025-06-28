<?php
require 'includes/auth_check.php';
require 'includes/db_fitness.php';
require 'includes/db_schedule.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID.');
}

$plan_id = (int)$_GET['id'];

// Получаем данные плана
$stmt = $pdo_schedule->prepare("SELECT * FROM planned_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $_SESSION['user_id']]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    die('План не найден или доступ запрещён.');
}

// Переносим в таблицу workouts
$stmt = $pdo_schedule->prepare("
    INSERT INTO completed_workouts (user_id, date_planned, date_completed, workout_type, notes, duration)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $_SESSION['user_id'],
    $plan['date'],
    date('Y-m-d'),
    $plan['workout_type'],
    $plan['notes'],
    0
]);

// Удаляем из planned_workouts
$stmt = $pdo_schedule->prepare("DELETE FROM planned_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $_SESSION['user_id']]);

// Редирект обратно
header("Location: planned_workouts.php");
exit();