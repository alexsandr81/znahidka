<?php
session_start();

$product_id = $_POST['product_id'] ?? 0;

if ($product_id > 0) {
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
}

header("Location: /znahidka/?page=cart");
exit;
