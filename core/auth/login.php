<?php
session_start();
require_once '../database/db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['message'] = "Заполните все поля!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = "Пользователь не найден!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем, совпадает ли пароль
$hashed_password = hash('sha256', $password);
if ($hashed_password !== $user['password']) {
    $_SESSION['message'] = "Неверный пароль!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Авторизация успешна
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];

$_SESSION['message'] = "Добро пожаловать, " . htmlspecialchars($user['name']) . "!";
header("Location: /znahidka/?page=profile"); // Перенаправляем в личный кабинет
exit;
