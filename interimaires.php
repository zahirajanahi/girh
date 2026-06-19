<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$search = trim($_GET['search'] ?? '');
$entrepriseId = (int) ($_GET['entreprise_id'] ?? 0);
$typeContrat = $_GET['type_contrat'] ?? '';
$statut = $_GET['statut'] ?? '';
$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';

require_once __DIR__ . '/php/entreprises.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $result = deleteInterimaire((int) $_POST['delete_id']);
    redirect('interimaires.php?message=' . urlencode($result['message']) . '&type=' . ($result['success'] ? 'success' : 'error'));
}

$interimaires = getAllInterimaires(
    $search ?: null,
    $entrepriseId ?: null,
    $typeContrat ?: null,
    $statut ?: null
);
$entreprises = getAllEntreprisesForSelect();

$pageTitle = 'Intérimaires';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>">
        <i class="fa-solid fa-<?= $messageType === 'error' ? 'circle-xmark' : 'circle-check' ?>"></i>
        <?= e($message) ?>
    </div>
<?php endif; ?>

<div class="page-actions">
    <form method="GET" class="search-form" id="searchForm">
        <input type="text" name="search" id="searchInput" class="form-control"
               placeholder="Rechercher..." value="<?= e($search) ?>">
        <select name="entreprise_id" class="form-control">
            <option value="">Toutes sociétés</option>
            <?php foreach ($entreprises as $ent): ?>
                <option value="<?= (int) $ent['id'] ?>" <?= $entrepriseId === (int) $ent['id'] ? 'selected' : '' ?>>
                    <?= e($ent['nom_entreprise']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="type_contrat" class="form-control">
            <option value="">Tous contrats</option>
            <option value="CDI" <?= $typeContrat === 'CDI' ? 'selected' : '' ?>>CDI</option>
            <option value="CDD" <?= $typeContrat === 'CDD' ? 'selected' : '' ?>>CDD</option>
            <option value="ANAPEC" <?= $typeContrat === 'ANAPEC' ? 'selected' : '' ?>>ANAPEC</option>
        </select>
        <select name="statut" class="form-control">
            <option value="">Tous statuts</option>
            <option value="en_mission" <?= $statut === 'en_mission' ? 'selected' : '' ?>>En mission</option>
            <option value="paie_bloquee" <?= $statut === 'paie_bloquee' ? 'selected' : '' ?>>Paie bloquée</option>
            <option value="fin_de_contrat" <?= $statut === 'fin_de_contrat' ? 'selected' : '' ?>>Fin de contrat</option>
        </select>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filtrer</button>
        <?php if ($search || $entrepriseId || $typeContrat || $statut): ?>
            <a href="interimaires.php" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
    </form>
    <a href="interimaire_ajouter.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Ajouter</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table" id="interimairesTable">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>CIN</th>
                    <th>Fonction</th>
                    <th>Société</th>
                    <th>Contrat</th>
                    <th>Début</th>
                    <th>Fin</th>
                    <th>Salaire</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($interimaires)): ?>
                    <tr><td colspan="11" class="text-center text-muted">Aucun intérimaire trouvé.</td></tr>
                <?php else: ?>
                    <?php foreach ($interimaires as $i): ?>
                        <tr>
                            <td><strong><?= e($i['nom']) ?></strong></td>
                            <td><?= e($i['prenom']) ?></td>
                            <td><?= e($i['cin']) ?></td>
                            <td><?= e($i['fonction']) ?></td>
                            <td><?= e($i['nom_entreprise'] ?? '—') ?></td>
                            <td><span class="badge badge-<?= e($i['type_contrat']) ?>"><?= e(getTypeContratLabel($i['type_contrat'])) ?></span></td>
                            <td><?= formatDate($i['date_debut']) ?></td>
                            <td><?= formatDate($i['date_fin']) ?></td>
                            <td><?= formatMontant((float) $i['salaire']) ?><?= $i['type_salaire'] === 'horaire' ? '/h' : '' ?></td>
                            <td><span class="badge badge-<?= e($i['statut']) ?>"><?= e(getStatutLabel($i['statut'])) ?></span></td>
                            <td class="actions">
                                <a href="interimaire_detail.php?id=<?= (int) $i['id'] ?>" class="btn btn-sm btn-outline" title="Détail"><i class="fa-solid fa-eye"></i></a>
                                <a href="interimaire_modifier.php?id=<?= (int) $i['id'] ?>" class="btn btn-sm btn-outline" title="Modifier"><i class="fa-solid fa-pen"></i></a>
                                <a href="simulation_paie.php?id=<?= (int) $i['id'] ?>" class="btn btn-sm btn-outline" title="Paie"><i class="fa-solid fa-calculator"></i></a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Supprimer cet intérimaire ?');">
                                    <input type="hidden" name="delete_id" value="<?= (int) $i['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
