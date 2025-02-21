<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../core/database/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем ID заказа
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    $_SESSION['message'] = "Ошибка: заказ не найден!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

// Проверяем, является ли пользователь владельцем заказа или админом
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND (user_id = ? OR ? = 'admin')");
$stmt->execute([$order_id, $_SESSION['user_id'], $_SESSION['role']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    $_SESSION['message'] = "Ошибка: заказ не найден или у вас нет доступа!";
    header("Location: /znahidka/?page=admin_orders");
    exit;
}

require_once __DIR__ . '/../../templates/header.php';
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
    <ul>
        <?php
        $stmt = $pdo->prepare("
            SELECT p.title AS product_name, oi.quantity 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$order_id]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($items):
            foreach ($items as $item):
        ?>
                <li><?= htmlspecialchars($item['product_name']) ?> - <?= intval($item['quantity']) ?> шт.</li>
        <?php
            endforeach;
        else:
            echo "<p>Нет товаров в заказе</p>";
        endif;
        ?>
    </ul>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
