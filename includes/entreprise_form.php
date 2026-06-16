<?php
$isEdit = isset($entreprise);
$formAction = $isEdit ? 'entreprise_modifier.php?id=' . (int) $entreprise['id'] : 'entreprise_ajouter.php';

$secteurs = ['Logistique', 'Informatique', 'Agroalimentaire', 'BTP', 'Santé', 'Commerce', 'Industrie', 'Services', 'Tourisme', 'Autre'];
?>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= e($formAction) ?>" class="form" id="entrepriseForm" novalidate>
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom_entreprise">Nom de l'entreprise <span class="required">*</span></label>
                    <input type="text" id="nom_entreprise" name="nom_entreprise"
                           class="form-control <?= isset($errors['nom_entreprise']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['nom_entreprise']) ?>" required>
                    <?php if (isset($errors['nom_entreprise'])): ?><span class="error-message"><?= e($errors['nom_entreprise']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="secteur_activite">Secteur d'activité <span class="required">*</span></label>
                    <input type="text" id="secteur_activite" name="secteur_activite" list="secteurs-list"
                           class="form-control <?= isset($errors['secteur_activite']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['secteur_activite']) ?>" required>
                    <datalist id="secteurs-list">
                        <?php foreach ($secteurs as $s): ?>
                            <option value="<?= e($s) ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <?php if (isset($errors['secteur_activite'])): ?><span class="error-message"><?= e($errors['secteur_activite']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="contact_principal">Contact principal</label>
                    <input type="text" id="contact_principal" name="contact_principal" class="form-control"
                           value="<?= e($data['contact_principal'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="tel" id="telephone" name="telephone"
                           class="form-control <?= isset($errors['telephone']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['telephone'] ?? '') ?>">
                    <?php if (isset($errors['telephone'])): ?><span class="error-message"><?= e($errors['telephone']) ?></span><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= e($data['email'] ?? '') ?>">
                    <?php if (isset($errors['email'])): ?><span class="error-message"><?= e($errors['email']) ?></span><?php endif; ?>
                </div>
                <div class="form-group full-width">
                    <label for="adresse">Adresse</label>
                    <textarea id="adresse" name="adresse" class="form-control" rows="2"><?= e($data['adresse'] ?? '') ?></textarea>
                </div>
            </div>
            <div class="form-actions">
                <a href="entreprises.php" class="btn btn-outline">Annuler</a>
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Enregistrer' : 'Ajouter' ?></button>
            </div>
        </form>
    </div>
</div>

<script src="js/validation.js"></script>
