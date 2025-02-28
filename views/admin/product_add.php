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
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $size = $_POST['size'] ?? '';
    $material = $_POST['material'] ?? '';
    $sku = $_POST['sku'] ?? '';

    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/";
    $image_urls = [];

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $file_name = uniqid() . '.' . pathinfo($_FILES['images']['name'][$key], PATHINFO_EXTENSION);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($tmp_name, $file_path)) {
                $image_urls[] = $file_name; // Сохраняем только имя файла!
            }
        }
    }

    $images_json = json_encode($image_urls, JSON_UNESCAPED_UNICODE);
    $stmt = $pdo->prepare("INSERT INTO products (title, description, price, size, material, sku, images) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $price, $size, $material, $sku, $images_json]);

    header("Location: /znahidka/?page=products");
    exit;
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

        <label>Фото (можно загрузить несколько):</label>
        <input type="file" name="images[]" accept="image/*" multiple>

        <button type="submit">Добавить</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
