<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../database/db.php';

// Проверяем, отправлен ли POST-запрос
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Ошибка: неверный запрос!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Получаем данные из формы
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

// Проверяем, заполнены ли все поля
if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['message'] = "Заполните все поля!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Проверяем, существует ли уже пользователь с таким email
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    $_SESSION['message'] = "Ошибка: этот email уже зарегистрирован!";
    header("Location: /znahidka/?page=register");
    exit;
}

// Хешируем пароль
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Добавляем пользователя в базу данных
$stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
$stmt->execute([$name, $email, $hashed_password]);

$_SESSION['message'] = "✅ Регистрация успешна! Войдите в систему.";
header("Location: /znahidka/?page=login");
exit;
