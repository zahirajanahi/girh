<?php
require_once __DIR__ . '/php/auth.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $errors = validateLogin($_POST);

    if (empty($errors)) {
        $result = login($email, $password);
        if ($result['success']) {
            redirect('index.php');
        }
        $errors['general'] = $result['message'];
    }
}

$pageTitle = 'Connexion';
include __DIR__ . '/includes/header.php';
?>

<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <div class="login-logo"><i class="fa-solid fa-users-gear"></i></div>
            <h2><?= e(APP_NAME) ?></h2>
            <p><?= e(APP_SUBTITLE) ?></p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-error"><?= e($errors['general']) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="form" id="loginForm" novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>"
                       class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                       placeholder="admin@girh.ma" required>
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= e($errors['email']) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password"
                       class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                       placeholder="••••••••" required>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-message"><?= e($errors['password']) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary btn-block"><i class="fa-solid fa-right-to-bracket"></i> Se connecter</button>
        </form>

        <div class="login-demo">
            <p><strong>Comptes de démonstration :</strong></p>
            <p>Admin : admin@girh.ma / admin123</p>
            <p>Agent : agent@girh.ma / agent123</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
