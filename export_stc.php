<?php
require_once __DIR__ . '/php/auth.php';
require_once __DIR__ . '/php/interimaires.php';

requireLogin();

$interimaires = getInterimairesFinContratProche(30);
$format = $_GET['format'] ?? 'csv';

if ($format === 'html') {
    header('Content-Type: text/html; charset=utf-8');
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>État STC — GIRH</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
        <style>
            body { font-family: Inter, sans-serif; padding: 40px; color: #1E293B; }
            h1 { font-size: 20px; margin-bottom: 4px; }
            .meta { color: #64748B; font-size: 13px; margin-bottom: 32px; }
            table { width: 100%; border-collapse: collapse; font-size: 13px; }
            th { background: #F8FAFC; padding: 10px 12px; text-align: left; border-bottom: 2px solid #E2E8F0; font-size: 11px; text-transform: uppercase; color: #64748B; }
            td { padding: 10px 12px; border-bottom: 1px solid #F1F5F9; }
            .footer { margin-top: 40px; font-size: 11px; color: #94A3B8; }
            @media print { body { padding: 20px; } }
        </style>
    </head>
    <body>
        <h1>État des intérimaires en fin de contrat — préparation STC</h1>
        <p class="meta">GIRH — Édité le <?= date('d/m/Y à H:i') ?> — <?= count($interimaires) ?> salarié(s)</p>
        <table>
            <thead>
                <tr>
                    <th>Nom</th><th>Prénom</th><th>CIN</th><th>Fonction</th>
                    <th>Société</th><th>Contrat</th><th>Date début</th><th>Date fin</th><th>Salaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interimaires as $i): ?>
                    <tr>
                        <td><?= htmlspecialchars($i['nom']) ?></td>
                        <td><?= htmlspecialchars($i['prenom']) ?></td>
                        <td><?= htmlspecialchars($i['cin']) ?></td>
                        <td><?= htmlspecialchars($i['fonction']) ?></td>
                        <td><?= htmlspecialchars($i['nom_entreprise'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($i['type_contrat']) ?></td>
                        <td><?= formatDate($i['date_debut']) ?></td>
                        <td><?= formatDate($i['date_fin']) ?></td>
                        <td><?= number_format((float) $i['salaire'], 2, ',', ' ') ?> DH</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p class="footer">Document généré par GIRH — Simulation pédagogique PFE. À valider par le service RH avant traitement STC.</p>
        <script>window.onload = function() { window.print(); };</script>
    </body>
    </html>
    <?php
    exit;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="stc_fin_contrat_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['Nom', 'Prénom', 'CIN', 'Fonction', 'Société', 'Type contrat', 'Date début', 'Date fin', 'Salaire (DH)', 'Statut'], ';');

foreach ($interimaires as $i) {
    fputcsv($output, [
        $i['nom'],
        $i['prenom'],
        $i['cin'],
        $i['fonction'],
        $i['nom_entreprise'] ?? '',
        $i['type_contrat'],
        $i['date_debut'],
        $i['date_fin'],
        number_format((float) $i['salaire'], 2, '.', ''),
        getStatutLabel($i['statut']),
    ], ';');
}

fclose($output);
exit;
