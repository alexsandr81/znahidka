<?php
session_start();
require_once '../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['message'] = "Ошибка: неверный запрос!";
    header("Location: /znahidka/?page=checkout");
    exit;
}

if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Ошибка: ваша корзина пуста!";
    header("Location: /znahidka/?page=cart");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$email = trim($_POST['email']);
$city = trim($_POST['city']);
$address = trim($_POST['address']);
$comment = trim($_POST['comment']);

$total_price = 0;
$cart = $_SESSION['cart'];

// Получаем товары из корзины
$placeholders = implode(',', array_fill(0, count($cart), '?'));
$stmt = $pdo->prepare("SELECT id, price FROM products WHERE id IN ($placeholders)");
$stmt->execute(array_keys($cart));
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($products as $product) {
    $quantity = (int) $cart[$product['id']];
    $total_price += $product['price'] * $quantity;
}

// Сохраняем заказ в БД (без оплаты)
try {
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, phone, email, city, address, comment, total_price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $name, $phone, $email, $city, $address, $comment, $total_price]);

    $order_id = $pdo->lastInsertId();

    // Сохраняем товары заказа
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($products as $product) {
        $stmt->execute([$order_id, $product['id'], $cart[$product['id']], $product['price']]);
    }

    // Очищаем корзину
    $_SESSION['cart'] = [];
    $_SESSION['message'] = "Заказ #$order_id успешно оформлен!";
    header("Location: /znahidka/?page=cart");
    exit;

} catch (PDOException $e) {
    $_SESSION['message'] = "Ошибка при создании заказа: " . $e->getMessage();
    header("Location: /znahidka/?page=checkout");
    exit;
}
