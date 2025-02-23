<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../../core/database/db.php';
require_once '../../templates/header.php';

$user_id = $_SESSION['user_id'] ?? null;
$order_id = $_GET['id'] ?? null;

if (!$user_id || !$order_id) {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/views/orders/my_orders.php");
    exit;
}

// Проверяем, является ли пользователь владельцем заказа
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$order_id, $user_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['message'] = "Ошибка: заказ не найден!";
    header("Location: /znahidka/views/orders/my_orders.php");
    exit;
}

// Получаем товары заказа
$stmt = $pdo->prepare("
    SELECT oi.quantity, oi.price, p.title, p.image 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Детали заказа #<?= htmlspecialchars($order['id']) ?></h2>
    <p><strong>Дата заказа:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    <p><strong>Статус:</strong> <?= htmlspecialchars($order['status'] ?? 'В обработке') ?></p>
    <p><strong>Общая сумма:</strong> <?= number_format($order['total_price'] ?? 0, 2) ?> грн</p>

    <h3>📦 Товары в заказе:</h3>

    <?php if (!empty($items)): ?>
        <table class="order-items-table">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>Товар</th>
                    <th>Количество</th>
                    <th>Цена</th>
                    <th>Сумма</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <img src="/znahidka/img/products/<?= htmlspecialchars($item['image']) ?>" width="80" alt="<?= htmlspecialchars($item['title']) ?>">
                        </td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 2) ?> грн</td>
                        <td><?= number_format($item['price'] * $item['quantity'], 2) ?> грн</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>❌ Нет товаров в этом заказе.</p>
    <?php endif; ?>
</div>

<?php require_once '../../templates/footer.php'; ?>
