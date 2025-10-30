<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/categorie.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if (isset($_GET['id'])) {
    Categorie::delete($_GET['id']);
}

header('Location: ' . url_for('Presentation/Categorie/afficherCategories.php'));
exit();