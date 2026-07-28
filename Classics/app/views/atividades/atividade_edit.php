<?php
if (!isset($_SESSION['empresa_logada'])) {
    header('Location: ' . URL_BASE);
    exit();
}

$atividade = $atividade ?? [];
$categorias = $categorias ?? [];
$atividadeCategoriaId = (int) ($atividade['Categoria_ID'] ?? 0);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Projeto Integrador • Editar Atividade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Editar Atividade</h2>
            <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card shadow-sm col-lg-8 mx-auto">
            <div class="card-body p-4">
                <form action="<?= URL_BASE ?>/atividade/atualizar" method="POST">
                    <input type="hidden" name="id" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome da atividade</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= htmlspecialchars($atividade['Nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3"><?= htmlspecialchars($atividade['Descricao'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="categoria_id" class="form-label">Categoria</label>
                        <select class="form-select" id="categoria_id" name="categoria_id" required <?= empty($categorias) ? 'disabled' : '' ?>>
                            <option value="">Selecione uma categoria</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['ID_Categoria'] ?>" <?= (int) $categoria['ID_Categoria'] === $atividadeCategoriaId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['Nome_Categoria'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (empty($categorias)): ?>
                        <div class="alert alert-warning" role="alert">
                            Nao ha categorias cadastradas para esta empresa.
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="duracao" class="form-label">Duração</label>
                        <input type="text" class="form-control" id="duracao" name="duracao" value="<?= htmlspecialchars($atividade['Duracao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="localizacao" class="form-label">Localização</label>
                        <input type="text" class="form-control" id="localizacao" name="localizacao" value="<?= htmlspecialchars($atividade['Localizacao'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor</label>
                        <input type="text" class="form-control" id="valor" name="valor" value="<?= htmlspecialchars((string) ($atividade['Valor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="fotos" class="form-label">Fotos</label>
                        <input type="text" class="form-control" id="fotos" name="fotos" value="<?= htmlspecialchars($atividade['Fotos'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary" <?= empty($categorias) ? 'disabled' : '' ?>>Atualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
