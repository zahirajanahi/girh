<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$errors = [];
$data = [
    'nom' => '', 'prenom' => '', 'cin' => '', 'telephone' => '', 'email' => '',
    'adresse' => '', 'date_naissance' => '', 'competences' => '',
    'disponibilite' => 'disponible', 'date_inscription' => date('Y-m-d'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($data, $_POST);
    $errors = validateInterimaire($data);

    if (empty($errors)) {
        createInterimaire($data);
        redirect('interimaires.php?message=' . urlencode('Intérimaire ajouté avec succès.') . '&type=success');
    }
}

$pageTitle = 'Ajouter un intérimaire';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/interimaire_form.php';
include __DIR__ . '/includes/footer.php';
