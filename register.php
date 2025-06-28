<?php
require 'includes/db_fitness.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // Проверка
   if (empty($name) || empty($email) || empty($password) || empty($password_confirm)) {
        $errors[] = 'Все поля обязательны для заполнения.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный email.';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Пароли не совпадают.';
    } else {
    // Проверка на существующего пользователя
    $stmt = $pdo_fitness->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'Пользователь с таким email уже зарегистрирован.';
    } else {
        //  ДОБАВЛЯЕМ НОВОГО ПОЛЬЗОВАТЕЛЯ
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo_fitness->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $hashed_password]);

        $success = 'Регистрация успешна! <a href="login.php">Войти</a>';
    }
}
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <h2>Регистрация</h2>
    <?php
    if ($errors) {
        echo '<ul class="error">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }

    if ($success) {
        echo '<p class="success">' . $success . '</p>';
    }
    ?>

    <form method="post" class="fade-in">
        <input type="text" name="name" placeholder="Имя" required><br>
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <input type="password" name="password_confirm" placeholder="Подтвердите пароль" required><br>
        <button type="submit">Зарегистрироваться</button>
    </form>
    <p class="center-box" >Уже есть аккаунт? <a href="login.php">Войти</a></p>

</body>
</html>