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

// Проверяем, пришли ли данные из формы
if (!isset($_POST['order_id'], $_POST['status'])) {
    $_SESSION['message'] = "Ошибка: некорректные данные!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Проверяем, есть ли такая колонка в базе
$columns = $pdo->query("SHOW COLUMNS FROM orders LIKE 'status'")->fetch();
if (!$columns) {
    $_SESSION['message'] = "Ошибка: колонка 'status' отсутствует в базе!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Обновляем статус заказа
$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->execute([$status, $order_id]);

$_SESSION['message'] = "Статус заказа обновлён!";
header("Location: /znahidka/?page=admin_orders");
exit;
