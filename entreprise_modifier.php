<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/entreprises.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$entreprise = getEntrepriseById($id);

if (!$entreprise) {
    redirect('entreprises.php?message=' . urlencode('Entreprise introuvable.') . '&type=error');
}

$errors = [];
$data = $entreprise;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($entreprise, $_POST);
    $errors = validateEntreprise($data);

    if (empty($errors)) {
        updateEntreprise($id, $data);
        redirect('entreprises.php?message=' . urlencode('Entreprise modifiée avec succès.') . '&type=success');
    }
}

$pageTitle = 'Modifier une entreprise';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/entreprise_form.php';
include __DIR__ . '/includes/footer.php';
