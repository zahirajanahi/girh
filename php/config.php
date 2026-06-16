<?php
/**
 * Configuration de la connexion à la base de données GIRH
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'girh');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('APP_NAME', 'GIRH');
define('APP_SUBTITLE', 'Gestion des Intérimaires RH');

/**
 * Retourne une instance PDO connectée à la base de données
 */
function getPDO(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage()));
        }
    }

    return $pdo;
}

/**
 * Démarre la session si elle n'est pas déjà active
 */
function initSession(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Redirige vers une URL et arrête l'exécution
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Échappe une chaîne pour l'affichage HTML
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Formate une date au format français
 */
function formatDate(?string $date): string
{
    if (!$date) {
        return '-';
    }
    $dt = DateTime::createFromFormat('Y-m-d', $date);
    return $dt ? $dt->format('d/m/Y') : $date;
}

/**
 * Formate un montant en dirhams
 */
function formatMontant(float $montant): string
{
    return number_format($montant, 2, ',', ' ') . ' DH';
}
