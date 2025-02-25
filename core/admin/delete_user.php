<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../core/database/db.php';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=home");
    exit;
}

$user_id = $_GET['id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "Ошибка: пользователь не найден!";
    header("Location: /znahidka/?page=admin_users");
    exit;
}

// Запрещаем удаление самого себя
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['message'] = "Ошибка: нельзя удалить самого себя!";
    header("Location: /znahidka/?page=admin_users");
    exit;
}

// Удаляем пользователя
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$user_id]);

$_SESSION['message'] = "✅ Пользователь удалён!";
header("Location: /znahidka/?page=admin_users");
exit;
