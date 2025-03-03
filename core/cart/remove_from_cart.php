<?php
session_start();

if (!isset($_GET['id'])) {
    header("Location: /znahidka/views/cart/index.php");
    exit;
}

$product_id = (int)$_GET['id'];

if (isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]); // Полностью удаляем товар из корзины
}

$_SESSION['message'] = "Товар удалён из корзины.";
header("Location: /znahidka/views/cart/index.php");
exit;
