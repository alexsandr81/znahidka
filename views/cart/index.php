<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$cart = $_SESSION['cart'] ?? [];
$message = $_SESSION['message'] ?? ''; 
unset($_SESSION['message']); // Очищаем сообщение после показа
?>

<div class="container">
    <h2>Корзина</h2>

    <?php if (!empty($message)): ?>
        <div class="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (empty($cart)): ?>
        <p>Корзина пуста</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Действие</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $placeholders = implode(',', array_fill(0, count($cart), '?'));
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
                $stmt->execute(array_keys($cart));
                $products = $stmt->fetchAll();
                $total_price = 0;

                foreach ($products as $product): 
                    $quantity = $cart[$product['id']];
                    $sum = $product['price'] * $quantity;
                    $total_price += $sum;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($product['title']) ?></td>
                        <td><?= htmlspecialchars($product['price']) ?> грн</td>
                        <td><?= $quantity ?></td>
                        <td><?= $sum ?> грн</td>
                        <td>
                            <a href="/znahidka/core/cart/remove_from_cart.php?id=<?= $product['id'] ?>" class="remove-btn">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Общая сумма: <?= $total_price ?> грн</h3>

        <form method="post" action="/znahidka/core/cart/checkout.php">
            <button type="submit">Оформить заказ</button>
        </form>
    <?php endif; ?>
</div>

<?php require_once 'templates/footer.php'; ?>
