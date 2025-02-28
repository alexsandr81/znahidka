<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Войдите в систему!";
    header("Location: /znahidka/?page=login");
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    $_SESSION['message'] = "У вас нет прав!";
    header("Location: /znahidka/?page=home");
    exit;
}

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
                <th>Фото</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th>Размер</th>
                <th>Материал</th>
                <th>Категория</th>
                <th>Артикул (SKU)</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): 
                $images = json_decode($product['images'], true);
                $image_path = !empty($images[0]) ? "/znahidka/img/products/" . htmlspecialchars($images[0]) : "/znahidka/img/no-image.png";
            ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td><img src="<?= $image_path ?>" width="50"></td>
                    <td><?= htmlspecialchars($product['title']) ?></td>
                    <td><textarea class="description-textarea"><?= htmlspecialchars($product['description']) ?></textarea></td>
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
