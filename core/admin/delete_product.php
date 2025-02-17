<?php
session_start();
require_once '../database/db.php';

$product_id = $_GET['id'] ?? 0;

// Проверяем права
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'admin') {
    $_SESSION['message'] = "Нет прав!";
    header("Location: /znahidka/?page=home");
    exit;
}

// Удаляем товар
$stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
$stmt->execute([$product_id]);

$_SESSION['message'] = "Товар удалён!";
header("Location: /znahidka/?page=products");
exit;
