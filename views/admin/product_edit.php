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

// ✅ Обработка формы обновления
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $size = trim($_POST['size']);
    $material = trim($_POST['material']);
    $category = trim($_POST['category']);
    $sku = trim($_POST['sku']); // ✅ Поле артикула (SKU)

    // ✅ Удаление выбранных фото
    if (!empty($_POST['delete_images'])) {
        $delete_images = $_POST['delete_images'];
        $images = array_filter($images, function ($image) use ($delete_images, $image_dir) {
            if (in_array($image, $delete_images)) {
                unlink($_SERVER['DOCUMENT_ROOT'] . $image_dir . $image);
                return false; // Удаляем фото из списка
            }
            return true;
        });
    }

    // ✅ Обработка новых загруженных изображений
    if (!empty($_FILES['images']['name'][0])) {
        $uploaded_images = [];
        foreach ($_FILES['images']['tmp_name'] as $index => $tmp_name) {
            if (!empty($_FILES['images']['name'][$index])) {
                $image_name = md5(time() . $_FILES['images']['name'][$index]) . "." . pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION);
                move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $image_dir . $image_name);
                $uploaded_images[] = $image_name;
            }
        }
        $images = array_merge($images, $uploaded_images);
    }

    $images_json = json_encode($images);

    // ✅ Обновляем товар с артикулами и изображениями
    $stmt = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, size=?, material=?, category=?, sku=?, images=? WHERE id=?");
    $stmt->execute([$title, $description, $price, $size, $material, $category, $sku, $images_json, $product_id]);

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
        <input type="text" name="material" value="<?= htmlspecialchars($product['material']) ?>" required>

        <label>Категория:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>

        <label>Артикул (SKU):</label>
        <input type="text" name="sku" value="<?= htmlspecialchars($product['sku']) ?>" required>

        <label>Текущие фото:</label>
        <div class="image-preview">
            <?php if (!empty($images)): ?>
                <?php foreach ($images as $image): ?>
                    <div class="image-container">
                        <img src="<?= $image_dir . htmlspecialchars($image) ?>" width="100">
                        <label>
                            <input type="checkbox" name="delete_images[]" value="<?= htmlspecialchars($image) ?>"> ❌ Удалить
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
