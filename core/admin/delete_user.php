<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=admin_users");
    exit;
}

$user_id = $_GET['id'] ?? null;

if ($user_id && $user_id != $_SESSION['user_id']) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);

    $_SESSION['message'] = "✅ Пользователь удалён!";
} else {
    $_SESSION['message'] = "❌ Ошибка: нельзя удалить самого себя!";
}

header("Location: /znahidka/?page=admin_users");
exit;
