<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/entreprises.php';

requireLogin();

$stats = [
    'interimaires'  => countInterimairesTotal(),
    'en_mission'    => countInterimairesEnMission(),
    'entreprises'   => countEntreprises(),
    'fin_contrat'   => countFinContratProche(30),
];

$parSecteur = getInterimairesParSecteur();
$parMois = getInterimairesParMois();

$pageTitle = 'Tableau de bord';
$extraJs = ['https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js', 'js/dashboard.js'];
include __DIR__ . '/includes/header.php';
?>

<?php if ($stats['fin_contrat'] > 0): ?>
<a href="alerte_fin_contrat.php" class="alert-card">
    <div class="alert-card-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="alert-card-content">
        <strong><?= $stats['fin_contrat'] ?> intérimaire(s) en fin de contrat dans les 30 prochains jours</strong>
        <span>Cliquez pour voir la liste et préparer les STC</span>
    </div>
    <div class="alert-card-arrow"><i class="fa-solid fa-chevron-right"></i></div>
</a>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-wrap teal"><i class="fa-solid fa-users"></i></div>
        <div>
            <span class="stat-value"><?= $stats['interimaires'] ?></span>
            <span class="stat-label">Intérimaires total</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap green"><i class="fa-solid fa-briefcase"></i></div>
        <div>
            <span class="stat-value"><?= $stats['en_mission'] ?></span>
            <span class="stat-label">En mission</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap blue"><i class="fa-solid fa-building"></i></div>
        <div>
            <span class="stat-value"><?= $stats['entreprises'] ?></span>
            <span class="stat-label">Entreprises clientes</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon-wrap amber"><i class="fa-solid fa-calendar-xmark"></i></div>
        <div>
            <span class="stat-value"><?= $stats['fin_contrat'] ?></span>
            <span class="stat-label">Fins de contrat (30j)</span>
        </div>
    </div>
</div>

<div class="charts-grid">
    <div class="card">
        <div class="card-header"><h3>Répartition par secteur d'activité</h3></div>
        <div class="card-body chart-container">
            <canvas id="chartSecteurs"
                    data-labels='<?= e(json_encode(array_column($parSecteur, 'secteur_activite'))) ?>'
                    data-values='<?= e(json_encode(array_map('intval', array_column($parSecteur, 'total')))) ?>'>
            </canvas>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Arrivées par mois</h3></div>
        <div class="card-body chart-container">
            <canvas id="chartMois"
                    data-labels='<?= e(json_encode(array_column($parMois, 'mois'))) ?>'
                    data-values='<?= e(json_encode(array_map('intval', array_column($parMois, 'total')))) ?>'>
            </canvas>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3>Accès rapide</h3></div>
    <div class="card-body quick-actions">
        <a href="interimaire_ajouter.php" class="btn btn-outline"><i class="fa-solid fa-user-plus"></i> Nouvel intérimaire</a>
        <a href="entreprise_ajouter.php" class="btn btn-outline"><i class="fa-solid fa-building"></i> Nouvelle entreprise</a>
        <a href="feuilles_temps.php" class="btn btn-outline"><i class="fa-solid fa-clock"></i> Feuilles de temps</a>
        <?php if ($stats['fin_contrat'] > 0): ?>
            <a href="alerte_fin_contrat.php" class="btn btn-outline"><i class="fa-solid fa-file-export"></i> Export STC</a>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
