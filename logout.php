<?php
session_start();
require 'includes/db_fitness.php'; // ✅ подключаем БД для логов

if (isset($_SESSION['user_id'])) {
    // ✅ Логируем выход
    $stmt = $pdo_fitness->prepare("
        INSERT INTO logs (user_id, action, details)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        'Выход из системы',
        'Пользователь вышел из профиля'
    ]);
}

// Завершаем сессию
session_unset();
session_destroy();

// Перенаправляем на index.php
header('Location: index.php');
exit();