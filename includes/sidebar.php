<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="fa-solid fa-users-gear"></i></div>
        <div class="brand-text">
            <strong><?= e(APP_NAME) ?></strong>
            <small>Gestion RH</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <i class="fa-solid fa-chart-pie"></i> Tableau de bord
        </a>
        <a href="interimaires.php" class="nav-link <?= in_array($currentPage, ['interimaires.php', 'interimaire_ajouter.php', 'interimaire_modifier.php', 'interimaire_detail.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-user-group"></i> Intérimaires
        </a>
        <a href="entreprises.php" class="nav-link <?= in_array($currentPage, ['entreprises.php', 'entreprise_ajouter.php', 'entreprise_modifier.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-building"></i> Entreprises
        </a>
        <a href="feuilles_temps.php" class="nav-link <?= in_array($currentPage, ['feuilles_temps.php', 'simulation_paie.php']) ? 'active' : '' ?>">
            <i class="fa-solid fa-clock"></i> Feuilles de temps
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-link logout-link">
            <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
        </a>
    </div>
</aside>
