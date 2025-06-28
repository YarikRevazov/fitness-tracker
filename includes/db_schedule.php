<?php
try {
    $pdo_schedule = new PDO('mysql:host=localhost;dbname=schedule_db;charset=utf8', 'root', 'root'); 
    $pdo_schedule->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных (schedule_db): " . $e->getMessage());
}
?>