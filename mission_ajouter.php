<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/missions.php';
require_once __DIR__ . '/php/entreprises.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$errors = [];
$data = [
    'entreprise_id' => '', 'interimaire_id' => '', 'poste' => '',
    'date_debut' => '', 'date_fin' => '', 'salaire_horaire' => '', 'statut' => 'en_cours',
];

$entreprises = getAllEntreprisesForSelect();
$interimairesDisponibles = getInterimairesDisponibles();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($data, $_POST);
    $errors = validateMission($data);

    if (empty($errors)) {
        createMission($data);
        redirect('missions.php?message=' . urlencode('Mission créée. L\'intérimaire est maintenant en mission.') . '&type=success');
    }
}

$pageTitle = 'Créer une mission';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/mission_form.php';
include __DIR__ . '/includes/footer.php';
