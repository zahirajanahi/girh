<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$search = trim($_GET['search'] ?? '');
$disponibilite = $_GET['disponibilite'] ?? '';
$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $result = deleteInterimaire((int) $_POST['delete_id']);
    redirect('interimaires.php?message=' . urlencode($result['message']) . '&type=' . ($result['success'] ? 'success' : 'error'));
}

$interimaires = getAllInterimaires($search ?: null, $disponibilite ?: null);

$pageTitle = 'Intérimaires';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="page-actions">
    <form method="GET" class="search-form" id="searchForm">
        <input type="text" name="search" id="searchInput" class="form-control"
               placeholder="Rechercher par nom, CIN, email..." value="<?= e($search) ?>">
        <select name="disponibilite" id="disponibiliteFilter" class="form-control">
            <option value="">Toutes disponibilités</option>
            <option value="disponible" <?= $disponibilite === 'disponible' ? 'selected' : '' ?>>Disponible</option>
            <option value="en_mission" <?= $disponibilite === 'en_mission' ? 'selected' : '' ?>>En mission</option>
            <option value="indisponible" <?= $disponibilite === 'indisponible' ? 'selected' : '' ?>>Indisponible</option>
        </select>
        <button type="submit" class="btn btn-primary">Filtrer</button>
        <?php if ($search || $disponibilite): ?>
            <a href="interimaires.php" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
    </form>
    <a href="interimaire_ajouter.php" class="btn btn-primary">+ Ajouter</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table" id="interimairesTable">
            <thead>
                <tr>
                    <th>Nom complet</th>
                    <th>CIN</th>
                    <th>Téléphone</th>
                    <th>Compétences</th>
                    <th>Disponibilité</th>
                    <th>Inscription</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($interimaires)): ?>
                    <tr><td colspan="7" class="text-center">Aucun intérimaire trouvé.</td></tr>
                <?php else: ?>
                    <?php foreach ($interimaires as $i): ?>
                        <tr>
                            <td><strong><?= e($i['prenom'] . ' ' . $i['nom']) ?></strong></td>
                            <td><?= e($i['cin']) ?></td>
                            <td><?= e($i['telephone'] ?? '-') ?></td>
                            <td><?= e($i['competences'] ?? '-') ?></td>
                            <td><span class="badge badge-<?= e($i['disponibilite']) ?>"><?= e(getDisponibiliteLabel($i['disponibilite'])) ?></span></td>
                            <td><?= formatDate($i['date_inscription']) ?></td>
                            <td class="actions">
                                <a href="interimaire_detail.php?id=<?= (int) $i['id'] ?>" class="btn btn-sm btn-outline" title="Détail">👁</a>
                                <a href="interimaire_modifier.php?id=<?= (int) $i['id'] ?>" class="btn btn-sm btn-outline" title="Modifier">✏️</a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Supprimer cet intérimaire ?');">
                                    <input type="hidden" name="delete_id" value="<?= (int) $i['id'] ?>">
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
