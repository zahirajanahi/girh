<?php
/**
 * Logique métier - Entreprises
 */

require_once __DIR__ . '/config.php';

function getAllEntreprises(?string $search = null): array
{
    $pdo = getPDO();
    $sql = 'SELECT * FROM entreprises WHERE 1=1';
    $params = [];

    if ($search) {
        $sql .= ' AND (nom_entreprise LIKE :search OR secteur_activite LIKE :search OR contact_principal LIKE :search OR email LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY nom_entreprise ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getEntrepriseById(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('SELECT * FROM entreprises WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

function validateEntreprise(array $data): array
{
    $errors = [];

    if (empty(trim($data['nom_entreprise'] ?? ''))) {
        $errors['nom_entreprise'] = 'Le nom de l\'entreprise est obligatoire.';
    }

    if (empty(trim($data['secteur_activite'] ?? ''))) {
        $errors['secteur_activite'] = 'Le secteur d\'activité est obligatoire.';
    }

    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide.';
    }

    if (!empty($data['telephone']) && !preg_match('/^[0-9+\s\-]{8,20}$/', $data['telephone'])) {
        $errors['telephone'] = 'Le numéro de téléphone n\'est pas valide.';
    }

    return $errors;
}

function createEntreprise(array $data): int
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        INSERT INTO entreprises (nom_entreprise, secteur_activite, adresse, telephone, email, contact_principal)
        VALUES (:nom_entreprise, :secteur_activite, :adresse, :telephone, :email, :contact_principal)
    ');

    $stmt->execute([
        'nom_entreprise'    => trim($data['nom_entreprise']),
        'secteur_activite'  => trim($data['secteur_activite']),
        'adresse'           => trim($data['adresse'] ?? '') ?: null,
        'telephone'         => trim($data['telephone'] ?? '') ?: null,
        'email'             => trim($data['email'] ?? '') ?: null,
        'contact_principal' => trim($data['contact_principal'] ?? '') ?: null,
    ]);

    return (int) $pdo->lastInsertId();
}

function updateEntreprise(int $id, array $data): bool
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        UPDATE entreprises SET
            nom_entreprise = :nom_entreprise, secteur_activite = :secteur_activite,
            adresse = :adresse, telephone = :telephone, email = :email,
            contact_principal = :contact_principal
        WHERE id = :id
    ');

    return $stmt->execute([
        'id'                => $id,
        'nom_entreprise'    => trim($data['nom_entreprise']),
        'secteur_activite'  => trim($data['secteur_activite']),
        'adresse'           => trim($data['adresse'] ?? '') ?: null,
        'telephone'         => trim($data['telephone'] ?? '') ?: null,
        'email'             => trim($data['email'] ?? '') ?: null,
        'contact_principal' => trim($data['contact_principal'] ?? '') ?: null,
    ]);
}

function deleteEntreprise(int $id): array
{
    $pdo = getPDO();

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM missions WHERE entreprise_id = :id');
    $stmt->execute(['id' => $id]);
    if ((int) $stmt->fetchColumn() > 0) {
        return ['success' => false, 'message' => 'Impossible de supprimer : cette entreprise est liée à une ou plusieurs missions.'];
    }

    $stmt = $pdo->prepare('DELETE FROM entreprises WHERE id = :id');
    $stmt->execute(['id' => $id]);

    return ['success' => true, 'message' => 'Entreprise supprimée avec succès.'];
}

function countEntreprises(): int
{
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT COUNT(*) FROM entreprises');
    return (int) $stmt->fetchColumn();
}

function getAllEntreprisesForSelect(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT id, nom_entreprise, secteur_activite FROM entreprises ORDER BY nom_entreprise ASC');
    return $stmt->fetchAll();
}
