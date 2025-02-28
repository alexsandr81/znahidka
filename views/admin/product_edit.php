<?php
require_once 'templates/header.php';
require_once 'core/database/db.php';

$product_id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='container'><h2>❌ Товар не найден</h2></div>";
    require_once 'templates/footer.php';
    exit;
}

// Загружаем изображения
$images = !empty($product['images']) ? json_decode($product['images'], true) : [];
$image_dir = "/znahidka/img/products/";
$default_image = "/znahidka/img/no-image.png";

// ✅ Загружаем существующие материалы и категории
$materials_stmt = $pdo->query("SELECT DISTINCT material FROM products ORDER BY material");
$materials = $materials_stmt->fetchAll(PDO::FETCH_COLUMN);

$categories_stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $categories_stmt->fetchAll(PDO::FETCH_COLUMN);

// ✅ Обработка формы обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $size = trim($_POST['size']);
    $material = trim($_POST['material']) ?: trim($_POST['new_material']);
    $category = trim($_POST['category']) ?: trim($_POST['new_category']);

    // ✅ Обработка новых загруженных изображений
    if (!empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
            if (!empty($_FILES['images']['name'][$index])) {
                $image_name = md5(time() . $_FILES['images']['name'][$index]) . "." . pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $image_dir . $image_name);
                $images[] = $image_name;
            }
        }
    }

    // ✅ Удаление фото (если нужно)
    if (!empty($_POST['delete_images'])) {
        foreach ($_POST['delete_images'] as $delete_image) {
            $image_path = $_SERVER['DOCUMENT_ROOT'] . $image_dir . $delete_image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
            $images = array_values(array_diff($images, [$delete_image]));
        }
    }

    $images_json = json_encode($images);

    // ✅ Обновляем товар в базе данных
    $stmt = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, size=?, material=?, category=?, images=? WHERE id=?");
    $stmt->execute([$title, $description, $price, $size, $material, $category, $images_json, $product_id]);

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
                <option value="<?= htmlspecialchars($mat) ?>" <?= ($product['material'] == $mat) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($mat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_material" placeholder="Или введите новый материал">

        <label>Категория:</label>
        <select name="category">
            <option value="">Выберите категорию</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= ($product['category'] == $cat) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="new_category" placeholder="Или введите новую категорию">

        <label>Текущие фото:</label>
        <div class="image-preview">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image): ?>
                    <div>
                        <img src="<?= $image_dir . htmlspecialchars($image) ?>" width="100">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($image) ?>">
                            ❌ Удалить
                        </label>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <img src="<?= $default_image ?>" width="100">
            <?php endif; ?>
        </div>

        <label>Добавить новые фото:</label>
        <input type="file" name="images[]" accept="image/*" multiple>

        <button type="submit">💾 Сохранить</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
