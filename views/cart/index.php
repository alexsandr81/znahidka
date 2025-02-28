<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$cart = $_SESSION['cart'] ?? [];
$message = $_SESSION['message'] ?? ''; 
unset($_SESSION['message']);
?>

<div class="container">
    <h2>🛒 Корзина</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <p>Корзина пуста</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_price = 0;
                $placeholders = implode(',', array_fill(0, count($cart), '?'));
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
                $stmt->execute(array_keys($cart));
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($products as $product): 
                    $product_id = $product['id'];
                    $quantity = isset($cart[$product_id]) ? (int)$cart[$product_id] : 0;
                    $price = floatval($product['price']);
                    $sum = $price * $quantity;
                    $total_price += $sum;

                    // ✅ Новый способ работы с фото
                    $images = json_decode($product['images'], true);
                    $image_path = !empty($images[0]) ? "/znahidka/img/products/" . htmlspecialchars($images[0]) : "/znahidka/img/no-image.png";
                ?>
                    <tr>
                        <td><img src="<?= $image_path ?>" width="80" alt="<?= htmlspecialchars($product['title']) ?>"></td>
                        <td><?= htmlspecialchars($product['title']) ?></td>
                        <td><?= number_format($price, 2) ?> грн</td>
                        <td><?= (int)$quantity ?></td>
                        <td><?= number_format($sum, 2) ?> грн</td>
                        <td>
                            <a href="/znahidka/core/cart/remove_from_cart.php?id=<?= $product_id ?>" class="remove-btn">❌ Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>💰 Общая сумма: <?= number_format($total_price, 2) ?> грн</h3>

        <form method="post" action="/znahidka/core/cart/checkout.php">
            <button type="submit">✅ Оформить заказ</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
