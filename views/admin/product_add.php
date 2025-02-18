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
    $price = trim($_POST['price'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $sku = trim($_POST['sku'] ?? '');  // Добавляем поле SKU

    // Проверяем, что все обязательные поля заполнены
    if (!empty($title) && !empty($price) && !empty($category) && !empty($sku)) {
        // Проверка на уникальность SKU
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE sku = ?");
        $stmt->execute([$sku]);
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['message'] = "Товар с таким SKU уже существует!";
        } else {
            // Добавляем товар в базу данных
            $stmt = $pdo->prepare("INSERT INTO products (title, price, category, sku) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $price, $category, $sku]);

            $_SESSION['message'] = "Товар добавлен!";
            header("Location: /znahidka/?page=products");
            exit;
        }
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

        <label>Цена:</label>
        <input type="number" name="price" required>

        <label>Категория:</label>
        <input type="text" name="category" required>

        <label>SKU:</label>  <!-- Добавлен ввод для SKU -->
        <input type="text" name="sku" required>

        <button type="submit">Добавить</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
