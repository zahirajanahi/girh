<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/missions.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$interimaire = getInterimaireById($id);

if (!$interimaire) {
    redirect('interimaires.php?message=' . urlencode('Intérimaire introuvable.') . '&type=error');
}

$missions = getMissionsByInterimaire($id);

$pageTitle = 'Fiche intérimaire';
include __DIR__ . '/includes/header.php';
?>

<div class="page-actions">
    <a href="interimaires.php" class="btn btn-outline">← Retour à la liste</a>
    <a href="interimaire_modifier.php?id=<?= $id ?>" class="btn btn-primary">Modifier</a>
</div>

<div class="detail-grid">
    <div class="card">
        <div class="card-header"><h3>Informations personnelles</h3></div>
        <div class="card-body">
            <dl class="detail-list">
                <dt>Nom complet</dt>
                <dd><?= e($interimaire['prenom'] . ' ' . $interimaire['nom']) ?></dd>
                <dt>CIN</dt>
                <dd><?= e($interimaire['cin']) ?></dd>
                <dt>Date de naissance</dt>
                <dd><?= formatDate($interimaire['date_naissance']) ?></dd>
                <dt>Téléphone</dt>
                <dd><?= e($interimaire['telephone'] ?? '-') ?></dd>
                <dt>Email</dt>
                <dd><?= e($interimaire['email'] ?? '-') ?></dd>
                <dt>Adresse</dt>
                <dd><?= e($interimaire['adresse'] ?? '-') ?></dd>
                <dt>Compétences</dt>
                <dd><?= e($interimaire['competences'] ?? '-') ?></dd>
                <dt>Disponibilité</dt>
                <dd><span class="badge badge-<?= e($interimaire['disponibilite']) ?>"><?= e(getDisponibiliteLabel($interimaire['disponibilite'])) ?></span></dd>
                <dt>Date d'inscription</dt>
                <dd><?= formatDate($interimaire['date_inscription']) ?></dd>
            </dl>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Historique des missions</h3></div>
        <div class="card-body table-responsive">
            <?php if (empty($missions)): ?>
                <p class="text-muted">Aucune mission enregistrée.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Poste</th>
                            <th>Entreprise</th>
                            <th>Période</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($missions as $m): ?>
                            <tr>
                                <td><a href="mission_detail.php?id=<?= (int) $m['id'] ?>"><?= e($m['poste']) ?></a></td>
                                <td><?= e($m['nom_entreprise']) ?></td>
                                <td><?= formatDate($m['date_debut']) ?> — <?= formatDate($m['date_fin']) ?></td>
                                <td><span class="badge badge-statut-<?= e($m['statut']) ?>"><?= e(getStatutLabel($m['statut'])) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
