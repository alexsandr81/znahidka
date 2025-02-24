<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=admin_users");
    exit;
}

$user_id = $_POST['user_id'] ?? null;
$new_role = $_POST['role'] ?? '';

if ($user_id && in_array($new_role, ['user', 'admin'])) {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $user_id]);

    $_SESSION['message'] = "✅ Роль пользователя изменена!";
} else {
    $_SESSION['message'] = "❌ Ошибка: неверные данные!";
}

header("Location: /znahidka/?page=admin_users");
exit;
