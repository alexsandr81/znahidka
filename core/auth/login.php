<?php
require_once __DIR__ . '/../../core/init.php';

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['message'] = "Заполните все поля!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем пользователя
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['message'] = "Неверный email или пароль!";
    header("Location: /znahidka/?page=login");
    exit;
}

// Проверяем пароль (новый или старый хеш)
if (password_verify($password, $user['password']) || hash('sha256', $password) === $user['password']) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = htmlspecialchars($user['name']);
    $_SESSION['role'] = $user['role'];

    // Если старый пароль — обновляем на новый `password_hash()`
    if (hash('sha256', $password) === $user['password']) {
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$newHash, $user['id']]);
    }

    header("Location: /znahidka/?page=profile");
    exit;
}

$_SESSION['message'] = "Неверный email или пароль!";
header("Location: /znahidka/?page=login");
exit;
?>

