/**
 * GIRH - Graphiques du tableau de bord (Chart.js)
 */

document.addEventListener('DOMContentLoaded', function () {
    initSecteursChart();
    initMoisChart();
});

function parseChartData(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    try {
        const labels = JSON.parse(canvas.dataset.labels || '[]');
        const values = JSON.parse(canvas.dataset.values || '[]');
        return { canvas, labels, values };
    } catch (e) {
        console.error('Erreur parsing données graphique:', e);
        return null;
    }
}

function initSecteursChart() {
    const data = parseChartData('chartSecteurs');
    if (!data || typeof Chart === 'undefined') return;

    new Chart(data.canvas, {
        type: 'doughnut',
        data: {
            labels: data.labels.length ? data.labels : ['Aucune donnée'],
            datasets: [{
                data: data.values.length ? data.values : [1],
                backgroundColor: [
                    '#1e5a9e', '#2c7be5', '#28a745', '#6f42c1',
                    '#f0ad4e', '#dc3545', '#17a2b8', '#fd7e14'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, usePointStyle: true }
                }
            }
        }
    });
}

function initMoisChart() {
    const data = parseChartData('chartMois');
    if (!data || typeof Chart === 'undefined') return;

    const formattedLabels = data.labels.map(function (mois) {
        if (!mois) return mois;
        const parts = mois.split('-');
        if (parts.length === 2) {
            const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            return months[parseInt(parts[1], 10) - 1] + ' ' + parts[0];
        }
        return mois;
    });

    new Chart(data.canvas, {
        type: 'bar',
        data: {
            labels: formattedLabels.length ? formattedLabels : ['Aucune donnée'],
            datasets: [{
                label: 'Missions créées',
                data: data.values.length ? data.values : [0],
                backgroundColor: 'rgba(30, 90, 158, 0.8)',
                borderColor: '#1e5a9e',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}
