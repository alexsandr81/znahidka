<?php
require_once '../../templates/header.php';
require_once '../../core/database/db.php';

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    $_SESSION['message'] = "Ошибка: заказ не найден!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Загружаем данные заказа
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['message'] = "Ошибка: заказ не найден!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Загружаем товары в заказе
$stmt = $pdo->prepare("
    SELECT p.id, p.title, p.images, oi.quantity, oi.price 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$order_id]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>📜 Детали заказа #<?= htmlspecialchars($order['id']) ?></h2>
    <p><strong>Имя:</strong> <?= htmlspecialchars($order['name'] ?? 'Не указано') ?></p>
    <p><strong>Телефон:</strong> <?= htmlspecialchars($order['phone'] ?? 'Не указано') ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? 'Не указано') ?></p>
    <p><strong>Адрес:</strong> <?= htmlspecialchars($order['address'] ?? 'Не указано') ?></p>
    <p><strong>Комментарий:</strong> <?= htmlspecialchars($order['comment'] ?? 'Нет комментариев') ?></p>
    <p><strong>Статус:</strong> <?= htmlspecialchars($order['status'] ?? 'Не указан') ?></p>

    <h3>📦 Товары в заказе</h3>
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
            <?php foreach ($items as $item): 
                $images = json_decode($item['images'], true);
                $image_path = !empty($images[0]) ? "/znahidka/img/products/" . htmlspecialchars($images[0]) : "/znahidka/img/no-image.png";
            ?>
                <tr>
                    <td><img src="<?= $image_path ?>" width="80" alt="<?= htmlspecialchars($item['title']) ?>"></td>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2) ?> грн</td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2) ?> грн</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once '../../templates/footer.php'; ?>
