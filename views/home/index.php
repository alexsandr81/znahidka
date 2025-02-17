<?php require_once 'templates/header.php'; ?>

<div class="container">
    <h2>Добро пожаловать в интернет-магазин "ЗНАХІДКА"!</h2>
    <p>Лучшие товары по отличным ценам.</p>

    <h3>Популярные товары</h3>
    <div class="products">
        <?php
        require_once 'core/database/db.php';
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 4");
        while ($product = $stmt->fetch()):
        ?>
            <div class="product">
                <img src="/znahidka/img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                <h4><?= htmlspecialchars($product['title']) ?></h4>
                <p>Цена: <?= htmlspecialchars($product['price']) ?> грн</p>
                <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">Подробнее</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
