/**
 * GIRH - Validation côté client
 */

document.addEventListener('DOMContentLoaded', function () {
    initLoginValidation();
    initInterimaireValidation();
    initEntrepriseValidation();
    initFeuilleValidation();
    initLiveSearch();
});

function showFieldError(input, message) {
    input.classList.add('is-invalid');
    let errorEl = input.parentElement.querySelector('.js-error');
    if (!errorEl) {
        errorEl = document.createElement('span');
        errorEl.className = 'error-message js-error';
        input.parentElement.appendChild(errorEl);
    }
    errorEl.textContent = message;
}

function clearFieldError(input) {
    input.classList.remove('is-invalid');
    const errorEl = input.parentElement.querySelector('.js-error');
    if (errorEl) errorEl.remove();
}

function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function validatePhone(phone) {
    if (!phone) return true;
    return /^[0-9+\s\-]{8,20}$/.test(phone);
}

function initLoginValidation() {
    const form = document.getElementById('loginForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const email = form.querySelector('#email');
        const password = form.querySelector('#password');
        [email, password].forEach(clearFieldError);

        if (!email.value.trim()) { showFieldError(email, 'L\'email est obligatoire.'); valid = false; }
        else if (!validateEmail(email.value.trim())) { showFieldError(email, 'Email invalide.'); valid = false; }
        if (!password.value) { showFieldError(password, 'Mot de passe obligatoire.'); valid = false; }

        if (!valid) e.preventDefault();
    });
}

function initInterimaireValidation() {
    const form = document.getElementById('interimaireForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const fields = ['nom', 'prenom', 'cin', 'fonction', 'date_debut', 'salaire'];
        fields.forEach(function (id) {
            const el = form.querySelector('#' + id);
            if (el) clearFieldError(el);
        });

        const nom = form.querySelector('#nom');
        const prenom = form.querySelector('#prenom');
        const cin = form.querySelector('#cin');
        const fonction = form.querySelector('#fonction');
        const dateDebut = form.querySelector('#date_debut');
        const salaire = form.querySelector('#salaire');

        if (!nom.value.trim()) { showFieldError(nom, 'Nom obligatoire.'); valid = false; }
        if (!prenom.value.trim()) { showFieldError(prenom, 'Prénom obligatoire.'); valid = false; }
        if (!cin.value.trim() || cin.value.trim().length < 5) { showFieldError(cin, 'CIN invalide.'); valid = false; }
        if (!fonction.value.trim()) { showFieldError(fonction, 'Fonction obligatoire.'); valid = false; }
        if (!dateDebut.value) { showFieldError(dateDebut, 'Date de début obligatoire.'); valid = false; }
        if (!salaire.value || parseFloat(salaire.value) <= 0) { showFieldError(salaire, 'Salaire invalide.'); valid = false; }

        if (!valid) e.preventDefault();
    });
}

function initEntrepriseValidation() {
    const form = document.getElementById('entrepriseForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const nom = form.querySelector('#nom_entreprise');
        const secteur = form.querySelector('#secteur_activite');
        [nom, secteur].forEach(clearFieldError);

        if (!nom.value.trim()) { showFieldError(nom, 'Nom obligatoire.'); valid = false; }
        if (!secteur.value.trim()) { showFieldError(secteur, 'Secteur obligatoire.'); valid = false; }
        if (!valid) e.preventDefault();
    });
}

function initFeuilleValidation() {
    const form = document.getElementById('feuilleForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const interimaire = form.querySelector('#interimaire_id');
        const date = form.querySelector('#date');
        const heures = form.querySelector('#heures_travaillees');
        [interimaire, date, heures].forEach(clearFieldError);

        if (!interimaire.value) { showFieldError(interimaire, 'Sélectionnez un intérimaire.'); valid = false; }
        if (!date.value) { showFieldError(date, 'Date obligatoire.'); valid = false; }
        const h = parseFloat(heures.value);
        if (!heures.value || h <= 0 || h > 24) { showFieldError(heures, 'Heures entre 0 et 24.'); valid = false; }
        if (!valid) e.preventDefault();
    });
}

function initLiveSearch() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('interimairesTable');
    if (!searchInput || !table) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach(function (row) {
            if (row.cells.length <= 1) return;
            row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });
}
