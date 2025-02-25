<?php
require_once 'templates/header.php';
?>

<div class="container">
    <h2>Регистрация</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post" action="/znahidka/core/auth/register.php">
        <label>Имя:</label>
        <input type="text" name="name" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Пароль:</label>
        <input type="password" name="password" required>

        <label>Подтвердите пароль:</label>
        <input type="password" name="confirm_password" required>

        <button type="submit">Зарегистрироваться</button>
    </form>
</div>

<?php require_once 'templates/footer.php'; ?>
