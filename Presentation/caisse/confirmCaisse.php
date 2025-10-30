<?php
require_once __DIR__ . '/../../config/app.php';
require_once __DIR__ . '/../../DAO/DAO.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: ' . url_for('index.php'));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url_for('Presentation/caisse/caisse.php'));
    exit();
}

$clientId = filter_input(INPUT_POST, 'client', FILTER_VALIDATE_INT);
$dateInput = $_POST['da'] ?? '';
$cart = $_POST['cart'] ?? [];

if (!$clientId || !is_array($cart) || $cart === []) {
    $_SESSION['flash_error'] = "Impossible d'enregistrer la commande : données manquantes.";
    header('Location: ' . url_for('Presentation/caisse/caisse.php'));
    exit();
}

$date = \DateTime::createFromFormat('Y-m-d H:i', $dateInput) ?: new \DateTime();
$normalizedItems = [];

foreach ($cart as $line) {
    $reference = $line[0] ?? '';
    $unitPrice = $line[1] ?? null;
    $quantity = $line[2] ?? null;

    if (!is_string($reference) || $reference === '') {
        continue;
    }

    if (!is_numeric($unitPrice) || (float) $unitPrice < 0) {
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
        'unit_price' => (float) $unitPrice,
    ];
}

if ($normalizedItems === []) {
    $_SESSION['flash_error'] = "La commande doit contenir au moins un produit valide.";
    header('Location: ' . url_for('Presentation/caisse/caisse.php'));
    exit();
}

$dao = new DAO();

try {
    $commandeId = $dao->createCommande($date->format('Y-m-d H:i:s'), $clientId, $normalizedItems);
} catch (\InvalidArgumentException | \RuntimeException $exception) {
    $_SESSION['flash_error'] = $exception->getMessage();
    header('Location: ' . url_for('Presentation/caisse/caisse.php'));
    exit();
} catch (\Throwable $exception) {
    $_SESSION['flash_error'] = "Une erreur inattendue s'est produite lors de l'enregistrement de la commande.";
    header('Location: ' . url_for('Presentation/caisse/caisse.php'));
    exit();
}

$redirectUrl = url_for('Presentation/caisse/caisse.php') . '?ref=' . urlencode((string) $commandeId);
header('Location: ' . $redirectUrl);
exit();
