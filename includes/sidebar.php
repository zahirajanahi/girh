<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">G</div>
        <div class="brand-text">
            <strong><?= e(APP_NAME) ?></strong>
            <small>Gestion RH</small>
        </div>
    </div>
    <nav class="sidebar-nav">
        <a href="index.php" class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>">
            <span class="nav-icon">📊</span> Tableau de bord
        </a>
        <a href="interimaires.php" class="nav-link <?= in_array($currentPage, ['interimaires.php', 'interimaire_ajouter.php', 'interimaire_modifier.php', 'interimaire_detail.php']) ? 'active' : '' ?>">
            <span class="nav-icon">👥</span> Intérimaires
        </a>
        <a href="entreprises.php" class="nav-link <?= in_array($currentPage, ['entreprises.php', 'entreprise_ajouter.php', 'entreprise_modifier.php']) ? 'active' : '' ?>">
            <span class="nav-icon">🏢</span> Entreprises
        </a>
        <a href="missions.php" class="nav-link <?= in_array($currentPage, ['missions.php', 'mission_ajouter.php', 'mission_modifier.php', 'mission_detail.php']) ? 'active' : '' ?>">
            <span class="nav-icon">📋</span> Missions
        </a>
        <a href="feuilles_temps.php" class="nav-link <?= $currentPage === 'feuilles_temps.php' ? 'active' : '' ?>">
            <span class="nav-icon">⏱️</span> Feuilles de temps
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php" class="nav-link logout-link">
            <span class="nav-icon">🚪</span> Déconnexion
        </a>
    </div>
</aside>
