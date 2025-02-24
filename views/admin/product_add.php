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

// Получаем существующие материалы и категории
$materials_stmt = $pdo->query("SELECT DISTINCT material FROM products ORDER BY material");
$materials = $materials_stmt->fetchAll(PDO::FETCH_COLUMN);

$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $size = trim($_POST['size']);
    $material = trim($_POST['material']) ?: trim($_POST['new_material']);
    $category = trim($_POST['category']) ?: trim($_POST['new_category']);
    $sku = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    // ✅ Проверяем загрузку файла
    $image_name = "no-image.png";
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/";

    if (!empty($_FILES['image']['name'])) {
        $image_name = md5(time() . $_FILES['image']['name']) . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $upload_file = $upload_dir . $image_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        move_uploaded_file($_FILES['image']['tmp_name'], $upload_file);
    }

    if (!empty($title) && !empty($description) && !empty($price) && !empty($size) && !empty($material) && !empty($category)) {
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, size, material, category, sku, image) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $size, $material, $category, $sku, $image_name]);

        $_SESSION['message'] = "✅ Товар добавлен!";
        header("Location: /znahidka/?page=products");
        exit;
    } else {
        $_SESSION['message'] = "Заполните все поля!";
    }
}
?>

<div class="container">
    <h2>Добавить товар</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label>Название:</label>
        <input type="text" name="title" required>

        <label>Описание:</label>
        <textarea name="description" required></textarea>

        <label>Цена:</label>
        <input type="number" name="price" required>

        <label>Размер:</label>
        <input type="text" name="size" required>

        <label>Материал:</label>
        <select name="material">
            <option value="">Выберите материал</option>
            <?php foreach ($materials as $mat): ?>
                <option value="<?= htmlspecialchars($mat) ?>"><?= htmlspecialchars($mat) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_material" placeholder="Или введите новый материал">

        <label>Категория:</label>
        <select name="category">
            <option value="">Выберите категорию</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_category" placeholder="Или введите новую категорию">

        <label>Фото:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">Добавить</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
