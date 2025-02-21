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

// Обрабатываем отправку формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $size = trim($_POST['size'] ?? '');
    $material = trim($_POST['material'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // Генерируем уникальный SKU
    $sku = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));

    // Проверяем, что все обязательные поля заполнены
    if (!empty($title) && !empty($description) && !empty($price) && !empty($size) && !empty($material) && !empty($category)) {
        // Добавляем товар в базу данных
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, size, material, category, sku) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $price, $size, $material, $category, $sku]);

        $_SESSION['message'] = "Товар добавлен!";
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

    <form method="post">
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

        <button type="submit">Добавить</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
