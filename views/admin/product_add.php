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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $size = trim($_POST['size']);
    $material = trim($_POST['material']);
    $category = trim($_POST['category']);
    $sku = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    // ✅ Проверяем загрузку файла
    $image_name = "no-image.png";
    $upload_dir = $_SERVER['DOCUMENT_ROOT'] . "/znahidka/img/products/";

    if (!empty($_FILES['image']['name'])) {
        $image_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array(strtolower($image_extension), $allowed_extensions)) {
            $image_name = md5(time() . $_FILES['image']['name']) . "." . $image_extension;
            $upload_file = $upload_dir . $image_name;

            // ✅ Создаём папку, если её нет
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // ✅ Перемещаем файл
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
                echo "✅ Файл загружен в: " . $upload_file;
            } else {
                $_SESSION['message'] = "❌ Ошибка загрузки файла!";
                header("Location: /znahidka/?page=product_add");
                exit;
            }
        } else {
            $_SESSION['message'] = "❌ Недопустимый формат изображения!";
            header("Location: /znahidka/?page=product_add");
            exit;
        }
    }

    // ✅ Проверяем заполнение всех полей перед вставкой в БД
    if (!empty($title) && !empty($description) && !empty($price) && !empty($size) && !empty($material) && !empty($category)) {
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, size, material, category, sku, image) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $size, $material, $category, $sku, $image_name]);

        $_SESSION['message'] = "✅ Товар добавлен!";
        header("Location: /znahidka/?page=products");
        exit;
    } else {
        $_SESSION['message'] = "❌ Заполните все поля!";
    }
}
?>

<div class="container">
    <h2>➕ Добавить товар</h2>

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
        <input type="text" name="material" required>

        <label>Категория:</label>
        <input type="text" name="category" required>

        <label>Артикул (SKU):</label>
        <input type="text" name="sku" required>

        <label>Фото:</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">💾 Добавить товар</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
