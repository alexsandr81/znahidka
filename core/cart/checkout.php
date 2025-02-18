<?php
session_start();
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../core/database/db.php';

if (empty($_SESSION['cart'])) {
    $_SESSION['message'] = "Ошибка: корзина пуста!";
    header("Location: /znahidka/?page=cart");
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;

// Получаем товары из корзины (защита от пустого массива)
$cart = $_SESSION['cart'] ?? [];
$total_price = 0;
$products = [];

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($cart));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container">
    <h2>🛍 Оформление заказа</h2>

    <form action="/znahidka/core/cart/process_order.php" method="POST">
        <h3>📦 Товары в заказе</h3>
        <ul>
            <?php foreach ($products as $product): 
                $quantity = (int) $cart[$product['id']];
                $sum = $product['price'] * $quantity;
                $total_price += $sum;
            ?>
                <li>
                    <?= htmlspecialchars($product['title']) ?> (<?= $quantity ?> шт.) - <?= number_format($sum, 2) ?> грн
                </li>
            <?php endforeach; ?>
        </ul>

        <h3>👤 Контактные данные</h3>
        <label>Имя:</label>
        <input type="text" name="name" required>

        <label>Телефон:</label>
        <input type="text" name="phone" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <h3>🚚 Адрес доставки</h3>
        <label>Город:</label>
        <input type="text" name="city" required>

        <label>Улица, дом, квартира:</label>
        <input type="text" name="address" required>

        <label>Комментарий к заказу:</label>
        <textarea name="comment"></textarea>

        <h3>💰 Итоговая сумма: <?= number_format($total_price, 2) ?> грн</h3>
        
        <button type="submit">✅ Оформить заказ</button>
    </form>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
