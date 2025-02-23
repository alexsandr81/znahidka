<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'templates/header.php';
require_once 'core/database/db.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "❌ Войдите в систему!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем, является ли пользователь администратором
$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    $_SESSION['message'] = "❌ У вас нет прав!";
    header("Location: /znahidka/?page=home");
    exit;
}

// Получаем ID товара
$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    $_SESSION['message'] = "❌ Товар не найден!";
    header("Location: /znahidka/?page=products");
    exit;
}

// Загружаем данные товара
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $_SESSION['message'] = "❌ Товар не найден!";
    header("Location: /znahidka/?page=products");
    exit;
}

// Обновление товара
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $size = trim($_POST['size']);
    $material = trim($_POST['material']);
    $category = trim($_POST['category']);
    $sku = trim($_POST['sku']);

    $image_name = $product['image']; // Оставляем старое изображение, если новое не загружено
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/";

    // Проверяем загрузку нового изображения
    if (!empty($_FILES['image']['name'])) {
        $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array(strtolower($image_extension), $allowed_extensions)) {
            $new_image_name = md5(time() . $_FILES['image']['name']) . "." . $image_extension;
            $upload_file = $upload_dir . $new_image_name;

            // Проверяем, существует ли папка img/products
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Перемещаем файл
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                // Удаляем старое изображение (если оно не дефолтное)
                if ($product['image'] !== "no-image.png" && file_exists($upload_dir . $product['image'])) {
                    unlink($upload_dir . $product['image']);
                }

                $image_name = $new_image_name;
            } else {
                $_SESSION['message'] = "❌ Ошибка загрузки файла!";
                header("Location: /znahidka/?page=product_edit&id=$product_id");
                exit;
            }
        } else {
            $_SESSION['message'] = "❌ Недопустимый формат изображения!";
            header("Location: /znahidka/?page=product_edit&id=$product_id");
            exit;
        }
    }

    // Обновляем данные товара в базе
    if (!empty($title) && !empty($description) && !empty($price) && !empty($size) && !empty($material) && !empty($category) && !empty($sku)) {
        $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, price = ?, size = ?, material = ?, category = ?, sku = ?, image = ? WHERE id = ?");
        $stmt->execute([$title, $description, $price, $size, $material, $category, $sku, $image_name, $product_id]);

        $_SESSION['message'] = "✅ Товар обновлён!";
        header("Location: /znahidka/?page=products");
        exit;
    } else {
        $_SESSION['message'] = "❌ Заполните все поля!";
    }
}
?>

<div class="container">
    <h2>✏️ Редактировать товар</h2>

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

        <label>Фото:</label>
        <?php if (!empty($product['image']) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/" . $product['image'])): ?>
            <div>
                <img src="/znahidka/img/products/<?= htmlspecialchars($product['image']) ?>" width="150">
                <p>Текущее изображение</p>
            </div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*">

        <button type="submit">💾 Сохранить изменения</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
