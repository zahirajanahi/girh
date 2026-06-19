<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/entreprises.php';

requireLogin();

$errors = [];
$data = [
    'nom' => '', 'prenom' => '', 'cin' => '', 'fonction' => '',
    'telephone' => '', 'email' => '', 'adresse' => '', 'date_naissance' => '',
    'entreprise_id' => '', 'type_contrat' => 'CDD', 'date_debut' => date('Y-m-d'),
    'date_fin' => '', 'salaire' => '', 'type_salaire' => 'mensuel',
    'mode_paiement' => 'virement', 'statut' => 'en_mission', 'competences' => '',
];

$entreprises = getAllEntreprisesForSelect();

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
