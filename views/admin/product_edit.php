<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Войдите в систему!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем, является ли пользователь администратором
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    $_SESSION['message'] = "У вас нет прав!";
    header("Location: /znahidka/?page=home");
    exit;
}

// Получаем ID товара
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    $_SESSION['message'] = "Ошибка: товар не найден!";
    header("Location: /znahidka/?page=products");
    exit;
}

// Загружаем данные товара
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = "Ошибка: товар не найден!";
    header("Location: /znahidka/?page=products");
    exit;
}

// Получаем существующие материалы и категории
$materials_stmt = $pdo->query("SELECT DISTINCT material FROM products ORDER BY material");
$materials = $materials_stmt->fetchAll(PDO::FETCH_COLUMN);

$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $size = trim($_POST['size']);
    $material = trim($_POST['material']) ?: trim($_POST['new_material']);
    $category = trim($_POST['category']) ?: trim($_POST['new_category']);
    $sku = trim($_POST['sku']);
    
    // ✅ Проверяем, был ли загружен новый файл
    $image_name = $product['image']; // Оставляем старое изображение по умолчанию
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/";

    if (!empty($_FILES['image']['name'])) {
        $image_name = md5(time() . $_FILES['image']['name']) . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $upload_file = $upload_dir . $image_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
    }

    // ✅ Обновляем товар в базе
    $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, size = ?, material = ?, category = ?, sku = ?, image = ? WHERE id = ?");
    $stmt->execute([$title, $description, $price, $size, $material, $category, $sku, $image_name, $product_id]);

    $_SESSION['message'] = "✅ Товар обновлён!";
    header("Location: /znahidka/?page=products");
    exit;
}
?>

<div class="container">
    <h2>Редактировать товар</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Название:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>

        <label>Описание:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label>Цена:</label>
        <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label>Размер:</label>
        <input type="text" name="size" value="<?= htmlspecialchars($product['size']) ?>" required>

        <label>Материал:</label>
        <select name="material">
            <option value="">Выберите материал</option>
            <?php foreach ($materials as $mat): ?>
                <option value="<?= htmlspecialchars($mat) ?>" <?= ($product['material'] === $mat) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_material" placeholder="Или введите новый материал">

        <label>Категория:</label>
        <select name="category">
            <option value="">Выберите категорию</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($product['category'] === $cat) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_category" placeholder="Или введите новую категорию">

        <label>Артикул (SKU):</label>
        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required>

        <label>Текущее фото:</label><br>
        <img src="/znahidka/img/products/<?= htmlspecialchars($product['image']) ?>" width="150"><br>

        <label>Новое фото (если хотите заменить):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Сохранить</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
