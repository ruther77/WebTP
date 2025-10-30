<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/commande.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

$commandeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($commandeId) {
    Commande::delete($commandeId);
}

header('Location: ' . url_for('Presentation/Commande/afficherCommandes.php'));
exit();
