<?php
require 'includes/auth_check.php';
require 'includes/db_fitness.php';

// Проверяем, передан ли id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Неверный ID тренировки.');
}

$workout_id = (int)$_GET['id'];

// Проверяем, существует ли тренировка и принадлежит ли пользователю
$stmt = $pdo_fitness->prepare("SELECT * FROM workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $_SESSION['user_id']]);
$workout = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$workout) {
    die('Тренировка не найдена или доступ запрещён.');
}

// Удаляем фото, если есть
if ($workout['photo'] && file_exists('uploads/' . $workout['photo'])) {
    unlink('uploads/' . $workout['photo']);
}

// Удаляем тренировку из БД
$stmt = $pdo_fitness->prepare("DELETE FROM workouts WHERE id = ? AND user_id = ?");
$stmt->execute([$workout_id, $_SESSION['user_id']]);

// Перенаправляем обратно на dashboard
header('Location: dashboard.php');
exit();
?>