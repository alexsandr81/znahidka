<?php
session_start();

$product_id = $_POST['product_id'] ?? 0;

if ($product_id > 0) {
    // Инициализируем сессию избранного
    if (!isset($_SESSION['favorites'])) {
        $_SESSION['favorites'] = [];
    }

    // Если товар уже в избранном, удаляем его, иначе добавляем
    if (isset($_SESSION['favorites'][$product_id])) {
        unset($_SESSION['favorites'][$product_id]);
    } else {
        $_SESSION['favorites'][$product_id] = true;
    }
}

// Возвращаем пользователя обратно на страницу товара
header("Location: /znahidka/?page=product&id=$product_id");
exit;
