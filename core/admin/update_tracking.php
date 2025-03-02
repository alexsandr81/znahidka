<?php
session_start();
require_once '../../core/database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "❌ Ошибка: неверный запрос!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

$order_id = $_POST['order_id'] ?? null;
$tracking_number = trim($_POST['tracking_number'] ?? '');

// ✅ Проверяем, переданы ли данные
if (!$order_id || $tracking_number === '') {
    $_SESSION['message'] = "❌ Ошибка: номер отслеживания не может быть пустым!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// ✅ Проверяем, существует ли заказ
$stmt = $pdo->prepare("SELECT id FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    $_SESSION['message'] = "❌ Ошибка: заказ не найден!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// ✅ Обновляем номер отслеживания
$stmt = $pdo->prepare("UPDATE orders SET tracking_number = ? WHERE id = ?");
if ($stmt->execute([$tracking_number, $order_id])) {
    $_SESSION['message'] = "✅ Номер отслеживания для заказа #$order_id обновлён!";
} else {
    $_SESSION['message'] = "❌ Ошибка при обновлении номера!";
}

header("Location: /znahidka/?page=admin_orders");
exit;
