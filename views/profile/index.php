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
    <h2>👤 Личный кабинет</h2>
    <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>

    <h3>⚡ Полезные ссылки:</h3>
    <ul>
        <li><a href="/znahidka/views/orders/my_orders.php">📦 Мои заказы</a></li>
        <li><a href="/znahidka/core/auth/logout.php">🚪 Выйти</a></li>
    </ul>
</div>

<?php require_once 'templates/footer.php'; ?>
