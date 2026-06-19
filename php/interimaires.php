<?php
/**
 * Logique métier - Intérimaires (v2)
 */

require_once __DIR__ . '/config.php';

function syncExpiredContracts(): void
{
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        UPDATE interimaires
        SET statut = 'fin_de_contrat'
        WHERE date_fin IS NOT NULL
          AND date_fin < CURDATE()
          AND statut != 'fin_de_contrat'
    ");
    $stmt->execute();
}

function computeDateFin(array $data): ?string
{
    $type = $data['type_contrat'] ?? '';

    if ($type === 'CDI') {
        return null;
    }

    if ($type === 'CDD') {
        if (!empty($data['date_fin'])) {
            return $data['date_fin'];
        }
        if (!empty($data['date_debut'])) {
            $dt = DateTime::createFromFormat('Y-m-d', $data['date_debut']);
            if ($dt) {
                $dt->modify('+6 months');
                return $dt->format('Y-m-d');
            }
        }
        return null;
    }

    return !empty($data['date_fin']) ? $data['date_fin'] : null;
}

function getAllInterimaires(?string $search = null, ?int $entrepriseId = null, ?string $typeContrat = null, ?string $statut = null): array
{
    syncExpiredContracts();

    $pdo = getPDO();
    $sql = '
        SELECT i.*, e.nom_entreprise
        FROM interimaires i
        LEFT JOIN entreprises e ON e.id = i.entreprise_id
        WHERE 1=1
    ';
    $params = [];

    if ($search) {
        $sql .= ' AND (i.nom LIKE :search OR i.prenom LIKE :search OR i.cin LIKE :search OR i.fonction LIKE :search OR i.email LIKE :search)';
        $params['search'] = '%' . $search . '%';
    }

    if ($entrepriseId) {
        $sql .= ' AND i.entreprise_id = :entreprise_id';
        $params['entreprise_id'] = $entrepriseId;
    }

    if ($typeContrat && in_array($typeContrat, ['CDI', 'CDD', 'ANAPEC'])) {
        $sql .= ' AND i.type_contrat = :type_contrat';
        $params['type_contrat'] = $typeContrat;
    }

    if ($statut && in_array($statut, ['paie_bloquee', 'en_mission', 'fin_de_contrat'])) {
        $sql .= ' AND i.statut = :statut';
        $params['statut'] = $statut;
    }

    $sql .= ' ORDER BY i.nom ASC, i.prenom ASC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getInterimaireById(int $id): ?array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('
        SELECT i.*, e.nom_entreprise, e.secteur_activite
        FROM interimaires i
        LEFT JOIN entreprises e ON e.id = i.entreprise_id
        WHERE i.id = :id
    ');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result ?: null;
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

    if (empty(trim($data['fonction'] ?? ''))) {
        $errors['fonction'] = 'La fonction est obligatoire.';
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

    $typeContrat = $data['type_contrat'] ?? '';
    if (!in_array($typeContrat, ['CDI', 'CDD', 'ANAPEC'])) {
        $errors['type_contrat'] = 'Le type de contrat est invalide.';
    }

    if (empty($data['date_debut'])) {
        $errors['date_debut'] = 'La date de début est obligatoire.';
    }

    if (!isset($data['salaire']) || $data['salaire'] === '' || (float) $data['salaire'] <= 0) {
        $errors['salaire'] = 'Le salaire doit être un montant positif.';
    }

    $typeSalaire = $data['type_salaire'] ?? '';
    if (!in_array($typeSalaire, ['horaire', 'mensuel'])) {
        $errors['type_salaire'] = 'Le type de salaire est invalide.';
    }

    $modePaiement = $data['mode_paiement'] ?? '';
    if (!in_array($modePaiement, ['espece', 'virement', 'cheque'])) {
        $errors['mode_paiement'] = 'Le mode de paiement est invalide.';
    }

    $statut = $data['statut'] ?? '';
    if (!in_array($statut, ['paie_bloquee', 'en_mission', 'fin_de_contrat'])) {
        $errors['statut'] = 'Le statut est invalide.';
    }

    if ($typeContrat === 'CDD' && empty($data['date_debut'])) {
        $errors['date_debut'] = 'La date de début est requise pour calculer la fin de contrat CDD.';
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

function prepareInterimaireData(array $data): array
{
    return [
        'nom'            => trim($data['nom']),
        'prenom'         => trim($data['prenom']),
        'cin'            => trim($data['cin']),
        'fonction'       => trim($data['fonction']),
        'telephone'      => trim($data['telephone'] ?? '') ?: null,
        'email'          => trim($data['email'] ?? '') ?: null,
        'adresse'        => trim($data['adresse'] ?? '') ?: null,
        'date_naissance' => !empty($data['date_naissance']) ? $data['date_naissance'] : null,
        'entreprise_id'  => !empty($data['entreprise_id']) ? (int) $data['entreprise_id'] : null,
        'type_contrat'   => $data['type_contrat'],
        'date_debut'     => $data['date_debut'],
        'date_fin'       => computeDateFin($data),
        'salaire'        => (float) $data['salaire'],
        'type_salaire'   => $data['type_salaire'],
        'mode_paiement'  => $data['mode_paiement'],
        'statut'         => $data['statut'],
        'competences'    => trim($data['competences'] ?? '') ?: null,
    ];
}

function createInterimaire(array $data): int
{
    $pdo = getPDO();
    $prepared = prepareInterimaireData($data);

    $stmt = $pdo->prepare('
        INSERT INTO interimaires (
            nom, prenom, cin, fonction, telephone, email, adresse, date_naissance,
            entreprise_id, type_contrat, date_debut, date_fin, salaire,
            type_salaire, mode_paiement, statut, competences
        ) VALUES (
            :nom, :prenom, :cin, :fonction, :telephone, :email, :adresse, :date_naissance,
            :entreprise_id, :type_contrat, :date_debut, :date_fin, :salaire,
            :type_salaire, :mode_paiement, :statut, :competences
        )
    ');
    $stmt->execute($prepared);

    return (int) $pdo->lastInsertId();
}

function updateInterimaire(int $id, array $data): bool
{
    $pdo = getPDO();
    $prepared = prepareInterimaireData($data);
    $prepared['id'] = $id;

    $stmt = $pdo->prepare('
        UPDATE interimaires SET
            nom = :nom, prenom = :prenom, cin = :cin, fonction = :fonction,
            telephone = :telephone, email = :email, adresse = :adresse,
            date_naissance = :date_naissance, entreprise_id = :entreprise_id,
            type_contrat = :type_contrat, date_debut = :date_debut, date_fin = :date_fin,
            salaire = :salaire, type_salaire = :type_salaire, mode_paiement = :mode_paiement,
            statut = :statut, competences = :competences
        WHERE id = :id
    ');

    return $stmt->execute($prepared);
}

function deleteInterimaire(int $id): array
{
    $pdo = getPDO();
    $stmt = $pdo->prepare('DELETE FROM interimaires WHERE id = :id');
    $stmt->execute(['id' => $id]);
    return ['success' => true, 'message' => 'Intérimaire supprimé avec succès.'];
}

function countInterimairesEnMission(): int
{
    syncExpiredContracts();
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT COUNT(*) FROM interimaires WHERE statut = 'en_mission'");
    return (int) $stmt->fetchColumn();
}

function countInterimairesTotal(): int
{
    $pdo = getPDO();
    $stmt = $pdo->query('SELECT COUNT(*) FROM interimaires');
    return (int) $stmt->fetchColumn();
}

function countFinContratProche(int $jours = 30): int
{
    syncExpiredContracts();
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM interimaires
        WHERE type_contrat IN ('CDD', 'ANAPEC')
          AND date_fin IS NOT NULL
          AND date_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :jours DAY)
          AND statut != 'fin_de_contrat'
    ");
    $stmt->execute(['jours' => $jours]);
    return (int) $stmt->fetchColumn();
}

function getInterimairesFinContratProche(int $jours = 30): array
{
    syncExpiredContracts();
    $pdo = getPDO();
    $stmt = $pdo->prepare("
        SELECT i.*, e.nom_entreprise
        FROM interimaires i
        LEFT JOIN entreprises e ON e.id = i.entreprise_id
        WHERE i.type_contrat IN ('CDD', 'ANAPEC')
          AND i.date_fin IS NOT NULL
          AND i.date_fin BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :jours DAY)
          AND i.statut != 'fin_de_contrat'
        ORDER BY i.date_fin ASC
    ");
    $stmt->execute(['jours' => $jours]);
    return $stmt->fetchAll();
}

function getInterimairesParSecteur(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('
        SELECT COALESCE(e.secteur_activite, "Non assigné") AS secteur_activite, COUNT(i.id) AS total
        FROM interimaires i
        LEFT JOIN entreprises e ON e.id = i.entreprise_id
        GROUP BY COALESCE(e.secteur_activite, "Non assigné")
        ORDER BY total DESC
    ');
    return $stmt->fetchAll();
}

function getInterimairesParMois(): array
{
    $pdo = getPDO();
    $stmt = $pdo->query('
        SELECT DATE_FORMAT(date_debut, "%Y-%m") AS mois, COUNT(*) AS total
        FROM interimaires
        GROUP BY DATE_FORMAT(date_debut, "%Y-%m")
        ORDER BY mois ASC
    ');
    return $stmt->fetchAll();
}

function getStatutLabel(string $statut): string
{
    return match ($statut) {
        'paie_bloquee'  => 'Paie bloquée',
        'en_mission'    => 'En mission',
        'fin_de_contrat'=> 'Fin de contrat',
        default         => $statut,
    };
}

function getTypeContratLabel(string $type): string
{
    return match ($type) {
        'CDI'    => 'CDI',
        'CDD'    => 'CDD',
        'ANAPEC' => 'ANAPEC',
        default  => $type,
    };
}

function getTypeSalaireLabel(string $type): string
{
    return $type === 'horaire' ? 'Horaire' : 'Mensuel';
}

function getModePaiementLabel(string $mode): string
{
    return match ($mode) {
        'espece'   => 'Espèce',
        'virement' => 'Virement',
        'cheque'   => 'Chèque',
        default    => $mode,
    };
}
