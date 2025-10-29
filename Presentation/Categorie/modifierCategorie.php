<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/categorie.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nom = $_POST['nom'] ?? '';

    $categorie = new Categorie($id, $nom);
    $dao = new DAO();
    $dao->updateCategorie($categorie);
    $_SESSION['succes'] = true;
}

header('Location: ' . url_for('Presentation/Categorie/afficherCategories.php'));
exit();

?>