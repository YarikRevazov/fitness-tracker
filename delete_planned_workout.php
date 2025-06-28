<?php
require 'includes/auth_check.php';
require 'includes/db_schedule.php';

// Проверяем наличие id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID плана.');
}

$plan_id = (int)$_GET['id'];

// Проверяем, существует ли план и принадлежит ли пользователю
$stmt = $pdo_schedule->prepare("SELECT * FROM planned_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $_SESSION['user_id']]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    die('План тренировки не найден или доступ запрещён.');
}

// Удаляем план из БД
$stmt = $pdo_schedule->prepare("DELETE FROM planned_workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$plan_id, $_SESSION['user_id']]);

// Перенаправляем обратно на список планов
header('Location: planned_workouts.php');
exit();
?>