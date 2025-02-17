<?php
session_start();
require_once '../../core/database/db.php';
require_once '../../templates/header.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "Ошибка: необходимо войти в аккаунт!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем заказы пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Мои заказы</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <p>У вас пока нет заказов.</p>
    <?php else: ?>
        <table class="order-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Дата</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Подробнее</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= $order['created_at'] ?></td>
                        <td><?= number_format($order['total_price'], 2) ?> грн</td>
                        <td><?= htmlspecialchars($order['status'] ?? 'В обработке') ?></td>
                        <td><a href="order_details.php?id=<?= $order['id'] ?>">Посмотреть</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once '../../templates/footer.php'; ?>
