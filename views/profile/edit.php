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

<div class="container mt-5">
    <h2 class="mb-4"><i class="bi bi-pencil-square"></i> Редактирование профиля</h2>

    <!-- Вывод сообщений -->
    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert alert-warning">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form id="editProfileForm" action="/znahidka/core/profile/update_profile.php" method="post" class="shadow p-4 bg-light rounded">
        <div class="mb-3">
            <label for="name" class="form-label">Имя:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control">
        </div>

        <hr>

        <h4 class="mb-3"><i class="bi bi-key"></i> Смена пароля</h4>

        <div class="mb-3">
            <label for="password" class="form-label">Новый пароль (необязательно):</label>
            <input type="password" id="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label for="confirm_new_password" class="form-label">Подтвердите новый пароль:</label>
            <input type="password" id="confirm_new_password" name="confirm_new_password" class="form-control">
            <div id="passwordError" class="text-danger mt-1" style="display: none;">❌ Пароли не совпадают!</div>
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Введите текущий пароль для подтверждения:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
        </div>

        <button type="submit" id="saveButton" class="btn btn-success"><i class="bi bi-save"></i> Сохранить изменения</button>
        <a href="/znahidka/views/profile/index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Назад</a>
    </form>
</div>

<script src="/znahidka/js/profile.js"></script>

<?php require_once __DIR__ . '/../../templates/footer.php'; ?>
