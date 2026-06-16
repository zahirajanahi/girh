/**
 * GIRH - Scripts globaux de l'application
 */

document.addEventListener('DOMContentLoaded', function () {
    initSidebarToggle();
    autoHideAlerts();
});

function initSidebarToggle() {
    const toggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (!toggle || !sidebar) return;

    toggle.addEventListener('click', function () {
        sidebar.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768 &&
            sidebar.classList.contains('open') &&
            !sidebar.contains(e.target) &&
            !toggle.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
}

function autoHideAlerts() {
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(function (alert) {
        if (!alert.classList.contains('alert-banner')) {
            setTimeout(function () {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function () { alert.remove(); }, 500);
            }, 5000);
        }
    });
}
