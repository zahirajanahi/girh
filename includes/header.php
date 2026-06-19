<?php
if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> - <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <?php if (!empty($extraCss)): ?>
        <?php foreach ($extraCss as $css): ?>
            <link rel="stylesheet" href="<?= e($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
<?php if (isLoggedIn()): ?>
<div class="app-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>
    <div class="main-content">
        <header class="topbar">
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
            <div class="topbar-info">
                <h1 class="page-title"><?= e($pageTitle) ?></h1>
                <div class="topbar-actions">
                    <div class="user-info">
                        <div class="user-avatar"><?= e(strtoupper(substr($currentUser['nom'], 0, 1))) ?></div>
                        <div class="user-meta">
                            <span class="user-name"><?= e($currentUser['nom']) ?></span>
                            <span class="user-role"><?= e(ucfirst($currentUser['role'])) ?></span>
                        </div>
                    </div>
                    <a href="logout.php" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
                    </a>
                </div>
            </div>
        </header>
        <main class="content-area">
<?php else: ?>
<div class="auth-layout">
<?php endif; ?>
