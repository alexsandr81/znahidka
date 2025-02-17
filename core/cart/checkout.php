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
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && isset($product['price'])) {
        $price = (float) $product['price']; // Приводим к числу
        $quantity = (int) $quantity; // Приводим количество к числу
        $total_price += $price * $quantity;
    }
}

if ($total_price == 0) {
    $_SESSION['message'] = "Ошибка: невозможно оформить заказ с нулевой суммой!";
    header("Location: /znahidka/?page=cart");
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$user_id, $total_price]);

    // Очищаем корзину после успешного заказа
    $_SESSION['cart'] = []; 

    $_SESSION['message'] = "Заказ успешно оформлен! 🎉";
    header("Location: /znahidka/?page=cart");
    exit;
} catch (PDOException $e) {
    $_SESSION['message'] = "Ошибка при создании заказа: " . $e->getMessage();
    header("Location: /znahidka/?page=cart");
    exit;
}
