<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$interimaires = getInterimairesFinContratProche(30);

$pageTitle = 'Alertes fin de contrat';
include __DIR__ . '/includes/header.php';
?>

<div class="page-actions">
    <a href="index.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Tableau de bord</a>
    <?php if (!empty($interimaires)): ?>
        <a href="export_stc.php" class="btn btn-primary">
            <i class="fa-solid fa-download"></i> Télécharger l'état STC (CSV)
        </a>
        <a href="export_stc.php?format=html" class="btn btn-outline" target="_blank">
            <i class="fa-solid fa-print"></i> Version imprimable
        </a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fa-solid fa-triangle-exclamation" style="color:#F59E0B"></i>
            Intérimaires en fin de contrat — 30 prochains jours</h3>
    </div>
    <div class="card-body table-responsive">
        <?php if (empty($interimaires)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i>
                Aucun intérimaire en fin de contrat dans les 30 prochains jours.
            </div>
        <?php else: ?>
            <p class="text-muted" style="margin-bottom:16px">
                <?= count($interimaires) ?> intérimaire(s) concerné(s) — préparation des Soldes de Tout Compte (STC).
            </p>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>CIN</th>
                        <th>Fonction</th>
                        <th>Société</th>
                        <th>Contrat</th>
                        <th>Date fin</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interimaires as $i): ?>
                        <tr>
                            <td><strong><?= e($i['nom']) ?></strong></td>
                            <td><?= e($i['prenom']) ?></td>
                            <td><?= e($i['cin']) ?></td>
                            <td><?= e($i['fonction']) ?></td>
                            <td><?= e($i['nom_entreprise'] ?? '—') ?></td>
                            <td><span class="badge badge-<?= e($i['type_contrat']) ?>"><?= e($i['type_contrat']) ?></span></td>
                            <td><?= formatDate($i['date_fin']) ?></td>
                            <td><span class="badge badge-<?= e($i['statut']) ?>"><?= e(getStatutLabel($i['statut'])) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
