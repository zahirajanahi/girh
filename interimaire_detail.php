<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/feuilles_temps.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$interimaire = getInterimaireById($id);

if (!$interimaire) {
    redirect('interimaires.php?message=' . urlencode('Intérimaire introuvable.') . '&type=error');
}

$feuilles = getFeuillesByInterimaire($id);
$totalHeures = getTotalHeuresInterimaire($id);

$pageTitle = 'Fiche intérimaire';
include __DIR__ . '/includes/header.php';
?>

<div class="page-actions">
    <a href="interimaires.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    <a href="interimaire_modifier.php?id=<?= $id ?>" class="btn btn-primary"><i class="fa-solid fa-pen"></i> Modifier</a>
    <a href="simulation_paie.php?id=<?= $id ?>" class="btn btn-outline"><i class="fa-solid fa-calculator"></i> Simulation paie</a>
</div>

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><h3>Informations personnelles</h3></div>
        <div class="card-body">
            <dl class="detail-list">
                <dt>Nom complet</dt><dd><?= e($interimaire['prenom'] . ' ' . $interimaire['nom']) ?></dd>
                <dt>CIN</dt><dd><?= e($interimaire['cin']) ?></dd>
                <dt>Fonction</dt><dd><?= e($interimaire['fonction']) ?></dd>
                <dt>Téléphone</dt><dd><?= e($interimaire['telephone'] ?? '—') ?></dd>
                <dt>Email</dt><dd><?= e($interimaire['email'] ?? '—') ?></dd>
                <dt>Adresse</dt><dd><?= e($interimaire['adresse'] ?? '—') ?></dd>
            </dl>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Contrat & rémunération</h3></div>
        <div class="card-body">
            <dl class="detail-list">
                <dt>Société</dt><dd><?= e($interimaire['nom_entreprise'] ?? '—') ?></dd>
                <dt>Type contrat</dt><dd><span class="badge badge-<?= e($interimaire['type_contrat']) ?>"><?= e(getTypeContratLabel($interimaire['type_contrat'])) ?></span></dd>
                <dt>Période</dt><dd><?= formatDate($interimaire['date_debut']) ?> — <?= formatDate($interimaire['date_fin']) ?></dd>
                <dt>Salaire</dt><dd><?= formatMontant((float) $interimaire['salaire']) ?> (<?= e(getTypeSalaireLabel($interimaire['type_salaire'])) ?>)</dd>
                <dt>Paiement</dt><dd><?= e(getModePaiementLabel($interimaire['mode_paiement'])) ?></dd>
                <dt>Statut</dt><dd><span class="badge badge-<?= e($interimaire['statut']) ?>"><?= e(getStatutLabel($interimaire['statut'])) ?></span></dd>
            </dl>
        </div>
    </div>
</div>

<?php if ($interimaire['type_salaire'] === 'horaire'): ?>
<div class="card">
    <div class="card-header"><h3>Heures travaillées (<?= number_format($totalHeures, 2, ',', ' ') ?> h total)</h3></div>
    <div class="card-body table-responsive">
        <?php if (empty($feuilles)): ?>
            <p class="text-muted">Aucune heure enregistrée.</p>
        <?php else: ?>
            <table class="table">
                <thead><tr><th>Date</th><th>Heures</th></tr></thead>
                <tbody>
                    <?php foreach ($feuilles as $f): ?>
                        <tr>
                            <td><?= formatDate($f['date']) ?></td>
                            <td><?= number_format((float) $f['heures_travaillees'], 2, ',', ' ') ?> h</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
