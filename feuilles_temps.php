<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/feuilles_temps.php';
require_once __DIR__ . '/php/missions.php';

requireLogin();

$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';
$selectedMissionId = (int) ($_GET['mission_id'] ?? 0);

$errors = [];
$data = [
    'mission_id' => $selectedMissionId ?: '',
    'date' => date('Y-m-d'),
    'heures_travaillees' => '',
];

$missionsActives = getMissionsActivesForSelect();
$feuilles = getAllFeuillesTemps();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        deleteFeuilleTemps((int) $_POST['delete_id']);
        redirect('feuilles_temps.php?message=' . urlencode('Feuille de temps supprimée.') . '&type=success');
    }

    $data = array_merge($data, $_POST);
    $errors = validateFeuilleTemps($data);

    if (empty($errors)) {
        createFeuilleTemps($data);
        redirect('feuilles_temps.php?message=' . urlencode('Heures enregistrées avec succès.') . '&type=success&mission_id=' . (int) $data['mission_id']);
    }
}

$pageTitle = 'Feuilles de temps';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>"><?= e($message) ?></div>
<?php endif; ?>

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><h3>Saisir des heures</h3></div>
        <div class="card-body">
            <?php if (empty($missionsActives)): ?>
                <div class="alert alert-warning">Aucune mission active disponible pour la saisie d'heures.</div>
            <?php else: ?>
                <form method="POST" class="form" id="feuilleForm" novalidate>
                    <div class="form-group">
                        <label for="mission_id">Mission <span class="required">*</span></label>
                        <select id="mission_id" name="mission_id" class="form-control <?= isset($errors['mission_id']) ? 'is-invalid' : '' ?>" required>
                            <option value="">— Sélectionner —</option>
                            <?php foreach ($missionsActives as $m): ?>
                                <option value="<?= (int) $m['id'] ?>" <?= (string) $data['mission_id'] === (string) $m['id'] ? 'selected' : '' ?>>
                                    <?= e($m['nom_entreprise']) ?> — <?= e($m['poste']) ?> (<?= e($m['prenom'] . ' ' . $m['nom']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['mission_id'])): ?><span class="error-message"><?= e($errors['mission_id']) ?></span><?php endif; ?>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date">Date <span class="required">*</span></label>
                            <input type="date" id="date" name="date" class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($data['date']) ?>" required>
                            <?php if (isset($errors['date'])): ?><span class="error-message"><?= e($errors['date']) ?></span><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="heures_travaillees">Heures travaillées <span class="required">*</span></label>
                            <input type="number" id="heures_travaillees" name="heures_travaillees" step="0.5" min="0.5" max="24"
                                   class="form-control <?= isset($errors['heures_travaillees']) ? 'is-invalid' : '' ?>"
                                   value="<?= e($data['heures_travaillees']) ?>" required>
                            <?php if (isset($errors['heures_travaillees'])): ?><span class="error-message"><?= e($errors['heures_travaillees']) ?></span><?php endif; ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Historique des saisies</h3></div>
    <div class="card-body table-responsive">
        <?php if (empty($feuilles)): ?>
            <p class="text-muted text-center">Aucune feuille de temps enregistrée.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Mission</th>
                        <th>Entreprise</th>
                        <th>Intérimaire</th>
                        <th>Heures</th>
                        <th>Montant</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feuilles as $f): ?>
                        <tr>
                            <td><?= formatDate($f['date']) ?></td>
                            <td><a href="mission_detail.php?id=<?= (int) $f['mission_id'] ?>"><?= e($f['poste']) ?></a></td>
                            <td><?= e($f['nom_entreprise']) ?></td>
                            <td><?= e($f['interimaire_prenom'] . ' ' . $f['interimaire_nom']) ?></td>
                            <td><?= number_format((float) $f['heures_travaillees'], 2, ',', ' ') ?> h</td>
                            <td><?= formatMontant((float) $f['heures_travaillees'] * (float) $f['salaire_horaire']) ?></td>
                            <td>
                                <form method="POST" class="inline-form" onsubmit="return confirm('Supprimer cette saisie ?');">
                                    <input type="hidden" name="delete_id" value="<?= (int) $f['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">🗑</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="js/validation.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
