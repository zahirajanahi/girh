<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/missions.php';

requireLogin();

$search = trim($_GET['search'] ?? '');
$statut = $_GET['statut'] ?? '';
$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $result = deleteMission((int) $_POST['delete_id']);
    redirect('missions.php?message=' . urlencode($result['message']) . '&type=' . ($result['success'] ? 'success' : 'error'));
}

$missions = getAllMissions($search ?: null, $statut ?: null);

$pageTitle = 'Missions';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="page-actions">
    <form method="GET" class="search-form">
        <input type="text" name="search" class="form-control" placeholder="Rechercher..."
               value="<?= e($search) ?>">
        <select name="statut" class="form-control">
            <option value="">Tous statuts</option>
            <option value="en_cours" <?= $statut === 'en_cours' ? 'selected' : '' ?>>En cours</option>
            <option value="terminee" <?= $statut === 'terminee' ? 'selected' : '' ?>>Terminée</option>
            <option value="renouvelee" <?= $statut === 'renouvelee' ? 'selected' : '' ?>>Renouvelée</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <?php if ($search || $statut): ?>
            <a href="missions.php" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
    </form>
    <a href="mission_ajouter.php" class="btn btn-primary">+ Nouvelle mission</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Poste</th>
                    <th>Entreprise</th>
                    <th>Intérimaire</th>
                    <th>Période</th>
                    <th>Salaire/h</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($missions)): ?>
                    <tr><td colspan="7" class="text-center">Aucune mission trouvée.</td></tr>
                <?php else: ?>
                    <?php foreach ($missions as $m): ?>
                        <tr>
                            <td><strong><?= e($m['poste']) ?></strong></td>
                            <td><?= e($m['nom_entreprise']) ?></td>
                            <td><?= e($m['interimaire_prenom'] . ' ' . $m['interimaire_nom']) ?></td>
                            <td><?= formatDate($m['date_debut']) ?> — <?= formatDate($m['date_fin']) ?></td>
                            <td><?= formatMontant((float) $m['salaire_horaire']) ?></td>
                            <td><span class="badge badge-statut-<?= e($m['statut']) ?>"><?= e(getStatutLabel($m['statut'])) ?></span></td>
                            <td class="actions">
                                <a href="mission_detail.php?id=<?= (int) $m['id'] ?>" class="btn btn-sm btn-outline" title="Détail">👁</a>
                                <a href="mission_modifier.php?id=<?= (int) $m['id'] ?>" class="btn btn-sm btn-outline" title="Modifier">✏️</a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Supprimer cette mission ?');">
                                    <input type="hidden" name="delete_id" value="<?= (int) $m['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">🗑</button>
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
