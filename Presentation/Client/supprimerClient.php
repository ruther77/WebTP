<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/client.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if (isset($_GET['id'])) {
    Client::delete((int) $_GET['id']);
}

header('Location: ' . url_for('Presentation/Client/afficherClients.php'));
exit();