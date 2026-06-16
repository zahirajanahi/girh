<?php
/**
 * Logique métier - Intérimaires
 */

require_once __DIR__ . '/config.php';

function getAllInterimaires(?string $search = null, ?string $disponibilite = null): array
{
    $pdo = getPDO();
    $sql = 'SELECT * FROM interimaires WHERE 1=1';
    $params = [];

    if ($search) {
        $sql .= ' AND (nom LIKE :search OR prenom LIKE :search OR cin LIKE :search OR email LIKE :search OR competences LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    if ($disponibilite && in_array($disponibilite, ['disponible', 'en_mission', 'indisponible'])) {
        $sql .= ' AND disponibilite = :disponibilite';
        $params['disponibilite'] = $disponibilite;
    }

    $sql .= ' ORDER BY nom ASC, prenom ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getInterimaireById(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM interimaires WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

function getInterimairesDisponibles(): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT id, nom, prenom, cin, competences FROM interimaires WHERE disponibilite = 'disponible' ORDER BY nom ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

function validateInterimaire(array $data, ?int $excludeId = null): array
{
    $errors = [];

    if (empty(trim($data['nom'] ?? ''))) {
        $errors['nom'] = 'Le nom est obligatoire.';
    }

    if (empty(trim($data['prenom'] ?? ''))) {
        $errors['prenom'] = 'Le prénom est obligatoire.';
    }

    $cin = trim($data['cin'] ?? '');
    if (empty($cin)) {
        $errors['cin'] = 'Le CIN est obligatoire.';
    } elseif (strlen($cin) < 5) {
        $errors['cin'] = 'Le CIN doit contenir au moins 5 caractères.';
    } elseif (cinExists($cin, $excludeId)) {
        $errors['cin'] = 'Ce CIN est déjà enregistré.';
    }

    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide.';
    }

    if (!empty($data['telephone']) && !preg_match('/^[0-9+\s\-]{8,20}$/', $data['telephone'])) {
        $errors['telephone'] = 'Le numéro de téléphone n\'est pas valide.';
    }

    if (!empty($data['date_naissance'])) {
        $d = DateTime::createFromFormat('Y-m-d', $data['date_naissance']);
        if (!$d || $d->format('Y-m-d') !== $data['date_naissance']) {
            $errors['date_naissance'] = 'La date de naissance n\'est pas valide.';
        }
    }

    $dispo = $data['disponibilite'] ?? '';
    if (!in_array($dispo, ['disponible', 'en_mission', 'indisponible'])) {
        $errors['disponibilite'] = 'La disponibilité sélectionnée n\'est pas valide.';
    }

    return $errors;
}

function cinExists(string $cin, ?int $excludeId = null): bool
{
    $pdo = getPDO();
    $sql = 'SELECT COUNT(*) FROM interimaires WHERE cin = :cin';
    $params = ['cin' => $cin];

    if ($excludeId) {
        $sql .= ' AND id != :id';
        $params['id'] = $excludeId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}

function createInterimaire(array $data): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        INSERT INTO interimaires (nom, prenom, cin, telephone, email, adresse, date_naissance, competences, disponibilite, date_inscription)
        VALUES (:nom, :prenom, :cin, :telephone, :email, :adresse, :date_naissance, :competences, :disponibilite, :date_inscription)
    ');

    $stmt->execute([
        'nom'              => trim($data['nom']),
        'prenom'           => trim($data['prenom']),
        'cin'              => trim($data['cin']),
        'telephone'        => trim($data['telephone'] ?? '') ?: null,
        'email'            => trim($data['email'] ?? '') ?: null,
        'adresse'          => trim($data['adresse'] ?? '') ?: null,
        'date_naissance'   => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
        'competences'      => trim($data['competences'] ?? '') ?: null,
        'disponibilite'    => $data['disponibilite'],
        'date_inscription' => $data['date_inscription'] ?? date('Y-m-d'),
    ]);

    return (int) $pdo->lastInsertId();
}

function updateInterimaire(int $id, array $data): bool
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        UPDATE interimaires SET
            nom = :nom, prenom = :prenom, cin = :cin, telephone = :telephone,
            email = :email, adresse = :adresse, date_naissance = :date_naissance,
            competences = :competences, disponibilite = :disponibilite
        WHERE id = :id
    ');

    return $stmt->execute([
        'id'               => $id,
        'nom'              => trim($data['nom']),
        'prenom'           => trim($data['prenom']),
        'cin'              => trim($data['cin']),
        'telephone'        => trim($data['telephone'] ?? '') ?: null,
        'email'            => trim($data['email'] ?? '') ?: null,
        'adresse'          => trim($data['adresse'] ?? '') ?: null,
        'date_naissance'   => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
        'competences'      => trim($data['competences'] ?? '') ?: null,
        'disponibilite'    => $data['disponibilite'],
    ]);
}

function deleteInterimaire(int $id): array
{
    $pdo = getPDO();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM missions WHERE interimaire_id = :id');
    $stmt->execute(['id' => $id]);
    if ((int) $stmt->fetchColumn() > 0) {
        return ['success' => false, 'message' => 'Impossible de supprimer : cet intérimaire est lié à une ou plusieurs missions.'];
    }

    $stmt = $pdo->prepare('DELETE FROM interimaires WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return ['success' => true, 'message' => 'Intérimaire supprimé avec succès.'];
}

function countInterimairesActifs(): int
{
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT COUNT(*) FROM interimaires WHERE disponibilite != 'indisponible'");
    return (int) $stmt->fetchColumn();
}

function getDisponibiliteLabel(string $dispo): string
{
    return match ($dispo) {
        'disponible'   => 'Disponible',
        'en_mission'   => 'En mission',
        'indisponible' => 'Indisponible',
        default        => $dispo,
    };
}

function getMissionsByInterimaire(int $interimaireId): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        SELECT m.*, e.nom_entreprise
        FROM missions m
        JOIN entreprises e ON e.id = m.entreprise_id
        WHERE m.interimaire_id = :id
        ORDER BY m.date_debut DESC
    ');
    $stmt->execute(['id' => $interimaireId]);
    return $stmt->fetchAll();
}
