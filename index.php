<?php
session_start();
require_once 'core/config/config.php';
require_once 'core/database/db.php';

// Определяем текущую страницу
$page = $_GET['page'] ?? 'home';

// Подключаем нужный шаблон
$allowed_pages = ['home', 'catalog', 'product', 'cart', 'profile', 'admin', 'login', 'favorites'];

if (in_array($page, $allowed_pages)) {
    require "views/{$page}/index.php";
} else {
    require "views/home/index.php"; // 404 пока не делаем
}
?>
