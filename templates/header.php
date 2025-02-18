<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../core/database/db.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?></title>
    <link rel="stylesheet" href="/znahidka/css/style.css">
</head>
<body>
<header>
    <div class="container">
        <h1><a href="/znahidka/">ЗНАХІДКА</a></h1>
        <nav>
            <ul>
                <li><a href="/znahidka/?page=catalog">Каталог</a></li>
                <li><a href="/znahidka/?page=cart">Корзина</a></li>
                <li><a href="/znahidka/?page=favorites">❤️</a></li>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- <li><a href="/znahidka/views/orders/my_orders.php">📦 Мои заказы</a></li> -->
                    <li><a href="/znahidka/?page=profile">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a></li>

                    <?php
                    $stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
                    $stmt->execute([$_SESSION['user_id']]);
                    $user = $stmt->fetch();

                    if ($user && $user['role'] === 'admin'): ?>
                        <li><a href="/znahidka/?page=admin">⚙️ Админ-панель</a></li>
                    <?php endif; ?>

                    <li><a href="/znahidka/core/auth/logout.php">Выход</a></li>
                <?php else: ?>
                    <li><a href="/znahidka/?page=login">Вход</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
<main>
