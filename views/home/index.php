<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

// Получаем последние добавленные товары
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 8");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>🔥 Новые поступления</h2>

    <?php if (empty($products)): ?>
        <p>📭 Товары не найдены.</p>
    <?php else: ?>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">
                        <?php
                        $image_path = "/znahidka/img/products/" . htmlspecialchars($product['image']);
                        if (!empty($product['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . $image_path)): ?>
                            <img src="<?= $image_path ?>" width="150">
                        <?php else: ?>
                            <img src="/znahidka/img/no-image.png" width="150">
                        <?php endif; ?>
                        
                        <h3><?= htmlspecialchars($product['title']) ?></h3>
                    </a>
                    <p><strong>Цена:</strong> <?= number_format($product['price'], 2) ?> грн</p>

                    <form method="post" action="/znahidka/core/cart/add_to_cart.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit">🛒 В корзину</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
