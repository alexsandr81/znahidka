<?php
require_once __DIR__ . '/../../core/init.php';

$product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if ($product_id) {
    $stmt = $pdo->prepare("SELECT id, title, price, image FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $_SESSION['cart'][$product_id] = $_SESSION['cart'][$product_id] ?? [
            'title' => $product['title'],
            'price' => $product['price'],
            'image' => $product['image'],
            'quantity' => 0
        ];
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    }
}

header("Location: /znahidka/?page=cart");
exit;
?>
