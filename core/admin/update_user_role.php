<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../core/database/db.php';

// Проверяем, является ли пользователь админом
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=home");
    exit;
}

// Проверяем входные данные
$user_id = $_POST['user_id'] ?? null;
$new_role = $_POST['role'] ?? null;

if (!$user_id || !$new_role || !in_array($new_role, ['user', 'admin'])) {
    $_SESSION['message'] = "Ошибка: неверные данные!";
    header("Location: /znahidka/?page=admin_users");
    exit;
}

// Запрещаем изменять свою роль
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['message'] = "Ошибка: нельзя изменить свою роль!";
    header("Location: /znahidka/?page=admin_users");
    exit;
}

// Обновляем роль пользователя
$stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->execute([$new_role, $user_id]);

$_SESSION['message'] = "✅ Роль пользователя обновлена!";
header("Location: /znahidka/?page=admin_users");
exit;
