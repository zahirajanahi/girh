<?php
$isEdit = $isEdit ?? isset($mission);
$formAction = $isEdit ? 'mission_modifier.php?id=' . (int) $mission['id'] : 'mission_ajouter.php';
?>

<div class="card">
    <div class="card-body">
        <?php if (!$isEdit && empty($interimairesDisponibles)): ?>
            <div class="alert alert-warning">
                Aucun intérimaire disponible. <a href="interimaire_ajouter.php">Ajouter un intérimaire</a> ou modifier la disponibilité d'un intérimaire existant.
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= e($formAction) ?>" class="form" id="missionForm" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label for="entreprise_id">Entreprise <span class="required">*</span></label>
                    <select id="entreprise_id" name="entreprise_id" class="form-control <?= isset($errors['entreprise_id']) ? 'is-invalid' : '' ?>" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($entreprises as $ent): ?>
                            <option value="<?= (int) $ent['id'] ?>" <?= (string) ($data['entreprise_id'] ?? '') === (string) $ent['id'] ? 'selected' : '' ?>>
                                <?= e($ent['nom_entreprise']) ?> (<?= e($ent['secteur_activite']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['entreprise_id'])): ?><span class="error-message"><?= e($errors['entreprise_id']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="interimaire_id">Intérimaire disponible <span class="required">*</span></label>
                    <select id="interimaire_id" name="interimaire_id" class="form-control <?= isset($errors['interimaire_id']) ? 'is-invalid' : '' ?>" required>
                        <option value="">— Sélectionner —</option>
                        <?php foreach ($interimairesDisponibles as $i): ?>
                            <option value="<?= (int) $i['id'] ?>" <?= (string) ($data['interimaire_id'] ?? '') === (string) $i['id'] ? 'selected' : '' ?>>
                                <?= e($i['prenom'] . ' ' . $i['nom']) ?> — <?= e($i['cin']) ?>
                                <?= !empty($i['competences']) ? '(' . e($i['competences']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['interimaire_id'])): ?><span class="error-message"><?= e($errors['interimaire_id']) ?></span><?php endif; ?>
                    <?php if (!$isEdit): ?>
                        <small class="form-hint">Seuls les intérimaires avec disponibilité « Disponible » sont listés.</small>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="poste">Poste <span class="required">*</span></label>
                    <input type="text" id="poste" name="poste" class="form-control <?= isset($errors['poste']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['poste']) ?>" required>
                    <?php if (isset($errors['poste'])): ?><span class="error-message"><?= e($errors['poste']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="salaire_horaire">Salaire horaire (DH) <span class="required">*</span></label>
                    <input type="number" id="salaire_horaire" name="salaire_horaire" step="0.01" min="0"
                           class="form-control <?= isset($errors['salaire_horaire']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['salaire_horaire']) ?>" required>
                    <?php if (isset($errors['salaire_horaire'])): ?><span class="error-message"><?= e($errors['salaire_horaire']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="date_debut">Date de début <span class="required">*</span></label>
                    <input type="date" id="date_debut" name="date_debut" class="form-control <?= isset($errors['date_debut']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['date_debut']) ?>" required>
                    <?php if (isset($errors['date_debut'])): ?><span class="error-message"><?= e($errors['date_debut']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="date_fin">Date de fin <span class="required">*</span></label>
                    <input type="date" id="date_fin" name="date_fin" class="form-control <?= isset($errors['date_fin']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['date_fin']) ?>" required>
                    <?php if (isset($errors['date_fin'])): ?><span class="error-message"><?= e($errors['date_fin']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="statut">Statut <span class="required">*</span></label>
                    <select id="statut" name="statut" class="form-control" required>
                        <option value="en_cours" <?= ($data['statut'] ?? '') === 'en_cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="terminee" <?= ($data['statut'] ?? '') === 'terminee' ? 'selected' : '' ?>>Terminée</option>
                        <option value="renouvelee" <?= ($data['statut'] ?? '') === 'renouvelee' ? 'selected' : '' ?>>Renouvelée</option>
                    </select>
                    <?php if (isset($errors['statut'])): ?><span class="error-message"><?= e($errors['statut']) ?></span><?php endif; ?>
                </div>
            </div>
            <div class="form-actions">
                <a href="missions.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Enregistrer' : 'Créer la mission' ?></button>
            </div>
        </form>
    </div>
</div>

<script src="js/validation.js"></script>
