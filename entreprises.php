<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/entreprises.php';

requireLogin();

$search = trim($_GET['search'] ?? '');
$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $result = deleteEntreprise((int) $_POST['delete_id']);
    redirect('entreprises.php?message=' . urlencode($result['message']) . '&type=' . ($result['success'] ? 'success' : 'error'));
}

$entreprises = getAllEntreprises($search ?: null);

$pageTitle = 'Entreprises';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="page-actions">
    <form method="GET" class="search-form">
        <input type="text" name="search" class="form-control" placeholder="Rechercher une entreprise..."
               value="<?= e($search) ?>">
        <button type="submit" class="btn btn-primary">Rechercher</button>
        <?php if ($search): ?>
            <a href="entreprises.php" class="btn btn-outline">Réinitialiser</a>
        <?php endif; ?>
    </form>
    <a href="entreprise_ajouter.php" class="btn btn-primary">+ Ajouter</a>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Entreprise</th>
                    <th>Secteur</th>
                    <th>Contact principal</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($entreprises)): ?>
                    <tr><td colspan="6" class="text-center">Aucune entreprise trouvée.</td></tr>
                <?php else: ?>
                    <?php foreach ($entreprises as $ent): ?>
                        <tr>
                            <td><strong><?= e($ent['nom_entreprise']) ?></strong></td>
                            <td><?= e($ent['secteur_activite']) ?></td>
                            <td><?= e($ent['contact_principal'] ?? '-') ?></td>
                            <td><?= e($ent['telephone'] ?? '-') ?></td>
                            <td><?= e($ent['email'] ?? '-') ?></td>
                            <td class="actions">
                                <a href="entreprise_modifier.php?id=<?= (int) $ent['id'] ?>" class="btn btn-sm btn-outline" title="Modifier">✏️</a>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Supprimer cette entreprise ?');">
                                    <input type="hidden" name="delete_id" value="<?= (int) $ent['id'] ?>">
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
