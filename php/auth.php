<?php
/**
 * Fonctions d'authentification et gestion de session
 */

require_once __DIR__ . '/config.php';

initSession();

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

/**
 * Exige une connexion, redirige vers login sinon
 */
function requireLogin(): void
{
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

/**
 * Retourne les informations de l'utilisateur connecté
 */
function getCurrentUser(): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    return [
        'id'    => $_SESSION['user_id'],
        'nom'   => $_SESSION['user_nom'],
        'email' => $_SESSION['user_email'],
        'role'  => $_SESSION['user_role'],
    ];
}

/**
 * Tente de connecter un utilisateur
 */
function login(string $email, string $password): array
{
    $pdo = getPDO();

    $stmt = $pdo->prepare('SELECT id, nom, email, mot_de_passe_hash, role FROM utilisateurs WHERE email = :email LIMIT 1');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['mot_de_passe_hash'])) {
        return ['success' => false, 'message' => 'Email ou mot de passe incorrect.'];
    }

    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_nom']   = $user['nom'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role']  = $user['role'];

    return ['success' => true, 'message' => 'Connexion réussie.'];
}

/**
 * Déconnecte l'utilisateur
 */
function logout(): void
{
    initSession();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}

/**
 * Valide les données de connexion côté serveur
 */
function validateLogin(array $data): array
{
    $errors = [];

    if (empty(trim($data['email'] ?? ''))) {
        $errors['email'] = 'L\'email est obligatoire.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'L\'email n\'est pas valide.';
    }

    if (empty($data['password'] ?? '')) {
        $errors['password'] = 'Le mot de passe est obligatoire.';
    }

    return $errors;
}
