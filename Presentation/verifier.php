<?php
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../DAO/DAO.php';

session_start();

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

$dao = new DAO();
$user = $dao->authentification($login, $password);

if ($user) {
    $_SESSION['user'] = $user;
    $_SESSION['login'] = $user['login'] ?? null;
    header('Location: ' . url_for('Presentation/dashboard.php'));
    exit();
}

header('Location: ' . url_for('index.php?error=1'));
exit();
