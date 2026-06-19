/**
 * GIRH - Graphiques tableau de bord
 */

document.addEventListener('DOMContentLoaded', function () {
    initSecteursChart();
    initMoisChart();
});

function parseChartData(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    try {
        return {
            canvas,
            labels: JSON.parse(canvas.dataset.labels || '[]'),
            values: JSON.parse(canvas.dataset.values || '[]'),
        };
    } catch (e) {
        return null;
    }
}

const CHART_COLORS = ['#14B8A6', '#0EA5E9', '#6366F1', '#F59E0B', '#10B981', '#EF4444', '#8B5CF6', '#F97316'];

function initSecteursChart() {
    const data = parseChartData('chartSecteurs');
    if (!data || typeof Chart === 'undefined') return;

    new Chart(data.canvas, {
        type: 'doughnut',
        data: {
            labels: data.labels.length ? data.labels : ['Aucune donnée'],
            datasets: [{
                data: data.values.length ? data.values : [1],
                backgroundColor: CHART_COLORS,
                borderWidth: 3,
                borderColor: '#FFFFFF',
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, font: { family: 'Inter', size: 12 } } },
            },
        },
    });
}

function initMoisChart() {
    const data = parseChartData('chartMois');
    if (!data || typeof Chart === 'undefined') return;

    const labels = data.labels.map(function (mois) {
        if (!mois) return mois;
        const p = mois.split('-');
        if (p.length === 2) {
            const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            return months[parseInt(p[1], 10) - 1] + ' ' + p[0];
        }
        return mois;
    });

    new Chart(data.canvas, {
        type: 'bar',
        data: {
            labels: labels.length ? labels : ['Aucune donnée'],
            datasets: [{
                label: 'Arrivées',
                data: data.values.length ? data.values : [0],
                backgroundColor: 'rgba(20, 184, 166, 0.85)',
                borderColor: '#14B8A6',
                borderWidth: 0,
                borderRadius: 8,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Inter' } }, grid: { color: '#F1F5F9' } },
                x: { ticks: { font: { family: 'Inter' } }, grid: { display: false } },
            },
        },
    });
}
