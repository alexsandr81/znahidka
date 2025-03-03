<?php
session_start(); // ОБЯЗАТЕЛЬНО запускаем сессию

if (!isset($_GET['id'])) {
    $_SESSION['message'] = "Ошибка: товар не найден.";
    header("Location: /znahidka/views/cart/index.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Если корзина не создана, создаём её
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Увеличиваем количество товара в корзине
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1; // Добавляем 1 шт., если товара нет
}

$_SESSION['message'] = "Товар добавлен в корзину.";
header("Location: /znahidka/views/cart/index.php");
exit;
