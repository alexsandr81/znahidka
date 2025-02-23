<?php
session_start();
require_once '../../core/database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "❌ Ошибка: некорректный запрос!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

$order_id = $_POST['order_id'] ?? null;
$new_status = $_POST['status'] ?? null;

if (!$order_id || !$new_status) {
    $_SESSION['message'] = "❌ Ошибка: отсутствуют данные!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Проверяем, существует ли заказ
$stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['message'] = "❌ Ошибка: заказ не найден!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Обновляем статус заказа
$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
if ($stmt->execute([$new_status, $order_id])) {
    $_SESSION['message'] = "✅ Статус заказа #$order_id обновлён!";
} else {
    $_SESSION['message'] = "❌ Ошибка при обновлении статуса!";
}

header("Location: /znahidka/?page=admin_orders");
exit;
