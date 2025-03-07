<?php
require_once 'templates/header.php';
?>

<div class="container">
    <h2>Вход</h2>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="alert"><?= htmlspecialchars($_SESSION['message']) ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <form method="post" action="/znahidka/core/auth/login.php">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Пароль:</label>
        <input type="password" name="password" required>

        <button type="submit">Войти</button>
    </form>

    <p>Нет аккаунта? <a href="/znahidka/?page=register">Зарегистрируйтесь</a></p>
</div>

<?php require_once 'templates/footer.php'; ?>
