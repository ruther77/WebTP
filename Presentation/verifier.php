<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../DAO/DAO.php';

session_start();

$login = trim($_POST['login'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($login === '' || $password === '') {
    header('Location: ' . url_for('index.php?error=missing_credentials'));
    exit();
}

try {
    $dao = new DAO();
    $user = $dao->authentification($login, $password);
} catch (\PDOException $exception) {
    error_log('[Login] Database connection failed: ' . $exception->getMessage());
    header('Location: ' . url_for('index.php?error=database'));
    exit();
}

if ($user) {
    $_SESSION['user'] = $user;
    $_SESSION['login'] = $user['login'] ?? null;
    header('Location: ' . url_for('Presentation/dashboard.php'));
    exit();
}

header('Location: ' . url_for('index.php?error=invalid_credentials'));
exit();
