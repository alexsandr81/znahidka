<?php
session_start();
require_once '../../core/database/db.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "❌ Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем, что запрос пришел методом POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "❌ Ошибка: некорректный запрос!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

$order_id = $_POST['order_id'] ?? null;
$new_status = $_POST['status'] ?? null;

// Массив допустимых статусов
$allowed_statuses = ['В обработке', 'Отправлен', 'Доставлен', 'Отменён', 'Завершён'];

if (!$order_id || !in_array($new_status, $allowed_statuses)) {
    $_SESSION['message'] = "❌ Ошибка: отсутствуют данные или недопустимый статус!";
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
