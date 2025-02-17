<?php
session_start();
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="/znahidka/?page=profile">Личный кабинет</a></li>
                        <li><a href="/znahidka/?page=favorites">❤️ Избранное</a></li>

                        <li><a href="/znahidka/core/auth/logout.php">Выход</a></li>
                    <?php else: ?>
                        <?php if (isset($_SESSION['user_id'])): ?>
    <li><a href="/znahidka/?page=profile">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a></li>
    <li><a href="/znahidka/core/auth/logout.php">Выход</a></li>
<?php else: ?>
    <li><a href="/znahidka/?page=login">Вход</a></li>
<?php endif; ?>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main>
