<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../core/database/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем ID заказа
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    $_SESSION['message'] = "Ошибка: заказ не найден!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Удаляем заказ
$stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
$stmt->execute([$order_id]);

$_SESSION['message'] = "Заказ успешно удалён!";
header("Location: /znahidka/?page=admin_orders");
exit;
