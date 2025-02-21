<?php
 error_reporting(E_ALL);
 ini_set('display_errors', 1);
 
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'core/config/config.php';
require_once 'core/database/db.php';

// Разрешённые страницы
$allowed_pages = [
    'home', 'catalog', 'product', 'cart', 'profile', 'admin', 
    'products', 'product_add', 'product_edit', 'admin_orders', 
    'login', 'favorites'
];

// Определяем текущую страницу
$page = $_GET['page'] ?? 'home';

// Если страницы нет в списке разрешённых — загружаем home
if (!in_array($page, $allowed_pages)) {
    $page = 'home';
}

// Определяем путь к файлу для страниц
if ($page === 'products') {
    require "views/admin/products.php";
    exit;
} elseif ($page === 'product_add') {
    require "views/admin/product_add.php";
    exit;
} elseif ($page === 'product_edit') {
    require "views/admin/product_edit.php";
    exit;
} elseif ($page === 'admin_orders') { 
    if (file_exists("views/admin/admin_orders.php")) {
        
        require "views/admin/admin_orders.php";
        exit;
    } else {
        $_SESSION['message'] = "Ошибка: страница управления заказами не найдена!";
        header("Location: /znahidka/");
        exit;
    }
}
 else {
    $page_path = "views/{$page}/index.php";

    // Проверяем, существует ли файл
    if (file_exists($page_path)) {
        require $page_path;
        exit;
    } else {
        require "views/home/index.php"; // Если файла нет, загружаем главную
        exit;
    }
}
