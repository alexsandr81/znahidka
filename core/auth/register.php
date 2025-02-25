<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Ошибка: неверный запрос!";
    header("Location: /znahidka/?page=register");
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');
$role = 'user'; // Роль по умолчанию

// Проверка на пустые поля
if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
    $_SESSION['message'] = "Заполните все поля!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Проверка совпадения паролей
if ($password !== $confirm_password) {
    $_SESSION['message'] = "Пароли не совпадают!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Проверка существования пользователя
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['message'] = "Этот email уже используется!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Хешируем пароль и создаем пользователя
$hashed_password = hash('sha256', $password);
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $hashed_password, $role]);

$_SESSION['message'] = "Регистрация успешна! Войдите в аккаунт.";
header("Location: /znahidka/?page=login");
exit;
