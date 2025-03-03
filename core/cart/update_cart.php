<?php
session_start();
require_once '../database/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['id'] ?? null;
$action = $data['action'] ?? null;

if (!$product_id || !isset($_SESSION['cart'][$product_id])) {
    echo json_encode(["success" => false]);
    exit;
}

if ($action === "increase") {
    $_SESSION['cart'][$product_id]++;
} elseif ($action === "decrease" && $_SESSION['cart'][$product_id] > 1) {
    $_SESSION['cart'][$product_id]--;
}

$quantity = $_SESSION['cart'][$product_id];

// Пересчёт суммы
$stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
$price = floatval($product['price']);
$sum = $price * $quantity;

// Пересчёт общей суммы корзины
$total_price = 0;
foreach ($_SESSION['cart'] as $id => $qty) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_price += floatval($prod['price']) * $qty;
}

echo json_encode([
    "success" => true,
    "quantity" => $quantity,
    "sum" => $sum,
    "total" => $total_price
]);
