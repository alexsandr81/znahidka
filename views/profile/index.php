<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Пожалуйста, войдите в аккаунт!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="container">
    <h2>Личный кабинет</h2>
    <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

    <h3>Мои заказы</h3>
    <table class="orders-table">
        <thead>
            <tr>
                <th>ID заказа</th>
                <th>Сумма</th>
                <th>Статус</th>
                <th>Дата</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
            $stmt->execute([$user_id]);
            while ($order = $stmt->fetch()):
            ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= $order['total_price'] ?> грн</td>
                    <td><?= ucfirst($order['status']) ?></td>
                    <td><?= $order['created_at'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>
