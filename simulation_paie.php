<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';
require_once __DIR__ . '/php/feuilles_temps.php';
require_once __DIR__ . '/php/paie.php';

requireLogin();

$id = (int) ($_GET['id'] ?? 0);
$interimaire = getInterimaireById($id);

if (!$interimaire) {
    redirect('feuilles_temps.php?message=' . urlencode('Intérimaire introuvable.') . '&type=error');
}

$moisCourant = date('Y-m');
$premierJour = date('Y-m-01');
$dernierJour = date('Y-m-t');
$heuresPeriode = getTotalHeuresPeriode($id, $premierJour, $dernierJour);

$salaireBaseDefault = (float) $interimaire['salaire'];
if ($interimaire['type_salaire'] === 'horaire') {
    $salaireBaseDefault = $heuresPeriode * (float) $interimaire['salaire'];
}

$resultat = null;
$formData = [
    'salaire_base'       => $salaireBaseDefault,
    'heures'             => $heuresPeriode,
    'primes'             => 0,
    'personnes_charge'   => 0,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['primes'] = (float) ($_POST['primes'] ?? 0);
    $formData['personnes_charge'] = (int) ($_POST['personnes_charge'] ?? 0);

    if ($interimaire['type_salaire'] === 'horaire') {
        $formData['heures'] = (float) ($_POST['heures'] ?? 0);
        $formData['salaire_base'] = $formData['heures'] * (float) $interimaire['salaire'];
    } else {
        $formData['salaire_base'] = (float) ($_POST['salaire_base'] ?? $salaireBaseDefault);
    }

    $resultat = calculerPaieMaroc2026(
        $formData['salaire_base'],
        $formData['primes'],
        $formData['personnes_charge']
    );
}

$pageTitle = 'Simulation paie — ' . $interimaire['prenom'] . ' ' . $interimaire['nom'];
include __DIR__ . '/includes/header.php';
?>

<div class="page-actions">
    <a href="feuilles_temps.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Feuilles de temps</a>
    <a href="interimaire_detail.php?id=<?= $id ?>" class="btn btn-outline"><i class="fa-solid fa-user"></i> Fiche intérimaire</a>
</div>

