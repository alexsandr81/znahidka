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

// Получаем ID товара из URL
$product_id = $_GET['id'] ?? 0;

// Загружаем товар
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['message'] = "Товар не найден!";
    header("Location: /znahidka/?page=products");
    exit;
}

// Обрабатываем отправку формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (!empty($title) && !empty($price) && !empty($category)) {
        $stmt = $pdo->prepare("UPDATE products SET title = ?, price = ?, category = ? WHERE id = ?");
        $stmt->execute([$title, $price, $category, $product_id]);

        $_SESSION['message'] = "Товар обновлён!";
        header("Location: /znahidka/?page=products");
        exit;
    } else {
        $_SESSION['message'] = "Заполните все поля!";
    }
}
?>

<div class="container">
    <h2>Редактировать товар</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post">
        <label>Название:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>

        <label>Цена:</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" required>

        <label>Категория:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>

        <button type="submit">Сохранить изменения</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
