<?php
require_once __DIR__ . '/../../templates/header.php';
require_once __DIR__ . '/../../core/database/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Пожалуйста, войдите в аккаунт!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Получаем данные пользователя
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<div class="container">
    <h2>✏ Редактирование профиля</h2>

    <!-- Выводим сообщение об ошибке -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form id="editProfileForm" action="/znahidka/core/profile/update_profile.php" method="post">
        <label for="name">Имя:</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <label for="phone">Телефон:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">

        <label for="password">Новый пароль (необязательно):</label>
        <input type="password" id="password" name="password">

        <label for="confirm_new_password">Подтвердите новый пароль:</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password">
        <p id="passwordError" style="color: red; display: none;">❌ Пароли не совпадают!</p>

        <label for="confirm_password">Введите текущий пароль для подтверждения:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" id="saveButton">💾 Сохранить изменения</button>
    </form>

    <a href="/znahidka/views/profile/index.php" class="btn">🔙 Назад</a>
</div>

<script src="/znahidka/js/profile.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
