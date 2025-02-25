<?php
session_start();
require_once '../database/db.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (!$name || !$email || !$password || !$confirm_password) {
    $_SESSION['message'] = "Заполните все поля!";
    header("Location: /znahidka/?page=register");
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['message'] = "Пароли не совпадают!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Проверка, существует ли пользователь
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['message'] = "Пользователь уже существует!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Хешируем пароль
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Сохраняем пользователя
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
$stmt->execute([$name, $email, $hashed_password]);

$_SESSION['message'] = "Регистрация успешна! Войдите в систему.";
header("Location: /znahidka/?page=login");
exit;