<div class="simulation-layout">
    <div class="card">
        <div class="card-header"><h3>Paramètres de simulation</h3></div>
        <div class="card-body">
            <dl class="detail-list" style="margin-bottom:20px">
                <dt>Salarié</dt><dd><?= e($interimaire['prenom'] . ' ' . $interimaire['nom']) ?></dd>
                <dt>CIN</dt><dd><?= e($interimaire['cin']) ?></dd>
                <dt>Fonction</dt><dd><?= e($interimaire['fonction']) ?></dd>
                <dt>Type salaire</dt><dd><?= e(getTypeSalaireLabel($interimaire['type_salaire'])) ?></dd>
            </dl>

            <form method="POST" class="form">
                <?php if ($interimaire['type_salaire'] === 'horaire'): ?>
                    <div class="form-group">
                        <label>Taux horaire</label>
                        <input type="text" class="form-control" value="<?= formatMontant((float) $interimaire['salaire']) ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="heures">Heures travaillées (période)</label>
                        <input type="number" id="heures" name="heures" step="0.5" min="0"
                               class="form-control" value="<?= e($formData['heures']) ?>">
                        <small class="form-hint">Récupérées depuis feuilles_temps (<?= date('m/Y') ?>)</small>
                    </div>
                    <div class="form-group">
                        <label>Salaire de base calculé</label>
                        <input type="text" class="form-control"
                               value="<?= formatMontant($formData['salaire_base']) ?>" readonly>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label for="salaire_base">Salaire de base mensuel (DH)</label>
                        <input type="number" id="salaire_base" name="salaire_base" step="0.01" min="0"
                               class="form-control" value="<?= e($formData['salaire_base']) ?>">
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="primes">Primes / indemnités (DH)</label>
                    <input type="number" id="primes" name="primes" step="0.01" min="0"
                           class="form-control" value="<?= e($formData['primes']) ?>">
                </div>
                <div class="form-group">
                    <label for="personnes_charge">Personnes à charge (max 6)</label>
                    <input type="number" id="personnes_charge" name="personnes_charge" min="0" max="6"
                           class="form-control" value="<?= e($formData['personnes_charge']) ?>">
                </div>
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fa-solid fa-calculator"></i> Calculer la paie
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (!$resultat): ?>
                <div class="text-center text-muted" style="padding:40px 0">
                    <i class="fa-solid fa-file-invoice" style="font-size:48px;color:#E2E8F0;margin-bottom:16px;display:block"></i>
                    Renseignez les paramètres et cliquez sur « Calculer la paie » pour générer le bulletin.
                </div>
            <?php else: ?>
                <div class="bulletin">
                    <div class="bulletin-header">
                        <h3>Bulletin de paie simulé</h3>
                        <p><?= e($interimaire['prenom'] . ' ' . $interimaire['nom']) ?> — Période <?= date('m/Y') ?></p>
                    </div>
                    <table class="bulletin-table">
                        <tr class="section-header"><td colspan="2">Éléments de rémunération</td></tr>
                        <tr><td>Salaire de base</td><td><?= formatMontant($resultat['salaire_base']) ?></td></tr>
                        <tr><td>Primes / indemnités</td><td><?= formatMontant($resultat['primes']) ?></td></tr>
                        <tr><td><strong>Salaire brut</strong></td><td><strong><?= formatMontant($resultat['brut']) ?></strong></td></tr>

                        <tr class="section-header"><td colspan="2">Cotisations salariales</td></tr>
                        <tr class="deduct"><td>CNSS (4,48 % × min(Brut, 6 000))</td><td>- <?= formatMontant($resultat['cnss']) ?></td></tr>
                        <tr class="deduct"><td>AMO (2,26 % × Brut)</td><td>- <?= formatMontant($resultat['amo']) ?></td></tr>
                        <tr class="deduct"><td>IPE (0,19 % × min(Brut, 6 000))</td><td>- <?= formatMontant($resultat['ipe']) ?></td></tr>
                        <tr class="deduct"><td><strong>Total cotisations</strong></td><td><strong>- <?= formatMontant($resultat['total_cotisations']) ?></strong></td></tr>

                        <tr class="section-header"><td colspan="2">Impôt sur le revenu</td></tr>
                        <tr><td>Frais professionnels (<?= e($resultat['regime_frais']) ?>)</td><td>- <?= formatMontant($resultat['frais_pro']) ?></td></tr>
                        <tr><td>Salaire net imposable (mensuel)</td><td><?= formatMontant($resultat['sni_mensuel']) ?></td></tr>
                        <tr><td>SNI annuel</td><td><?= formatMontant($resultat['sni_annuel']) ?></td></tr>
                        <tr><td>Tranche IR applicable</td><td><?= e($resultat['ir_tranche']) ?> (<?= $resultat['ir_taux'] ?> %)</td></tr>
                        <tr class="deduct"><td>IR brut annuel</td><td>- <?= formatMontant($resultat['ir_brut_annuel']) ?></td></tr>
                        <tr class="deduct"><td>Déduction charges de famille (<?= $resultat['personnes_charge'] ?> pers.)</td><td>+ <?= formatMontant($resultat['deduction_famille']) ?></td></tr>
                        <tr class="deduct"><td>IR net mensuel</td><td>- <?= formatMontant($resultat['ir_net_mensuel']) ?></td></tr>
                    </table>

                    <div class="net-pay-box">
                        <span class="net-label">Salaire Net à Payer</span>
                        <span class="net-value"><?= formatMontant($resultat['net_a_payer']) ?></span>
                    </div>
                    <p class="pay-mode-tag">
                        Mode de paiement : <strong><?= e(getModePaiementLabel($interimaire['mode_paiement'])) ?></strong>
                    </p>
                    <div class="disclaimer">
                        Simulation pédagogique PFE — Barème CNSS/AMO/IR marocain 2026. Ne pas utiliser pour une paie réelle sans validation expert-comptable (CGI, CNSS).
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
