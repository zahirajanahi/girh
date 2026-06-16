<?php
/**
 * Logique métier - Feuilles de temps
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/missions.php';

function getFeuillesByMission(int $missionId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM feuilles_temps WHERE mission_id = :mission_id ORDER BY date DESC');
    $stmt->execute(['mission_id' => $missionId]);
    return $stmt->fetchAll();
}

function getAllFeuillesTemps(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('
        SELECT ft.*, m.poste, m.salaire_horaire, e.nom_entreprise,
               i.nom AS interimaire_nom, i.prenom AS interimaire_prenom
        FROM feuilles_temps ft
        JOIN missions m ON m.id = ft.mission_id
        JOIN entreprises e ON e.id = m.entreprise_id
        JOIN interimaires i ON i.id = m.interimaire_id
        ORDER BY ft.date DESC
    ');
    return $stmt->fetchAll();
}

function validateFeuilleTemps(array $data): array
{
    $errors = [];

    if (empty($data['mission_id'])) {
        $errors['mission_id'] = 'Veuillez sélectionner une mission.';
    }

    if (empty($data['date'])) {
        $errors['date'] = 'La date est obligatoire.';
    }

    if (!isset($data['heures_travaillees']) || $data['heures_travaillees'] === '') {
        $errors['heures_travaillees'] = 'Le nombre d\'heures est obligatoire.';
    } elseif (!is_numeric($data['heures_travaillees']) || (float) $data['heures_travaillees'] <= 0 || (float) $data['heures_travaillees'] > 24) {
        $errors['heures_travaillees'] = 'Les heures doivent être entre 0 et 24.';
    }

    return $errors;
}

function createFeuilleTemps(array $data): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        INSERT INTO feuilles_temps (mission_id, date, heures_travaillees)
        VALUES (:mission_id, :date, :heures_travaillees)
    ');

    $stmt->execute([
        'mission_id'         => (int) $data['mission_id'],
        'date'               => $data['date'],
        'heures_travaillees' => (float) $data['heures_travaillees'],
    ]);

    return (int) $pdo->lastInsertId();
}

function deleteFeuilleTemps(int $id): bool
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM feuilles_temps WHERE id = :id');
    return $stmt->execute(['id' => $id]);
}

function getMissionsActivesForSelect(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query("
        SELECT m.id, m.poste, e.nom_entreprise, i.nom, i.prenom
        FROM missions m
        JOIN entreprises e ON e.id = m.entreprise_id
        JOIN interimaires i ON i.id = m.interimaire_id
        WHERE m.statut IN ('en_cours', 'renouvelee')
        ORDER BY e.nom_entreprise ASC
    ");
    return $stmt->fetchAll();
}
