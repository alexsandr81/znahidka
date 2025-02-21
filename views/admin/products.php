<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Войдите в систему!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем роль пользователя
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Проверяем, является ли пользователь администратором
if (!$user || $user['role'] !== 'admin') {
    $_SESSION['message'] = "У вас нет прав!";
    header("Location: /znahidka/?page=home");
    exit;
}

// Получаем список товаров
$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<div class="container">
    <h2>Управление товарами</h2>
    
    <a href="/znahidka/?page=product_add" class="add-btn">➕ Добавить товар</a>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Размер</th>
                <th>Материал</th>
                <th>Категория</th>
                <th>SKU</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><?= htmlspecialchars($product['title']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= $product['price'] ?> грн</td>
                    <td><?= htmlspecialchars($product['size']) ?></td>
                    <td><?= htmlspecialchars($product['material']) ?></td>
                    <td><?= htmlspecialchars($product['category']) ?></td>
                    <td><?= htmlspecialchars($product['sku']) ?></td>
                    <td>
                        <a href="/znahidka/?page=product_edit&id=<?= $product['id'] ?>">✏️</a>
                        <a href="/znahidka/core/admin/delete_product.php?id=<?= $product['id'] ?>" class="delete-btn">❌</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once 'templates/footer.php'; ?>
