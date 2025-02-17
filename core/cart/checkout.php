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
$order_items = [];

foreach ($_SESSION['cart'] as $id => $quantity) {
    $stmt = $pdo->prepare("SELECT title, price FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product && isset($product['price'])) {
        $price = (float) $product['price']; // Приводим к числу
        $quantity = (int) $quantity; // Приводим количество к числу
        $total_price += $price * $quantity;

        // Добавляем в массив товаров для заказа
        $order_items[] = [
            'product_id' => $id,
            'title' => $product['title'],
            'quantity' => $quantity,
            'price' => $price
        ];
    }
}

if ($total_price == 0) {
    $_SESSION['message'] = "Ошибка: невозможно оформить заказ с нулевой суммой!";
    header("Location: /znahidka/?page=cart");
    exit;
}

try {
    // Вставляем заказ в `orders`
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
    $stmt->execute([$user_id, $total_price]);

    $order_id = $pdo->lastInsertId(); // Получаем ID заказа

    // Вставляем товары в `order_items`
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($order_items as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }

    // Очищаем корзину
    $_SESSION['cart'] = []; 

    $_SESSION['message'] = "Заказ #$order_id успешно оформлен! 🎉";
    header("Location: /znahidka/?page=cart");
    exit;
} catch (PDOException $e) {
    $_SESSION['message'] = "Ошибка при создании заказа: " . $e->getMessage();
    header("Location: /znahidka/?page=cart");
    exit;
}
