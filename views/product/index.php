<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$product_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='container'><h2>❌ Товар не найден</h2></div>";
    require_once 'templates/footer.php';
    exit;
}

// Проверяем, есть ли товар в избранном
$favorites = $_SESSION['favorites'] ?? [];
$is_favorite = in_array($product_id, $favorites);
?>

<div class="container">
    <h2><?= htmlspecialchars($product['title']) ?></h2>
    <div class="product-details">
        <?php
        // ✅ ПРАВИЛЬНЫЙ ПУТЬ К ФОТО
        $image_path = "/znahidka/img/products/" . htmlspecialchars($product['image']);
        $full_image_path = $_SERVER['DOCUMENT_ROOT'] . $image_path;

        if (!empty($product['image']) && file_exists($full_image_path)): ?>
            <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($product['title']) ?>">
        <?php else: ?>
            <img src="/znahidka/img/no-image.png" alt="Нет изображения">
        <?php endif; ?>

        <div class="product-info">
            <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Размер:</strong> <?= htmlspecialchars($product['size']) ?></p>
            <p><strong>Материал:</strong> <?= htmlspecialchars($product['material']) ?></p>
            <p><strong>Артикул:</strong> <?= htmlspecialchars($product['sku']) ?></p>
            <p><strong>Цена:</strong> <?= htmlspecialchars($product['price']) ?> грн</p>

            <!-- Кнопка "Добавить в избранное" -->
            <form method="post" action="/znahidka/core/favorites/toggle_favorite.php">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <button type="submit" class="favorite-btn">
                    <?= $is_favorite ? "💔 Убрать из избранного" : "❤️ Добавить в избранное" ?>
                </button>
            </form>

            <!-- Кнопка "Добавить в корзину" -->
            <form method="post" action="/znahidka/core/cart/add_to_cart.php">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <button type="submit">🛒 Добавить в корзину</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
