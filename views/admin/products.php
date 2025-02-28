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

// Загружаем существующие материалы и категории
$materials_stmt = $pdo->query("SELECT DISTINCT material FROM products ORDER BY material");
$materials = $materials_stmt->fetchAll(PDO::FETCH_COLUMN);

$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// Обработка фильтрации
$where = [];
$params = [];

if (!empty($_GET['category'])) {
    $where[] = "category = :category";
    $params['category'] = $_GET['category'];
}

if (!empty($_GET['material'])) {
    $where[] = "material = :material";
    $params['material'] = $_GET['material'];
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";
$stmt = $pdo->prepare("SELECT * FROM products $where_sql ORDER BY created_at DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="container">
    <h2>Управление товарами</h2>

    <a href="/znahidka/?page=product_add" class="add-btn">➕ Добавить товар</a>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <!-- Форма фильтрации -->
    <form method="GET" action="">
        <input type="hidden" name="page" value="products">
        
        <label>Фильтр по категории:</label>
        <select name="category" onchange="this.form.submit()">
            <option value="">Все категории</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($_GET['category'] ?? '') == $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Фильтр по материалу:</label>
        <select name="material" onchange="this.form.submit()">
            <option value="">Все материалы</option>
            <?php foreach ($materials as $mat): ?>
                <option value="<?= htmlspecialchars($mat) ?>" <?= ($_GET['material'] ?? '') == $mat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mat) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

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
                <th>SKU</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
    <?php 
    $images = json_decode($product['images'], true);
    $image_path = (!empty($images) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/" . $images[0])) 
        ? "/znahidka/img/products/" . htmlspecialchars($images[0]) 
        : "/znahidka/img/no-image.png"; 
    ?>
    <img src="<?= $image_path ?>" width="50">
</td>
                    <td><?= htmlspecialchars($product['title']) ?></td>
                    <td><textarea class="description-textarea"><?= htmlspecialchars($product['description']) ?></textarea></td>
                    <td><?= number_format($product['price'], 2) ?> грн</td>
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
