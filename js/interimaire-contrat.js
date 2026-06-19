/**
 * GIRH - Logique contrat CDD/CDI/ANAPEC (date fin automatique)
 */

document.addEventListener('DOMContentLoaded', function () {
    const typeContrat = document.getElementById('type_contrat');
    const dateDebut = document.getElementById('date_debut');
    const dateFin = document.getElementById('date_fin');
    const dateFinHint = document.getElementById('dateFinHint');
    const unlockBtn = document.getElementById('unlockDateFin');

    if (!typeContrat || !dateFin) return;

    let dateFinLocked = false;

    function addMonths(dateStr, months) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        const d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
        d.setMonth(d.getMonth() + months);
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + day;
    }

    function applyContratRules() {
        const type = typeContrat.value;

        if (type === 'CDI') {
            dateFin.value = '';
            dateFin.disabled = true;
            dateFin.readOnly = false;
            dateFinLocked = false;
            unlockBtn.style.display = 'none';
            dateFinHint.textContent = 'CDI : pas de date de fin.';
            return;
        }

        if (type === 'CDD') {
            dateFin.disabled = false;
            if (dateDebut.value) {
                dateFin.value = addMonths(dateDebut.value, 6);
            }
            dateFin.readOnly = true;
            dateFinLocked = true;
            unlockBtn.style.display = 'inline-flex';
            dateFinHint.textContent = 'CDD : date de fin = date début + 6 mois (modifiable manuellement).';
            return;
        }

        if (type === 'ANAPEC') {
            dateFin.disabled = false;
            dateFin.readOnly = false;
            dateFinLocked = false;
            unlockBtn.style.display = 'none';
            dateFinHint.textContent = 'ANAPEC : saisissez la date de fin selon la convention.';
        }
    }

    unlockBtn.addEventListener('click', function () {
        dateFin.readOnly = false;
        dateFinLocked = false;
        dateFin.focus();
        unlockBtn.style.display = 'none';
    });

    typeContrat.addEventListener('change', applyContratRules);
    dateDebut.addEventListener('change', function () {
        if (typeContrat.value === 'CDD' && dateFinLocked) {
            dateFin.value = addMonths(dateDebut.value, 6);
        }
    });

    applyContratRules();
});
