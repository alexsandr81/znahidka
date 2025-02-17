<?php
session_start();

$product_id = $_GET['id'] ?? 0;

if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header("Location: /znahidka/?page=cart");
exit;
