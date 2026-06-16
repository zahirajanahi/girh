/**
 * GIRH - Validation côté client des formulaires
 */

document.addEventListener('DOMContentLoaded', function () {
    initLoginValidation();
    initInterimaireValidation();
    initEntrepriseValidation();
    initMissionValidation();
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
    if (errorEl) {
        errorEl.remove();
    }
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

        clearFieldError(email);
        clearFieldError(password);

        if (!email.value.trim()) {
            showFieldError(email, 'L\'email est obligatoire.');
            valid = false;
        } else if (!validateEmail(email.value.trim())) {
            showFieldError(email, 'L\'email n\'est pas valide.');
            valid = false;
        }

        if (!password.value) {
            showFieldError(password, 'Le mot de passe est obligatoire.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

function initInterimaireValidation() {
    const form = document.getElementById('interimaireForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const nom = form.querySelector('#nom');
        const prenom = form.querySelector('#prenom');
        const cin = form.querySelector('#cin');
        const email = form.querySelector('#email');
        const telephone = form.querySelector('#telephone');

        [nom, prenom, cin, email, telephone].forEach(clearFieldError);

        if (!nom.value.trim()) { showFieldError(nom, 'Le nom est obligatoire.'); valid = false; }
        if (!prenom.value.trim()) { showFieldError(prenom, 'Le prénom est obligatoire.'); valid = false; }
        if (!cin.value.trim() || cin.value.trim().length < 5) {
            showFieldError(cin, 'Le CIN doit contenir au moins 5 caractères.');
            valid = false;
        }
        if (email.value && !validateEmail(email.value.trim())) {
            showFieldError(email, 'L\'email n\'est pas valide.');
            valid = false;
        }
        if (telephone.value && !validatePhone(telephone.value.trim())) {
            showFieldError(telephone, 'Le téléphone n\'est pas valide.');
            valid = false;
        }

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
        const email = form.querySelector('#email');
        const telephone = form.querySelector('#telephone');

        [nom, secteur, email, telephone].forEach(clearFieldError);

        if (!nom.value.trim()) { showFieldError(nom, 'Le nom est obligatoire.'); valid = false; }
        if (!secteur.value.trim()) { showFieldError(secteur, 'Le secteur est obligatoire.'); valid = false; }
        if (email.value && !validateEmail(email.value.trim())) {
            showFieldError(email, 'L\'email n\'est pas valide.');
            valid = false;
        }
        if (telephone.value && !validatePhone(telephone.value.trim())) {
            showFieldError(telephone, 'Le téléphone n\'est pas valide.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

function initMissionValidation() {
    const form = document.getElementById('missionForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const entreprise = form.querySelector('#entreprise_id');
        const interimaire = form.querySelector('#interimaire_id');
        const poste = form.querySelector('#poste');
        const salaire = form.querySelector('#salaire_horaire');
        const dateDebut = form.querySelector('#date_debut');
        const dateFin = form.querySelector('#date_fin');

        [entreprise, interimaire, poste, salaire, dateDebut, dateFin].forEach(clearFieldError);

        if (!entreprise.value) { showFieldError(entreprise, 'Sélectionnez une entreprise.'); valid = false; }
        if (!interimaire.value) { showFieldError(interimaire, 'Sélectionnez un intérimaire.'); valid = false; }
        if (!poste.value.trim()) { showFieldError(poste, 'Le poste est obligatoire.'); valid = false; }
        if (!salaire.value || parseFloat(salaire.value) <= 0) {
            showFieldError(salaire, 'Le salaire doit être positif.');
            valid = false;
        }
        if (!dateDebut.value) { showFieldError(dateDebut, 'La date de début est obligatoire.'); valid = false; }
        if (!dateFin.value) { showFieldError(dateFin, 'La date de fin est obligatoire.'); valid = false; }
        if (dateDebut.value && dateFin.value && dateFin.value < dateDebut.value) {
            showFieldError(dateFin, 'La date de fin doit être après la date de début.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

function initFeuilleValidation() {
    const form = document.getElementById('feuilleForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        let valid = true;
        const mission = form.querySelector('#mission_id');
        const date = form.querySelector('#date');
        const heures = form.querySelector('#heures_travaillees');

        [mission, date, heures].forEach(clearFieldError);

        if (!mission.value) { showFieldError(mission, 'Sélectionnez une mission.'); valid = false; }
        if (!date.value) { showFieldError(date, 'La date est obligatoire.'); valid = false; }
        const h = parseFloat(heures.value);
        if (!heures.value || h <= 0 || h > 24) {
            showFieldError(heures, 'Les heures doivent être entre 0 et 24.');
            valid = false;
        }

        if (!valid) e.preventDefault();
    });
}

function initLiveSearch() {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('interimairesTable');
    if (!searchInput || !table) return;

    searchInput.addEventListener('input', function () {
        const query = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(function (row) {
            if (row.cells.length <= 1) return;
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}
