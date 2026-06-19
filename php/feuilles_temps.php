<?php
/**
 * Logique métier - Feuilles de temps (v2)
 */

require_once __DIR__ . '/config.php';

function getInterimairesForFeuillesTemps(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('
        SELECT i.id, i.nom, i.prenom, i.cin, i.fonction,
               i.type_salaire, i.mode_paiement, e.nom_entreprise
        FROM interimaires i
        LEFT JOIN entreprises e ON e.id = i.entreprise_id
        ORDER BY i.nom ASC, i.prenom ASC
    ');
    return $stmt->fetchAll();
}

function getFeuillesByInterimaire(int $interimaireId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM feuilles_temps WHERE interimaire_id = :id ORDER BY date DESC');
    $stmt->execute(['id' => $interimaireId]);
    return $stmt->fetchAll();
}

function getTotalHeuresInterimaire(int $interimaireId, ?string $mois = null): float
{
    $pdo = getPDO();
    $sql = 'SELECT COALESCE(SUM(heures_travaillees), 0) FROM feuilles_temps WHERE interimaire_id = :id';
    $params = ['id' => $interimaireId];

    if ($mois) {
        $sql .= ' AND DATE_FORMAT(date, "%Y-%m") = :mois';
        $params['mois'] = $mois;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (float) $stmt->fetchColumn();
}

function getTotalHeuresPeriode(int $interimaireId, string $dateDebut, string $dateFin): float
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        SELECT COALESCE(SUM(heures_travaillees), 0)
        FROM feuilles_temps
        WHERE interimaire_id = :id AND date BETWEEN :debut AND :fin
    ');
    $stmt->execute([
        'id'    => $interimaireId,
        'debut' => $dateDebut,
        'fin'   => $dateFin,
    ]);
    return (float) $stmt->fetchColumn();
}

function validateFeuilleTemps(array $data): array
{
    $errors = [];

    if (empty($data['interimaire_id'])) {
        $errors['interimaire_id'] = 'Veuillez sélectionner un intérimaire.';
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
        INSERT INTO feuilles_temps (interimaire_id, date, heures_travaillees)
        VALUES (:interimaire_id, :date, :heures_travaillees)
    ');

    $stmt->execute([
        'interimaire_id'     => (int) $data['interimaire_id'],
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
