<?php
$atividade = $atividade ?? [];
$localizacao = $localizacao ?? [];
$origem = $origem ?? 'create';
$submitLabel = $submitLabel ?? 'Salvar localização';
?>

<input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
<input type="hidden" name="origem" value="<?= htmlspecialchars($origem, ENT_QUOTES, 'UTF-8') ?>">

<div class="mb-3">
    <label for="cep" class="form-label">CEP</label>
    <input type="text" class="form-control" id="cep" name="cep" value="<?= htmlspecialchars($localizacao['CEP'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
</div>

<div class="mb-3">
    <label for="complemento" class="form-label">Complemento</label>
    <input type="text" class="form-control" id="complemento" name="complemento" value="<?= htmlspecialchars($localizacao['Complemento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
</div>

<div class="d-flex justify-content-end gap-2">
    <button type="submit" class="btn btn-primary"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></button>
</div>
