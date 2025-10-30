<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../Metier/approvisionnement.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

$approId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($approId) {
    Approvis::delete($approId);
}

header('Location: ' . url_for('Presentation/Approvisionnement/afficherApprovisionnements.php'));
exit();
