<?php
$isEdit = isset($interimaire);
$formAction = $isEdit ? 'interimaire_modifier.php?id=' . (int) $interimaire['id'] : 'interimaire_ajouter.php';
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= e($formAction) ?>" class="form" id="interimaireForm" novalidate>
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
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone" class="form-control <?= isset($errors['telephone']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['telephone'] ?? '') ?>">
                    <?php if (isset($errors['telephone'])): ?><span class="error-message"><?= e($errors['telephone']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['email'] ?? '') ?>">
                    <?php if (isset($errors['email'])): ?><span class="error-message"><?= e($errors['email']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" class="form-control <?= isset($errors['date_naissance']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['date_naissance'] ?? '') ?>">
                    <?php if (isset($errors['date_naissance'])): ?><span class="error-message"><?= e($errors['date_naissance']) ?></span><?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse" class="form-control" rows="2"><?= e($data['adresse'] ?? '') ?></textarea>
                </div>
                <div class="form-group full-width">
                    <label for="competences">Compétences</label>
                    <textarea id="competences" name="competences" class="form-control" rows="2"
                              placeholder="Ex : Secrétariat, Comptabilité, Logistique"><?= e($data['competences'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label for="disponibilite">Disponibilité <span class="required">*</span></label>
                    <select id="disponibilite" name="disponibilite" class="form-control" required>
                        <option value="disponible" <?= ($data['disponibilite'] ?? '') === 'disponible' ? 'selected' : '' ?>>Disponible</option>
                        <option value="en_mission" <?= ($data['disponibilite'] ?? '') === 'en_mission' ? 'selected' : '' ?>>En mission</option>
                        <option value="indisponible" <?= ($data['disponibilite'] ?? '') === 'indisponible' ? 'selected' : '' ?>>Indisponible</option>
                    </select>
                    <?php if (isset($errors['disponibilite'])): ?><span class="error-message"><?= e($errors['disponibilite']) ?></span><?php endif; ?>
                </div>
                <?php if (!$isEdit): ?>
                <div class="form-group">
                    <label for="date_inscription">Date d'inscription</label>
                    <input type="date" id="date_inscription" name="date_inscription" class="form-control"
                           value="<?= e($data['date_inscription']) ?>">
                </div>
                <?php endif; ?>
            </div>
            <div class="form-actions">
                <a href="interimaires.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Enregistrer' : 'Ajouter' ?></button>
            </div>
        </form>
    </div>
</div>

<script src="js/validation.js"></script>
