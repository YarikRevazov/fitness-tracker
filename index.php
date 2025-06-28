<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Добро пожаловать в Fitness Tracker</title>
</head>
<body>

    <h1 class="fade-in">🏋️‍♀️ Добро пожаловать в Fitness Tracker! 🏋️‍♂️</h1>
    <p class="center-box">Отслеживайте свои тренировки, планируйте будущее и следите за прогрессом!</p>

    <img src="assets/img/1.jpg" alt="Фитнес" style="display: block; margin: 30px auto; max-width: 80%; border-radius: 15px;">

    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a class="center-box" href="dashboard.php">🏠 Перейти в Личный кабинет</a>
            <a class="center-box" href="logout.php" >🚪 Выйти</a>
        <?php else: ?>
            <a href="login.php" >🔑 Войти</a>
            <a href="register.php" >📝 Зарегистрироваться</a>
        <?php endif; ?>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Fitness Tracker. Все права защищены.
    </footer>

</body>
</html>