<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/missions.php';
require_once __DIR__ . '/php/entreprises.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$mission = getMissionById($id);

if (!$mission) {
    redirect('missions.php?message=' . urlencode('Mission introuvable.') . '&type=error');
}

$errors = [];
$data = $mission;

$entreprises = getAllEntreprisesForSelect();
$interimairesDisponibles = getInterimairesDisponibles();

$currentInterimaire = getInterimaireById((int) $mission['interimaire_id']);
if ($currentInterimaire && $currentInterimaire['disponibilite'] !== 'disponible') {
    $alreadyListed = false;
    foreach ($interimairesDisponibles as $i) {
        if ((int) $i['id'] === (int) $mission['interimaire_id']) {
            $alreadyListed = true;
            break;
        }
    }
    if (!$alreadyListed) {
        array_unshift($interimairesDisponibles, [
            'id' => $currentInterimaire['id'],
            'nom' => $currentInterimaire['nom'],
            'prenom' => $currentInterimaire['prenom'],
            'cin' => $currentInterimaire['cin'],
            'competences' => $currentInterimaire['competences'],
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = array_merge($mission, $_POST);
    $errors = validateMission($data, $id);

    if (empty($errors)) {
        updateMission($id, $data);
        redirect('missions.php?message=' . urlencode('Mission modifiée avec succès.') . '&type=success');
    }
}

$pageTitle = 'Modifier une mission';
$isEdit = true;
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/mission_form.php';
include __DIR__ . '/includes/footer.php';
