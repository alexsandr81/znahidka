<?php
session_start();
require_once 'core/config/config.php';
require_once 'core/database/db.php';

// Разрешённые страницы
$allowed_pages = ['home', 'catalog', 'product', 'cart', 'profile', 'admin', 'products', 'product_add', 'product_edit', 'login', 'favorites'];


// Определяем текущую страницу
$page = $_GET['page'] ?? 'home';

// Если страницы нет в списке разрешённых — загружаем home
if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Определяем путь к файлу
if ($page === 'products') {
    require "views/admin/products.php";
    exit;
} elseif ($page === 'product_add') {
    require "views/admin/product_add.php";
    exit;
} else {
    $page_path = "views/{$page}/index.php";
}

// Проверяем, существует ли файл
if (file_exists($page_path)) {
    require $page_path;
} else {
    require "views/home/index.php"; // Если файла нет, загружаем главную
}

if ($page === 'product_edit') {
    require "views/admin/product_edit.php";
    exit;
}

