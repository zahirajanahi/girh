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
                <span></span><span></span><span></span>
            </button>
            <div class="topbar-info">
                <h1 class="page-title"><?= e($pageTitle) ?></h1>
                <div class="user-info">
                    <span class="user-name"><?= e($currentUser['nom']) ?></span>
                    <span class="user-role badge badge-<?= e($currentUser['role']) ?>"><?= e(ucfirst($currentUser['role'])) ?></span>
                </div>
            </div>
        </header>
        <main class="content-area">
<?php else: ?>
<div class="auth-layout">
<?php endif; ?>
