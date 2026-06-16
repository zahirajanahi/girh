<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/entreprises.php';
require_once __DIR__ . '/php/missions.php';

requireLogin();

$stats = [
    'interimaires' => countInterimairesActifs(),
    'missions'     => countMissionsEnCours(),
    'entreprises'  => countEntreprises(),
];

$missionsParSecteur = getMissionsParSecteur();
$missionsParMois = getMissionsParMois();
$alertesMissions = getMissionsFinProche(7);

$pageTitle = 'Tableau de bord';
$extraJs = ['https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', 'js/dashboard.js'];
include __DIR__ . '/includes/header.php';
?>

<?php if (!empty($alertesMissions)): ?>
<div class="alert alert-warning alert-banner">
    <strong>⚠ Missions se terminant dans les 7 prochains jours :</strong>
    <ul class="alert-list">
        <?php foreach ($alertesMissions as $alerte): ?>
            <li>
                <a href="mission_detail.php?id=<?= (int) $alerte['id'] ?>">
                    <?= e($alerte['poste']) ?> — <?= e($alerte['nom_entreprise']) ?>
                    (<?= e($alerte['interimaire_prenom'] . ' ' . $alerte['interimaire_nom']) ?>)
                    — Fin : <?= formatDate($alerte['date_fin']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card stat-blue">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['interimaires'] ?></span>
            <span class="stat-label">Intérimaires actifs</span>
        </div>
    </div>
    <div class="stat-card stat-green">
        <div class="stat-icon">📋</div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['missions'] ?></span>
            <span class="stat-label">Missions en cours</span>
        </div>
    </div>
    <div class="stat-card stat-purple">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
            <span class="stat-value"><?= $stats['entreprises'] ?></span>
            <span class="stat-label">Entreprises clientes</span>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="card">
        <div class="card-header">
            <h3>Répartition des missions par secteur</h3>
        </div>
        <div class="card-body chart-container">
            <canvas id="chartSecteurs"
                    data-labels='<?= e(json_encode(array_column($missionsParSecteur, 'secteur_activite'))) ?>'
                    data-values='<?= e(json_encode(array_map('intval', array_column($missionsParSecteur, 'total')))) ?>'>
            </canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3>Évolution des missions par mois</h3>
        </div>
        <div class="card-body chart-container">
            <canvas id="chartMois"
                    data-labels='<?= e(json_encode(array_column($missionsParMois, 'mois'))) ?>'
                    data-values='<?= e(json_encode(array_map('intval', array_column($missionsParMois, 'total')))) ?>'>
            </canvas>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>Accès rapide</h3>
    </div>
    <div class="card-body quick-actions">
        <a href="interimaire_ajouter.php" class="btn btn-outline">+ Nouvel intérimaire</a>
        <a href="entreprise_ajouter.php" class="btn btn-outline">+ Nouvelle entreprise</a>
        <a href="mission_ajouter.php" class="btn btn-outline">+ Nouvelle mission</a>
        <a href="feuilles_temps.php" class="btn btn-outline">+ Saisir des heures</a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
