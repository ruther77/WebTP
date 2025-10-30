<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/produit.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'] ?? '';
    $libelle = $_POST['libelle'] ?? '';
    $prix = $_POST['prix'] ?? 0;
    $quantite = $_POST['quantite'] ?? 0;
    $achat = $_POST['achat'] ?? 0;
    $cat = $_POST['cat'] ?? '';

    $produit = new Produit($reference, $libelle, $prix, $quantite, $achat, '', $cat);
    $dao = new DAO();
    $dao->updateProduit($produit);
    $_SESSION['succes'] = true;
}

header('Location: ' . url_for('Presentation/Produit/afficherProduits.php'));
exit();
?>