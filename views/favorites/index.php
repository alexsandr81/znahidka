<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$favorites = $_SESSION['favorites'] ?? [];

if (empty($favorites)) {
    echo "<div class='container'><h2>Избранное пусто</h2></div>";
    require_once 'templates/footer.php';
    exit;
}

// Получаем список товаров из базы
$placeholders = implode(',', array_fill(0, count($favorites), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute(array_keys($favorites));
$products = $stmt->fetchAll();
?>

<div class="container">
    <h2>Избранные товары ❤️</h2>
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="/znahidka/img/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                <h4><?= htmlspecialchars($product['title']) ?></h4>
                <p>Цена: <?= htmlspecialchars($product['price']) ?> грн</p>
                <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">Подробнее</a>
                <form method="post" action="/znahidka/core/favorites/toggle_favorite.php">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="favorite-btn">💔 Убрать из избранного</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>
