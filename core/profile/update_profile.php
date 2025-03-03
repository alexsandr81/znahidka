<?php
require_once '../database/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Пожалуйста, войдите в аккаунт!";
    header("Location: /znahidka/?page=login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Получаем данные из формы
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';
$confirm_new_password = isset($_POST['confirm_new_password']) ? trim($_POST['confirm_new_password']) : '';
$confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

// Проверяем обязательные поля
if (empty($name) || empty($email) || empty($confirm_password)) {
    $_SESSION['message'] = "Имя, email и текущий пароль обязательны!";
    header("Location: /znahidka/views/profile/edit.php");
    exit;
}

// Проверяем корректность email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['message'] = "Некорректный email!";
    header("Location: /znahidka/views/profile/edit.php");
    exit;
}

// Получаем текущий хеш пароля пользователя
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Проверяем правильность текущего пароля
if (!$user || !password_verify($confirm_password, $user['password'])) {
    $_SESSION['message'] = "Неверный текущий пароль!";
    header("Location: /znahidka/views/profile/edit.php");
    exit;
}

// Если введён новый пароль, проверяем его подтверждение и хешируем
if (!empty($password)) {
    if ($password !== $confirm_new_password) {
        $_SESSION['message'] = "Новые пароли не совпадают!";
        header("Location: /znahidka/views/profile/edit.php");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $hashed_password, $user_id]);
} else {
    // Обновляем без изменения пароля
    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->execute([$name, $email, $phone, $user_id]);
}

$_SESSION['message'] = "Профиль успешно обновлён!";
header("Location: /znahidka/views/profile/index.php");
exit;
