<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/config.php';
require_once '../database/db.php';

$product_id = $_POST['product_id'] ?? 0;

if ($product_id > 0) {
    // Получаем данные о товаре из базы
    $stmt = $pdo->prepare("SELECT id, title, price, image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        // Если товар уже есть в корзине, увеличиваем количество
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            // Если товара нет, добавляем его в корзину
            $_SESSION['cart'][$product_id] = [
                'title' => $product['title'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => 1
            ];
        }
    }
}

// Возвращаем пользователя обратно в корзину
header("Location: /znahidka/?page=cart");
exit;
