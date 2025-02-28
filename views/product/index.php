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

// ✅ Получаем массив изображений
$images = json_decode($product['images'], true);
if (!is_array($images)) {
    $images = [];
}

$image_dir = "/znahidka/img/products/";
$default_image = "/znahidka/img/no-image.png";
?>

<div class="container">
    <h2><?= htmlspecialchars($product['title']) ?></h2>

    <div class="product-details">
        <div class="product-gallery">
            <?php if (!empty($images)): ?>
                <div class="gallery-slider">
                    <?php foreach ($images as $image): ?>
                        <?php 
                        $image_path = $image_dir . $image;
                        ?>
                        <div class="gallery-slide">
                            <img src="<?= htmlspecialchars($image_path) ?>" alt="Фото <?= htmlspecialchars($product['title']) ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <img src="<?= $default_image ?>" alt="Нет изображения">
            <?php endif; ?>
        </div>

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
