<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/missions.php';
require_once __DIR__ . '/php/feuilles_temps.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$mission = getMissionById($id);

if (!$mission) {
    redirect('missions.php?message=' . urlencode('Mission introuvable.') . '&type=error');
}

$feuilles = getFeuillesByMission($id);
$totalHeures = getTotalHeuresMission($id);
$salaireDu = getSalaireDuMission($id);

$pageTitle = 'Détail mission';
include __DIR__ . '/includes/header.php';
?>

<div class="page-actions">
    <a href="missions.php" class="btn btn-outline">← Retour à la liste</a>
    <a href="mission_modifier.php?id=<?= $id ?>" class="btn btn-primary">Modifier</a>
    <a href="feuilles_temps.php?mission_id=<?= $id ?>" class="btn btn-outline">Saisir des heures</a>
</div>

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><h3>Informations de la mission</h3></div>
        <div class="card-body">
            <dl class="detail-list">
                <dt>Poste</dt>
                <dd><?= e($mission['poste']) ?></dd>
                <dt>Entreprise</dt>
                <dd><?= e($mission['nom_entreprise']) ?> (<?= e($mission['secteur_activite']) ?>)</dd>
                <dt>Contact</dt>
                <dd><?= e($mission['contact_principal'] ?? '-') ?></dd>
                <dt>Intérimaire</dt>
                <dd>
                    <a href="interimaire_detail.php?id=<?= (int) $mission['interimaire_id'] ?>">
                        <?= e($mission['interimaire_prenom'] . ' ' . $mission['interimaire_nom']) ?>
                    </a> (<?= e($mission['interimaire_cin']) ?>)
                </dd>
                <dt>Période</dt>
                <dd><?= formatDate($mission['date_debut']) ?> — <?= formatDate($mission['date_fin']) ?></dd>
                <dt>Salaire horaire</dt>
                <dd><?= formatMontant((float) $mission['salaire_horaire']) ?></dd>
                <dt>Statut</dt>
                <dd><span class="badge badge-statut-<?= e($mission['statut']) ?>"><?= e(getStatutLabel($mission['statut'])) ?></span></dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Rémunération</h3></div>
        <div class="card-body">
            <div class="salary-summary">
                <div class="salary-item">
                    <span class="salary-label">Total heures travaillées</span>
                    <span class="salary-value"><?= number_format($totalHeures, 2, ',', ' ') ?> h</span>
                </div>
                <div class="salary-item highlight">
                    <span class="salary-label">Salaire dû</span>
                    <span class="salary-value"><?= formatMontant($salaireDu) ?></span>
                </div>
                <p class="form-hint">Calcul : heures travaillées × salaire horaire (<?= formatMontant((float) $mission['salaire_horaire']) ?>)</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Feuilles de temps</h3></div>
    <div class="card-body table-responsive">
        <?php if (empty($feuilles)): ?>
            <p class="text-muted">Aucune heure saisie pour cette mission.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Heures travaillées</th>
                        <th>Montant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($feuilles as $f): ?>
                        <tr>
                            <td><?= formatDate($f['date']) ?></td>
                            <td><?= number_format((float) $f['heures_travaillees'], 2, ',', ' ') ?> h</td>
                            <td><?= formatMontant((float) $f['heures_travaillees'] * (float) $mission['salaire_horaire']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
