<?php
require_once __DIR__ . '/../../core/init.php';
require_once __DIR__ . '/../../templates/header.php';

// Проверяем, админ ли пользователь
if ($_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "Доступ запрещён!";
    header("Location: /znahidka/?page=home");
    exit;
}

$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll();
?>

<div class="container">
    <h2>📦 Управление заказами</h2>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['name']) ?></td>
                    <td><?= htmlspecialchars($order['phone']) ?></td>
                    <td><?= htmlspecialchars($order['email']) ?></td>
                    <td><?= number_format($order['total_price'], 2) ?> грн</td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['created_at']) ?></td>
                    <td>
                        <a href="/znahidka/views/admin/order_details.php?id=<?= htmlspecialchars($order['id']) ?>">👁 Просмотр</a>
                        <a href="/znahidka/core/admin/delete_order.php?id=<?= htmlspecialchars($order['id']) ?>" class="delete-btn">❌</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
