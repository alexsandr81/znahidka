<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$product_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "<div class='container'><h2>Товар не найден</h2></div>";
    require_once 'templates/footer.php';
    exit;
}

// Проверяем, есть ли товар в избранном
$favorites = $_SESSION['favorites'] ?? [];
$is_favorite = isset($favorites[$product_id]);
?>

<div class="container">
    <h2><?= htmlspecialchars($product['title']) ?></h2>
    <div class="product-details">
        <img src="/znahidka/img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
        <div class="product-info">
            <p><strong>Цена:</strong> <?= htmlspecialchars($product['price']) ?> грн</p>
            <p><strong>Описание:</strong> <?= nl2br(htmlspecialchars($product['description'])) ?></p>
            <p><strong>Артикул:</strong> <?= htmlspecialchars($product['sku']) ?></p>

            <!-- Кнопка "Добавить в избранное" -->
            <form method="post" action="/znahidka/core/favorites/toggle_favorite.php">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit" class="favorite-btn">
                    <?= $is_favorite ? "💔 Убрать из избранного" : "❤️ Добавить в избранное" ?>
                </button>
            </form>

            <!-- Кнопка "Добавить в корзину" -->
            <form method="post" action="/znahidka/core/cart/add_to_cart.php">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <button type="submit">Добавить в корзину</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
