<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

// Проверяем, админ ли пользователь
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Доступ запрещён!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем роль
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($user['role'] !== 'admin') {
    $_SESSION['message'] = "У вас нет прав доступа!";
    header("Location: /znahidka/?page=home");
    exit;
}
?>

<div class="container">
    <h2>Админ-панель</h2>
    <ul>
        <li><a href="/znahidka/?page=products">📦 Управление товарами</a></li>
        <li><a href="/znahidka/?page=admin_orders">📋 Управление заказами</a></li>
        <li><a href="/znahidka/?page=admin_users">👥 Управление пользователями</a></li>
    </ul>
</div>

<?php require_once 'templates/footer.php'; ?>
