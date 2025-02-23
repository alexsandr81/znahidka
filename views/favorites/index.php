<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "❌ Войдите в систему, чтобы просматривать избранное!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем избранные товары пользователя
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
    <h2>❤️ Избранное</h2>

    <?php if (empty($products)): ?>
        <p>📭 В избранном пока пусто.</p>
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

                    <form method="post" action="/znahidka/core/favorites/toggle_favorite.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="favorite-btn">💔 Убрать</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
