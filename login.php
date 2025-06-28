<?php
require 'includes/db_fitness.php';
session_start();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $errors[] = 'Все поля обязательны для заполнения.';
    } else {
        $stmt = $pdo_fitness->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // ✅ Успешный вход
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name']; 

            // ✅ Логируем вход
            $stmt = $pdo_fitness->prepare("
                INSERT INTO logs (user_id, action, details)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $user['id'],
                'Вход в систему',
                'Пользователь успешно вошёл: ' . $email
            ]);

            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = 'Неверный email или пароль.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <h2>Вход</h2>
    <?php
    if ($errors) {
        echo '<ul class="error">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }
    ?>

    <form method="post" class="fade-in">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <button type="submit">Войти</button>
    </form>
    <p class="center-box" >Нет аккаунта? <a href="register.php">Регистрация</a></p>

</body>
</html>