<?php
/**
 * Logique métier - Missions
 */

require_once __DIR__ . '/config.php';

function getAllMissions(?string $search = null, ?string $statut = null): array
{
    $pdo = getPDO();
    $sql = '
        SELECT m.*, e.nom_entreprise, e.secteur_activite,
               i.nom AS interimaire_nom, i.prenom AS interimaire_prenom
        FROM missions m
        JOIN entreprises e ON e.id = m.entreprise_id
        JOIN interimaires i ON i.id = m.interimaire_id
        WHERE 1=1
    ';
    $params = [];

    if ($search) {
        $sql .= ' AND (m.poste LIKE :search OR e.nom_entreprise LIKE :search OR i.nom LIKE :search OR i.prenom LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    if ($statut && in_array($statut, ['en_cours', 'terminee', 'renouvelee'])) {
        $sql .= ' AND m.statut = :statut';
        $params['statut'] = $statut;
    }

    $sql .= ' ORDER BY m.date_debut DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getMissionById(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        SELECT m.*, e.nom_entreprise, e.secteur_activite, e.contact_principal,
               i.nom AS interimaire_nom, i.prenom AS interimaire_prenom, i.cin AS interimaire_cin
        FROM missions m
        JOIN entreprises e ON e.id = m.entreprise_id
        JOIN interimaires i ON i.id = m.interimaire_id
        WHERE m.id = :id
    ');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

function validateMission(array $data, ?int $excludeId = null): array
{
    $errors = [];

    if (empty($data['entreprise_id'])) {
        $errors['entreprise_id'] = 'Veuillez sélectionner une entreprise.';
    }

    if (empty($data['interimaire_id'])) {
        $errors['interimaire_id'] = 'Veuillez sélectionner un intérimaire.';
    } else {
        $interimaireId = (int) $data['interimaire_id'];
        $interimaire = getInterimaireByIdForMission($interimaireId);

        if (!$interimaire) {
            $errors['interimaire_id'] = 'Intérimaire introuvable.';
        } elseif (!$excludeId && $interimaire['disponibilite'] !== 'disponible') {
            $errors['interimaire_id'] = 'L\'intérimaire sélectionné n\'est pas disponible.';
        } elseif ($excludeId) {
            $current = getMissionById($excludeId);
            if ($current && (int) $current['interimaire_id'] !== $interimaireId && $interimaire['disponibilite'] !== 'disponible') {
                $errors['interimaire_id'] = 'L\'intérimaire sélectionné n\'est pas disponible.';
            }
        }
    }

    if (empty(trim($data['poste'] ?? ''))) {
        $errors['poste'] = 'Le poste est obligatoire.';
    }

    if (empty($data['date_debut'])) {
        $errors['date_debut'] = 'La date de début est obligatoire.';
    }

    if (empty($data['date_fin'])) {
        $errors['date_fin'] = 'La date de fin est obligatoire.';
    }

    if (!empty($data['date_debut']) && !empty($data['date_fin'])) {
        if ($data['date_fin'] < $data['date_debut']) {
            $errors['date_fin'] = 'La date de fin doit être postérieure à la date de début.';
        }
    }

    if (!isset($data['salaire_horaire']) || $data['salaire_horaire'] === '') {
        $errors['salaire_horaire'] = 'Le salaire horaire est obligatoire.';
    } elseif (!is_numeric($data['salaire_horaire']) || (float) $data['salaire_horaire'] <= 0) {
        $errors['salaire_horaire'] = 'Le salaire horaire doit être un nombre positif.';
    }

    $statut = $data['statut'] ?? '';
    if (!in_array($statut, ['en_cours', 'terminee', 'renouvelee'])) {
        $errors['statut'] = 'Le statut sélectionné n\'est pas valide.';
    }

    return $errors;
}

function getInterimaireByIdForMission(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT id, nom, prenom, disponibilite FROM interimaires WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

function createMission(array $data): int
{
    $pdo = getPDO();

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('
            INSERT INTO missions (entreprise_id, interimaire_id, poste, date_debut, date_fin, salaire_horaire, statut)
            VALUES (:entreprise_id, :interimaire_id, :poste, :date_debut, :date_fin, :salaire_horaire, :statut)
        ');

        $stmt->execute([
            'entreprise_id'   => (int) $data['entreprise_id'],
            'interimaire_id'  => (int) $data['interimaire_id'],
            'poste'           => trim($data['poste']),
            'date_debut'      => $data['date_debut'],
            'date_fin'        => $data['date_fin'],
            'salaire_horaire' => (float) $data['salaire_horaire'],
            'statut'          => $data['statut'] ?? 'en_cours',
        ]);

        $missionId = (int) $pdo->lastInsertId();

        $stmt = $pdo->prepare("UPDATE interimaires SET disponibilite = 'en_mission' WHERE id = :id");
        $stmt->execute(['id' => (int) $data['interimaire_id']]);

        $pdo->commit();
        return $missionId;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function updateMission(int $id, array $data): bool
{
    $pdo = getPDO();
    $current = getMissionById($id);
    if (!$current) {
        return false;
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('
            UPDATE missions SET
                entreprise_id = :entreprise_id, interimaire_id = :interimaire_id,
                poste = :poste, date_debut = :date_debut, date_fin = :date_fin,
                salaire_horaire = :salaire_horaire, statut = :statut
            WHERE id = :id
        ');

        $stmt->execute([
            'id'              => $id,
            'entreprise_id'   => (int) $data['entreprise_id'],
            'interimaire_id'  => (int) $data['interimaire_id'],
            'poste'           => trim($data['poste']),
            'date_debut'      => $data['date_debut'],
            'date_fin'        => $data['date_fin'],
            'salaire_horaire' => (float) $data['salaire_horaire'],
            'statut'          => $data['statut'],
        ]);

        syncInterimaireDisponibilite($pdo, (int) $current['interimaire_id']);
        if ((int) $current['interimaire_id'] !== (int) $data['interimaire_id']) {
            syncInterimaireDisponibilite($pdo, (int) $data['interimaire_id']);
        }

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function syncInterimaireDisponibilite(PDO $pdo, int $interimaireId): void
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM missions WHERE interimaire_id = :id AND statut IN ('en_cours', 'renouvelee')");
    $stmt->execute(['id' => $interimaireId]);
    $hasActiveMission = (int) $stmt->fetchColumn() > 0;

    $disponibilite = $hasActiveMission ? 'en_mission' : 'disponible';
    $stmt = $pdo->prepare('UPDATE interimaires SET disponibilite = :disponibilite WHERE id = :id AND disponibilite != :indisponible');
    $stmt->execute([
        'disponibilite' => $disponibilite,
        'id'            => $interimaireId,
        'indisponible'  => 'indisponible',
    ]);
}

function deleteMission(int $id): array
{
    $pdo = getPDO();
    $mission = getMissionById($id);

    if (!$mission) {
        return ['success' => false, 'message' => 'Mission introuvable.'];
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('DELETE FROM missions WHERE id = :id');
        $stmt->execute(['id' => $id]);

        syncInterimaireDisponibilite($pdo, (int) $mission['interimaire_id']);

        $pdo->commit();
        return ['success' => true, 'message' => 'Mission supprimée avec succès.'];
    } catch (Exception $e) {
        $pdo->rollBack();
        return ['success' => false, 'message' => 'Erreur lors de la suppression : ' . $e->getMessage()];
    }
}

function countMissionsEnCours(): int
{
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT COUNT(*) FROM missions WHERE statut = 'en_cours'");
    return (int) $stmt->fetchColumn();
}

function getMissionsParSecteur(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('
        SELECT e.secteur_activite, COUNT(m.id) AS total
        FROM missions m
        JOIN entreprises e ON e.id = m.entreprise_id
        GROUP BY e.secteur_activite
        ORDER BY total DESC
    ');
    return $stmt->fetchAll();
}

function getMissionsParMois(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('
        SELECT DATE_FORMAT(date_debut, "%Y-%m") AS mois, COUNT(*) AS total
        FROM missions
        GROUP BY DATE_FORMAT(date_debut, "%Y-%m")
        ORDER BY mois ASC
    ');
    return $stmt->fetchAll();
}

function getMissionsFinProche(int $jours = 7): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        SELECT m.*, e.nom_entreprise, i.nom AS interimaire_nom, i.prenom AS interimaire_prenom
        FROM missions m
        JOIN entreprises e ON e.id = m.entreprise_id
        JOIN interimaires i ON i.id = m.interimaire_id
        WHERE m.statut IN ('en_cours', 'renouvelee')
          AND m.date_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :jours DAY)
        ORDER BY m.date_fin ASC
    ");
    $stmt->execute(['jours' => $jours]);
    return $stmt->fetchAll();
}

function getStatutLabel(string $statut): string
{
    return match ($statut) {
        'en_cours'   => 'En cours',
        'terminee'   => 'Terminée',
        'renouvelee' => 'Renouvelée',
        default      => $statut,
    };
}

function getTotalHeuresMission(int $missionId): float
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(heures_travaillees), 0) FROM feuilles_temps WHERE mission_id = :id');
    $stmt->execute(['id' => $missionId]);
    return (float) $stmt->fetchColumn();
}

function getSalaireDuMission(int $missionId): float
{
    $mission = getMissionById($missionId);
    if (!$mission) {
        return 0;
    }
    return getTotalHeuresMission($missionId) * (float) $mission['salaire_horaire'];
}
