<?php
/**
 * Simulation de paie — Barème marocain 2026 (PFE)
 *
 * NOTE IMPORTANTE : ces taux et tranches sont ceux en vigueur en 2026 selon le barème
 * CNSS/AMO/IR marocain ; il s'agit d'une simulation pédagogique pour un projet PFE,
 * à ne pas utiliser tel quel pour une paie réelle sans vérification auprès d'un
 * expert-comptable ou des textes officiels (CGI, CNSS).
 */

require_once __DIR__ . '/config.php';

function calculerIRBrutAnnuel(float $sniAnnuel): array
{
    if ($sniAnnuel <= 40000) {
        return ['taux' => 0, 'deduction' => 0, 'montant' => 0, 'tranche' => '0 – 40 000 DH'];
    }
    if ($sniAnnuel <= 60000) {
        $montant = $sniAnnuel * 0.10 - 4000;
        return ['taux' => 10, 'deduction' => 4000, 'montant' => $montant, 'tranche' => '40 001 – 60 000 DH'];
    }
    if ($sniAnnuel <= 80000) {
        $montant = $sniAnnuel * 0.20 - 10000;
        return ['taux' => 20, 'deduction' => 10000, 'montant' => $montant, 'tranche' => '60 001 – 80 000 DH'];
    }
    if ($sniAnnuel <= 100000) {
        $montant = $sniAnnuel * 0.30 - 18000;
        return ['taux' => 30, 'deduction' => 18000, 'montant' => $montant, 'tranche' => '80 001 – 100 000 DH'];
    }
    if ($sniAnnuel <= 180000) {
        $montant = $sniAnnuel * 0.34 - 22000;
        return ['taux' => 34, 'deduction' => 22000, 'montant' => $montant, 'tranche' => '100 001 – 180 000 DH'];
    }

    $montant = $sniAnnuel * 0.37 - 27400;
    return ['taux' => 37, 'deduction' => 27400, 'montant' => $montant, 'tranche' => 'Au-delà de 180 000 DH'];
}

function calculerPaieMaroc2026(float $salaireBase, float $primes, int $personnesCharge): array
{
    $personnesCharge = min(max($personnesCharge, 0), 6);
    $brut = $salaireBase + $primes;

    $cnss = 0.0448 * min($brut, 6000);
    $amo  = 0.0226 * $brut;
    $ipe  = 0.0019 * min($brut, 6000);
    $totalCotisations = $cnss + $amo + $ipe;

    if ($brut <= 6500) {
        $tauxFrais = 0.35;
        $plafondFrais = 2916.67;
        $regimeFrais = 'Régime ≤ 6 500 DH (35 %, plafond 2 916,67 DH)';
    } else {
        $tauxFrais = 0.20;
        $plafondFrais = 2500;
        $regimeFrais = 'Régime > 6 500 DH (20 %, plafond 2 500 DH)';
    }

    $fraisPro = min($brut * $tauxFrais, $plafondFrais);
    $sniMensuel = $brut - $totalCotisations - $fraisPro;
    $sniAnnuel = $sniMensuel * 12;

    $irInfo = calculerIRBrutAnnuel($sniAnnuel);
    $irBrutAnnuel = max(0, $irInfo['montant']);
    $deductionFamille = $personnesCharge * 500;
    $irNetAnnuel = max(0, $irBrutAnnuel - $deductionFamille);
    $irNetMensuel = $irNetAnnuel / 12;
    $netAPayer = $brut - $totalCotisations - $irNetMensuel;

    return [
        'salaire_base'        => $salaireBase,
        'primes'              => $primes,
        'brut'                => $brut,
        'cnss'                => $cnss,
        'amo'                 => $amo,
        'ipe'                 => $ipe,
        'total_cotisations'   => $totalCotisations,
        'taux_frais'          => $tauxFrais * 100,
        'plafond_frais'       => $plafondFrais,
        'regime_frais'        => $regimeFrais,
        'frais_pro'           => $fraisPro,
        'sni_mensuel'         => $sniMensuel,
        'sni_annuel'          => $sniAnnuel,
        'ir_tranche'          => $irInfo['tranche'],
        'ir_taux'             => $irInfo['taux'],
        'ir_deduction'        => $irInfo['deduction'],
        'ir_brut_annuel'      => $irBrutAnnuel,
        'personnes_charge'    => $personnesCharge,
        'deduction_famille'   => $deductionFamille,
        'ir_net_annuel'       => $irNetAnnuel,
        'ir_net_mensuel'      => $irNetMensuel,
        'net_a_payer'         => $netAPayer,
    ];
}
