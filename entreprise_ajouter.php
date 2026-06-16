<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/entreprises.php';

requireLogin();

$errors = [];
$data = [
    'nom_entreprise' => '', 'secteur_activite' => '', 'adresse' => '',
    'telephone' => '', 'email' => '', 'contact_principal' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($data, $_POST);
    $errors = validateEntreprise($data);

    if (empty($errors)) {
        createEntreprise($data);
        redirect('entreprises.php?message=' . urlencode('Entreprise ajoutée avec succès.') . '&type=success');
    }
}

$pageTitle = 'Ajouter une entreprise';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/entreprise_form.php';
include __DIR__ . '/includes/footer.php';
