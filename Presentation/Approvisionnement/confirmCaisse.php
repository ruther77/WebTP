<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../DAO/DAO.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url_for('Presentation/Approvisionnement/caisse.php'));
    exit();
}

$supplierId = filter_input(INPUT_POST, 'client', FILTER_VALIDATE_INT);
$dateInput = $_POST['da'] ?? '';
$cart = $_POST['cart'] ?? [];

if (!$supplierId || !is_array($cart) || $cart === []) {
    $_SESSION['flash_error'] = "Impossible d'enregistrer l'approvisionnement : données manquantes.";
    header('Location: ' . url_for('Presentation/Approvisionnement/caisse.php'));
    exit();
}

$date = \DateTime::createFromFormat('Y-m-d H:i', $dateInput) ?: new \DateTime();
$normalizedItems = [];

foreach ($cart as $line) {
    $reference = $line[0] ?? '';
    $purchasePrice = $line[1] ?? null;
    $quantity = $line[2] ?? null;

    if (!is_string($reference) || $reference === '') {
        continue;
    }

    if (!is_numeric($purchasePrice) || (float) $purchasePrice < 0) {
        continue;
    }

    $quantityValue = filter_var($quantity, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    if ($quantityValue === false) {
        continue;
    }

    $normalizedItems[] = [
        'reference' => $reference,
        'quantity' => $quantityValue,
        'purchase_price' => (float) $purchasePrice,
    ];
}

if ($normalizedItems === []) {
    $_SESSION['flash_error'] = "L'approvisionnement doit contenir au moins un produit valide.";
    header('Location: ' . url_for('Presentation/Approvisionnement/caisse.php'));
    exit();
}

$dao = new DAO();

try {
    $approId = $dao->createApprovisionnement($date->format('Y-m-d H:i:s'), $supplierId, $normalizedItems);
} catch (\InvalidArgumentException | \RuntimeException $exception) {
    $_SESSION['flash_error'] = $exception->getMessage();
    header('Location: ' . url_for('Presentation/Approvisionnement/caisse.php'));
    exit();
} catch (\Throwable $exception) {
    $_SESSION['flash_error'] = "Une erreur inattendue s'est produite lors de l'enregistrement de l'approvisionnement.";
    header('Location: ' . url_for('Presentation/Approvisionnement/caisse.php'));
    exit();
}

$redirectUrl = url_for('Presentation/Approvisionnement/caisse.php') . '?ref=' . urlencode((string) $approId);
header('Location: ' . $redirectUrl);
exit();
