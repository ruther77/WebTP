<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/client.php';

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

    $client = new Client($nom, $adresse, $telephone, $email);
    $client->setId($id);

    $dao = new DAO();
    $dao->updateClient($client);
    $_SESSION['succes'] = true;
}

header('Location: ' . url_for('Presentation/Client/afficherClients.php'));
exit();
