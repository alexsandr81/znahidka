<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../core/database/db.php';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['message'] = "❌ Ошибка: доступ запрещён!";
    header("Location: /znahidka/?page=login");
    exit;
}

require_once __DIR__ . '/../../templates/header.php';

// Получаем список заказов
$stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>📦 Управление заказами</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Клиент</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>№ Отслеживания</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= htmlspecialchars($order['id'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($order['name'] ?? 'Не указано') ?></td>
                    <td><?= htmlspecialchars($order['phone'] ?? 'Не указано') ?></td>
                    <td><?= htmlspecialchars($order['email'] ?? 'Не указано') ?></td>
                    <td><?= number_format($order['total_price'] ?? 0, 2) ?> грн</td>
                    
                    <!-- Форма изменения статуса заказа -->
                    <td>
                        <form action="/znahidka/core/admin/update_order_status.php" method="POST">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id'] ?? '') ?>">
                            <select name="status" onchange="this.form.submit()">
                                <?php
                                $statuses = ['В обработке', 'Отправлен', 'Доставлен', 'Отменён', 'Завершён'];
                                foreach ($statuses as $status) {
                                    $selected = (!empty($order['status']) && $order['status'] === $status) ? 'selected' : '';
                                    echo "<option value='$status' $selected>$status</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </td>

                    <!-- Форма изменения номера отслеживания -->
                    <td>
                        <form action="/znahidka/core/admin/update_tracking.php" method="POST">
                            <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id'] ?? '') ?>">
                            <input type="text" name="tracking_number" value="<?= htmlspecialchars($order['tracking_number'] ?? '') ?>" placeholder="Введите номер">
                            <button type="submit">💾</button>
                        </form>
                    </td>

                    <td><?= htmlspecialchars($order['created_at'] ?? '—') ?></td>
                    <td>
                        <a href="/znahidka/views/admin/order_details.php?id=<?= htmlspecialchars($order['id'] ?? '') ?>">👁 Просмотр</a>
                        <a href="/znahidka/core/admin/delete_order.php?id=<?= htmlspecialchars($order['id'] ?? '') ?>" class="delete-btn">❌</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
