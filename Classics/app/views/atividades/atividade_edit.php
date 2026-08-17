<?php
if (!isset($_SESSION['empresa_logada'])) {
    header('Location: ' . URL_BASE);
    exit();
}

$atividade = $atividade ?? [];
$categorias = $categorias ?? [];
$localizacao = $localizacao ?? [];
$atividadeCategoriaId = (int) ($atividade['Categoria_ID'] ?? 0);
$abaAtiva = $aba_ativa ?? 'atividade';
$ativaAtividade = $abaAtiva === 'atividade' ? 'active' : '';
$ativaLocalizacao = $abaAtiva === 'localizacao' ? 'active' : '';
$paneAtividade = $abaAtiva === 'atividade' ? 'show active' : '';
$paneLocalizacao = $abaAtiva === 'localizacao' ? 'show active' : '';
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

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <ul class="nav nav-tabs mb-4" id="atividadeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $ativaAtividade ?>" id="atividade-tab" data-bs-toggle="tab" data-bs-target="#atividade-pane" type="button" role="tab" aria-controls="atividade-pane" aria-selected="<?= $abaAtiva === 'atividade' ? 'true' : 'false' ?>">
                            Dados da atividade
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $ativaLocalizacao ?>" id="localizacao-tab" data-bs-toggle="tab" data-bs-target="#localizacao-pane" type="button" role="tab" aria-controls="localizacao-pane" aria-selected="<?= $abaAtiva === 'localizacao' ? 'true' : 'false' ?>">
                            Localização
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="atividadeTabsContent">
                    <div class="tab-pane fade <?= $paneAtividade ?>" id="atividade-pane" role="tabpanel" aria-labelledby="atividade-tab" tabindex="0">
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
                                <label for="valor" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="valor" name="valor" value="<?= htmlspecialchars((string) ($atividade['Valor'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            </div>


                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary" <?= empty($categorias) ? 'disabled' : '' ?>>Atualizar</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade <?= $paneLocalizacao ?>" id="localizacao-pane" role="tabpanel" aria-labelledby="localizacao-tab" tabindex="0">
                        <div class="row g-4">
                            <div class="col-lg-4">
                                <div class="card h-100 border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Resumo da atividade</h5>
                                        <p class="mb-2"><strong>Nome:</strong> <?= htmlspecialchars($atividade['Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="mb-2"><strong>Categoria:</strong> <?= htmlspecialchars($atividade['Categoria_Nome'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                        <p class="mb-0"><strong>Localização atual:</strong> <?= htmlspecialchars($atividade['Localizacao'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="card shadow-sm">
                                    <div class="card-body">
                                        <form action="<?= URL_BASE ?>/atividade/localizacao/salvar" method="POST">
                                            <input type="hidden" name="id_atividade" value="<?= (int) ($atividade['ID_Atividade'] ?? 0) ?>">
                                            <input type="hidden" name="origem" value="editar">

                                            <div class="mb-3">
                                                <label for="cep" class="form-label">CEP</label>
                                                <input type="text" class="form-control" id="cep" name="cep" value="<?= htmlspecialchars($localizacao['CEP'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                            </div>

                                            <div class="mb-3">
                                                <label for="complemento" class="form-label">Complemento</label>
                                                <input type="text" class="form-control" id="complemento" name="complemento" value="<?= htmlspecialchars($localizacao['Complemento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="<?= URL_BASE ?>/atividade/listar" class="btn btn-outline-secondary">Cancelar</a>
                                                <button type="submit" class="btn btn-primary">
                                                    <?= !empty($localizacao) ? 'Salvar alterações' : 'Cadastrar localização' ?>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
