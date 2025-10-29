<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/fournisseur.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

    $fournisseur = new Fournisseur($nom, $adresse, $telephone, $email);
    $fournisseur->setId($id);
    $fournisseur->update($fournisseur);
    $_SESSION['succes'] = true;
}

header('Location: ' . url_for('Presentation/Fournisseur/afficherFournisseurs.php'));
exit();
?>