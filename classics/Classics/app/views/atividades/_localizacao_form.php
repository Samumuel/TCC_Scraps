<?php
$atividade = $atividade ?? [];
$localizacao = $localizacao ?? [];
$erros = $erros ?? [];
$origem = $origem ?? 'create';
$submitLabel = $submitLabel ?? 'Salvar localização';
?>

<input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
<input type="hidden" name="origem" value="<?= htmlspecialchars($origem, ENT_QUOTES, 'UTF-8') ?>">

<div class="mb-3">
    <label for="cep" class="form-label">CEP</label>
    <input type="text" class="form-control <?= isset($erros['cep']) ? 'is-invalid' : '' ?>" id="cep" name="cep" value="<?= htmlspecialchars($localizacao['CEP'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    <?php if (isset($erros['cep'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($erros['cep'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
</div>

<div class="mb-3">
    <label for="complemento" class="form-label">Complemento</label>
    <input type="text" class="form-control <?= isset($erros['complemento']) ? 'is-invalid' : '' ?>" id="complemento" name="complemento" value="<?= htmlspecialchars($localizacao['Complemento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    <?php if (isset($erros['complemento'])): ?>
        <div class="invalid-feedback"><?= htmlspecialchars($erros['complemento'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></button>
</div>
