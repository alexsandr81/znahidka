<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../core/database/db.php';

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

    <!-- Вывод сообщения об успешном обновлении -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Телефон:</strong> <?= htmlspecialchars($user['phone'] ?? 'Не указан') ?></p>

    
    

    <h3>⚡ Полезные ссылки:</h3>
    <ul>
        <a href="/znahidka/views/profile/edit.php" class="btn">✏ Изменить данные о себе</a>
        <li><a href="/znahidka/views/orders/my_orders.php">📦 Мои заказы</a></li>
        <li><a href="/znahidka/core/auth/logout.php">🚪 Выйти</a></li>
    </ul>
</div>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
