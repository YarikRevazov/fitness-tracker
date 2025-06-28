<?php
try {
    $pdo_fitness = new PDO('mysql:host=localhost;dbname=fitness_tracker;charset=utf8', 'root', 'root');
    $pdo_fitness->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>