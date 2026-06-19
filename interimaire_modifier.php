<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/entreprises.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$interimaire = getInterimaireById($id);

if (!$interimaire) {
    redirect('interimaires.php?message=' . urlencode('Intérimaire introuvable.') . '&type=error');
}

$errors = [];
$data = $interimaire;
$entreprises = getAllEntreprisesForSelect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($interimaire, $_POST);
    $errors = validateInterimaire($data, $id);

    if (empty($errors)) {
        updateInterimaire($id, $data);
        redirect('interimaires.php?message=' . urlencode('Intérimaire modifié avec succès.') . '&type=success');
    }
}

$pageTitle = 'Modifier un intérimaire';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/interimaire_form.php';
include __DIR__ . '/includes/footer.php';
