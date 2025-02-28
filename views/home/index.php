<?php require_once 'templates/header.php'; ?>
<?php require_once 'core/database/db.php'; ?>

<div class="container">
    <h2>🔥 Новые товары</h2>

    <div class="products">
        <?php
        // Получаем последние добавленные товары
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
        while ($product = $stmt->fetch()):
            $images = json_decode($product['images'], true);
            $image_path = !empty($images[0]) ? "/znahidka/img/products/" . htmlspecialchars($images[0]) : "/znahidka/img/no-image.png";
        ?>
            <div class="product">
                <img src="<?= $image_path ?>" width="200" alt="<?= htmlspecialchars($product['title']) ?>">
                <h4><?= htmlspecialchars($product['title']) ?></h4>
                <p>Цена: <?= htmlspecialchars($product['price']) ?> грн</p>
                <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">Подробнее</a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
