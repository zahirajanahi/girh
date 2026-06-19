<?php
$isEdit = isset($interimaire);
$formAction = $isEdit ? 'interimaire_modifier.php?id=' . (int) $interimaire['id'] : 'interimaire_ajouter.php';
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= e($formAction) ?>" class="form" id="interimaireForm" novalidate>
            <h4 class="form-section-title"><i class="fa-solid fa-user"></i> Identité</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom">Nom <span class="required">*</span></label>
                    <input type="text" id="nom" name="nom" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['nom']) ?>" required>
                    <?php if (isset($errors['nom'])): ?><span class="error-message"><?= e($errors['nom']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom <span class="required">*</span></label>
                    <input type="text" id="prenom" name="prenom" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['prenom']) ?>" required>
                    <?php if (isset($errors['prenom'])): ?><span class="error-message"><?= e($errors['prenom']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="cin">CIN <span class="required">*</span></label>
                    <input type="text" id="cin" name="cin" class="form-control <?= isset($errors['cin']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['cin']) ?>" required>
                    <?php if (isset($errors['cin'])): ?><span class="error-message"><?= e($errors['cin']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="fonction">Fonction <span class="required">*</span></label>
                    <input type="text" id="fonction" name="fonction" class="form-control <?= isset($errors['fonction']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['fonction'] ?? '') ?>" required>
                    <?php if (isset($errors['fonction'])): ?><span class="error-message"><?= e($errors['fonction']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control"
                           value="<?= e($data['telephone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= e($data['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" class="form-control"
                           value="<?= e($data['date_naissance'] ?? '') ?>">
                </div>
                <div class="form-group full-width">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" class="form-control"
                           value="<?= e($data['adresse'] ?? '') ?>">
                </div>
            </div>

            <h4 class="form-section-title"><i class="fa-solid fa-file-contract"></i> Contrat</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label for="entreprise_id">Société cliente</label>
                    <select id="entreprise_id" name="entreprise_id" class="form-control">
                        <option value="">— Non assigné —</option>
                        <?php foreach ($entreprises as $ent): ?>
                            <option value="<?= (int) $ent['id'] ?>" <?= (string) ($data['entreprise_id'] ?? '') === (string) $ent['id'] ? 'selected' : '' ?>>
                                <?= e($ent['nom_entreprise']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="type_contrat">Type de contrat <span class="required">*</span></label>
                    <select id="type_contrat" name="type_contrat" class="form-control" required>
                        <option value="CDI" <?= ($data['type_contrat'] ?? '') === 'CDI' ? 'selected' : '' ?>>CDI</option>
                        <option value="CDD" <?= ($data['type_contrat'] ?? '') === 'CDD' ? 'selected' : '' ?>>CDD</option>
                        <option value="ANAPEC" <?= ($data['type_contrat'] ?? '') === 'ANAPEC' ? 'selected' : '' ?>>ANAPEC</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="date_debut">Date de début <span class="required">*</span></label>
                    <input type="date" id="date_debut" name="date_debut" class="form-control <?= isset($errors['date_debut']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['date_debut'] ?? '') ?>" required>
                    <?php if (isset($errors['date_debut'])): ?><span class="error-message"><?= e($errors['date_debut']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="date_fin">Date de fin</label>
                    <input type="date" id="date_fin" name="date_fin" class="form-control"
                           value="<?= e($data['date_fin'] ?? '') ?>">
                    <small class="form-hint" id="dateFinHint">CDD : calculée automatiquement (+6 mois). CDI : non applicable.</small>
                    <button type="button" class="btn btn-ghost btn-sm" id="unlockDateFin" style="display:none;margin-top:6px;">
                        <i class="fa-solid fa-pen"></i> Modifier manuellement
                    </button>
                </div>
                <div class="form-group">
                    <label for="statut">Statut <span class="required">*</span></label>
                    <select id="statut" name="statut" class="form-control" required>
                        <option value="en_mission" <?= ($data['statut'] ?? '') === 'en_mission' ? 'selected' : '' ?>>En mission</option>
                        <option value="paie_bloquee" <?= ($data['statut'] ?? '') === 'paie_bloquee' ? 'selected' : '' ?>>Paie bloquée</option>
                        <option value="fin_de_contrat" <?= ($data['statut'] ?? '') === 'fin_de_contrat' ? 'selected' : '' ?>>Fin de contrat</option>
                    </select>
                </div>
            </div>

            <h4 class="form-section-title"><i class="fa-solid fa-coins"></i> Rémunération</h4>
            <div class="form-grid">
                <div class="form-group">
                    <label for="salaire">Salaire (DH) <span class="required">*</span></label>
                    <input type="number" id="salaire" name="salaire" step="0.01" min="0"
                           class="form-control <?= isset($errors['salaire']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['salaire'] ?? '') ?>" required>
                    <?php if (isset($errors['salaire'])): ?><span class="error-message"><?= e($errors['salaire']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="type_salaire">Type de salaire <span class="required">*</span></label>
                    <select id="type_salaire" name="type_salaire" class="form-control" required>
                        <option value="mensuel" <?= ($data['type_salaire'] ?? 'mensuel') === 'mensuel' ? 'selected' : '' ?>>Mensuel</option>
                        <option value="horaire" <?= ($data['type_salaire'] ?? '') === 'horaire' ? 'selected' : '' ?>>Horaire</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mode_paiement">Mode de paiement <span class="required">*</span></label>
                    <select id="mode_paiement" name="mode_paiement" class="form-control" required>
                        <option value="virement" <?= ($data['mode_paiement'] ?? 'virement') === 'virement' ? 'selected' : '' ?>>Virement</option>
                        <option value="espece" <?= ($data['mode_paiement'] ?? '') === 'espece' ? 'selected' : '' ?>>Espèce</option>
                        <option value="cheque" <?= ($data['mode_paiement'] ?? '') === 'cheque' ? 'selected' : '' ?>>Chèque</option>
                    </select>
                </div>
                <div class="form-group full-width">
                    <label for="competences">Compétences</label>
                    <textarea id="competences" name="competences" class="form-control" rows="2"><?= e($data['competences'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="form-actions">
                <a href="interimaires.php" class="btn btn-outline"><i class="fa-solid fa-arrow-left"></i> Annuler</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-check"></i> <?= $isEdit ? 'Enregistrer' : 'Ajouter' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>.form-section-title{font-size:14px;font-weight:600;color:#1E293B;margin:24px 0 16px;display:flex;align-items:center;gap:8px}.form-section-title:first-child{margin-top:0}</style>
<script src="js/validation.js"></script>
<script src="js/interimaire-contrat.js"></script>
