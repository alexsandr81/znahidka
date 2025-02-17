<?php
session_start();
require_once '../database/db.php';

if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Ошибка: корзина пуста!";
    header("Location: /znahidka/?page=cart");
    exit;
}

$user_id = $_SESSION['user_id'] ?? 1; // Заменить на реальную авторизацию

$total_price = 0;
foreach ($_SESSION['cart'] as $id => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    $total_price += $product['price'] * $quantity;
}

try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$user_id, $total_price]);
    $_SESSION['cart'] = []; // Очищаем корзину

    $_SESSION['message'] = "Заказ успешно оформлен! 🎉";
    header("Location: /znahidka/?page=cart");
    exit;
} catch (PDOException $e) {
    $_SESSION['message'] = "Ошибка при создании заказа: " . $e->getMessage();
    header("Location: /znahidka/?page=cart");
    exit;
}

