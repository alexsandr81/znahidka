<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "Ошибка: необходимо войти в аккаунт!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Загружаем избранные товары
$favorites = $_SESSION['favorites'] ?? [];

if (!empty($favorites)) {
    $placeholders = implode(',', array_fill(0, count($favorites), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($favorites));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $products = [];
}
?>

<div class="container">
    <h2>❤️ Избранные товары</h2>

    <?php if (empty($products)): ?>
        <p>У вас нет избранных товаров.</p>
    <?php else: ?>
        <div class="products">
            <?php foreach ($products as $product): 
                $images = json_decode($product['images'], true);
                $image_path = !empty($images[0]) ? "/znahidka/img/products/" . htmlspecialchars($images[0]) : "/znahidka/img/no-image.png";
            ?>
                <div class="product">
                    <img src="<?= $image_path ?>" width="200" alt="<?= htmlspecialchars($product['title']) ?>">
                    <h4><?= htmlspecialchars($product['title']) ?></h4>
                    <p>Цена: <?= htmlspecialchars($product['price']) ?> грн</p>
                    <a href="/znahidka/?page=product&id=<?= $product['id'] ?>">Подробнее</a>

                    <!-- Кнопка "Убрать из избранного" -->
                    <form method="post" action="/znahidka/core/favorites/toggle_favorite.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="remove-favorite">💔 Убрать</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
