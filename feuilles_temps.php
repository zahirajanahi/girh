<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/feuilles_temps.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$message = $_GET['message'] ?? '';
$messageType = $_GET['type'] ?? 'success';
$selectedId = (int) ($_GET['interimaire_id'] ?? 0);

$errors = [];
$data = [
    'interimaire_id'     => $selectedId ?: '',
    'date'               => date('Y-m-d'),
    'heures_travaillees' => '',
];

$salaries = getInterimairesForFeuillesTemps();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        deleteFeuilleTemps((int) $_POST['delete_id']);
        redirect('feuilles_temps.php?message=' . urlencode('Saisie supprimée.') . '&type=success');
    }

    $data = array_merge($data, $_POST);
    $errors = validateFeuilleTemps($data);

    if (empty($errors)) {
        createFeuilleTemps($data);
        redirect('feuilles_temps.php?message=' . urlencode('Heures enregistrées.') . '&type=success');
    }
}

$pageTitle = 'Feuilles de temps';
include __DIR__ . '/includes/header.php';
?>

<?php if ($message): ?>
    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>">
        <i class="fa-solid fa-circle-check"></i> <?= e($message) ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><h3><i class="fa-solid fa-plus"></i> Saisir des heures</h3></div>
    <div class="card-body">
        <form method="POST" class="form" id="feuilleForm" style="max-width:600px" novalidate>
            <div class="form-group">
                <label for="interimaire_id">Intérimaire <span class="required">*</span></label>
                <select id="interimaire_id" name="interimaire_id" class="form-control" required>
                    <option value="">— Sélectionner —</option>
                    <?php foreach ($salaries as $s): ?>
                        <option value="<?= (int) $s['id'] ?>" <?= (string) $data['interimaire_id'] === (string) $s['id'] ? 'selected' : '' ?>>
                            <?= e($s['prenom'] . ' ' . $s['nom']) ?> — <?= e($s['fonction']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label for="date">Date <span class="required">*</span></label>
                    <input type="date" id="date" name="date" class="form-control" value="<?= e($data['date']) ?>" required>
                </div>
                <div class="form-group">
                    <label for="heures_travaillees">Heures <span class="required">*</span></label>
                    <input type="number" id="heures_travaillees" name="heures_travaillees" step="0.5" min="0.5" max="24"
                           class="form-control" value="<?= e($data['heures_travaillees']) ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Enregistrer</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Salariés — cliquez pour simuler la paie</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($salaries)): ?>
            <p class="text-muted text-center">Aucun intérimaire enregistré.</p>
        <?php else: ?>
            <table class="table" id="salariesTable">
                <thead>
                    <tr>
                        <th>Salarié</th>
                        <th>CIN</th>
                        <th>Fonction</th>
                        <th>Type de salaire</th>
                        <th>Mode de paiement</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($salaries as $s): ?>
                        <tr class="clickable-row" onclick="window.location='simulation_paie.php?id=<?= (int) $s['id'] ?>'">
                            <td><strong><?= e($s['prenom'] . ' ' . $s['nom']) ?></strong></td>
                            <td><?= e($s['cin']) ?></td>
                            <td><?= e($s['fonction']) ?></td>
                            <td><?= e(getTypeSalaireLabel($s['type_salaire'])) ?></td>
                            <td><?= e(getModePaiementLabel($s['mode_paiement'])) ?></td>
                            <td><i class="fa-solid fa-calculator" style="color:#14B8A6"></i></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script src="js/validation.js"></script>
<?php include __DIR__ . '/includes/footer.php'; ?>
