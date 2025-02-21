<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../core/database/db.php';
require_once '../../templates/header.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    $_SESSION['message'] = "Ошибка: Вы не авторизованы!";
    header("Location: /znahidka/auth/login.php"); // Перенаправляем на страницу входа
    exit;
}

// Получаем заказы пользователя
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>🛒 Мои заказы</h2>

    <?php if (!empty($orders)): ?>
        <?php foreach ($orders as $order): ?>
            <div class="order">
                <h3>Заказ #<?= htmlspecialchars($order['id']) ?></h3>
                <p><strong>Дата:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
                <p><strong>Статус:</strong> <?= htmlspecialchars($order['status']) ?></p>
                <p><strong>Сумма:</strong> <?= number_format($order['total_price'], 2) ?> грн</p>
                <a href="order_details.php?id=<?= $order['id'] ?>" class="btn">Подробнее</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>❌ У вас нет заказов.</p>
    <?php endif; ?>
</div>

<?php require_once '../../templates/footer.php'; ?>
